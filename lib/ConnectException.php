<?php

/**
 * MONEI Payment Gateway API v1
 * ConnectException
 *
 * @category Class
 * @package  Monei
 * @author   MONEI
 * @link     https://monei.com
 */

namespace Monei;

/**
 * A failed token exchange of the "Connect with MONEI" flow.
 *
 * The message and the exception never hold the response body, because a body can hold keys.
 */
class ConnectException extends \RuntimeException
{
    /** @var string */
    private $error;

    /**
     * @param string          $error    OAuth error code, such as invalid_grant
     * @param int             $status   HTTP status, or 0 when no answer came
     * @param \Throwable|null $previous Transport error
     */
    public function __construct(string $error, int $status = 0, ?\Throwable $previous = null)
    {
        parent::__construct('MONEI connect failed: ' . $error, $status, $previous);
        $this->error = $error;
    }

    /**
     * The OAuth error code: invalid_request, invalid_client, invalid_grant, access_denied or
     * temporarily_unavailable.
     *
     * @return string
     */
    public function getError(): string
    {
        return $this->error;
    }
}
