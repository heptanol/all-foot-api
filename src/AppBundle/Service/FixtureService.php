<?php
/**
 * Created by PhpStorm.
 * User: alaeddine.thamine
 * Date: 08/04/2018
 * Time: 17:17
 */

namespace AppBundle\Service;


class FixtureService
{
    /**
     * @var ApiService
     */
    protected $apiService;

    /**
     * FixtureService constructor.
     */
    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }
}