<?php

namespace Gitlab\HttpClient\Message;

use Gitlab\HttpClient\Util\QueryStringBuilder as UtilQueryStringBuilder;

/**
 * @deprecated since 9.18 and will be removed in 10.0.
 */
final class QueryStringBuilder
{
    /**
     * Encode a query as a query string according to RFC 3986. Indexed arrays are encoded using
     * empty squared brackets ([]) unlike http_build_query.
     *
     * @param mixed $query
     *
     * @return string
     *
     * @deprecated since 9.18 and will be removed in 10.0.
     */
    public static function build($query)
    {
        @\trigger_error(\sprintf('The %s() method is deprecated since version 9.18 and will be removed in 10.0.', __METHOD__), E_USER_DEPRECATED);

        if (\is_array($query)) {
            $query = \array_filter($query, function ($value) {
                return null !== $value;
            });
        }

        return UtilQueryStringBuilder::build($query);
    }

    /**
     * Tell if the given array is an indexed one (i.e. contains only sequential integer keys starting from 0).
     *
     * @param array $query
     *
     * @return bool
     *
     * @deprecated since 9.18 and will be removed in 10.0.
     */
    public static function isIndexedArray(array $query)
    {
        @\trigger_error(\sprintf('The %s() method is deprecated since version 9.18 and will be removed in 10.0.', __METHOD__), E_USER_DEPRECATED);

        if (0 === \count($query) || !isset($query[0])) {
            return false;
        }

        return \array_keys($query) === \range(0, \count($query) - 1);
    }
}
