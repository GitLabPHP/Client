<?php


namespace Gitlab\HttpClient\Plugin;

use Http\Client\Common\Plugin;
use Psr\Http\Message\RequestInterface;

/**
 * Prefix requests path with /api/v4/ if required.
 *
 * @author Fabien Bourigault <bourigaultfabien@gmail.com>
 */
class ApiVersion implements Plugin
{
    protected $version = 'v4';
    
    /**
     * {@inheritdoc}
     */
    public function handleRequest(RequestInterface $request, callable $next, callable $first)
    {
        $uri = $request->getUri();

        if (substr($uri->getPath(), 0, 8) !== '/api/' . $this->version . '/') {
            $request = $request->withUri($uri->withPath('/api/' . $this->version . '/'.$uri->getPath()));
        }

        return $next($request);
    }
}
