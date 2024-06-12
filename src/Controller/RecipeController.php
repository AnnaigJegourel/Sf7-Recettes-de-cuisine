<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RecipeController extends AbstractController
{

    // -------------- READ ALL --------------
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


    // -------------- READ ONE --------------

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


    // -------------- EDIT --------------
    #[Route('recettes/{id}/edit', name: 'recipe.edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    //sans importer l'entité
    //public function edit(int $id) {
    //le framework va trouver tout seul le find($id) et trouver l'objet recette
    public function edit(Recipe $recipe, Request $request, EntityManagerInterface $em) {

        // créer le formulaire en indiquant le Type à utiliser + les données
        $form = $this->createForm(RecipeType::class, $recipe);
        //vérifie si le formulaire a été soumis, 
        //si oui, modifie l'entité avec ses données (utilise les setters)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $recipe->setUpdatedAt(new DateTimeImmutable());
            $em->flush();

            $this->addFlash('success', "La recette a bien été modifiée");

            return $this->redirectToRoute('recipe.index');
        }

        return $this->render('recipe/edit.html.twig', [
            'recipe' => $recipe,
            'form' => $form
        ]);
    }


    // -------------- CREATE --------------
    #[Route('recettes/create', name: 'recipe.create')]
    public function create(Request $request, EntityManagerInterface $em)
    {
        // je crée un objet vide à envoyer dans mon formulaire
        $recipe = new Recipe;
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // à ne pas oublier pour une création d'objet
            $recipe->setCreatedAt(new DateTimeImmutable());
            $recipe->setUpdatedAt(new DateTimeImmutable());
            $em->persist($recipe);
            $em->flush();
            $this->addFlash('success', 'La recette a bien été créée');

            return $this->redirectToRoute('recipe.index');
        }

        return $this->render('recipe/create.html.twig', [
            'form' => $form
        ]);
    }

    // -------------- DELETE --------------
    #[Route('recettes/{id}', name: 'recipe.delete', methods: ['DELETE'])]
    public function remove(Recipe $recipe, EntityManagerInterface $em)
    {
        $em->remove($recipe);
        $em->flush();
        $this->addFlash('success', 'La recette a bien été supprimée');

        return $this->redirectToRoute('recipe.index');
}

}
