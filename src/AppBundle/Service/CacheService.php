<?php

namespace AppBundle\Service;


use GuzzleHttp\Client;
use Monolog\Logger;
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

    /**
     * @var Logger
     */
    private $logger;

    public function __construct(Client $client, Logger $logger)
    {
        $this->client = $client;
        $this->logger = $logger;
        $this->cache = new FilesystemCache('', 60);
    }

    public function getResponse($url)
    {
        $key = $this->generateKey($url);

        if ($this->cache->has($key)) {
            $this->logger->info('[From Cache] '. $url);
            return $this->cache->get($key);
        }
        try {
            $apiResponse = $this->client->get($url);
        } catch(\Exception $e){
            $this->logger->error($e->getMessage());
        }
        if (isset($apiResponse)) {
            //TODO: Sauvgarder la reponse en Base de donnèes
            $result = json_decode($apiResponse->getBody()->getContents());
            $this->cache->set($key, $result);
            $this->logger->info('[From API] '. $url);
        } else {
            $this->logger->info('[From Cache] '. $url);
            //TODO: Récupérer la reponse de la Base de donnèes
            return $this->cache->get($key);
        }

        return $result;
    }

    private function generateKey($url)
    {
        return str_replace('/', '.', $url);
    }
}