<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use Doctrine\ORM\EntityManager;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Flex\Command\RecipesCommand;

class RecipeController extends AbstractController
{

    #[Route('/recette', name: 'recipe.index')]
    public function index(RecipeRepository $repository): Response
    {
        $recipes = $repository->findWithDurationLowerThan(20);
        $recipeTotalDuration = $repository->findTotalDuration();

        return $this->render('recipe/index.html.twig', [
            'recipes' => $recipes,
            'recipeTotalDuration' => $recipeTotalDuration
        ]);
    }

    #[Route('/recette/{slug}-{id}', name: 'recipe.show', requirements: ['id' => '\d+', 'slug'=> '[a-z0-9-]+'])]
    public function show(Request $request, string $slug, int $id, RecipeRepository $repository): Response
    {
    
        $recipe = $repository->find($id);
        if($recipe->getSlug() != $slug) {
            return $this->redirectToRoute('recipe.show', ['slug' => $recipe->getSlug(), 'id' => $recipe->getId() ]);
        }
        // return new Response('Recette des : ' . $slug);
        return $this->render('recipe/show.html.twig', [
            'recipe' => $recipe
        
        ]);
    }

    #[Route('/recette/{id}/edit', name: 'recipe.edit', methods: ['GET', 'POST'])]
    public function edit(RecipeRepository $repository, Recipe $recipe, Request $request, EntityManagerInterface $em): Response
    {
  
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $recipe->setCreatedAt(new \DateTimeImmutable());
            $recipe->setUpdatedAt(new \DateTimeImmutable());
            $em->flush();
            $this->addFlash('success', 'La recette a bien été modifiée');
            return $this->redirectToRoute('recipe.index');
        }

        return $this->render('recipe/edit.html.twig', [
            'recipe' => $recipe,
            'form' => $form
        ]);
    }


    #[Route('/recette/create', name: 'recipe.create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $recipe = new Recipe();
  
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $recipe->setCreatedAt(new \DateTimeImmutable());
            $recipe->setUpdatedAt(new \DateTimeImmutable());
            $em->persist($recipe);
            $em->flush();
            $this->addFlash('success', 'La recette a bien été crée');
            return $this->redirectToRoute('recipe.index');
        }

        return $this->render('recipe/create.html.twig', [
            'form' => $form
        ]);
    }

    
    #[Route('/recette/{id}/edit', name: 'recipe.delete', methods: ['DELETE'])]
    public function remove(Recipe $recipe, EntityManagerInterface $em)
    {
        $em->remove($recipe);
        $em->flush();
        $this->addFlash('success', 'La recette a bien été suprimée');

        return $this->redirectToRoute('recipe.index');
    }

}
