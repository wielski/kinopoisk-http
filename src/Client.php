<?php

namespace Siqwell\Kinopoisk;

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
        $this->client = new HttpClient([
            'base_uri' => 'https://www.kinopoisk.ru/',
            'timeout'  => 10,
        ]);
    }

    /**
     * @return SearchApi
     */
    public function getSearchApi()
    {
        return new SearchApi($this->client);
    }
}