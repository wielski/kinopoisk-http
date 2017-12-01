<?php

namespace Siqwell\Kinopoisk\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class KinopoiskParser
 * @package Siqwell\Kinopoisk\Facades
 * @method static \Siqwell\Kinopoisk\Apis\FilmApi getFilmApi()
 * @method static \Siqwell\Kinopoisk\Apis\SearchApi getSearchApi()
 * @method static \Siqwell\Kinopoisk\Apis\NameApi getNameApi()
 * @method static \Siqwell\Kinopoisk\Apis\MetaApi getMetaApi()
 */
class KinopoiskParser extends Facade
{

    /**
     * @return string
     */
    public static function getFacadeAccessor()
    {
        return 'kinopoisk.parser';
    }
}