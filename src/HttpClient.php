<?php

namespace Siqwell\Kinopoisk;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\TransferStats;
use Illuminate\Support\Facades\Cache;
use Kevinrob\GuzzleCache\CacheMiddleware;
use Kevinrob\GuzzleCache\Storage\LaravelCacheStorage;
use Kevinrob\GuzzleCache\Strategy\GreedyCacheStrategy;
use Phlib\Guzzle\ConvertCharset;

/**
 * Class HttpClient
 * @package Siqwell\Kinopoisk
 */
class HttpClient extends Client
{

    /**
     * @var string
     */
    protected $cstore = 'file';

    /**
     * HttpClient constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $stack = HandlerStack::create();

        $stack->push($this->cacheMiddleware(), 'cache');
        $stack->push($this->charsetMiddleware(), 'charset');

        $config = array_merge([
            'headers'         => [
                'Referer'         => 'https://www.kinopoisk.ru/',
                'User-Agent'      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/57.0.2987.133 Safari/537.36',
                'Accept'          => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                'Accept-Language' => 'ru-RU,ru;q=0.8,en-US;q=0.6,en;q=0.4',
            ],
            'allow_redirects' => true,
            'handler'         => $stack,
        ], $config);

        parent::__construct($config);
    }

    /**
     * @param int $ttl
     *
     * @return CacheMiddleware
     */
    protected function cacheMiddleware($ttl = 86400): CacheMiddleware
    {
        $store = new LaravelCacheStorage(Cache::store($this->cstore));

        return new CacheMiddleware(new GreedyCacheStrategy($store, $ttl));
    }

    /**
     * @return ConvertCharset
     */
    protected function charsetMiddleware(): ConvertCharset
    {
        return new ConvertCharset();
    }
}