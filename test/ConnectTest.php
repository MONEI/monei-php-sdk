<?php

namespace Tests;

use Monei\Connect;
use Monei\ConnectException;
use Monei\Internal\GuzzleHttp\Client;
use Monei\Internal\GuzzleHttp\Exception\ConnectException as TransportException;
use Monei\Internal\GuzzleHttp\Exception\RequestException;
use Monei\Internal\GuzzleHttp\Handler\MockHandler;
use Monei\Internal\GuzzleHttp\HandlerStack;
use Monei\Internal\GuzzleHttp\Middleware;
use Monei\Internal\GuzzleHttp\Psr7\Request;
use Monei\Internal\GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class ConnectTest extends TestCase
{
    private const WOO = 'https://shop.example/wp-admin/admin-post.php?action=monei_connect';
    private const WOO_PATH = '/wp-admin/admin-post.php?action=monei_connect';

    /** @var MockHandler */
    private $mockHandler;

    /** @var array */
    private $history = [];

    /** @var Client */
    private $httpClient;

    protected function setUp(): void
    {
        $this->mockHandler = new MockHandler();
        $stack = HandlerStack::create($this->mockHandler);
        $stack->push(Middleware::history($this->history));
        $this->httpClient = new Client(['handler' => $stack]);
    }

    // The keys are concatenated so that secret scanners do not flag these test values.
    private static function liveKey(): string
    {
        return 'mk' . '_live_c2NvcGVkLWxpdmUta2V5';
    }

    private static function liveSecret(): string
    {
        return 'mksec' . '_bGl2ZS1zaWduaW5nLXNlY3JldA';
    }

    private static function testKey(): string
    {
        return 'mk' . '_test_c2NvcGVkLXRlc3Qta2V5';
    }

    private static function testSecret(): string
    {
        return 'mksec' . '_dGVzdC1zaWduaW5nLXNlY3JldA';
    }

    private function exchange(): array
    {
        return Connect::exchange(
            'code-1',
            'verifier-1',
            'woocommerce',
            self::WOO,
            Connect::BASE_URL,
            $this->httpClient
        );
    }

    private function expectConnectError(string $error): void
    {
        $this->expectException(ConnectException::class);
        $this->expectExceptionMessage('MONEI connect failed: ' . $error);
    }

    /**
     * A challenge that is not S256 of the verifier fails every PKCE check of the broker.
     */
    public function testCodeChallengeIsS256OfTheVerifier(): void
    {
        // RFC 7636 appendix B
        $this->assertSame(
            'E9Melhoa2OwvFrEMTJguCHaoeK1t8URWbuGJSstw-cM',
            Connect::codeChallenge('dBjftJeZ4CVP-mB92K27uhbUJU1p1r_wW1gFWFOEjXk')
        );
    }

    /**
     * A redirect_uri that is not encoded breaks the query on its own `?` and `&`, and a challenge
     * of another verifier fails the exchange.
     */
    public function testStartUrlCarriesTheAuthorizeParameters(): void
    {
        $start = Connect::start('woocommerce', self::WOO);

        $this->assertStringStartsWith(
            'https://oauth.monei.com/integrations/authorize?client_id=woocommerce&redirect_uri='
            . 'https%3A%2F%2Fshop.example%2Fwp-admin%2Fadmin-post.php%3Faction%3Dmonei_connect&state=',
            $start['url']
        );
        parse_str((string) parse_url($start['url'], PHP_URL_QUERY), $query);
        $this->assertSame(self::WOO, $query['redirect_uri']);
        $this->assertSame($start['state'], $query['state']);
        $this->assertSame(Connect::codeChallenge($start['verifier']), $query['code_challenge']);
        $this->assertSame('S256', $query['code_challenge_method']);
    }

    /**
     * A reused state lets a callback of one flow land in another; a reused verifier weakens PKCE.
     * The state is 32 random bytes and the verifier has 64 characters.
     */
    public function testStateAndVerifierAreFreshOnEachCall(): void
    {
        $first = Connect::start('woocommerce', self::WOO);
        $second = Connect::start('woocommerce', self::WOO);

        $this->assertNotSame($first['state'], $second['state']);
        $this->assertNotSame($first['verifier'], $second['verifier']);
        $this->assertMatchesRegularExpression('/^[A-Za-z0-9_-]{43}$/', $first['state']);
        $this->assertMatchesRegularExpression('/^[A-Za-z0-9_-]{64}$/', $first['verifier']);
    }

    /**
     * The broker reads exactly these form fields. An Authorization header would send a shop key
     * to a route that needs none.
     */
    public function testExchangeSendsTheExactFormWithoutAuthorization(): void
    {
        $this->mockHandler->append(new Response(200, [], '{"errors":{"live":"cap"}}'));

        Connect::exchange(
            'code-1',
            'verifier-1',
            'woocommerce',
            self::WOO,
            'https://oauth.monei-dev.com/',
            $this->httpClient
        );

        /** @var Request $request */
        $request = $this->history[0]['request'];
        $this->assertSame('POST', $request->getMethod());
        $this->assertSame('https://oauth.monei-dev.com/integrations/token', (string) $request->getUri());
        $this->assertSame('application/x-www-form-urlencoded', $request->getHeaderLine('Content-Type'));
        $this->assertFalse($request->hasHeader('Authorization'));
        parse_str((string) $request->getBody(), $form);
        $this->assertSame([
            'code' => 'code-1',
            'code_verifier' => 'verifier-1',
            'client_id' => 'woocommerce',
            'redirect_uri' => self::WOO,
        ], $form);
    }

    /**
     * The broker Lambda runs up to 25 s. A shorter client timeout gives up while the broker still
     * makes keys, which then count toward the cap with nobody holding them.
     */
    public function testExchangeWaits35Seconds(): void
    {
        $this->mockHandler->append(new Response(200, [], '{"errors":{"live":"cap"}}'));

        $this->exchange();

        $this->assertSame(35, $this->mockHandler->getLastOptions()['timeout']);
    }

    /**
     * A plugin that reads the keys from the wrong place stores nothing, or stores the Live key as
     * Test.
     */
    public function testExchangeReturnsTheKeysAndErrors(): void
    {
        $body = json_encode([
            'live' => ['accountId' => 'acc-live', 'apiKey' => self::liveKey(), 'signingSecret' => self::liveSecret()],
            'test' => ['accountId' => 'acc-test', 'apiKey' => self::testKey(), 'signingSecret' => self::testSecret()],
            'errors' => [],
        ]);
        $this->mockHandler->append(new Response(200, [], $body));

        $this->assertSame([
            'live' => ['accountId' => 'acc-live', 'apiKey' => self::liveKey(), 'signingSecret' => self::liveSecret()],
            'test' => ['accountId' => 'acc-test', 'apiKey' => self::testKey(), 'signingSecret' => self::testSecret()],
            'errors' => [],
        ], $this->exchange());
    }

    /**
     * Both modes refused is a 200, not an error answer. Treating it as a failure hides the codes
     * that tell the merchant what to do (cap: revoke unused keys).
     */
    public function testExchangeReturnsOnlyErrors(): void
    {
        $this->mockHandler->append(new Response(200, [], '{"errors":{"live":"cap","test":"cap"}}'));

        $this->assertSame(['errors' => ['live' => 'cap', 'test' => 'cap']], $this->exchange());
    }

    /**
     * A plugin shows a message per code. An error answer that loses its code shows the wrong one,
     * for example "try again" when the merchant denied access.
     */
    public function testExchangeThrowsTheErrorCodeOfAnErrorAnswer(): void
    {
        $this->mockHandler->append(new Response(403, [], '{"error":"access_denied"}'));

        try {
            $this->exchange();
            $this->fail('No exception');
        } catch (ConnectException $e) {
            $this->assertSame('access_denied', $e->getError());
            $this->assertSame(403, $e->getCode());
        }
    }

    /**
     * Only the broker sends an OAuth error code. Any other answer must not pass as a success or
     * surface a gateway text as a code.
     *
     * @dataProvider provideAnswersWithoutErrorCode
     */
    public function testExchangeAnswerWithoutErrorCodeIsTemporarilyUnavailable(
        int $status,
        string $body,
        array $headers = []
    ): void {
        // The answer of the redirect target, should a redirect be followed.
        $this->mockHandler->append(
            new Response($status, $headers, $body),
            new Response(200, [], '{"errors":{"live":"cap"}}')
        );

        $this->expectConnectError('temporarily_unavailable');
        $this->expectExceptionCode($status);

        $this->exchange();
    }

    public function provideAnswersWithoutErrorCode(): array
    {
        return [
            'API Gateway 503' => [503, '{"message":"Service Unavailable"}'],
            'API Gateway 504' => [504, '{"message":"Endpoint request timed out"}'],
            'broker switch off' => [404, '{"message":"Not Found"}'],
            'non-JSON body' => [502, '<html>Bad Gateway</html>'],
            // A followed redirect would turn this into the answer of another URL.
            'redirect' => [302, '', ['Location' => 'https://oauth.monei.com/elsewhere']],
            '200 without keys or errors' => [200, '{}'],
            // The plugin would have neither keys to store nor a code to show.
            '200 with an empty errors map' => [200, '{"errors":{}}'],
            '200 with an empty refusal code' => [200, '{"errors":{"live":""}}'],
        ];
    }

    /**
     * A timeout or a DNS error must reach the plugin as the one exception type it catches.
     */
    public function testExchangeTransportErrorIsTemporarilyUnavailable(): void
    {
        $this->mockHandler->append(
            new TransportException('cURL error 28', new Request('POST', Connect::BASE_URL . '/integrations/token'))
        );

        $this->expectConnectError('temporarily_unavailable');

        $this->exchange();
    }

    /**
     * cURL keeps the partial answer of a transfer cut in the body (for example error 18). An
     * exception that chains it puts the keys in the shop's log.
     */
    public function testExchangeTransferCutInTheBodyHoldsNoKeyInTheException(): void
    {
        $truncated = '{"live":{"accountId":"acc-live","apiKey":"' . self::liveKey();
        $request = new Request('POST', Connect::BASE_URL . '/integrations/token');
        $this->mockHandler->append(
            new RequestException('cURL error 18', $request, new Response(200, [], $truncated))
        );

        try {
            $this->exchange();
            $this->fail('No exception');
        } catch (ConnectException $e) {
            $this->assertSame('temporarily_unavailable', $e->getError());
            $this->assertNull($e->getPrevious());
        }
    }

    /**
     * Plugins log the exception message. A message that quotes the body would write keys to the
     * shop's log.
     */
    public function testExchangeUnparsableSuccessHoldsNoKeyInTheException(): void
    {
        $truncated = '{"live":{"accountId":"acc-live","apiKey":"' . self::liveKey()
            . '","signingSecret":"' . self::liveSecret();
        $this->mockHandler->append(new Response(200, [], $truncated));

        try {
            $this->exchange();
            $this->fail('No exception');
        } catch (ConnectException $e) {
            $this->assertSame('temporarily_unavailable', $e->getError());
            $this->assertStringNotContainsString('mk_', $e->getMessage());
            $this->assertStringNotContainsString('mksec_', $e->getMessage());
            $this->assertNull($e->getPrevious());
        }
    }

    /**
     * Transitional IDNA maps ß to ss: `strasse.de` is another domain, so the broker refuses the
     * URI as not canonical.
     *
     * @requires extension intl
     */
    public function testCanonicalRedirectUriWritesAnIdnHostAsNontransitionalPunycode(): void
    {
        $this->assertSame('https://xn--strae-oqa.de/', Connect::canonicalRedirectUri('https://straße.de'));
    }

    /**
     * The broker accepts only a URI that equals its own WHATWG form.
     *
     * @dataProvider provideCanonicalRedirectUris
     */
    public function testCanonicalRedirectUriMatchesWhatwg(string $uri, string $canonical): void
    {
        $this->assertSame($canonical, Connect::canonicalRedirectUri($uri));
    }

    public function provideCanonicalRedirectUris(): array
    {
        return [
            'upper case, default port, empty path' => ['HTTPS://Shop.com:443', 'https://shop.com/'],
            'another port' => ['https://shop.example:8443/cb', 'https://shop.example:8443/cb'],
            'percent escape in the host' => ['https://%73hop.example/cb', 'https://shop.example/cb'],
            'non-ASCII path' => [
                'https://shop.example/tienda-ñ' . self::WOO_PATH,
                'https://shop.example/tienda-%C3%B1' . self::WOO_PATH,
            ],
        ];
    }

    /**
     * The broker cannot parse such a URI: the plugin must show "Use manual keys", not the button.
     *
     * @dataProvider provideInvalidAuthorities
     */
    public function testCanonicalRedirectUriRefusesAnInvalidAuthority(string $uri): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Connect::canonicalRedirectUri($uri);
    }

    public function provideInvalidAuthorities(): array
    {
        return [
            'not a number' => ['https://shop.example:44a/cb'],
            'over 65535' => ['https://shop.example:65536/cb'],
            // %2F decodes to /, which no host may hold.
            'a host escape of a forbidden character' => ['https://shop%2Fevil.example/cb'],
        ];
    }

    /**
     * Each row: the broker accepts the URI. A plugin that refuses it hides the Connect button from
     * a shop that could connect. Rows of authorization-service src/broker/integrations.test.ts.
     *
     * @dataProvider provideAcceptedRedirectUris
     */
    public function testCallbackProblemAccepts(string $client, string $uri): void
    {
        $this->assertNull(Connect::callbackProblem($client, $uri));
    }

    public function provideAcceptedRedirectUris(): array
    {
        $label = str_repeat('a', 63);
        return [
            'woocommerce in a subdirectory' => ['woocommerce', 'https://shop.example/blog' . self::WOO_PATH],
            'woocommerce three prefix segments' => ['woocommerce', 'https://shop.example/a/b/c' . self::WOO_PATH],
            'prestashop friendly, with a language' => ['prestashop', 'https://shop.example/en/module/monei/connect'],
            'prestashop index.php, with id_lang' => [
                'prestashop',
                'https://shop.example/shop/index.php?fc=module&module=monei&controller=connect&id_lang=2',
            ],
            'prestashop index.php, one language' => [
                'prestashop',
                'https://shop.example/index.php?fc=module&module=monei&controller=connect',
            ],
            // index.php is not one of the 2 Magento prefix segments.
            'magento index.php and a store code' => [
                'magento',
                'https://shop.example/a/index.php/default/monei/payment/connect/',
            ],
            // `WooCommerce: shop.example/` is 26 characters: the key name has 300.
            'a key name of 300 characters' => [
                'woocommerce',
                'https://shop.example/' . str_repeat('a', 274) . self::WOO_PATH,
            ],
            // SDK only: the broker's z.hostname().max(253) at its limits.
            'labels of 63 and a host of 253' => [
                'woocommerce',
                "https://$label.$label.$label." . str_repeat('a', 61) . self::WOO_PATH,
            ],
        ];
    }

    /**
     * Each row: the broker refuses the URI with this code. A plugin that shows the Connect button
     * for it sends the merchant to a broker error page.
     *
     * @dataProvider provideRefusedRedirectUris
     */
    public function testCallbackProblemRefuses(string $client, string $uri, string $problem): void
    {
        $this->assertSame($problem, Connect::callbackProblem($client, $uri));
    }

    public function provideRefusedRedirectUris(): array
    {
        $label = str_repeat('a', 63);
        return [
            // The pin of the client.
            'three Magento prefix segments' => [
                'magento',
                'https://shop.example/a/b/c/monei/payment/connect/',
                'callbackDepth',
            ],
            'four prefix segments' => ['woocommerce', 'https://shop.example/a/b/c/d' . self::WOO_PATH, 'callbackDepth'],
            'another path with the right query' => [
                'woocommerce',
                'https://shop.example/wp-admin/admin.php?action=monei_connect',
                'callbackPath',
            ],
            'the friendly path with the index.php query' => [
                'prestashop',
                'https://shop.example/module/monei/connect?fc=module&module=monei&controller=connect',
                'callbackPath',
            ],
            'an extra query key' => ['woocommerce', self::WOO . '&next=https://evil.example', 'callbackPath'],
            'an open redirect path before the suffix' => [
                'woocommerce',
                'https://shop.example/out/evil.com' . self::WOO_PATH,
                'callbackDot',
            ],
            'index.php outside Magento' => [
                'woocommerce',
                'https://shop.example/index.php' . self::WOO_PATH,
                'callbackIndexPhp',
            ],
            'a percent-encoded segment' => [
                'woocommerce',
                'https://shop.example/sh%6Fp' . self::WOO_PATH,
                'callbackEncoded',
            ],
            'an empty segment' => ['woocommerce', 'https://shop.example/' . self::WOO_PATH, 'callbackEmptySegment'],
            'a segment outside [A-Za-z0-9_-]' => [
                'woocommerce',
                'https://shop.example/~shop' . self::WOO_PATH,
                'callbackSegment',
            ],
            // The host rules run before the pin, so `/cb` still gets the host code.
            'a loopback http URI' => ['woocommerce', 'http://localhost:3000/cb', 'notHttps'],
            'the Cursor callback' => ['woocommerce', 'cursor://anysphere.cursor-mcp/oauth/callback', 'notHttps'],
            'https on localhost' => ['woocommerce', 'https://localhost/cb', 'localhost'],
            'https on a .localhost host' => ['woocommerce', 'https://shop.localhost/cb', 'localhost'],
            'a host without a dot' => ['woocommerce', 'https://intranet/cb', 'noDot'],
            'a host the key refuses (_)' => ['woocommerce', 'https://my_shop.example/cb', 'keyHost'],
            'a label part that starts with monei' => ['woocommerce', 'https://monei-connect.shop/cb', 'moneiHost'],
            'a whole label that ends with monei' => ['woocommerce', 'https://securemonei.net/cb', 'moneiHost'],
            'a part after the - split' => ['woocommerce', 'https://secure-moneipay.shop/cb', 'moneiHost'],
            'a label other than the first' => ['woocommerce', 'https://pay.securemonei.net/cb', 'moneiHost'],
            'the exempt domain itself' => ['woocommerce', 'https://monei-dev-tunnel.com/cb', 'moneiHost'],
            'the exempt domain not as the suffix' => [
                'woocommerce',
                'https://monei-dev-tunnel.com.shop-secure.net/cb',
                'moneiHost',
            ],
            'the exempt domain without a dot before it' => [
                'woocommerce',
                'https://shop-monei-dev-tunnel.com/cb',
                'moneiHost',
            ],
            'a demo host not as the suffix' => [
                'woocommerce',
                'https://woocommerce-demo.monei.com.evil.io/cb',
                'moneiHost',
            ],
            // The broker refuses these as reservedHost; the SDK has no such check.
            'an unlisted -demo host' => ['woocommerce', 'https://api-demo.monei.com/cb', 'moneiHost'],
            'a host under a demo host' => ['woocommerce', 'https://x.woocommerce-demo.monei.com/cb', 'moneiHost'],
            // Not moneiHost: a host under the exempt domain, a MONEI demo store, and monei inside a word.
            'a host under the exempt domain' => [
                'woocommerce',
                'https://magento.monei-dev-tunnel.com/cb',
                'callbackPath',
            ],
            'a MONEI demo store' => ['woocommerce', 'https://woocommerce-demo.monei.com/cb', 'callbackPath'],
            'monei inside a word' => ['woocommerce', 'https://simoneinteriors.com/cb', 'callbackPath'],
            'a key name of 301 characters' => [
                'woocommerce',
                'https://shop.example/' . str_repeat('a', 275) . self::WOO_PATH,
                'nameLength',
            ],
            // redirectUri.test.ts asserts a refusal; the codes come from redirectUri.ts.
            'an IPv4 literal' => ['woocommerce', 'https://93.184.215.14/cb', 'ipLiteral'],
            'an IPv6 literal' => ['woocommerce', 'https://[2606:2800:220:1::1]/cb', 'ipLiteral'],
            // SDK only: WHATWG reads 127.1 as 127.0.0.1, so the broker refuses it.
            'an IPv4 shorthand' => ['woocommerce', 'https://127.1/cb', 'ipLiteral'],
            'any trailing-dot host' => ['woocommerce', 'https://evil.com./cb', 'trailingDot'],
            'userinfo' => ['woocommerce', 'https://user@x.com/cb', 'userinfo'],
            'a fragment' => ['woocommerce', 'https://x.com/cb#x', 'fragment'],
            'an empty fragment' => ['woocommerce', 'https://x.com/cb#', 'fragment'],
            '513 characters' => ['woocommerce', 'https://x.com/' . str_repeat('a', 499), 'length'],
            '512 characters' => ['woocommerce', 'https://x.com/' . str_repeat('a', 498), 'callbackPath'],
            // SDK only: the broker's z.hostname().max(253) past its limits.
            'a label of 64' => [
                'woocommerce',
                'https://' . str_repeat('a', 64) . '.example' . self::WOO_PATH,
                'keyHost',
            ],
            'a host of 254' => [
                'woocommerce',
                "https://$label.$label.$label." . str_repeat('a', 62) . self::WOO_PATH,
                'keyHost',
            ],
        ];
    }

    /**
     * A misspelt client would otherwise be checked against no pin at all.
     */
    public function testCallbackProblemRefusesAnUnknownClient(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Connect::callbackProblem('shopify', self::WOO);
    }
}
