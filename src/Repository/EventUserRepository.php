<?php

namespace App\Repository;

use App\Entity\EventUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EventUser>
 *
 * @method EventUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method EventUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method EventUser[]    findAll()
 * @method EventUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EventUser::class);
    }

//    /**
//     * @return EventUser[] Returns an array of EventUser objects
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

//    public function findOneBySomeField($value): ?EventUser
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }


/*public function findByRev_Event(){
    $em = $this->getEntityManager();
    return $em->createQuery('
        SELECT r.cin, r.nom_u, r.prenom_u, e.nom AS event_name, e.date, e.lieu, e.description, e.image, e.prix
        FROM App\Entity\Reservation r
        JOIN r.event e WITH r.event = e.id
    ')
    ->getResult();
}*/


}
