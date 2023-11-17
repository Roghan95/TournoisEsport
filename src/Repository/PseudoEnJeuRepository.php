<?php

namespace App\Repository;

use App\Entity\PseudoEnJeu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PseudoEnJeu>
 *
 * @method PseudoEnJeu|null find($id, $lockMode = null, $lockVersion = null)
 * @method PseudoEnJeu|null findOneBy(array $criteria, array $orderBy = null)
 * @method PseudoEnJeu[]    findAll()
 * @method PseudoEnJeu[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PseudoEnJeuRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PseudoEnJeu::class);
    }

//    /**
//     * @return PseudoEnJeu[] Returns an array of PseudoEnJeu objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?PseudoEnJeu
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
