<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ConsentController extends AbstractController {

    /**
     * @Route("/consent", name="app_consent", methods={"GET","POST"})
     *
     * @param Request $request
     *
     * @return Response
     */
    final public function showForm(Request $request): Response {
        return new JsonResponse(['saying' => 'Hello World']);
    }
}
