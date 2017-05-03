<?php

namespace Siqwell\Kinopoisk\Apis;

use Siqwell\Kinopoisk\Mappers\FilmDetailsMapper;

/**
 * Class FilmApi
 * @package Siqwell\Kinopoisk\Apis
 */
class FilmApi extends Api
{

    /**
     * @var string
     */
    protected $pattern = "/film/{id}/";

    /**
     * @param $film_id
     * @return string
     */
    public function details($film_id)
    {
        $result = $this->setMapper(FilmDetailsMapper::class)->get(['id' => $film_id]);

        return $result;
    }
}