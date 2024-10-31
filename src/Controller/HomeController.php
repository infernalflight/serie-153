<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function home(HttpClientInterface $httpClient): Response
    {
        $response = $httpClient->request('GET', 'https://api.chucknorris.io/jokes/random');

        if ($response->getStatusCode() === Response::HTTP_OK) {
            $blague = json_decode($response->getContent(), true)['value'];
        }

        return $this->render('home.html.twig', [
            'blague' => $blague ?? '',
        ]);
    }
}
