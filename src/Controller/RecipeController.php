<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RecipeController extends AbstractController
{
    #[Route('/recettes', name: 'recipe.index')]
    public function index(Request $request): Response
    {

        return $this->render('recipe/index.html.twig');

        // code donné par défaut
/*         return $this->render('recipe/index.html.twig', [
            'controller_name' => 'RecipeController',
        ]);
 */    }

 #[Route('/recettes/{slug}-{id}', name: 'recipe.show', requirements: ['id' => '\d+', 'slug' => '[a-z0-9-]+'])]
 //public function show(Request $request): Response
 // mettre les paramètres au niveau de la méthdoe
 public function show(Request $request, string $slug, int $id)
 {

    return $this->render('recipe/show.html.twig', [
        'slug' => $slug,
        'echappee' => "<strong>ceci est échappé par Twig</strong>",
        'person' => [
            'firstname' => 'Jane',
            'lastname' => 'Doe'
        ],
        'id' => $id
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
