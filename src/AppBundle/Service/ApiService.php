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

    /**
     * @var array
     */
    private $url;

    public function __construct(Client $client, array $url)
    {
        $this->client = $client;
        $this->url = $url;
        $this->cache = new FilesystemCache('', 80000);
    }

    /**
     * @param Request $request
     * @param $requestUrl
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function call(Request $request, $requestUrl)
    {
        $url = '/v1/' . $requestUrl . '?' . $request->getQueryString();
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

    /**
     * @param $id
     * @param array $query
     * @return string
     */
    public function getCompetionMatches($id, array $query)
    {
        $url = str_replace('{competitions_id}', $id, $this->url['get_fixtures']);
        $key = $this->generateKey($url);

        if ($this->cache->has($key)) {
            $result = $this->cache->get($key);
        } else {
            $result = json_decode($this->client->get($url)->getBody());
            $this->cache->set($key, $result);
        }
        if (property_exists($result, 'matches') && array_key_exists('matchday', $query)) {
            $result = $this->filterByMatchDay($result, $query['matchday']);
        }

        return json_encode($result);
    }

    /**
     * @param $id
     * @return mixed|null|string
     */
    public function getCompetition($id)
    {
        $url = str_replace('{competitions_id}', $id, $this->url['get_competition']);
        $key = $this->generateKey($url);

        if ($this->cache->has($key)) {
            $result = $this->cache->get($key);
        } else {
            $result = $this->client->get($url)->getBody()->getContents();
            $this->cache->set($key, $result);
        }

        return $result;
    }

    /**
     * @param $id
     * @return mixed|null|string
     */
    public function getTable($id, array $query)
    {
        $url = str_replace('{competitions_id}', $id, $this->url['get_table']) .'?'. http_build_query($query);
        $key = $this->generateKey($url);

        if ($this->cache->has($key)) {
            $result = $this->cache->get($key);
        } else {
            $result = json_decode($this->client->get($url)->getBody());
            $this->cache->set($key, $result);
        }

        if (property_exists($result, 'standings')) {
            $result = $this->getTotalStanding($result->standings);
        }

        return json_encode($result);
    }

    private function generateKey($url)
    {
        return str_replace('/', '.', $url);
    }

    private function filterByMatchDay($data, $matchDay)
    {
        $matches = array();
        $totalMatchDay = -1;
        foreach ($data->matches as $match) {
            if ($match->matchday == $matchDay) {
                $day = new \DateTime($match->utcDate);
                $now = new \DateTime();
                $diff = date_diff($now, $day)->i;
                $day->setTime(0,0,0);
                $match->day = $day->format('Y-m-d\TH:i:s\Z');
                $match->diff = $diff;
                $matches[] = $match;
            }
            if ($match->matchday >= $totalMatchDay) {
                $totalMatchDay = $match->matchday;
            }
        }
        $data->totalMatchDays = $totalMatchDay;
        $data->matches = $matches;
        return $data;
    }

    /**
     * @param array $standings
     * @return mixed
     */
    private function getTotalStanding(array $standings)
    {
        foreach ($standings as $standing) {
            if (property_exists($standing, 'type') && $standing->type === 'TOTAL') {
                return $standing->table;
            }
        }
        return [];
    }


}