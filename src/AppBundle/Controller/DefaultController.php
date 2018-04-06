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
     * @Route("/api/competitions/{id}/fixtures", name="fixtures")
     */
    public function getFixturesAction(Request $request, $id)
    {
        $response = $this->get(ApiService::class)->getFixtures($id, $request->query->all());

        $responseApi = new Response($response, JsonResponse::HTTP_OK);
        $responseApi->headers->set('Content-Type', 'application/json');
        $responseApi->headers->set('Access-Control-Allow-Origin', 'http://localhost:4200');

        return $responseApi;
    }

    /**
     * @Route("/api/competitions/{id}/leagueTable", name="table")
     */
    public function getTableAction(Request $request, $id)
    {
        $response = $this->get(ApiService::class)->getTable($id);

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
     * @Route("/api/test", name="test")
     */
    public function getTestAction(Request $request)
    {
        $data = $this->get('guzzle.client.api_foot')->get('/v1/competitions')->getBody();
        dump(\GuzzleHttp\json_decode($data));die;
    }

}
