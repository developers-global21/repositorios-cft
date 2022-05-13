<?php

namespace App\Repository;

use App\Entity\Subproceso;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Subproceso>
 *
 * @method Subproceso|null find($id, $lockMode = null, $lockVersion = null)
 * @method Subproceso|null findOneBy(array $criteria, array $orderBy = null)
 * @method Subproceso[]    findAll()
 * @method Subproceso[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubprocesoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subproceso::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Subproceso $entity, bool $flush = false): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Subproceso $entity, bool $flush = false): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    //    /**
    //     * @return Subproceso[] Returns an array of Subproceso objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Subproceso
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findAllSubprocesosProceso(int $id): array
    {
        /*
    Subproceso
        id	int(11) AI PK
        categoria_id	int(11)
        subcategoria_id	int(11)
        nombre	varchar(255)
        directorio	varchar(255)


    */
        $conn = $this->getEntityManager()->getConnection();
        $sql =  'SELECT 
        p.id,
        p.nombre,
        p.directorio 
        from subproceso p                  
        where p.subcategoria_id = :categoriaId';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['categoriaId' => $id]);
        return $resultSet->fetchAllAssociative();
    }
}
