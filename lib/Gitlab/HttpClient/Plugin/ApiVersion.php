<?php

namespace Gitlab\HttpClient\Plugin;

use Http\Client\Common\Plugin;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Prefix requests path with /api/v4/ if required.
 *
 * @author Fabien Bourigault <bourigaultfabien@gmail.com>
 */
class ApiVersion implements Plugin
{
    use Plugin\VersionBridgePlugin;

    private $redirected = false;

    /**
     * {@inheritdoc}
     */
    public function doHandleRequest(RequestInterface $request, callable $next, callable $first)
    {
        $uri = $request->getUri();

        if (substr($uri->getPath(), 0, 8) !== '/api/v4/' && !$this->redirected) {
            $request = $request->withUri($uri->withPath('/api/v4/'.$uri->getPath()));
        }

        return $next($request)->then(function (ResponseInterface $response) {
            $this->redirected = $response->getStatusCode() === 302;

            return $response;
        });
    }
}
