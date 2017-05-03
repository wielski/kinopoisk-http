<?php

namespace Siqwell\Kinopoisk\Mappers;

use Symfony\Component\DomCrawler\Crawler;

/**
 * Class Mapper
 * @package Siqwell\Kinopoisk\Mappers
 */
abstract class Mapper
{

    /**
     * @var Crawler
     */
    protected $crawler;

    /**
     * Mapper constructor.
     * @param $content
     * @param null $url
     * @param null $base_href
     */
    function __construct($content, $url = null, $base_href = null)
    {
        $this->crawler = new Crawler($content, $url, $base_href);
    }

    /**
     * @return mixed
     */
    abstract public function get();
}