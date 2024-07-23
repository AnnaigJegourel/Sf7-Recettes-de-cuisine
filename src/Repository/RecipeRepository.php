<?php

namespace App\Repository;

use App\Entity\Recipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Gedmo\Translatable\Query\TreeWalker\TranslationWalker;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;


/**
 * @extends ServiceEntityRepository<Recipe>
 */
class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginator)
    {
        parent::__construct($registry, Recipe::class);
    }


    //avec Knp paginator
    public function paginateRecipes(int $page, ?int $userId): PaginationInterface
    {
        $builder = $this->createQueryBuilder('r')->leftJoin('r.category', 'c')->select('r', 'c');
        if($userId) {
            $builder = $builder->andWhere('r.author = :user')
                ->setParameter('user', $userId);
        }
        //fonction prédéfinie dans le paginator knp
        return $this->paginator->paginate(
            $builder->getQuery()->setHint(
                Query::HINT_CUSTOM_OUTPUT_WALKER,
                TranslationWalker::class
            ),
            $page,
            20,
            //options pour sécuriser le tri par propriété
            [
                'distinct' => true,
                'sortFieldAllowList' => ['r.id', 'r.title', 'c.name']
            ]
        );
    }


    public function findTotalDuration()
    {
        return $this->createQueryBuilder('r')
            ->select('SUM(r.duration) as total')
            ->getQuery()
            ->getSingleResult();
    }

    /**
     * @return Recipe[]
     */
    public function findWithDurationLowerThan(int $duration) : array
    {
        // 'r' est un alias, comme en SQL
        return $this->createQueryBuilder('r')
            //récupérer les infos concernant les recettes, mais aussi les catégories
            ->select("r", "c")
            ->where('r.duration < :duration')
            ->orderBy('r.duration', 'ASC')
            //faire la liaison pour récupérer les catégories
            ->leftJoin("r.category", "c")
            //sélectionner seulement les plats principaux
            //->andWhere("c.slug = 'plat-principal'")
            // prendre un seul résultat
            ->setMaxResults(10)
            ->setParameter('duration', $duration)
            //générer l'objet Query
            ->getQuery()
            ->getResult();
    }
}
