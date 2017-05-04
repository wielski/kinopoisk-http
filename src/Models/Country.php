<?php

namespace Siqwell\Kinopoisk\Models;

/**
 * Class Country
 * @package Siqwell\Kinopoisk\Models
 */
class Country extends Model
{

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getAttribute('name');
    }
}