<?php

namespace Siqwell\Kinopoisk\Apis;

/**
 * Class NameApi
 * @package Siqwell\Kinopoisk\Apis
 */
class NameApi extends Api
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