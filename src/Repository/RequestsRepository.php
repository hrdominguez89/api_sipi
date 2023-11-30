<?php

namespace App\Repository;

use App\Constants\Constants;
use App\Entity\Requests;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Requests>
 *
 * @method Requests|null find($id, $lockMode = null, $lockVersion = null)
 * @method Requests|null findOneBy(array $criteria, array $orderBy = null)
 * @method Requests[]    findAll()
 * @method Requests[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RequestsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Requests::class);
    }

    public function add(Requests $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Requests $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    /**
     * @return Requests[] Returns an array of Requests objects
     */
    public function findRequestsByUserId($profesor_id)
    {
        return $this->createQueryBuilder('r')
            ->where('r.professor = :val')
            ->setParameter('val', $profesor_id)
            ->orderBy('r.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Requests[] Returns an array of Requests objects
     */
    public function findRequestsPending()
    {
        return $this->createQueryBuilder('r')
            ->where('r.statusRequest = :val')
            ->setParameter('val', Constants::STATUS_REQUEST_PENDING)
            ->orderBy('r.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Requests[] Returns an array of Requests objects
     */
    public function findRequestsAcepted()
    {
        return $this->createQueryBuilder('r')
            ->where('r.statusRequest = :val')
            ->setParameter('val', Constants::STATUS_REQUEST_ACCEPTED)
            ->orderBy('r.requestedDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Requests[] Returns an array of Requests objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Requests
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
