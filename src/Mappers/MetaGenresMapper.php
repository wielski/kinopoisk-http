<?php

namespace Siqwell\Kinopoisk\Mappers;

use Illuminate\Support\Collection;
use Siqwell\Kinopoisk\Models\Genre;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class MetaGenresMapper
 * @package Siqwell\Kinopoisk\Mappers
 */
class MetaGenresMapper extends Mapper
{
    /**
     * @var Collection
     */
    protected $genres;

    /**
     * @return Collection
     */
    public function get()
    {
        $this->genres = collect();

        $this->crawler->filter("#formSearchMain [name='m_act[genre][]'] option")->each(function (Crawler $node) {
            if (!$node->attr('value') || !$node->text()) {
                return;
            }

            $this->genres->push(
                new Genre([
                    'id'   => $node->attr('value'),
                    'name' => $node->text(),
                ])
            );
        });

        return $this->genres;
    }
}