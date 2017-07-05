<?php

namespace Siqwell\Kinopoisk\Mappers;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Siqwell\Kinopoisk\Models\Company;
use Siqwell\Kinopoisk\Models\Country;
use Siqwell\Kinopoisk\Models\Film;
use Siqwell\Kinopoisk\Models\Genre;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class Mapper
 * @package Siqwell\Kinopoisk\Mappers
 */
class FilmDetailsMapper extends Mapper
{

    /**
     * @var array
     */
    protected $types = [
        'video.tv_show' => 'tv',
        'video.movie'   => 'movie',
    ];

    /**
     * @var array
     */
    protected $genres;

    /**
     * @var array
     */
    protected $countries;

    /**
     * @return Film|null
     */
    public function get()
    {
        if ($this->detectId()) {
            return new Film([
                'id'        => $this->detectId(),
                'type'      => $this->detectType(),
                'title'     => $this->parseTitle(),
                'original'  => $this->parseOriginalTitle(),
                'poster'    => $this->parsePoster(),
                'tagline'   => $this->parseTagline(),
                'genres'    => $this->parseGenres(),
                'countries' => $this->parseCountries(),
                'runtime'   => $this->parseRuntime(),
                'premiere'  => $this->parsePremiere(),
                'age_limit' => $this->parseAgeLimit(),
                'mpaa'      => $this->parseMpaaRating(),
                'company'   => $this->parseCompany(),
            ]);
        }

        return null;
    }

    /**
     * @return mixed|null
     */
    private function detectId()
    {
        try {
            if ($canonical = $this->crawler->filterXPath("//link[@rel='canonical']/@href")->text()) {
                if ($id = intval(collect(explode('/', trim($canonical, '/')))->last())) {
                    return $id;
                }
            }
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @return mixed|null
     */
    private function detectType()
    {
        try {
            if ($type = $this->crawler->filterXPath("//meta[@property='og:type']/@content")->text()) {
                return isset($this->types[$type]) ? $this->types[$type] : null;
            }
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @return null|string
     */
    private function parseTitle()
    {
        try {
            if ($title = $this->crawler->filter('#headerFilm [itemprop="name"]')->text()) {
                return $this->clearTitle($title);
            }
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @return null|string
     */
    private function parseOriginalTitle()
    {
        try {
            if ($title = $this->crawler->filter('#headerFilm [itemprop="alternativeHeadline"]')->text()) {
                return $this->clearTitle($title);
            }
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @return null|string
     */
    private function parsePoster()
    {
        try {
            if ($script = $this->crawler->filter('.film-img-box .popupBigImage')->attr('onclick')) {
                if (preg_match("/openImgPopup\\('(.+?)'\\)/", $script, $match)) {
                    return (string)$this->crawler->getBaseHref()->withPath($match[1]);
                }
            }
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @return null|string
     */
    private function parseTagline()
    {
        try {
            if ($tagline = $this->crawler->filterXPath('//*/td[normalize-space(text())="слоган"]/parent::tr/td[last()]')->text()) {
                return $this->clearTagline($tagline);
            }
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @return null|string
     */
    private function parseGenres()
    {
        try {
            $this->crawler->filter('span[itemprop=genre] > a')->each(function (Crawler $node) {
                $this->genres[] = new Genre([
                    'id'   => intval(collect(explode('/', trim($node->attr('href'), '/')))->last()),
                    'name' => $node->text()
                ]);
            });

            return $this->genres;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @return null|string
     */
    private function parseCountries()
    {
        try {
            $this->crawler->filterXPath('//*/td[normalize-space(text())="страна"]/parent::tr/td[last()]/div/a')->each(function (
                Crawler $node
            ) {
                $this->countries[] = new Country([
                    'id'   => intval(collect(explode('/', trim($node->attr('href'), '/')))->last()),
                    'name' => $node->text()
                ]);
            });

            return $this->countries;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @return null|string
     */
    private function parseRuntime()
    {
        try {
            if ($time = $this->crawler->filter('td.time#runtime')->text()) {
                return $this->clearRuntime($time);
            }
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @return null|string
     */
    private function parsePremiere()
    {
        try {
            if ($this->crawler->filter('.prem_ical')->count()) {
                if ($text = $this->crawler->filter('.prem_ical')->attr('data-date-premier-start-link')) {
                    return Carbon::createFromFormat('Ymd', $text)->toDateString();
                }
            }
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @return int|null
     */
    private function parseAgeLimit()
    {
        try {
            if ($this->crawler->filter('.ageLimit')->count()) {
                if (!$classes = $this->crawler->filter('.ageLimit')->attr('class')) {
                    return null;
                }

                $class = collect(explode(' ', $classes))->last();

                if (Str::startsWith($class, 'age') && $age = intval(Str::replaceFirst('age', '', $class))) {
                    return $age;
                }
            }
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @return null|string
     */
    private function parseMpaaRating()
    {
        try {
            if ($mpaa = $this->crawler->filterXPath('//*/td[normalize-space(text())="рейтинг MPAA"]/parent::tr/td/a/@href')->text()) {
                return collect(explode('/', trim($mpaa, '/')))->last();
            }
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @return null|string
     */
    private function parseCompany()
    {
        try {
            if ($container = $this->crawler->filterXPath('//*/td[normalize-space(text())="премьера (РФ)"]/parent::tr/td[last()]')) {
                $id = $container->filterXPath('//*/a[contains(@href, "company")]/@href')->text();

                if (Str::contains($id, 'company')) {
                    return (int)collect(explode('/', trim($id, '/')))->last();
                }
            }
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @param string $string
     *
     * @return string
     */
    private function clearTitle(string $string): string
    {
        $string = preg_replace('~\x{00a0}~siu', ' ', $string);
        $string = html_entity_decode($string, ENT_QUOTES);
        $string = preg_replace("/ \\((.+?)\\)/", '', $string);
        $string = trim($string);

        return $string;
    }

    /**
     * @param string $tagline
     *
     * @return string|null
     */
    private function clearTagline(string $tagline)
    {
        if (ord(Str::substr($tagline, 0, 1)) == 194) {
            $tagline = Str::substr($tagline, 1);
        }

        if (ord(Str::substr($tagline, -1, 1)) == 194) {
            $tagline = Str::substr($tagline, 0, -1);
        }

        if ($tagline == '-') {
            return null;
        }

        return $tagline;
    }

    /**
     * @param string $time
     *
     * @return string
     */
    private function clearRuntime(string $time)
    {
        if (Str::contains($time, '/')) {
            $time = collect(explode('/', $time))->last();
        }

        $time = trim($time);

        return $time;
    }
}