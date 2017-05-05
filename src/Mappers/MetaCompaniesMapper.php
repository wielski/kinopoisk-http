<?php

namespace Siqwell\Kinopoisk\Mappers;

use Illuminate\Support\Collection;
use Siqwell\Kinopoisk\Models\Company;
use Siqwell\Kinopoisk\Models\Genre;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class MetaCompaniesMapper
 * @package Siqwell\Kinopoisk\Mappers
 */
class MetaCompaniesMapper extends Mapper
{
    /**
     * @var Collection
     */
    protected $companies;

    /**
     * @return Collection
     */
    public function get()
    {
        $this->companies = collect();

        $this->crawler->filter("#formSearchMain [name='m_act[company]'] option")->each(function (Crawler $node) {
            if (!$node->attr('value') || !$node->text()) {
                return;
            }

            $this->companies->push(
                new Company([
                    'id'   => $node->attr('value'),
                    'name' => $node->text(),
                ])
            );
        });

        return $this->companies;
    }
}