<?php

/**
 * MONEI Payment Gateway API v1
 * Connect
 *
 * @category Class
 * @package  Monei
 * @author   MONEI
 * @link     https://monei.com
 */

namespace Monei;

use Monei\Internal\GuzzleHttp\Client;
use Monei\Internal\GuzzleHttp\ClientInterface;
use Monei\Internal\GuzzleHttp\Exception\GuzzleException;
use Monei\Internal\GuzzleHttp\Exception\RequestException;

/**
 * "Connect with MONEI" for the shop plugins: the OAuth flow of the MONEI broker that gives a shop
 * its own scoped API keys (mk_) and signing secrets (mksec_).
 */
final class Connect
{
    /** @var string */
    public const BASE_URL = 'https://oauth.monei.com';

    /**
     * Seconds. The broker Lambda times out at 25 s: a shorter wait gives up while the broker still
     * makes the keys.
     *
     * @var int
     */
    public const EXCHANGE_TIMEOUT = 35;

    private const MAX_URI_LENGTH = 512;
    private const MAX_HOST_LENGTH = 253;
    private const MAX_KEY_NAME_LENGTH = 300;
    private const MONEI_EXEMPT_DOMAIN = 'monei-dev-tunnel.com';

    // The zod 4 `z.hostname()` pattern, which the broker uses for the key's host.
    private const HOST_PATTERN = '/^(?=.{1,253}\.?\z)[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?'
        . '(?:\.[a-zA-Z0-9](?:[-0-9a-zA-Z]{0,61}[0-9a-zA-Z])?)*\.?\z/';

    // The callback forms that the broker pins per client (authorization-service
    // src/broker/integrations.ts). `search` is matched against `URL.search`.
    private const CLIENTS = [
        'woocommerce' => [
            'name' => 'WooCommerce',
            'callbacks' => [
                ['path' => '/wp-admin/admin-post.php', 'search' => '/^\?action=monei_connect\z/'],
            ],
            'maxPrefix' => 3,
            'indexPhp' => 0,
        ],
        'prestashop' => [
            'name' => 'PrestaShop',
            'callbacks' => [
                ['path' => '/module/monei/connect', 'search' => '/^\z/'],
                [
                    'path' => '/index.php',
                    'search' => '/^\?fc=module&module=monei&controller=connect(?:&id_lang=[0-9]+)?\z/',
                ],
            ],
            'maxPrefix' => 3,
            'indexPhp' => 0,
        ],
        'magento' => [
            'name' => 'Magento',
            'callbacks' => [
                ['path' => '/monei/payment/connect/', 'search' => '/^\z/'],
            ],
            'maxPrefix' => 2,
            'indexPhp' => 1,
        ],
    ];

    /**
     * Start a connect. Store `state` with `verifier` and the redirect URI, then redirect the
     * merchant to `url`.
     *
     * @param string $client      woocommerce, prestashop or magento
     * @param string $redirectUri The output of canonicalRedirectUri()
     * @param string $baseUrl     The broker base URL
     * @return array{url: string, state: string, verifier: string}
     */
    public static function start(string $client, string $redirectUri, string $baseUrl = self::BASE_URL): array
    {
        $state = self::base64Url(random_bytes(32));
        $verifier = self::base64Url(random_bytes(48));
        $query = http_build_query([
            'client_id' => $client,
            'redirect_uri' => $redirectUri,
            'state' => $state,
            'code_challenge' => self::codeChallenge($verifier),
            'code_challenge_method' => 'S256',
        ], '', '&', PHP_QUERY_RFC3986);

        return [
            'url' => rtrim($baseUrl, '/') . '/integrations/authorize?' . $query,
            'state' => $state,
            'verifier' => $verifier,
        ];
    }

    /**
     * The PKCE S256 challenge of a verifier (RFC 7636).
     *
     * @param string $verifier
     * @return string
     */
    public static function codeChallenge(string $verifier): string
    {
        return self::base64Url(hash('sha256', $verifier, true));
    }

