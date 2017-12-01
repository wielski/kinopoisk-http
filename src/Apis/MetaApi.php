<?php

namespace Siqwell\Kinopoisk\Apis;

use Illuminate\Support\Collection;
use Siqwell\Kinopoisk\Mappers\MetaCompaniesMapper;
use Siqwell\Kinopoisk\Mappers\MetaCountriesMapper;
use Siqwell\Kinopoisk\Mappers\MetaGenresMapper;
use Siqwell\Kinopoisk\Mappers\MetaMpaaMapper;

/**
 * Class MetaApi
 * @package Siqwell\Kinopoisk\Apis
 */
class MetaApi extends Api
{
    /**
     * @var string
     */
    protected $pattern = "/s/";

    /**
     * @return Collection
     */
    public function genres()
    {
        return $this->setMapper(MetaGenresMapper::class)->get();
    }

    /**
     * @return Collection
     */
    public function countries()
    {
        return $this->setMapper(MetaCountriesMapper::class)->get();
    }

    /**
     * @return Collection
     */
    public function companies()
    {
        return $this->setMapper(MetaCompaniesMapper::class)->get();
    }

    /**
     * @return Collection
     */
    public function mpaa()
    {
        return $this->setMapper(MetaMpaaMapper::class)->get();
    }
}