<?php

namespace Siqwell\Kinopoisk\Apis;

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
     */
    public function details($film_id)
    {
        $result = $this->get(['id' => $film_id]);
    }
}