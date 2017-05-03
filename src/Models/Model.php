<?php

namespace Siqwell\Kinopoisk\Models;

use Illuminate\Contracts\Support\Arrayable;

/**
 * Class Model
 * @package Siqwell\Kinopoisk\Models
 */
abstract class Model implements Arrayable
{

    /**
     * @var array
     */
    protected $attributes;

    /**
     * Model constructor.
     * @param array $attributes
     */
    function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }

    /**
     * @param $name
     * @return mixed|null
     */
    public function getAttribute($name)
    {
        return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
    }

    /**
     * @param $name
     * @param $value
     */
    public function setAttribute($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    /**
     * @param $name
     * @return mixed|null
     */
    public function __get($name)
    {
        return $this->getAttribute($name);
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->setAttribute($name, $value);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->attributes;
    }
}