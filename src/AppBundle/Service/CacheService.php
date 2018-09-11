<?php

namespace AppBundle\Service;


use AppBundle\Entity\ApiCalls;
use Doctrine\ORM\EntityManager;
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

    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(Client $client, Logger $logger, EntityManager $em)
    {
        $this->client = $client;
        $this->logger = $logger;
        $this->em = $em;
        $this->cache = new FilesystemCache('', 60);
    }

    public function getResponse($url)
    {
        $key = $this->generateKey($url);

        if ($this->cache->has($key)) {
            $this->logger->info('[From Cache] '. $url);
            return json_decode($this->cache->get($key));
        }

        try {
            $apiResponse = $this->client->get($url);
        } catch(\Exception $e){
            $this->logger->error($e->getMessage());
        }
        if (isset($apiResponse)) {
            $result = $apiResponse->getBody()->getContents();
            $this->cache->set($key, $result);
            $this->saveResponse($key, $result);
            $this->logger->info('[From API] '. $url);
        } else {
            $this->logger->info('[From DATABASE] '. $url);
            $result = $this->findResponse($key);
        }

        return json_decode($result);
    }

    private function generateKey($url)
    {
        return str_replace('/', '.', $url);
    }

    private function saveResponse($url, $apiRespons)
    {
        $apiCall = $this->em->getRepository('AppBundle:ApiCalls')->findOneBy(array('url' => $url));
        if (is_null($apiCall)) {
            $apiCall = new ApiCalls();
        }
        $apiCall->setResponse($apiRespons);
        $apiCall->setUrl($url);
        $this->em->persist($apiCall);
        $this->em->flush();
    }

    private function findResponse($url)
    {
        $apiCall = $this->em->getRepository('AppBundle:ApiCalls')->findOneBy(array('url' => $url));

        return $apiCall->getResponse();
    }
}