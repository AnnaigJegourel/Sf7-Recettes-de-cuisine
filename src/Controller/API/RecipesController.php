<?php

namespace App\Controller\API;

use App\Entity\Recipe;
use App\Repository\RecipeRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class RecipesController extends AbstractController
{
    #[Route("/api/recipes", methods: ['GET'])]
    public function index(RecipeRepository $repository, Request $request)
    {
        //$recipes = $repository->findAll();
        //avec pagination :
        $recipes = $repository->paginateRecipes($request->query->getInt('page', 1));

        return $this->json($recipes, 200, [], [
            'groups' => ['recipes.index']
        ]);
    }


    #[Route("/api/recipes/{id}", requirements: ['id' => Requirement::DIGITS])]
    public function show(Recipe $recipe)
    {

        return $this->json($recipe, 200, [], [
            'groups' => ['recipes.index', 'recipes.show']
        ]);
    }


    #[Route("/api/recipes", methods: ['POST'])]
    public function create(
        Request $request,
        #[MapRequestPayload(serializationContext: ['groups' => ['recipes.create']])]
        Recipe $recipe,
        EntityManagerInterface $em)
    {
        $recipe->setCreatedAt(new DateTimeImmutable());
        $recipe->setUpdatedAt(new DateTimeImmutable());
        $em->persist($recipe);
        $em->flush();

        return $this->json(
            $recipe,
            200,
            [],
            ['groups' => 'recipes.create']
        );
    }
    //en utilisant le SerializerInterface, sans créer automatiquement l'objet à partir des données de l'API
/*     public function create(Request $request, SerializerInterface $serializer)
    {
        //créer un objet à remplir avec les données une fois désérialisées
        $recipe = new Recipe();
        $recipe->setCreatedAt(new DateTimeImmutable());
        $recipe->setUpdatedAt(new DateTimeImmutable());

        //paramètres: données à désérialiser, type à rendre, format à traiter, contexte
        dd($serializer->deserialize(
            $request->getContent(), 
            Recipe::class, 
            'json',
            [
                //utiliser cette constante pour remplir l'objet déjà créé au lieu d'en créer un nouveau
                AbstractNormalizer::OBJECT_TO_POPULATE => $recipe,
                //préciser ce qui peut être modifié
                'groups' => ['recipes.create']
            ]
        ));
    }
 */
}