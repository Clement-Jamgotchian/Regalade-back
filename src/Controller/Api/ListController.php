<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

    /**
     *
     * @Route("/api/list", name="app_api_list_")
     */

class ListController extends AbstractController
{
        /**
     * afficher la liste des repas
     *
     * @Route("", name="add", methods = {"POST"})
     */
    public function add(Request $request):JsonResponse
    {


    }
}
