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
    public function get()
    {
        dd($this->crawler->html());
    }
}