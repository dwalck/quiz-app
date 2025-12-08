<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Attribute\Route;

class IndexController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('index.html.twig', []);
    }

    #[Route('/publish', name: 'publish')]
    public function publish(HubInterface $hub): Response
    {
        $update = new Update(
            'x1',
            json_encode(['status' => 'OutOfStock'])
        );

        $hub->publish($update);

        return new Response('published!');
    }
}
