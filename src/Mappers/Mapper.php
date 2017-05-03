<?php

namespace Siqwell\Kinopoisk\Mappers;

use Symfony\Component\DomCrawler\Crawler;

abstract class Mapper
{

    /**
     * @var Crawler
     */
    protected $crawler;

    /**
     * Mapper constructor.
     * @param $content
     */
    function __construct($content)
    {
        $this->crawler = new Crawler($content);
    }

    /**
     * @return mixed
     */
    abstract function get();
}