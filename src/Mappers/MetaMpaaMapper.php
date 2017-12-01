<?php

namespace Siqwell\Kinopoisk\Mappers;

use Illuminate\Support\Collection;
use Siqwell\Kinopoisk\Models\Company;
use Siqwell\Kinopoisk\Models\Genre;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class MetaMpaaMapper
 * @package Siqwell\Kinopoisk\Mappers
 */
class MetaMpaaMapper extends Mapper
{
    /**
     * @var Collection
     */
    protected $mpaa;

    /**
     * @return Collection
     */
    public function get()
    {
        $this->mpaa = collect();

        $this->crawler->filter("#formSearchMain [name='m_act[mpaa]'] option")->each(function (Crawler $node) {
            if (!$node->attr('value') || !$node->text()) {
                return;
            }

            $this->mpaa->push($node->attr('value'));
        });

        return $this->mpaa;
    }
}