<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RecipeController extends AbstractController
{
    #[Route('/recettes', name: 'recipe.index')]
    public function index(Request $request, RecipeRepository $repository, EntityManagerInterface $em): Response
    {
        //dd($repository->findTotalDuration());
        $recipes = $repository->findAll();
        //dd($recipes);

        // Modifier un enregistrement
        //$recipes[0]->setTitle("Pâtes boloniaises") ;

        // Créer un nouvel enregistrement
        $recipe = new Recipe;
        $recipe->setTitle('Barbe à papa')
            ->setSlug('barbe-a-papa')
            ->setContent('Mettez du sucre')
            ->setDuration(2)
            ->setCreatedAt(new \DateTimeImmutable())
            ->setUpdatedAt(new \DateTimeImmutable());

        // l'entity manager doit enregistrer la présente de ce nouvel objet
        //$em->persist($recipe);

        // Supprimer un objet
       // $em->remove($recipes[0]);

        // pour que Doctrine compare avec la bdd et modifie:
        //$em->flush() ;

        $recipes10 = $repository->findWithDurationLowerThan(10);

        return $this->render('recipe/index.html.twig', [
            'recipes' => $recipes,
            'recipes10' => $recipes10
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

    #[Route('recettes/{id}/edit', name: 'recipe.edit', requirements: ['id' => '\d+'])]
    //sans importer l'entité
    //public function edit(int $id) {
    //le framework va trouver tout seul le find($id) et trouver l'objet recette
    public function edit(Recipe $recipe) {
        // créer le formulaire en indiquant le Type à utiliser + les données
        $form = $this->createForm(RecipeType::class, $recipe);
        return $this->render('recipe/edit.html.twig', [
            'recipe' => $recipe,
            'form' => $form
        ]);
    }

}
