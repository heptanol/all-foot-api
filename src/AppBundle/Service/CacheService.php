<?php

namespace AppBundle\Service;


use GuzzleHttp\Client;
use Symfony\Component\Cache\Simple\FilesystemCache;

class CacheService
{

    /**
     * @var $client Client
     */
    private $client;
    /**
     * @var $arrayCache FilesystemCache
     */
    private $cache;

    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->cache = new FilesystemCache('', 80000);
    }

    public function getResponse($url)
    {
        $key = $this->generateKey($url);

        if ($this->cache->has($key)) {
            return $this->cache->get($key);
        }
        $result = json_decode($this->client->get($url)->getBody()->getContents());
        $this->cache->set($key, $result);

        return $result;
    }

    private function generateKey($url)
    {
        return str_replace('/', '.', $url);
    }
}