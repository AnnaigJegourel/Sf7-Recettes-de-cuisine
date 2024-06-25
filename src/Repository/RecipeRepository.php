<?php

namespace App\Repository;

use App\Entity\Recipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

/**
 * @extends ServiceEntityRepository<Recipe>
 */
class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recipe::class);
    }


    public function paginateRecipes(int $page, int $limit): Paginator
    {
        return new Paginator($this
            ->createQueryBuilder('r')
            //si page 1, commencer à la recette 0...
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            //transforme objet QueryBuilder en Query
            ->getQuery()
            //passe petites infos pour mieux gérer requêtes SQL
            ->setHint(Paginator::HINT_ENABLE_DISTINCT, false)
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

    //    /**
    //     * @return Recipe[] Returns an array of Recipe objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Recipe
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
