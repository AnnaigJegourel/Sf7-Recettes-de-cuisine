<?php

namespace App\Controller;

use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RecipeController extends AbstractController
{
    #[Route('/recettes', name: 'recipe.index')]
    public function index(Request $request, RecipeRepository $repository): Response
    {
        $recipes = $repository->findAll();
        //dd($recipes);

        return $this->render('recipe/index.html.twig', [
            'recipes' => $recipes
        ]);

        // code donné par défaut
/*         return $this->render('recipe/index.html.twig', [
            'controller_name' => 'RecipeController',
        ]);
 */    }

#[Route('/recettes/{slug}-{id}', name: 'recipe.show', requirements: ['id' => '\d+', 'slug' => '[a-z0-9-]+'])]
 //public function show(Request $request): Response
 // mettre les paramètres au niveau de la méthdoe
public function show(Request $request, string $slug, int $id, RecipeRepository $repository)
{
    $recipe = $repository->find($id);
    //$recipe = $repository->findOneBy(['slug' => $slug]);
    if ($recipe->getSlug() !== $slug) {
        return $this->redirectToRoute('recipe.show', ['slug' => $recipe->getSlug(), 'id' => $recipe->getId()]);
    }

    return $this->render('recipe/show.html.twig', [
        'slug' => $slug,
        'echappee' => "<strong>ceci est échappé par Twig</strong>",
        'person' => [
            'firstname' => 'Jane',
            'lastname' => 'Doe'
        ],
        'id' => $id,
        'recipe' => $recipe
    ]);

    // Retour Json avec AbstractController
    //return $this->json(['slug' => $slug]);
    
    // Retour Json sans AbstractController
   // return new JsonResponse(['slug' => $slug]);

    // Retour classique
    //return new Response('Recette ' . $slug);

    // Récupérer les attributs de la requête
    //dd($request->attributes->get('slug'), $request->attributes->get('id'));
    // si les paramètres sont dans les arguments de la méthdoes :
    //dd($slug, $id);
    //dd($request);

    }

}
