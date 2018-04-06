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
     * @Route("/api/{requestUrl}", name="api", requirements={"requestUrl"=".+"})
     */
    public function apiCallAction(Request $request, $requestUrl)
    {

        $response = $this->get(ApiService::class)->call($request, $requestUrl);
        $responseApi = new Response($response, JsonResponse::HTTP_OK);
        $responseApi->headers->set('Content-Type', 'application/json');
        $responseApi->headers->set('Access-Control-Allow-Origin', 'http://localhost:4200');

        return $responseApi;
    }

}
