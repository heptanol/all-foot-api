<?php

namespace AppBundle\Service;


class ApiService
{
    /**
     * @var array
     */
    private $url;

    /**
     * @var CacheService
     */
    private $cacheService;

    /**
     * @var MappingService
     */
    private $mappingService;

    public function __construct(array $url, CacheService $cacheService, MappingService $mappingService)
    {
        $this->url = $url;
        $this->cacheService = $cacheService;
        $this->mappingService = $mappingService;
    }


    /**
     * @param $id
     * @param array $query
     * @return string
     */
    public function getCompetionMatches($id, array $query)
    {
        $url = str_replace('{competitions_id}', $id, $this->url['get_fixtures']);

        $result = $this->cacheService->getResponse($url);

        if (property_exists($result, 'matches') && array_key_exists('matchday', $query)) {
            $result = $this->mappingService->filterByMatchDay($result, $query['matchday']);
        }

        return json_encode($result);
    }

    /**
     * @param $id
     * @return mixed|null|string
     */
    public function getCompetition($id)
    {
        $url = str_replace('{competitions_id}', $id, $this->url['get_table']);

        $result = $this->cacheService->getResponse($url);

        $result = $this->mappingService->getCompetition($result);

        return json_encode($result);
    }

    /**
     * @param $id
     * @return mixed|null|string
     */
    public function getTable($id, array $query)
    {
        $url = str_replace('{competitions_id}', $id, $this->url['get_table']) .'?'. http_build_query($query);

        $result = $this->cacheService->getResponse($url);

        if (property_exists($result, 'standings')) {
            $result = $this->mappingService->getTotalStanding($result->standings);
        }

        return json_encode($result);
    }

    /**
     * @return string
     */
    public function getTodayMatchs()
    {
        $now = new \DateTime();
        $query = array(
            'dateFrom' => $now->setTime(0,0, 0),
            'dateTo' => $now->setTime(23,59, 59)
        );
        $url = $this->url['get_today_matchs'] .'?'. http_build_query($query);
        $result = $this->cacheService->getResponse($url);

        $result = $this->mappingService->filterTodayMatchs($result->matches);

        return json_encode($result);
    }

}