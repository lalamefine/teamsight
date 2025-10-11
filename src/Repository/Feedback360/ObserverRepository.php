<?php

namespace App\Repository\Feedback360;

use App\Entity\Feedback360\Observation360;
use App\Entity\Feedback360\Observer;
use App\Entity\WebUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Observer>
 */
class ObserverRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Observer::class);
    }

    public function countsByObsForCampaign(int $campaignId): array
    {
        $raw = $this->createQueryBuilder('o')
            ->select(['obs.id', 'COUNT(o.id) AS count'])
            ->innerJoin('o.observation', 'obs')
            ->andWhere('obs.campaign = :campaignId')
            ->setParameter('campaignId', $campaignId)
            ->groupBy('obs.id')
            ->getQuery()
            ->getArrayResult();
        $counts = [];
        foreach ($raw as $row) {
            $counts[$row['id']] = $row['count'];
        }
        return $counts;
    }

//    /**
//     * @return Observer[] Returns an array of Observer objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('o.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Observer
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
