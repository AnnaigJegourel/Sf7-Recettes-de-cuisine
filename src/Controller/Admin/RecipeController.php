<?php

namespace App\Controller\Admin;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Message\RecipePDFMessage;
use App\Repository\RecipeRepository;
use App\Security\Voter\RecipeVoter;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\UX\Turbo\TurboBundle;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;


#[Route('/admin/recettes', name: 'admin.recipe.')]
#[IsGranted('ROLE_USER')]
class RecipeController extends AbstractController
{

    // -------------- CREATE --------------
    #[Route('/create', name: 'create')]
    #[IsGranted(RecipeVoter::CREATE)]
    public function create(Request $request, EntityManagerInterface $em)
    {
        $recipe = new Recipe;
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
    #[IsGranted(RecipeVoter::LIST)]
    public function index(Request $request, RecipeRepository $repository, Security $security): Response
    {
        //récupérer la page courante (valeur 1 par défaut)
        $page = $request->query->getInt('page', 1);
        
        //id utilisateur (ou null si droit de tout lister)
        $userId = $security->getUser()->getId();
        $canListAll = $security->isGranted(RecipeVoter::LIST_ALL);
        $recipes = $repository->paginateRecipes($page, $canListAll ? null : $userId);

        $recipes10 = $repository->findWithDurationLowerThan(10);

        return $this->render('admin/recipe/index.html.twig', [
            'recipes' => $recipes,
            'recipes10' => $recipes10
        ]);

    }


    // -------------- READ ONE --------------

    #[Route('/{slug}-{id}', name: 'show', requirements: ['id' => '\d+', 'slug' => '[a-z0-9-]+'])]
    public function show(string $slug, int $id, RecipeRepository $repository)
    {
        $recipe = $repository->find($id);
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
    }


    // -------------- EDIT --------------
    #[Route('/{id}', name: 'edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    #[IsGranted(RecipeVoter::EDIT, subject: 'recipe')]
    public function edit(Recipe $recipe, Request $request, EntityManagerInterface $em, UploaderHelper $helper, MessageBusInterface $messageBus) {
        $fileUrl = $helper->asset($recipe, 'thumbnailFile');

        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recipe->setUpdatedAt(new DateTimeImmutable());
            $em->flush();
            $messageBus->dispatch(new RecipePDFMessage($recipe->getId()));
            $this->addFlash('success', "La recette a bien été modifiée");

            return $this->redirectToRoute('admin.recipe.index');
        }

        return $this->render('admin/recipe/edit.html.twig', [
            'recipe' => $recipe,
            'form' => $form
        ]);
    }


    // -------------- DELETE --------------
    #[Route('/{id}', name: 'delete', methods: ['DELETE'], requirements: ['id' => Requirement::DIGITS])]
    #[IsGranted(RecipeVoter::EDIT, subject: 'recipe')]
    public function remove(Recipe $recipe, EntityManagerInterface $em, Request $request)
    {
        $recipeId = $recipe->getId();
        $em->remove($recipe);
        $em->flush();
        //est-ce que ça supporte les turbo stream?
        if($request->getPreferredFormat(TurboBundle::STREAM_FORMAT)) {
            $request->setRequestFormat(TurboBundle::STREAM_FORMAT);

            return $this->render('admin/recipe/delete.html.twig', ['recipeId' => $recipeId]);
        }
        $this->addFlash('success', 'La recette a bien été supprimée');

        return $this->redirectToRoute('admin.recipe.index');
    }

}
