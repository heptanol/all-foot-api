<?php

namespace AppBundle\Service;


use GuzzleHttp\Client;
use Symfony\Component\Cache\Simple\FilesystemCache;
use Symfony\Component\HttpFoundation\Request;

class ApiService
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
        $this->cache = new FilesystemCache('', 60);
    }

    /**
     * @param Request $request
     * @param $requestUrl
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function call(Request $request, $requestUrl)
    {
        $url = '/v1/'. $requestUrl .'?'. $request->getQueryString();
        $key = str_replace('/', '.', $url);
        $callNumber = $this->cache->has('count') ? $this->cache->get('count') : 0;

        if ($this->cache->get($key)) {
           $result = $this->cache->get($key);
        } else {
            $result = (string) $this->client->get($url)->getBody();
            $this->cache->set($key, $result);
            $this->cache->set('count', ++$callNumber);
        }
        return $result;
    }


}