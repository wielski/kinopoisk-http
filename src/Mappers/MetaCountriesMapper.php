<?php

namespace Siqwell\Kinopoisk\Mappers;

use Illuminate\Support\Collection;
use Siqwell\Kinopoisk\Models\Country;
use Siqwell\Kinopoisk\Models\Genre;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class MetaCountriesMapper
 * @package Siqwell\Kinopoisk\Mappers
 */
class MetaCountriesMapper extends Mapper
{
    /**
     * @var Collection
     */
    protected $countries;

    /**
     * @return Collection
     */
    public function get()
    {
        $this->countries = collect();

        $this->crawler->filter("#formSearchMain [name='m_act[country]'] option")->each(function (Crawler $node) {
            if (!$node->attr('value') || !$node->text()) {
                return;
            }

            $this->countries->push(
                new Country([
                    'id'   => $node->attr('value'),
                    'name' => $node->text(),
                ])
            );
        });

        return $this->countries;
    }
}