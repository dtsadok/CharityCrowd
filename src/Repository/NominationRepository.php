<?php

namespace App\Repository;

use App\Entity\Nomination;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Nomination|null find($id, $lockMode = null, $lockVersion = null)
 * @method Nomination|null findOneBy(array $criteria, array $orderBy = null)
 * @method Nomination[]    findAll()
 * @method Nomination[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NominationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Nomination::class);
    }

    public function findAllForMonth($month, $year)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            "SELECT nom
             FROM App\Entity\Nomination nom
             WHERE MONTH(nom.createdAt) = :month
             AND YEAR(nom.createdAt) = :year
             ORDER BY nom.id ASC"
        );

        return $query
            ->setParameter('month', $month)
            ->setParameter('year', $year)
            ->getResult();

    }

    public function findAllForMonthWithMemberVotes($month, $year, $member)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            "SELECT nom, v.value AS vote
             FROM App\Entity\Nomination nom
             LEFT JOIN nom.votes v
             WITH v.member = :member
             WHERE MONTH(nom.createdAt) = :month
             AND YEAR(nom.createdAt) = :year
             GROUP BY nom.id, v.value
             ORDER BY nom.id ASC"
        );

        return $query
            ->setParameter('member', $member)
            ->setParameter('month', $month)
            ->setParameter('year', $year)
            ->getResult();
    }

    // /**
    //  * @return Nomination[] Returns an array of Nomination objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Nomination
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
