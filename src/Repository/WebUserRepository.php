<?php

namespace App\Repository;

use App\Entity\Company;
use App\Entity\WebUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<WebUser>
 */
class WebUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WebUser::class);
    }

    public function search(Company $company, string $search, int $maxResults = 100) : array
    {
        $qb = $this->createQueryBuilder('w')
            ->andWhere('w.company = :company')
            ->setParameter('company', $company)
            ->andWhere('w.email LIKE :search OR UPPER(CONCAT(w.firstName, \' \', w.lastName)) LIKE :search OR UPPER(CONCAT(w.lastName, \' \', w.firstName)) LIKE :search OR UPPER(w.team) LIKE :search')    
            ->setParameter('search', '%' . strtoupper($search) . '%')
            ->orderBy('w.team,w.lastName,w.firstName', 'ASC')
            ->setMaxResults($maxResults);

        return $qb->getQuery()->getResult();
    }

    public function findByIdIn(Company $company, array $ids): array
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.company = :company')
            ->setParameter('company', $company)
            ->andWhere('w.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return WebUser[] Returns an array of WebUser objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('w')
    //            ->andWhere('w.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('w.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?WebUser
    //    {
    //        return $this->createQueryBuilder('w')
    //            ->andWhere('w.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
