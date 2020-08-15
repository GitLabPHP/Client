<?php

declare(strict_types=1);

namespace Gitlab\HttpClient\Util;

/**
 * @internal
 */
final class QueryStringBuilder
{
    /**
     * Encode a query as a query string according to RFC 3986.
     *
     * @param array $query
     *
     * @return string
     */
    public static function build(array $query)
    {
        if (0 === \count($query)) {
            return '';
        }

        return \sprintf('?%s', \http_build_query($query, '', '&', \PHP_QUERY_RFC3986));
    }
}
