<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HelloController extends AbstractController {

    /**
     * @Route("/hello", name="app_hello")
     *
     * @return Response
     */
    final public function hello(): Response {
        return new JsonResponse(['saying' => 'Hello World!']);
    }
}
