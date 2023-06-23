<?php

namespace App\Repository;

use App\Entity\ContainsIngredient;
use App\Entity\Recipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Recipe>
 *
 * @method Recipe|null find($id, $lockMode = null, $lockVersion = null)
 * @method Recipe|null findOneBy(array $criteria, array $orderBy = null)
 * @method Recipe[]    findAll()
 * @method Recipe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recipe::class);
    }

    public function add(Recipe $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Recipe $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findWhere($title): array
    {
       return $this->createQueryBuilder('r')
           ->andWhere('r.title LIKE :title')
           ->andWhere('r.motherRecipe IS null')
           ->setParameter('title', '%'. $title .'%')
           ->orderBy('r.rating', 'DESC')
           ->getQuery()
           ->getResult()
       ;
    }

    public function findMotherRecipes(): array
    {
       return $this->createQueryBuilder('r')
           ->andWhere('r.motherRecipe IS null')
           ->getQuery()
           ->getResult()
       ;
    }

    public function findTop($category): array
    {
       return $this->createQueryBuilder('r')
           ->innerJoin('r.category', 'c')
           ->andWhere('c.title = :category')
           ->andWhere('r.motherRecipe IS null')
           ->setParameter('category', $category)
           ->orderBy('r.rating', 'DESC')
           ->setMaxResults(5)
           ->getQuery()
           ->getResult()
       ;
    }

    public function findNew(): array
    {
       return $this->createQueryBuilder('r')
           ->andWhere('r.motherRecipe IS null')
           ->orderBy('r.createdAt', 'DESC')
           ->setMaxResults(5)
           ->getQuery()
           ->getResult()
       ;
    }


    public function findNoValidate(): array
    {
       return $this->createQueryBuilder('r')
           ->andWhere('r.isValidate IS null OR r.isValidate = 0')
           ->orderBy('r.createdAt', 'ASC')
            ->getQuery()
           ->getResult()
       ;
    }

    public function findAllergen($allergen): array
    {
       return $this->createQueryBuilder('r')
           ->innerJoin('r.allergen', 'a')
           ->andWhere('a.name != :allergen')
           ->andWhere('r.motherRecipe IS null')
           ->setParameter('allergen', $allergen)
           ->orderBy('r.rating', 'DESC')
           ->getQuery()
           ->getResult()
       ;
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
