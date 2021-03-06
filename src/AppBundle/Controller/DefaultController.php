<?php

namespace AppBundle\Controller;

use AppBundle\Service\ApiService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/api/competitions/{id}/matches", name="matches")
     */
    public function getFixturesAction(Request $request, $id)
    {
        $response = $this->get(ApiService::class)->getCompetionMatches($id, $request->query->all());

        $responseApi = new Response($response, JsonResponse::HTTP_OK);
        $responseApi->headers->set('Content-Type', 'application/json');
        $responseApi->headers->set('Access-Control-Allow-Origin', 'http://localhost:4200');

        return $responseApi;
    }

    /**
     * @Route("/api/competitions/{id}/standings", name="table")
     */
    public function getTableAction(Request $request, $id)
    {
        $response = $this->get(ApiService::class)->getTable($id, $request->query->all());

        $responseApi = new Response($response, JsonResponse::HTTP_OK);
        $responseApi->headers->set('Content-Type', 'application/json');
        $responseApi->headers->set('Access-Control-Allow-Origin', 'http://localhost:4200');

        return $responseApi;
    }

    /**
     * @Route("/api/competitions/{id}", name="competition")
     */
    public function getCompetitionAction(Request $request, $id)
    {
        $response = $this->get(ApiService::class)->getCompetition($id);

        $responseApi = new Response($response, JsonResponse::HTTP_OK);
        $responseApi->headers->set('Content-Type', 'application/json');
        $responseApi->headers->set('Access-Control-Allow-Origin', 'http://localhost:4200');

        return $responseApi;
    }

    /**
     * @Route("/api/matches/today", name="test")
     */
    public function getTodayMatchsAction(Request $request)
    {
        $response = $response = $this->get(ApiService::class)->getTodayMatchs();

        $responseApi = new Response($response, JsonResponse::HTTP_OK);
        $responseApi->headers->set('Content-Type', 'application/json');
        $responseApi->headers->set('Access-Control-Allow-Origin', 'http://localhost:4200');

        return $responseApi;
    }

}
