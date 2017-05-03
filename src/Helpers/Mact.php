<?php

namespace Siqwell\Kinopoisk\Helpers;

/**
 * Class Mact
 * @package Siqwell\Kinopoisk\Helpers
 */
class Mact
{

    /**
     * @param $field
     * @param $value
     * @param $pattern
     * @return string
     */
    public static function mact($field, $value, $pattern)
    {
        return self::query("m_act[{$field}]", $value, $pattern);
    }

    /**
     * @param $field
     * @param $value
     * @param $pattern
     * @return string
     */
    public static function query($field, $value, $pattern)
    {
        $segments = explode('/', $pattern);
        $segments = array_filter($segments, 'strlen');

        if ($position = array_search($field, $segments)) {
            $segments[$position + 1] = $value;
        } else {
            array_push($segments, $field, $value);
        }

        return '/' . implode('/', $segments) . '/';
    }
}