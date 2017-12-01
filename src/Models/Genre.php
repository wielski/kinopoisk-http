<?php

namespace Siqwell\Kinopoisk\Models;

/**
 * Class Genre
 * @package Siqwell\Kinopoisk\Models
 */
class Genre extends Model
{

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getAttribute('name');
    }
}