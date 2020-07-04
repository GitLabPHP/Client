<?php

namespace Gitlab\HttpClient\Plugin;

use Gitlab\Client;
use Http\Client\Common\Plugin;
use Http\Promise\Promise;
use Psr\Http\Message\RequestInterface;

/**
 * Add authentication to the request.
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 * @author Fabien Bourigault <bourigaultfabien@gmail.com>
 */
class Authentication implements Plugin
{
    /**
     * @var string
     */
    private $method;

    /**
     * @var string
     */
    private $token;

    /**
     * @var string|null
     */
    private $sudo;

    /**
     * @param string      $method
     * @param string      $token
     * @param string|null $sudo
     *
     * @return void
     */
    public function __construct(string $method, string $token, string $sudo = null)
    {
        $this->method = $method;
        $this->token = $token;
        $this->sudo = $sudo;
    }

    /**
     * {@inheritdoc}
     */
    public function handleRequest(RequestInterface $request, callable $next, callable $first): Promise
    {
        switch ($this->method) {
            case Client::AUTH_HTTP_TOKEN:
                $request = $request->withHeader('PRIVATE-TOKEN', $this->token);
                if (!is_null($this->sudo)) {
                    $request = $request->withHeader('SUDO', $this->sudo);
                }

                break;

            case Client::AUTH_OAUTH_TOKEN:
                $request = $request->withHeader('Authorization', 'Bearer '.$this->token);
                if (!is_null($this->sudo)) {
                    $request = $request->withHeader('SUDO', $this->sudo);
                }

                break;
        }

        return $next($request);
    }
}
