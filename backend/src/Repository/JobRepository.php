<?php

namespace App\Repository;

use App\Entity\Job;
use App\Entity\Location;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Job>
 */
class JobRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Job::class);
    }

    /**
     * Find all available jobs, optionally filtered by location
     */
    public function findAvailable(?Location $location = null): array
    {
        $qb = $this->createQueryBuilder('j')
            ->leftJoin('j.location', 'l')
            ->addSelect('l')
            ->where('j.status = :status')
            ->setParameter('status', Job::STATUS_AVAILABLE);

        if ($location) {
            $qb->andWhere('j.location = :location')
            ->setParameter('location', $location);
        }

        return $qb->orderBy('j.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find jobs assigned to a specific inspector
     */
    public function findAssignedToInspector(User $inspector): array
    {
        return $this->createQueryBuilder('j')
            ->where('j.assignedTo = :inspector')
            ->andWhere('j.status IN (:statuses)')
            ->setParameter('inspector', $inspector)
            ->setParameter('statuses', [Job::STATUS_ASSIGNED, Job::STATUS_COMPLETED])
            ->orderBy('j.scheduledDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find pending jobs for an inspector (assigned but not completed)
     */
    public function findPendingForInspector(User $inspector): array
    {
        return $this->createQueryBuilder('j')
            ->where('j.assignedTo = :inspector')
            ->andWhere('j.status = :status')
            ->setParameter('inspector', $inspector)
            ->setParameter('status', Job::STATUS_ASSIGNED)
            ->orderBy('j.scheduledDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find completed jobs for an inspector
     */
    public function findCompletedByInspector(User $inspector): array
    {
        return $this->createQueryBuilder('j')
            ->where('j.assignedTo = :inspector')
            ->andWhere('j.status = :status')
            ->setParameter('inspector', $inspector)
            ->setParameter('status', Job::STATUS_COMPLETED)
            ->orderBy('j.completedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
