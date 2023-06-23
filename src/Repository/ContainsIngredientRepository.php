<?php

namespace App\Repository;

use App\Entity\ContainsIngredient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ContainsIngredient>
 *
 * @method ContainsIngredient|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContainsIngredient|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContainsIngredient[]    findAll()
 * @method ContainsIngredient[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContainsIngredientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContainsIngredient::class);
    }

    public function add(ContainsIngredient $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ContainsIngredient $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

   public function ingredientIsInRecipe($ingredient, $recipe): ?ContainsIngredient
   {
       $em = $this->getEntityManager();

       $query = $em->createQuery('SELECT contains FROM App\Entity\ContainsIngredient contains JOIN contains.recipe recipe WHERE contains.ingredient = :ingredient AND contains.recipe = :recipe');
       $query->setParameter('ingredient', $ingredient);
       $query->setParameter('recipe', $recipe);
       return $query->getOneOrNullResult();
   }

//    /**
//     * @return ContainsIngredient[] Returns an array of ContainsIngredient objects
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

//    public function findOneBySomeField($value): ?ContainsIngredient
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
