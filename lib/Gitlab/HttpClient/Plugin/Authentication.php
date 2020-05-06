<?php

namespace Gitlab\HttpClient\Plugin;

use Gitlab\Client;
use Http\Client\Common\Plugin;
use Psr\Http\Message\RequestInterface;

/**
 * Add authentication to the request.
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 * @author Fabien Bourigault <bourigaultfabien@gmail.com>
 */
class Authentication implements Plugin
{
    use Plugin\VersionBridgePlugin;

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
     */
    public function __construct($method, $token, $sudo = null)
    {
        $this->method  = $method;
        $this->token = $token;
        $this->sudo = $sudo;
    }

    /**
     * {@inheritdoc}
     */
    public function doHandleRequest(RequestInterface $request, callable $next, callable $first)
    {
        switch ($this->method) {
            case Client::AUTH_HTTP_TOKEN:
                $request = $request->withHeader('PRIVATE-TOKEN', $this->token);
                if (!is_null($this->sudo)) {
                    $request = $request->withHeader('SUDO', $this->sudo);
                }
                break;

            case Client::AUTH_URL_TOKEN:
                $uri = $request->getUri();
                $query = $uri->getQuery();

                $parameters = [
                    'private_token' => $this->token,
                ];

                if (!is_null($this->sudo)) {
                    $parameters['sudo'] = $this->sudo;
                }

                $query .= empty($query) ? '' : '&';
                $query .= utf8_encode(http_build_query($parameters, '', '&'));

                $uri = $uri->withQuery($query);
                $request = $request->withUri($uri);
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
