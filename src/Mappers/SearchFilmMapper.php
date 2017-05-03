<?php

namespace Siqwell\Kinopoisk\Mappers;

/**
 * Class SearchFilmMapper
 * @package Siqwell\Kinopoisk\Mappers
 */
class SearchFilmMapper extends Mapper
{

    /**
     * @return mixed
     */
    function get()
    {
        dd($this->crawler->html());
    }
}