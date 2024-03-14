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
    public function index(Request $request, RecipeRepository $repository, EntityManagerInterface $em): Response
    {
        $recipes = $repository->findWithDurationLowerThan(20);
        $recipeTotalDuration = $repository->findTotalDuration();

        // **Créer**
        // $recipe = new Recipe();
        // $recipe->setTitle('Risotto')
        //     ->setSlug('risotto')
        //     ->setContent('dzdq')
        //     ->setDuration(18)
        //     ->setCreatedAt(new \DateTimeImmutable())
        //     ->setUpdatedAt(new \DateTimeImmutable());
        // $em->persist($recipe);
        // $em->flush();

        // **Supprimer**
        // $em->remove($recipes[0]);
        // $em->flush();
        
        // dd($repository->findTotalDuration());
        // return new Response('Recette');
        return $this->render('recipe/index.html.twig', [
            'recipes' => $recipes,
            'recipeTotalDuration' => $recipeTotalDuration
        ]);
    }

    #[Route('/recette/{slug}-{id}', name: 'recipe.show', requirements: ['id' => '\d+', 'slug'=> '[a-z0-9-]+'])]
    public function show(Request $request, string $slug, int $id, RecipeRepository $repository): Response
    {
        // dd($request->attributes->get('slug'), $request->attributes->get('id'));
        // dd($slug, $id);
        // return new JsonResponse([
        //     'slug' => $slug
        // ]);
        $recipe = $repository->find($id);
        if($recipe->getSlug() != $slug) {
            return $this->redirectToRoute('recipe.show', ['slug' => $recipe->getSlug(), 'id' => $recipe->getId() ]);
        }
        // return new Response('Recette des : ' . $slug);
        return $this->render('recipe/show.html.twig', [
            'recipe' => $recipe
        
        ]);
    }

    #[Route('/recette/{id}/edit', name: 'recipe.edit')]
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

}
