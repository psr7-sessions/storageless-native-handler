<?php

declare(strict_types=1);

namespace PSR7SessionsHandler;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Claim\Basic;
use Lcobucci\JWT\Token;
use PSR7Session\Http\SessionMiddleware;
use PSR7SessionEncodeDecode\Decoder;
use SessionHandlerInterface;

/**
 * - {@see SessionHandlerInterface::close} — Close the session
 * - {@see SessionHandlerInterface::destroy} — Destroy a session
 * - {@see SessionHandlerInterface::gc} — Cleanup old sessions
 * - {@see SessionHandlerInterface::open} — Initialize session
 * - {@see SessionHandlerInterface::read} — Read session data
 * - {@see SessionHandlerInterface::write} — Write session data
 */
class NativeSessionHandler implements SessionHandlerInterface
{
    /**
     * @var Builder
     */
    public $builder;

    /**
     * @var string
     */
    private $sessionName;

    /**
     * @var string
     */
    private $sessionSavePath;

    /**
     * @var Token
     */
    private $token;

    // @todo receive and store session object
    public function __construct(Builder $builder, Token $token)
    {
        $this->builder = $builder;
        $this->token   = $token;
    }

    /**
     * {@inheritDoc}
     *
     * @return true for interface compatibility purpose
     */
    public function close()
    {
        setcookie(SessionMiddleware::DEFAULT_COOKIE, (string) $this->builder->getToken()->__toString());

        return true;
    }

    /**
     * {@inheritDoc}
     *
     * @return true for interface compatibility purpose
     */
    public function destroy($session_id)
    {
        // TODO: Implement destroy() method.
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function gc($maxlifetime)
    {
        // TODO: Implement gc() method.

        return true;
    }

    /**
     * {@inheritDoc}
     *
     * @return true for interface compatibility purpose
     */
    public function open($save_path, $name)
    {
        $this->sessionSavePath = $save_path;
        $this->sessionName     = $name;

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function read($session_id)
    {
        $a = array_filter(array_map(function (Basic $value) {
            return in_array($value->getName(), ['iss', 'aud', 'jti', 'iat', 'exp'], true)
                ? null
                : $value->getValue();
        }, $this->token->getClaims()));

        return (new \PSR7SessionEncodeDecode\Encoder())->__invoke((array) $a['slsession']);
    }

    /**
     * {@inheritDoc}
     *
     * @throws \BadMethodCallException
     */
    public function write($session_id, $session_data)
    {
        $this->builder->unsign();
        $this->builder->set(SessionMiddleware::DEFAULT_COOKIE, (new Decoder())->__invoke($session_data));

        return true;
    }
}
