<?php

namespace App\Controller;

use SebastianBergmann\CodeCoverage\Report\Html\Renderer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(Request $request): Response
    {
        // return $this->render('home/index.html.twig', [
        //     'controller_name' => 'HomeController',
        // ]);

        // return new Response('Bonjour ' . $request->query->get('name', 'Inconnu'));
        return $this->render('home/index.html.twig');
    }
}
