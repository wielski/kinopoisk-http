<?php

namespace Siqwell\Kinopoisk\Mappers;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Siqwell\Kinopoisk\Models\Cast;
use Siqwell\Kinopoisk\Models\Work;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class FilmCastsMapper
 * @package Siqwell\Kinopoisk\Mappers
 */
class FilmCastsMapper extends Mapper
{

    /**
     * @var Collection
     */
    protected $casts;

    /**
     * @var Collection
     */
    protected $works;

    /**
     * @var string
     */
    protected $current;

    /**
     * @return mixed
     */
    public function get()
    {
        $this->casts = collect();
        $this->works = collect();

        try {
            if ($crawler = $this->crawler->filterXPath('.//td[@id="block_left"]')->children()->children()) {
                $this->parseAll($crawler);
            }
        } catch (\Exception $e) {
            return null;
        }

        return collect([
            'works' => $this->works,
            'casts' => $this->casts,
        ]);
    }

    /**
     * @param Crawler $crawler
     */
    protected function parseAll(Crawler $crawler)
    {
        $crawler->each(function (Crawler $node) {
            switch ($node->nodeName()) {
                case 'a':
                    if ($this->current != $node->attr('name')) {
                        if ($this->current = $node->attr('name')) {
                            $this->works->push(new Work([
                                'key'  => $this->current,
                                'name' => $node->nextAll('div')->html()
                            ]));
                        }
                    }
                    break;
                case 'div':
                    $class = $node->attr('class');
                    if ($class && in_array('dub', explode(' ', $class))) {
                        $this->parseCast($node, $this->current);
                    }
                    break;
            }
        });
    }

    /**
     * @param Crawler $node
     * @param string|null $work
     * @return array|bool
     */
    private function parseCast(Crawler $node, string $work = null)
    {
        if (!$href = $node->filter('.actorInfo .info .name > a')->attr('href')) {
            return false;
        }

        if (!$cast_id = collect(explode('/', trim($href, '/')))->last()) {
            return false;
        }

        $localized = $this->clearName($node->filterXPath('*//div[@class="name"]/a')->text());
        $original  = $this->clearName($node->filterXPath('*//div[@class="name"]/span')->text());

        if ($work == 'actor') {
            if ($role = $node->filterXPath("*//div[@class='role']/text()")->text()) {
                $character = $this->clearRole($role);
            }
        }

        $this->casts->push(new Cast([
            'work'        => $work,
            'external_id' => $cast_id,
            'character'   => isset($character) ? $character : null,
            'name_ru'     => $localized ? $localized : null,
            'name_en'     => $original ? $original : null,
        ]));
    }

    /**
     * @param $string
     * @return null|string
     */
    private function clearName($string): ?string
    {
        $string = preg_replace('~\x{00a0}~siu', ' ', $string);
        $string = html_entity_decode($string, ENT_QUOTES);
        $string = preg_replace("/ \\((.+?)\\)/", '', $string);
        $string = trim($string);
        $string = trim($string, ',');

        return !empty($string) ? $string : null;
    }

    /**
     * @param $string
     * @return null|string
     */
    private function clearRole($string): ?string
    {
        $string = preg_replace('~\x{00a0}~siu', ' ', $string);

        if (Str::contains($string, ',')) {
            $string = collect(explode(',', $string))->first();
        }

        if (Str::startsWith($string, '...')) {
            $string = Str::replaceFirst('...', '', $string);
        }

        $string = trim($string);

        return !empty($string) ? $string : null;
    }
}