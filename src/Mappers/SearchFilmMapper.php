<?php

namespace Siqwell\Kinopoisk\Mappers;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Siqwell\Kinopoisk\Models\Film;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class SearchFilmMapper
 * @package Siqwell\Kinopoisk\Mappers
 */
class SearchFilmMapper extends Mapper
{

    /**
     * @var string[]
     */
    protected $types = [
        'series' => 'tv',
        'film'   => 'movie',
    ];

    /**
     * @var Collection
     */
    protected $result;

    /**
     * @return Collection|null
     */
    public function get()
    {
        $this->result = collect();

        if ($this->crawler->filter('.search_results .element')->count()) {
            $this->crawler->filter('.search_results .element')->each(function (Crawler $node) {
                $this->result->push(
                    new Film([
                        'id'       => $this->detectId($node),
                        'url'      => $this->detectUrl($node),
                        'type'     => $this->detectType($node->filter('.pic a')->attr('data-type')),
                        'title'    => $node->filter('.pic a img')->attr('alt'),
                        'original' => $this->original($node),
                        'poster'   => $this->poster($node->filter('.pic a img')),
                        'year'     => $this->year($node),
                    ])
                );
            });
        }

        return $this->result->isNotEmpty() ? $this->result : null;
    }

    /**
     * @param Crawler $node
     *
     * @return int|null
     */
    private function detectId(Crawler $node): ?int
    {
        return $node->filter('.pic a')->attr('data-id');
    }

    /**
     * @param Crawler $node
     *
     * @return string
     */
    private function detectUrl(Crawler $node): string
    {
        return $node->getBaseHref()->withPath('/film/') . $this->detectId($node) . '/';
    }

    /**
     * @param string $type
     *
     * @return mixed|null
     */
    private function detectType(string $type)
    {
        return isset($this->types[$type]) ? $this->types[$type] : null;
    }

    /**
     * @param Crawler $node
     *
     * @return string|null
     */
    private function poster(Crawler $node)
    {
        if (Str::contains($node->attr('title'), 'no-poster.gif')) {
            return null;
        }

        if ($path = $node->attr('title')) {
            $path = Str::replaceFirst('/sm_film/', '/film/', $path);

            return (string)$node->getBaseHref()->withPath($path);
        }

        return null;
    }

    /**
     * @param Crawler $node
     *
     * @return string|null
     */
    private function original(Crawler $node)
    {
        if ($text = $node->filter('.gray')->first()->text()) {
            $text = collect(explode(',', $text))->first();

            if (Str::contains($text, 'мин')) {
                return null;
            }

            return $text;
        }

        return null;
    }

    /**
     * @param Crawler $node
     *
     * @return null|string
     */
    private function year(Crawler $node)
    {
        return $node->filter('.info .name .year')->count() ? $node->filter('.info .name .year')->text() : null;
    }
}
