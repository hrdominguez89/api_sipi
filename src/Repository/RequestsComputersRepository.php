<?php

namespace App\Repository;

use App\Entity\RequestsComputers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RequestsComputers>
 *
 * @method RequestsComputers|null find($id, $lockMode = null, $lockVersion = null)
 * @method RequestsComputers|null findOneBy(array $criteria, array $orderBy = null)
 * @method RequestsComputers[]    findAll()
 * @method RequestsComputers[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RequestsComputersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RequestsComputers::class);
    }

    public function add(RequestsComputers $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(RequestsComputers $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

   /**
    * @return RequestsComputers[] Returns an array of RequestsComputers objects
    */
   public function findNotAvailable(): array
   {
       return $this->createQueryBuilder('r')
           ->where('r.returnetAt is null')
           ->orderBy('r.id', 'ASC')
           ->getQuery()
           ->getResult()
       ;
   }

//    public function findOneBySomeField($value): ?RequestsComputers
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
