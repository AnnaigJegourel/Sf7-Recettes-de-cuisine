<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Gedmo\Translatable\Query\TreeWalker\TranslationWalker;
use Gedmo\Translatable\TranslatableListener;
use Symfony\Config\Doctrine\Orm\EntityManagerConfig\DqlConfig;

/**
 * @extends ServiceEntityRepository<Category>
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }


    /**
     * Fetches the number of recipes for each category
     *
     * @return CategoryWithCountDTO
     */
    public function findAllWithCount(): array
    {
        //générer du DQL:
/*         dd(
            $this->getEntityManager()->createQuery( 
                'SELECT c 
                FROM App\Entity\Category c'
            )->getResult()
        );
 */
        return $this->createQueryBuilder('c')                   //crée un QueryBuilder pour l'entité courante
            // renvoie les identifiants des catégories & compte le nombre de recettes associées à chaque catégorie
            //->select('c as category', 'COUNT(c.id) as total') //sans DTO
            //avec DTO : l'objet est construit à la volée à partir des données de la bdd
            ->select('NEW App\DTO\CategoryWithCountDTO(c.id, c.name, COUNT(c.id))')  //mot-clé pour utiliser un DTO
            ->leftJoin('c.recipes', 'r')        //jointure externe avec la relation recipes de l'entité
            ->groupBy('c.id')       //groupe les résultats par identifiant de catégorie, nécessaire pour compter le total de recettes par catégorie
            ->getQuery()
            //astuce pour retrouver la chaine de traduction
            ->setHint(
                Query::HINT_CUSTOM_OUTPUT_WALKER,
                TranslationWalker::class
            )
            //astuce pour changer à la volée la locale pour une query (ici, récupérer les données en anglais)
/*             ->setHint(
                TranslatableListener::HINT_TRANSLATABLE_LOCALE,
                'en'
            )
 */            //astuce pour (dés)activer le fallback : s'il ne trouve pas une clé, il utilise la version en bdd directement
            ->setHint(
                TranslatableListener::HINT_FALLBACK,
                1
            )
            ->getResult();
    }


    //    /**
    //     * @return Category[] Returns an array of Category objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Category
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
