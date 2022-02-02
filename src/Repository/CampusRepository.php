<?php

namespace App\Repository;

use App\Entity\Campus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\ParameterType;
use Doctrine\Persistence\ManagerRegistry;
use app\controller\SortieController;

/**
 * @method Campus|null find($id, $lockMode = null, $lockVersion = null)
 * @method Campus|null findOneBy(array $criteria, array $orderBy = null)
 * @method Campus[]    findAll()
 * @method Campus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CampusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Campus::class);
    }

    public function findByCampus(Campus $cri){
        $queryBuilder = $this->createQueryBuilder('s');
        $queryBuilder->andWhere('s.nom like :val');
        $queryBuilder->setParameter('val', '%' . $cri->getNom().'%' );
        $query = $queryBuilder->getQuery();

        $query->setMaxResults(50);

        return $query->getResult();



    }


  /*  public function findByCampus($cri)
    {
        return $this->createQueryBuilder('c')

            ->setParameter('c.', $cri)
            ->andWhere('c.nom like :nom')
            ->orderBy('c.nom', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }*/



  /* public function findByCampus($cri): ?Campus
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.nom like :val')
            ->setParameter('val', $cri('val'))
            ->getQuery()
            ->getResult()
        ;
    }*/

}
