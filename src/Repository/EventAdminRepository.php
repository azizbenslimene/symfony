<?php

namespace App\Repository;

use App\Entity\EventAdmin;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EventAdmin>
 *
 * @method EventAdmin|null find($id, $lockMode = null, $lockVersion = null)
 * @method EventAdmin|null findOneBy(array $criteria, array $orderBy = null)
 * @method EventAdmin[]    findAll()
 * @method EventAdmin[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventAdminRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EventAdmin::class);
    }

//    /**
//     * @return EventAdmin[] Returns an array of EventAdmin objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?EventAdmin
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
