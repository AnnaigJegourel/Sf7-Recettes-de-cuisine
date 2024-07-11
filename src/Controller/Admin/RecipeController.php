<?php

namespace App\Controller\Admin;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use App\Security\Voter\RecipeVoter;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;


#[Route('/admin/recettes', name: 'admin.recipe.')]
//#[IsGranted('ROLE_ADMIN')]
#[IsGranted('ROLE_USER')]
class RecipeController extends AbstractController
{


    // -------------- CREATE --------------
    #[Route('/create', name: 'create')]
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

            return $this->redirectToRoute('admin.recipe.index');
        }

        return $this->render('admin/recipe/create.html.twig', [
            'form' => $form
        ]);
    }


    // -------------- READ ALL --------------
    #[Route('/', name: 'index')]
    // #[IsGranted('ROLE_USER')]
    public function index(Request $request, RecipeRepository $repository, EntityManagerInterface $em): Response
    {
        //récupérer la page courante (valeur 1 par défaut)
        $page = $request->query->getInt('page', 1);
        //définir le nombre de recette par page (sans knp)
        //$limit = 2;
        
        //avec pagination knp template
        $recipes = $repository->paginateRecipes($page);
        
        //avec pagination sans knp template
        // $recipes = $repository->paginateRecipes($page, $limit);
        //récupérer le nombre total de recettes & le diviser par le nb de recettes par page, arrondi à virgule sup
        //$maxPage = ceil($recipes->getTotalItemCount() / 2);
        //sans knp
        //$maxPage = ceil($recipes->count() / 2);
        
        //$this->denyAccessUnlessGranted('ROLE_USER');

        //dd($repository->findTotalDuration());
        //$recipes = $repository->findAll();
        //dd($recipes);

        // Modifier un enregistrement
        //$recipes[0]->setTitle("Pâtes boloniaises") ;

        // Créer un nouvel enregistrement
/*         $recipe = new Recipe;
        $recipe->setTitle('Barbe à papa')
            ->setSlug('barbe-a-papa')
            ->setContent('Mettez du sucre')
            ->setDuration(2)
            ->setCreatedAt(new \DateTimeImmutable())
            ->setUpdatedAt(new \DateTimeImmutable());
 */
        // l'entity manager doit enregistrer la présente de ce nouvel objet
        //$em->persist($recipe);

        // Supprimer un objet
       // $em->remove($recipes[0]);

        // pour que Doctrine compare avec la bdd et modifie:
        //$em->flush() ;

        $recipes10 = $repository->findWithDurationLowerThan(10);

        return $this->render('admin/recipe/index.html.twig', [
            'recipes' => $recipes,
            'recipes10' => $recipes10
            //'maxPage' => $maxPage,        //utiles si pas knp template
            //'page' => $page
        ]);

        // code donné par défaut
/*         return $this->render('admin/recipe/index.html.twig', [
            'controller_name' => 'RecipeController',
        ]);
 */    }


    // -------------- READ ONE --------------

    #[Route('/{slug}-{id}', name: 'show', requirements: ['id' => '\d+', 'slug' => '[a-z0-9-]+'])]
    //public function show(Request $request): Response
    // mettre les paramètres au niveau de la méthdoe
    public function show(Request $request, string $slug, int $id, RecipeRepository $repository)
    {
        $recipe = $repository->find($id);
        //$recipe = $repository->findOneBy(['slug' => $slug]);
        if ($recipe->getSlug() !== $slug) {
            return $this->redirectToRoute('admin.recipe.show', ['slug' => $recipe->getSlug(), 'id' => $recipe->getId()]);
        }

        return $this->render('admin/recipe/show.html.twig', [
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
    #[Route('/{id}', name: 'edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    #[IsGranted(RecipeVoter::EDIT, subject: 'recipe')]
    //sans importer l'entité
    //public function edit(int $id) {
    //le framework va trouver tout seul le find($id) et trouver l'objet recette
    public function edit(Recipe $recipe, Request $request, EntityManagerInterface $em, UploaderHelper $helper) {
        $fileUrl = $helper->asset($recipe, 'thumbnailFile');

        // créer le formulaire en indiquant le Type à utiliser + les données
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        //vérifie si le formulaire a été soumis, 
        //si oui, modifie l'entité avec ses données (utilise les setters)
        if ($form->isSubmitted() && $form->isValid()) {

            $recipe->setUpdatedAt(new DateTimeImmutable());

           // GERE UPLOAD DE FICHIER SANS VICH UPLOADER
            /** @var UploadedFile $file */
            //$file = $form->get('thumbnailFile')->getData();
            //on crée le nom du fichier en récupérant l'extesion du fichier d'origine
            // $filename = $recipe->getId() . '.' . $file->getClientOriginalExtension();
            //un objet de type UploadedFile dispose de cette méthode
            //on déplace le 2e élément (fichier) dans le 1e élément (dossier)
            // $file->move($this->getParameter('kernel.project_dir') . '/public/recettes/images', $filename);
            //on enregistre le nom du fichier pour la bdd
            // $recipe->setThumbnail($filename);

/*             //récupérer nom et extension du fichier
            $file->getClientOriginalName();
            $file->getClientOriginalExtension();
 */
            $em->flush();
            $this->addFlash('success', "La recette a bien été modifiée");

            return $this->redirectToRoute('admin.recipe.index');
        }

        return $this->render('admin/recipe/edit.html.twig', [
            'recipe' => $recipe,
            'form' => $form
        ]);
    }


    // -------------- DELETE --------------
    //Requirement = énumérateur avec les req d'url courants; DIGITS = nombres
    #[Route('/{id}', name: 'delete', methods: ['DELETE'], requirements: ['id' => Requirement::DIGITS])]
    #[IsGranted(RecipeVoter::EDIT, subject: 'recipe')]
    public function remove(Recipe $recipe, EntityManagerInterface $em)
    {
        $em->remove($recipe);
        $em->flush();
        $this->addFlash('success', 'La recette a bien été supprimée');

        return $this->redirectToRoute('admin.recipe.index');
    }

}
