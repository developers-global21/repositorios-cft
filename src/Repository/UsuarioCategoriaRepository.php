<?php

namespace App\Repository;

use App\Entity\UsuarioCategoria;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UsuarioCategoria>
 *
 * @method UsuarioCategoria|null find($id, $lockMode = null, $lockVersion = null)
 * @method UsuarioCategoria|null findOneBy(array $criteria, array $orderBy = null)
 * @method UsuarioCategoria[]    findAll()
 * @method UsuarioCategoria[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsuarioCategoriaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UsuarioCategoria::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(UsuarioCategoria $entity, bool $flush = false): void
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
    public function remove(UsuarioCategoria $entity, bool $flush = false): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    //    /**
    //     * @return UsuarioCategoria[] Returns an array of UsuarioCategoria objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?UsuarioCategoria
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function findAllUsuarioCategoria(): array
    {
        /*
        usuarios_categoria   
            id	int(11) AI PK
            user_id	int(11)
            categoria_id	int(11)       
            
        Usuario
            id	int(11) AI PK
            email	varchar(180)
            roles	longtext
            password	varchar(255)
            nombre	varchar(255)
            apellido	varchar(255)
            estado	int(11)
            nivel	int(11)            

        Categoria
        	id	int(11) AI PK
	n       ombre	varchar(255)
            directorio	varchar(255)
    */
        $conn = $this->getEntityManager()->getConnection();
        $sql =  'SELECT 
        uc.id,
        u.nombre,
        u.apellido,
        c.nombre as nombre_categoria 
        from user u                    
        inner join usuario_categoria uc 
        on (u.id = uc.user_id)
        inner join categoria c
        on (uc.categoria_id = c.id)';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    public function findUsuarioCategoria(int $userId): array
    {
        /*
        usuarios_categoria   
            id	int(11) AI PK
            user_id	int(11)
            categoria_id	int(11)       
            
        Usuario
            id	int(11) AI PK
            email	varchar(180)
            roles	longtext
            password	varchar(255)
            nombre	varchar(255)
            apellido	varchar(255)
            estado	int(11)
            nivel	int(11)            

        Categoria
        	id	int(11) AI PK
	n       ombre	varchar(255)
            directorio	varchar(255)
    */
        $conn = $this->getEntityManager()->getConnection();
        $sql =  'SELECT 
        uc.id,
        u.nombre,
        u.apellido,
        c.id as categoria_id,
        c.nombre as nombre_categoria 
        from user u                    
        inner join usuario_categoria uc 
        on (u.id = uc.user_id)
        inner join categoria c
        on (uc.categoria_id = c.id)
        where u.id=:userId';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['userId' => $userId]);
        return $resultSet->fetchAllAssociative();
    }
}
