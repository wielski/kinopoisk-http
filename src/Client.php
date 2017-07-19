<?php

namespace Siqwell\Kinopoisk;

use Siqwell\Kinopoisk\Apis\FilmApi;
use Siqwell\Kinopoisk\Apis\MetaApi;
use Siqwell\Kinopoisk\Apis\NameApi;
use Siqwell\Kinopoisk\Apis\SearchApi;

/**
 * Class Client
 * @package Siqwell\Kinopoisk
 */
class Client
{
    /**
     * @var HttpClient
     */
    protected $client;

    /**
     * Client constructor.
     */
    function __construct()
    {
        $this->client = new HttpClient(
            array_merge(
                config('services.kinopoisk.client'),
                ['base_uri' => 'https://www.kinopoisk.ru/']
            ),
            config('services.kinopoisk.cache')
        );
    }

    /**
     * @return SearchApi
     */
    public function getSearchApi()
    {
        return new SearchApi($this->client);
    }

    /**
     * @return FilmApi
     */
    public function getFilmApi()
    {
        return new FilmApi($this->client);
    }

    /**
     * @return NameApi
     */
    public function getNameApi()
    {
        return new NameApi($this->client);
    }

    /**
     * @return MetaApi
     */
    public function getMetaApi()
    {
        return new MetaApi($this->client);
    }
}