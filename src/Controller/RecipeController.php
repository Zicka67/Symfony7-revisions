<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class RecipeController extends AbstractController
{

    #[Route('/recette', name: 'recipe.index')]
    public function index(Request $request): Response
    {
        return new Response('Recette');
    }

    #[Route('/recette/{slug}-{id}', name: 'recipe.show', requirements: ['id' => '\d+', 'slug'=> '[a-z0-9-]+'])]
    public function show(Request $request, $slug, $id): Response
    {
        // dd($request->attributes->get('slug'), $request->attributes->get('id'));
        // dd($slug, $id);
        // return new JsonResponse([
        //     'slug' => $slug
        // ]);
        return new Response('Recette des : ' . $slug);
    }
}
