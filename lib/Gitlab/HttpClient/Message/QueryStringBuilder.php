<?php

namespace Gitlab\HttpClient\Message;

final class QueryStringBuilder
{
    /**
     * Encode a query as a query string according to RFC 3986. Indexed arrays are encoded using
     * empty squared brackets ([]) unlike http_build_query.
     *
     * @param mixed $query
     *
     * @return string
     */
    public static function build($query)
    {
        if (!is_array($query)) {
            return static::rawurlencode($query);
        }
        $query = array_filter($query, function ($value) {
            return (null !== $value);
        });

        return implode('&', array_map(function ($value, $key) {
            return static::encode($value, $key);
        }, $query, array_keys($query)));
    }

    /**
     * Encode a value
     * @param mixed $query
     * @param string $prefix
     *
     * @return string
     */
    private static function encode($query, $prefix)
    {
        if (!is_array($query)) {
            return static::rawurlencode($prefix).'='.static::rawurlencode($query);
        }

        $isIndexedArray = static::isIndexedArray($query);
        return implode('&', array_map(function ($value, $key) use ($prefix, $isIndexedArray) {
            $prefix = $isIndexedArray ? $prefix.'[]' : $prefix.'['.$key.']';
            return static::encode($value, $prefix);
        }, $query, array_keys($query)));
    }

    /**
     * Tell if the given array is an indexed one (i.e. contains only sequential integer keys starting from 0).
     *
     * @param array $query
     *
     * @return bool
     */
    public static function isIndexedArray(array $query)
    {
        if (empty($query) || !isset($query[0])) {
            return false;
        }

        return array_keys($query) === range(0, count($query) - 1);
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

        return rawurlencode($value);
    }
}
