<?php

declare(strict_types=1);

/*
 * This file is part of the Gitlab API library.
 *
 * (c) Matt Humphrey <matth@windsor-telecom.co.uk>
 * (c) Graham Campbell <hello@gjcampbell.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gitlab\HttpClient\Plugin;

use Gitlab\Client;
use Gitlab\Exception\RuntimeException;
use Http\Client\Common\Plugin;
use Http\Promise\Promise;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

/**
 * Add authentication to the request.
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 * @author Fabien Bourigault <bourigaultfabien@gmail.com>
 *
 * @internal
 */
final class Authentication implements Plugin
{
    /**
     * @var array<string,string>
     */
    private readonly array $headers;

    private UriInterface $url;

    public function __construct(string $method, #[\SensitiveParameter] string $token, ?string $sudo, UriInterface $url)
    {
        $this->headers = self::buildHeaders($method, $token, $sudo);
        $this->url = $url;
    }

    public function withUrl(UriInterface $url): self
    {
        $plugin = clone $this;
        $plugin->url = $url;

        return $plugin;
    }

    /**
     * Handle the request and return the response coming from the next callable.
     *
     * @return Promise<ResponseInterface>
     */
    public function handleRequest(RequestInterface $request, callable $next, callable $first): Promise
    {
        $sameOrigin = self::isSameOrigin($request->getUri(), $this->url);
        $credential = (string) \array_key_first($this->headers);
        $carried = $request->getHeaderLine($credential) === $this->headers[$credential];

        foreach ($this->headers as $header => $value) {
            if ($sameOrigin) {
                $request = $request->withHeader($header, $value);
            } elseif ($carried && $request->getHeaderLine($header) === $value) {
                // a redirected request already carries it
                $request = $request->withoutHeader($header);
            }
        }

        return $next($request);
    }

    private static function isSameOrigin(UriInterface $a, UriInterface $b): bool
    {
        return 0 === \strcasecmp($a->getScheme(), $b->getScheme())
            && 0 === \strcasecmp($a->getHost(), $b->getHost())
            && self::getPort($a) === self::getPort($b);
    }

    private static function getPort(UriInterface $uri): ?int
    {
        return $uri->getPort() ?? ['http' => 80, 'https' => 443][\strtolower($uri->getScheme())] ?? null;
    }

    /**
     * Build the headers to be attached to the request.
     *
     * @throws RuntimeException
     *
     * @return array<string,string>
     */
    private static function buildHeaders(string $method, #[\SensitiveParameter] string $token, ?string $sudo = null): array
    {
        $headers = [];

        switch ($method) {
            case Client::AUTH_HTTP_TOKEN:
                $headers['PRIVATE-TOKEN'] = $token;

                break;
            case Client::AUTH_HTTP_JOB_TOKEN:
                $headers['JOB-TOKEN'] = $token;

                break;
            case Client::AUTH_OAUTH_TOKEN:
                $headers['Authorization'] = \sprintf('Bearer %s', $token);

                break;
            default:
                throw new RuntimeException(\sprintf('Authentication method "%s" not implemented.', $method));
        }

        if (null !== $sudo) {
            $headers['SUDO'] = $sudo;
        }

        return $headers;
    }
}