    /**
     * Exchange the callback code for the keys.
     *
     * Each mode in the result has `accountId`, `apiKey` and `signingSecret`. `test.accountId` is
     * the test account. `errors` holds the refusal code of a mode that got no key: blocked,
     * ineligible, cap, not_approved or temporarily_unavailable.
     *
     * @param string               $code        The code of the callback
     * @param string               $verifier    The verifier that start() gave
     * @param string               $client      woocommerce, prestashop or magento
     * @param string               $redirectUri The redirect URI that start() sent
     * @param string               $baseUrl     The broker base URL
     * @param ClientInterface|null $httpClient  The HTTP client, for tests
     * @throws ConnectException With the OAuth error code of the broker
     * @return array<string, array<string, string>> Keys `live`, `test` and `errors`, each optional
     */
    public static function exchange(
        string $code,
        string $verifier,
        string $client,
        string $redirectUri,
        string $baseUrl = self::BASE_URL,
        ?ClientInterface $httpClient = null
    ): array {
        $httpClient = $httpClient ?: new Client();
        try {
            $response = $httpClient->request('POST', rtrim($baseUrl, '/') . '/integrations/token', [
                'form_params' => [
                    'code' => $code,
                    'code_verifier' => $verifier,
                    'client_id' => $client,
                    'redirect_uri' => $redirectUri,
                ],
                'timeout' => self::EXCHANGE_TIMEOUT,
                'http_errors' => false,
                'allow_redirects' => false,
            ]);
        } catch (GuzzleException $e) {
            // A transfer cut in the body keeps the partial response, which can hold keys.
            $previous = $e instanceof RequestException && $e->hasResponse() ? null : $e;
            throw new ConnectException('temporarily_unavailable', 0, $previous);
        }

        $status = $response->getStatusCode();
        $body = json_decode((string) $response->getBody(), true);
        if ($status < 200 || $status >= 300) {
            // A gateway answer ({"message": ...}), a non-JSON body or a 404 of the switched-off
            // broker has no OAuth error code.
            $error = is_array($body) && isset($body['error']) && is_string($body['error'])
                && preg_match('/^[a-z_]{1,64}\z/', $body['error'])
                ? $body['error']
                : 'temporarily_unavailable';
            throw new ConnectException($error, $status);
        }

        $result = is_array($body) ? self::tokenResult($body) : null;
        if ($result === null) {
            throw new ConnectException('temporarily_unavailable', $status);
        }
        return $result;
    }

    /**
     * The redirect URI in the canonical WHATWG form that the broker requires: lower-case scheme and
     * host, an IDN host as punycode, no default port, `/` for an empty path, and non-ASCII
     * characters of the path and the query percent-encoded.
     *
     * @param string $uri
     * @throws \InvalidArgumentException When the URI has no host, or when the host is not ASCII and
     *     ext-intl is missing or refuses it
     * @return string
     */
    public static function canonicalRedirectUri(string $uri): string
    {
        $parts = self::split($uri);
        if ($parts === null) {
            throw new \InvalidArgumentException('The redirect URI has no scheme and host');
        }
        $scheme = strtolower($parts['scheme']);
        $authority = self::splitAuthority($parts['authority']);
        $host = $authority['host'];
        if (preg_match('/[^\x00-\x7F]/', $host)) {
            if (!function_exists('idn_to_ascii')) {
                throw new \InvalidArgumentException('A non-ASCII host needs ext-intl');
            }
            $ascii = idn_to_ascii($host, IDNA_NONTRANSITIONAL_TO_ASCII, INTL_IDNA_VARIANT_UTS46);
            if ($ascii === false) {
                throw new \InvalidArgumentException('The host is not a valid IDN');
            }
            $host = $ascii;
        }
        $host = strtolower($host);

        $port = $authority['port'];
        $defaultPorts = ['http' => 80, 'https' => 443];
        if ($port !== null && ctype_digit($port)) {
            $port = (string) (int) $port;
        }
        if ($port === '' || (isset($defaultPorts[$scheme]) && $port === (string) $defaultPorts[$scheme])) {
            $port = null;
        }

        $path = $parts['path'] === '' ? '/' : $parts['path'];

        return $scheme . '://'
            . ($authority['userinfo'] !== null ? $authority['userinfo'] . '@' : '')
            . $host
            . ($port !== null ? ':' . $port : '')
            . self::percentEncode($path)
            . ($parts['query'] !== null ? '?' . self::percentEncode($parts['query']) : '')
            . ($parts['fragment'] !== null ? '#' . $parts['fragment'] : '');
    }

