<?php

namespace Siqwell\Kinopoisk\Apis;

use Siqwell\Kinopoisk\Helpers\Mact;
use Siqwell\Kinopoisk\Mappers\SearchFilmMapper;

/**
 * Class SearchApi
 * @package Siqwell\Kinopoisk\Apis
 */
class SearchApi extends Api
{

    /**
     * @var string
     */
    protected $pattern = "/s/";

    /**
     * @param string $title
     * @param array $params
     */
    public function searchFilm(string $title, array $params = [])
    {
        $this->pattern = Mact::query('type', 'film', $this->pattern);
        $this->pattern = Mact::query('list', '1', $this->pattern);
        $this->pattern = Mact::query('find', $title, $this->pattern);

        if (isset($params['year'])) {
            $this->pattern = Mact::mact('year', $params['year'], $this->pattern);
        }

        $result = $this->setMapper(SearchFilmMapper::class)->get();

        dd($result);
    }

    /**
     * @param string $name
     * @param array $params
     */
    public function searchName(string $name, array $params = [])
    {
        $this->pattern = Mact::query('type', 'name', $this->pattern);
        $this->pattern = Mact::query('list', '1', $this->pattern);
        $this->pattern = Mact::query('find', $name, $this->pattern);

        $result = $this->get();

        dd($result);
    }
}