<?php

namespace Siqwell\Kinopoisk\Mappers;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Siqwell\Kinopoisk\Models\Film;

/**
 * Class Mapper
 * @package Siqwell\Kinopoisk\Mappers
 */
class FilmDetailsMapper extends Mapper
{

    /**
     * @return mixed
     */
    public function get()
    {
        return new Film([
            'title'    => $this->parseTitle(),
            'original' => $this->parseOriginalTitle(),
            'tagline'  => $this->parseTagline(),
            'runtime'  => $this->parseRuntime(),
            'premiere' => $this->parsePremiere(),
        ]);
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
     * @param string $string
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