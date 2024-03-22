<?php

namespace App\Controller;

use App\Repository\RecipeRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
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

    //******* RECETTE *******
    #[Route('/recettes', name: 'show')]
    public function show(RecipeRepository $repository): Response
    {
    
        $recipe = $repository->findAll();
        
        $recipeTotalDuration = $repository->findTotalDuration();
        // dd($recipe);
        
        return $this->render('recipe/show.html.twig', [
            'recipes' => $recipe,
            'recipeTotalDuration' => $recipeTotalDuration
        
        ]);
    }

    #[Route('/recettes/{slug}', name: 'recipe.detail')]
    public function details(string $slug, RecipeRepository $repository): Response
    {
    
        $recipe = $repository->findOneBy(['slug' => $slug]);
        
        if (!$recipe) {
            throw $this->createNotFoundException('La recette demandée n\'existe pas.');
        }
        
        return $this->render('recipe/details.html.twig', [
            'recipe' => $recipe
        
        ]);
    }

     //******* CATEGORIE *******
    #[Route('/category', name: 'category.index')]
    public function indexCategory(CategoryRepository $repository): Response
    {

        $category = $repository->findAll();

        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategoryController',
            'categories' => $category
        ]);
    }

    #[Route('/category/{slug}', name: 'category.detail')]
    public function detailsCategory(string $slug, CategoryRepository $categoryRepository, EntityManagerInterface $em, RecipeRepository $recipeRepository): Response
    {
        $platPrincipal = $categoryRepository->findOneBy(['slug' => 'plats']);
        $category = $categoryRepository->findOneBy(['slug' => $slug]);
    
        if (!$category) {
            throw $this->createNotFoundException('La catégorie demandée n\'existe pas.');
        }
        
        //GetRecipes pour accéder a la collection de recettes dans category ( OneToMany )
        $recipes = $category->getRecipes();

        $em->flush();
    
        return $this->render('category/details.html.twig', [
            // 'pates' => $pates,
            'platPrincipal' => $platPrincipal,
            'recipes' => $recipes,
            'category' => $category
        
        ]);
    }

}