    /**
     * Why the broker refuses the redirect URI, or null.
     *
     * It makes the checks of the broker that a plugin can make, in the broker's order and with its
     * codes: length, userinfo, fragment, notHttps, ipLiteral, trailingDot, localhost, noDot,
     * keyHost, moneiHost, then the callback form of the client (callbackPath, callbackDepth,
     * callbackEncoded, callbackEmptySegment, callbackIndexPhp, callbackDot, callbackSegment), then
     * nameLength. The broker can still refuse a URI that passes, for example a blocklisted host.
     *
     * @param string $client      woocommerce, prestashop or magento
     * @param string $redirectUri The output of canonicalRedirectUri()
     * @throws \InvalidArgumentException For an unknown client
     * @return string|null
     */
    public static function callbackProblem(string $client, string $redirectUri): ?string
    {
        if (!isset(self::CLIENTS[$client])) {
            throw new \InvalidArgumentException('Unknown client: ' . $client);
        }
        if (strlen($redirectUri) > self::MAX_URI_LENGTH) {
            return 'length';
        }
        $parts = self::split($redirectUri);
        $authority = $parts !== null ? self::splitAuthority($parts['authority']) : null;
        if ($authority !== null && $authority['userinfo'] !== null) {
            return 'userinfo';
        }
        if (strpos($redirectUri, '#') !== false) {
            return 'fragment';
        }
        // The broker gives httpHost, query or scheme for some of these; a plugin shows the same
        // "use manual keys" for each.
        if ($parts === null || $parts['scheme'] !== 'https') {
            return 'notHttps';
        }

        $hostname = $authority['host'];
        if (
            substr($hostname, 0, 1) === '['
            || filter_var($hostname, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false
        ) {
            return 'ipLiteral';
        }
        if (substr($hostname, -1) === '.') {
            return 'trailingDot';
        }
        if ($hostname === 'localhost' || substr($hostname, -strlen('.localhost')) === '.localhost') {
            return 'localhost';
        }
        if (strpos($hostname, '.') === false) {
            return 'noDot';
        }
        if (strlen($hostname) > self::MAX_HOST_LENGTH || !preg_match(self::HOST_PATTERN, $hostname)) {
            return 'keyHost';
        }
        if (self::isMoneiLike($hostname)) {
            return 'moneiHost';
        }

        $path = $parts['path'] === '' ? '/' : $parts['path'];
        $search = $parts['query'] === null || $parts['query'] === '' ? '' : '?' . $parts['query'];
        $prefix = self::prefixOf($client, $path, $search);
        $problem = self::pathProblem($client, $prefix);
        if ($problem !== null) {
            return $problem;
        }

        $host = $hostname . ($authority['port'] !== null ? ':' . $authority['port'] : '');
        $keyName = self::CLIENTS[$client]['name'] . ': ' . $host . $prefix;
        if (strlen($keyName) > self::MAX_KEY_NAME_LENGTH) {
            return 'nameLength';
        }
        return null;
    }

    /**
     * @param array $body
     * @return array|null
     */
    private static function tokenResult(array $body): ?array
    {
        $result = [];
        foreach (['live', 'test'] as $mode) {
            if (!array_key_exists($mode, $body)) {
                continue;
            }
            $keys = $body[$mode];
            foreach (['accountId', 'apiKey', 'signingSecret'] as $field) {
                if (!is_array($keys) || !isset($keys[$field]) || !is_string($keys[$field])) {
                    return null;
                }
            }
            $result[$mode] = [
                'accountId' => $keys['accountId'],
                'apiKey' => $keys['apiKey'],
                'signingSecret' => $keys['signingSecret'],
            ];
        }
        if (array_key_exists('errors', $body)) {
            if (!is_array($body['errors'])) {
                return null;
            }
            $errors = [];
            foreach (['live', 'test'] as $mode) {
                if (isset($body['errors'][$mode])) {
                    if (!is_string($body['errors'][$mode])) {
                        return null;
                    }
                    $errors[$mode] = $body['errors'][$mode];
                }
            }
            $result['errors'] = $errors;
        }
        return $result === [] ? null : $result;
    }

    /**
     * The install path before the client's callback suffix, or null for a URI with no callback form.
     *
     * @param string $client
     * @param string $path
     * @param string $search
     * @return string|null
     */
    private static function prefixOf(string $client, string $path, string $search): ?string
    {
        foreach (self::CLIENTS[$client]['callbacks'] as $form) {
            $suffix = $form['path'];
            if (substr($path, -strlen($suffix)) === $suffix && preg_match($form['search'], $search)) {
                return substr($path, 0, strlen($path) - strlen($suffix));
            }
        }
        return null;
    }

    /**
     * @param string      $client
     * @param string|null $prefix
     * @return string|null
     */
    private static function pathProblem(string $client, ?string $prefix): ?string
    {
        if ($prefix === null) {
            return 'callbackPath';
        }
        $segments = $prefix === '' ? [] : explode('/', substr($prefix, 1));
        $plain = array_values(array_filter($segments, function ($segment) {
            return $segment !== 'index.php';
        }));
        if (count($plain) > self::CLIENTS[$client]['maxPrefix']) {
            return 'callbackDepth';
        }
        if (strpos($prefix, '%') !== false) {
            return 'callbackEncoded';
        }
        if (in_array('', $segments, true)) {
            return 'callbackEmptySegment';
        }
        if (count($segments) - count($plain) > self::CLIENTS[$client]['indexPhp']) {
            return 'callbackIndexPhp';
        }
        foreach ($plain as $segment) {
            if (strpos($segment, '.') !== false) {
                return 'callbackDot';
            }
        }
        foreach ($plain as $segment) {
            if (!preg_match('/^[A-Za-z0-9_-]+\z/', $segment)) {
                return 'callbackSegment';
            }
        }
        return null;
    }

    /**
     * A label part, split on `-`, starts or ends with `monei`. Hosts under the exempt domain pass.
     *
     * @param string $hostname
     * @return bool
     */
    private static function isMoneiLike(string $hostname): bool
    {
        $exempt = '.' . self::MONEI_EXEMPT_DOMAIN;
        if (substr($hostname, -strlen($exempt)) === $exempt) {
            return false;
        }
        foreach (explode('.', $hostname) as $label) {
            foreach (explode('-', $label) as $part) {
                if (strpos($part, 'monei') === 0 || substr($part, -5) === 'monei') {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * The RFC 3986 parts of a URI with a scheme and an authority, or null.
     *
     * @param string $uri
     * @return array{scheme: string, authority: string, path: string, query: string|null, fragment: string|null}|null
     */
    private static function split(string $uri): ?array
    {
        if (!preg_match('~^([A-Za-z][A-Za-z0-9+.-]*)://([^/?#]*)([^?#]*)(\?[^#]*)?(#.*)?\z~s', $uri, $m)) {
            return null;
        }
        return [
            'scheme' => $m[1],
            'authority' => $m[2],
            'path' => $m[3],
            'query' => isset($m[4]) && $m[4] !== '' ? substr($m[4], 1) : null,
            'fragment' => isset($m[5]) && $m[5] !== '' ? substr($m[5], 1) : null,
        ];
    }

    /**
     * @param string $authority
     * @return array{userinfo: string|null, host: string, port: string|null}
     */
    private static function splitAuthority(string $authority): array
    {
        $userinfo = null;
        $at = strrpos($authority, '@');
        if ($at !== false) {
            $userinfo = substr($authority, 0, $at);
            $authority = substr($authority, $at + 1);
        }
        $port = null;
        $colon = strrpos($authority, ':');
        $bracket = strrpos($authority, ']');
        if ($colon !== false && ($bracket === false || $colon > $bracket)) {
            $port = substr($authority, $colon + 1);
            $authority = substr($authority, 0, $colon);
        }
        return ['userinfo' => $userinfo, 'host' => $authority, 'port' => $port];
    }

    /**
     * Percent-encodes the bytes that WHATWG encodes in both a path and a query: controls, space,
     * `"`, `<`, `>` and non-ASCII.
     *
     * @param string $value
     * @return string
     */
    private static function percentEncode(string $value): string
    {
        return preg_replace_callback('/[\x00-\x20"<>\x7F-\xFF]/', function ($m) {
            return sprintf('%%%02X', ord($m[0]));
        }, $value);
    }

    /**
     * @param string $bytes
     * @return string
     */
    private static function base64Url(string $bytes): string
    {
        return rtrim(strtr(base64_encode($bytes), '+/', '-_'), '=');
    }
}
