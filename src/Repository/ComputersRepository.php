<?php

namespace App\Repository;

use App\Entity\Computers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Constants\Constants;

/**
 * @extends ServiceEntityRepository<Computers>
 *
 * @method Computers|null find($id, $lockMode = null, $lockVersion = null)
 * @method Computers|null findOneBy(array $criteria, array $orderBy = null)
 * @method Computers[]    findAll()
 * @method Computers[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ComputersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Computers::class);
    }

    public function add(Computers $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Computers $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Computers[] Returns an array of Computers objects
     */
    public function getAllComputers(): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.visible = true')
            ->orderBy('c.id', 'ASC')
            ->getQuery()
            ->getResult();
    }


    /**
     * @return Computers[] Returns an array of Computers objects
     */
    public function getComputersByStatus($status): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.visible = true')
            ->andWhere('c.statusComputer = :statusComputer')
            ->setParameter('statusComputer', $status)
            ->orderBy('c.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    //    public function findOneBySomeField($value): ?Computers
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
