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

    /**
     * @param string|null $searchNom
     * @param string|null $searchLieu
     * @return EventUser[]
     */
    public function findBySearchCriteria(?string $searchNom, ?string $searchLieu): array
    {
        $queryBuilder = $this->createQueryBuilder('e');

        // Ajoutez des clauses WHERE conditionnelles en fonction des critères de recherche
        if ($searchNom) {
            $queryBuilder->andWhere('e.nom LIKE :searchNom')
                ->setParameter('searchNom', '%' . $searchNom . '%');
        }

        if ($searchLieu) {
            $queryBuilder->andWhere('e.lieu LIKE :searchLieu')
                ->setParameter('searchLieu', '%' . $searchLieu . '%');
        }

        // Exécutez la requête et retournez les résultats
        return $queryBuilder->getQuery()->getResult();
    }

    public function deleteExpiredEvents()
    {
        $entityManager = $this->getEntityManager();

        // Date actuelle
        $currentDate = new \DateTime();

        // Date seuil (24 heures avant la date actuelle)
        $thresholdDate = clone $currentDate;
        $thresholdDate->sub(new \DateInterval('P1D'));

        // Récupérer les événements expirés
        $expiredEvents = $this->createQueryBuilder('e')
            ->where('CAST(e.date AS datetime) < :thresholdDate')
            ->setParameter('thresholdDate', $thresholdDate)
            ->getQuery()
            ->getResult();

        // Supprimer les événements expirés
        foreach ($expiredEvents as $event) {
            $entityManager->remove($event);
        }

        $entityManager->flush();
    }
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



