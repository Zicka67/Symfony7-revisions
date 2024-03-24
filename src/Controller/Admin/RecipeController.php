<?php

namespace App\Controller\Admin;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\CategoryRepository;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/recettes', name: 'admin.recipe.')]
class RecipeController extends AbstractController
{

    #[Route('/', name: 'index')]
    public function index(RecipeRepository $recipeRepository, CategoryRepository $categoryRepository, EntityManagerInterface $em): Response
    {

        $recipes = $recipeRepository->findWithDurationLowerThan(20);
        // $recipeTotalDuration = $recipeRepository->findTotalDuration();

        return $this->render('admin/recipe/index.html.twig', [
            'recipes' => $recipes,
            // 'recipeTotalDuration' => $recipeTotalDuration
        ]);
    }

    #[Route('/create', name: 'create')]
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
            return $this->redirectToRoute('admin.recipe.index');
        }

        return $this->render('admin/recipe/create.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/{id}', name: 'edit', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS])]
    public function edit(Recipe $recipe, Request $request, EntityManagerInterface $em): Response
    {
  
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $recipe->setCreatedAt(new \DateTimeImmutable());
            $recipe->setUpdatedAt(new \DateTimeImmutable());
            /** @var UploadedFile $file */
            //On récupère le fichier
            $file = $form->get('thumbnailFile')->getData();
            //On génère un fichier
            $filename = $recipe->getId() . '.' . $file->getClientOriginalExtension();
            //Ondéplace avec la function move
            $file->move($this->getParameter('kernel.project_dir') . '/public/images/recipes', $filename);
            //on sauvegarde en base de donnée
            $recipe->setThumbnail($filename);
            $em->flush();

            $this->addFlash('success', 'La recette a bien été modifiée');
            return $this->redirectToRoute('admin.recipe.index');
        }

        return $this->render('admin/recipe/edit.html.twig', [
            'recipe' => $recipe,
            'form' => $form
        ]);
    }
    
    #[Route('/{id}', name: 'delete', methods: ['DELETE'], requirements: ['id' => Requirement::DIGITS])]
    public function remove(Recipe $recipe, EntityManagerInterface $em)
    {
        $em->remove($recipe);
        $em->flush();

        $this->addFlash('success', 'La recette a bien été supprimée');
        return $this->redirectToRoute('admin.recipe.index');
    }

}
