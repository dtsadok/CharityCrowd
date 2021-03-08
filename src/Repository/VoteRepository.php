<?php

namespace App\Repository;

use App\Entity\Vote;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Vote|null find($id, $lockMode = null, $lockVersion = null)
 * @method Vote|null findOneBy(array $criteria, array $orderBy = null)
 * @method Vote[]    findAll()
 * @method Vote[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vote::class);
    }

    public function findAllForMemberForMonth($member, $month, $year)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            "SELECT vote
             FROM App\Entity\Vote vote
             WHERE vote.member = :member
             AND MONTH(vote.createdAt) = :month
             AND YEAR(vote.createdAt) = :year
             ORDER BY vote.id ASC"
        );

        return $query
            ->setParameter('member', $member)
            ->setParameter('month', $month)
            ->setParameter('year', $year)
            ->getResult();

    }

     ///**
    //* @return Vote[] Returns an array of Vote objects
    //*/
    public function countYesVotesByNomination($nomination)
    {
        return $this->createQueryBuilder('v')
            ->select('COUNT(v.value) as count')
            ->andWhere('v.value = \'Y\'')
            ->andWhere('v.nomination = :nom')
            ->setParameter('nom', $nomination)
            ->getQuery()
            ->getScalarResult()
        ;
    }

   ///**
    //* @return Vote[] Returns an array of Vote objects
    //*/
    public function countNoVotesByNomination($nomination)
    {
        return $this->createQueryBuilder('v')
            ->select('COUNT(v.value) as count')
            ->andWhere('v.value = \'N\'')
            ->andWhere('v.nomination = :nom')
            ->setParameter('nom', $nomination)
            ->getQuery()
            ->getScalarResult()
        ;
    }

    /*
    public function findOneBySomeField($value): ?Vote
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
