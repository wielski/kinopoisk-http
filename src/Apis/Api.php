<?php

namespace Siqwell\Kinopoisk\Apis;

use GuzzleHttp\Psr7\Uri;
use Illuminate\Support\Str;
use Siqwell\Kinopoisk\HttpClient;
use Siqwell\Kinopoisk\Mappers\Mapper;

/**
 * Class ContractApi
 * @package Siqwell\Kinopoisk\Apis
 */
abstract class Api
{

    /**
     * @var string
     */
    protected $pattern;

    /**
     * @var HttpClient
     */
    protected $client;

    /**
     * @var string|\Closure|null
     */
    protected $mapper;

    /**
     * Api constructor.
     *
     * @param HttpClient $client
     */
    function __construct(HttpClient $client)
    {
        $this->client = $client;
    }

    /**
     * @param string|\Closure $mapper
     *
     * @return $this
     */
    public function setMapper($mapper)
    {
        if (is_string($mapper) && class_exists($mapper)) {
            $this->mapper = $mapper;
        } else {
            if ($mapper instanceof \Closure) {
                $this->mapper = $mapper;
            } else {
                $this->mapper = null;
            }
        }

        return $this;
    }

    /**
     * @param      $result
     * @param null $url
     *
     * @return mixed
     */
    public function callMap($result, $url = null)
    {
        if ($this->mapper instanceof \Closure) {
            return call_user_func_array($this->mapper, [$result]);
        }

        if (is_string($this->mapper) && class_exists($this->mapper)) {
            if (is_subclass_of($this->mapper, Mapper::class, true)) {
                return app($this->mapper, [
                    'content'   => $result,
                    'url'       => $url,
                    'base_href' => $this->client->getConfig('base_uri')
                ])->get();
            }
        }
    }

    /**
     * @return bool
     */
    public function isMapped()
    {
        return $this->mapper != null;
    }

    /**
     * @param array       $variables
     * @param string|null $path
     *
     * @return mixed
     */
    protected function get(array $variables = [], string $path = null)
    {
        $url = $this->getPattern($variables, $path);

        $response = $this->client->get($url);

        if ($response->getStatusCode() != 200) {
            return false;
        }

        if (!$content = $response->getBody()->getContents()) {
            return false;
        }

        if (!$content = $this->checkContent($content)) {
            return false;
        }

        return $this->isMapped() ? $this->callMap($content, $url) : $content;
    }

    /**
     * @param string $content
     *
     * @return string|bool
     */
    protected function checkContent(string $content)
    {
        if (Str::contains($content, 'captchaSound')) {
            return false;
        }

        return $content;
    }

    /**
     * @param array $variables
     * @param null  $pattern
     *
     * @return mixed
     */
    protected function getPattern(array $variables = [], $pattern = null)
    {
        $pattern = $pattern ?: $this->pattern;

        if (count($variables)) {
            foreach ($variables as $key => $value) {
                $pattern = Str::replaceFirst('{' . $key . '}', $value, $pattern);
            }
        }

        /* @var Uri $uri */
        $uri = $this->client->getConfig('base_uri');

        return (string)$uri->withPath($pattern);
    }

    /**
     * @return HttpClient
     */
    protected function getClient()
    {
        return $this->client;
    }
}