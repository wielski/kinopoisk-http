<?php

namespace Siqwell\Kinopoisk\Apis;

use Illuminate\Support\Collection;
use Siqwell\Kinopoisk\Mappers\FilmCastsMapper;
use Siqwell\Kinopoisk\Mappers\FilmDetailsMapper;

/**
 * Class FilmApi
 * @package Siqwell\Kinopoisk\Apis
 */
class FilmApi extends Api
{
    /**
     * @param $film_id
     * @return string
     */
    public function details($film_id)
    {
        return $this->setMapper(FilmDetailsMapper::class)->get(['id' => $film_id], "/film/{id}/");
    }

    /**
     * @param $film_id
     * @return Collection|null
     */
    public function casts($film_id)
    {
        return $this->setMapper(FilmCastsMapper::class)->get(['id' => $film_id], "/film/{id}/cast/");
    }
}