<?php

namespace AppBundle\Service;


class MappingService
{
    private $competitionsId;

    public function __construct(array $competitionsId)
    {
        $this->competitionsId = $competitionsId;
    }

    public function filterByMatchDay($data, $matchDay)
    {
        $matches = array();
        $totalMatchDay = -1;
        foreach ($data->matches as $match) {
            if ($match->matchday == $matchDay) {
                $matches[] = $this->matchMapping($match);
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
    public function getTotalStanding(array $standings)
    {
        $result = array();
        foreach ($standings as $standing) {
            if (property_exists($standing, 'type') && $standing->type === 'TOTAL') {
                $result[] = $standing->table;
            }
        }
        return $result;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function matchMapping($data)
    {
        $day = new \DateTime($data->utcDate);
        $now = new \DateTime();
        $diff = date_diff($now, $day)->i;
        $day->setTime(0,0,0);
        $data->day = $day->format('Y-m-d\TH:i:s\Z');
        $data->diff = $diff;
        return $data;
    }

    /**
     * @param $matchs
     * @return mixed
     */
    public function filterTodayMatchs($matchs)
    {
        $result = [];
        foreach ($matchs as $key => $match) {
            $match = $this->matchMapping($match);
            if (property_exists($match, 'competition') && property_exists($match->competition, 'name') ) {
                $match->competitionName = $match->competition->name;
            }
            if (in_array($match->competition->id, $this->competitionsId)) {
                $result[] = $match;
            }
        }
        return $result;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function getCompetition($data)
    {
        if($data->standings) {
            unset($data->standings);
        }
        return $data;
    }
}