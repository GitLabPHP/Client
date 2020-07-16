<?php

namespace Gitlab\HttpClient\Util;

/**
 * @internal
 */
final class QueryStringBuilder
{
    /**
     * Encode a query as a query string according to RFC 3986.
     *
     * Indexed arrays are encoded using empty squared brackets ([]) unlike
     * `http_build_query`.
     *
     * @param mixed $query
     *
     * @return string
     */
    public static function build($query)
    {
        if (!\is_array($query)) {
            return self::rawurlencode($query);
        }

        return \implode('&', \array_map(function ($value, $key) {
            return self::encode($value, $key);
        }, $query, \array_keys($query)));
    }

    /**
     * Encode a value.
     *
     * @param mixed  $query
     * @param string $prefix
     *
     * @return string
     */
    private static function encode($query, $prefix)
    {
        if (!\is_array($query)) {
            return self::rawurlencode($prefix).'='.self::rawurlencode($query);
        }

        $isList = self::isList($query);

        return \implode('&', \array_map(function ($value, $key) use ($prefix, $isList) {
            $prefix = $isList ? $prefix.'[]' : $prefix.'['.$key.']';

            return self::encode($value, $prefix);
        }, $query, \array_keys($query)));
    }

    /**
     * Tell if the given array is a list.
     *
     * @param array $query
     *
     * @return bool
     */
    private static function isList(array $query)
    {
        if (0 === \count($query) || !isset($query[0])) {
            return false;
        }

        return \array_keys($query) === \range(0, \count($query) - 1);
    }

    /**
     * Encode a value like rawurlencode, but return "0" when false is given.
     *
     * @param mixed $value
     *
     * @return string
     */
    private static function rawurlencode($value)
    {
        if (false === $value) {
            return '0';
        }

        return \rawurlencode((string) $value);
    }
}
