<?php

namespace App\Repository;

use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    public function findByData($data, $user = null)
    {
        $queryBuilder = $this->createQueryBuilder('s');
        $queryBuilder->andWhere('s.nom like :val');
        $queryBuilder->andWhere('s.campus = :val2');
        $queryBuilder->andWhere($queryBuilder->expr()->between('s.dateHeureDebut', ':date_from', ':date_to'));
        $queryBuilder->setParameter('date_from', $data['from'], 'datetime');
        $queryBuilder->setParameter('date_to', $data['to'], 'datetime');
        $queryBuilder->setParameter('val', '%' . $data['search'] . '%');
        $queryBuilder->setParameter('val2', $data['campus']);

        $choices = $data['choix'];

        if (in_array("organisateur", $choices)) {
            $queryBuilder->andWhere('s.organisateur = :val3');
            $queryBuilder->setParameter('val3', $user);
        }
        if (in_array("inscrit", $choices)) {
            //to do : verifier si l'on est inscrit
            //$queryBuilder->andWhere('s.organisateur like :val3');
            //$queryBuilder->setParameter('val3', $user);
        }
        if (in_array("/inscrit", $choices)) {
            //to do : verifier si l'on est inscrit
            //$queryBuilder->andWhere('s.organisateur like :val3');
            //$queryBuilder->setParameter('val3', $user);
        }

        $queryBuilder->leftJoin('s.etat', 'e')
            ->addSelect('e');
        if (in_array("past", $choices)) {
            $queryBuilder->andWhere('e.libelle = :val6');
        } else {
            $queryBuilder->andWhere('e.libelle != :val6');
        }
        $queryBuilder->setParameter('val6', "passée");




        $query = $queryBuilder->getQuery();

        $query->setMaxResults(50);

        return $query->getResult();
    }

    public function findAllCurrentSorties()
    {
        $queryBuilder = $this->createQueryBuilder('s');
        $queryBuilder->leftJoin('s.etat', 'e')
            ->addSelect('e');
        $queryBuilder->andWhere('e.libelle != :val6');
        $queryBuilder->setParameter('val6', "passée");
        $query = $queryBuilder->getQuery();

        $query->setMaxResults(50);

        return $query->getResult();
    }
    // /**
    //  * @return Sortie[] Returns an array of Sortie objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Sortie
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
