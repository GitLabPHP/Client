<?php

declare(strict_types=1);

namespace Gitlab\HttpClient\Message;

use Psr\Http\Message\ResponseInterface;

/**
 * Utilities to parse response headers and content.
 */
class ResponseMediator
{
    /**
     * Return the response body as a string or json array if content type is application/json.
     *
     * @param ResponseInterface $response
     *
     * @return array|string
     */
    public static function getContent(ResponseInterface $response)
    {
        $body = $response->getBody()->__toString();
        if (0 === strpos($response->getHeaderLine('Content-Type'), 'application/json')) {
            $content = json_decode($body, true);
            if (JSON_ERROR_NONE === json_last_error()) {
                return $content;
            }
        }

        return $body;
    }

    /**
     * Extract pagination URIs from Link header.
     *
     * @param ResponseInterface $response
     *
     * @return array|null
     */
    public static function getPagination(ResponseInterface $response)
    {
        if (!$response->hasHeader('Link')) {
            return null;
        }

        /** @var string */
        $header = self::getHeader($response, 'Link');
        $pagination = [];
        foreach (explode(',', $header) as $link) {
            preg_match('/<(.*)>; rel="(.*)"/i', trim($link, ','), $match);

            if (3 === count($match)) {
                $pagination[$match[2]] = $match[1];
            }
        }

        return $pagination;
    }

    /**
     * Get the value for a single header.
     *
     * @param ResponseInterface $response
     * @param string            $name
     *
     * @return string|null
     */
    private static function getHeader(ResponseInterface $response, $name)
    {
        $headers = $response->getHeader($name);

        return array_shift($headers);
    }
}
