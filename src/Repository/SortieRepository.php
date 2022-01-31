<?php

namespace App\Repository;

use App\Classes\FiltresSorties;
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

    public function findByData(FiltresSorties $filtre, $user)
    {
        $queryBuilder = $this->createQueryBuilder('s');

        $queryBuilder->leftJoin('s.campus', 'camp')
            ->addSelect('camp');
        $queryBuilder->leftJoin('s.lieu', 'addr')
            ->addSelect('addr');
        $queryBuilder->leftJoin('s.etat', 'state')
            ->addSelect('state');
        $queryBuilder->leftJoin('s.organisateur', 'orga')
            ->addSelect('orga');
        $queryBuilder->leftJoin('s.participants', 'parti')
            ->addSelect('parti');


        $queryBuilder->andWhere('s.nom like :val');
        $queryBuilder->andWhere('s.campus = :val2');
        $queryBuilder->andWhere($queryBuilder->expr()->between('s.dateHeureDebut', ':date_from', ':date_to'));
        $queryBuilder->setParameter('date_from', $filtre->getFrom(), 'datetime');
        $queryBuilder->setParameter('date_to', $filtre->getTo(), 'datetime');
        $queryBuilder->setParameter('val', '%' . $filtre->getSearch() . '%');
        $queryBuilder->setParameter('val2', $filtre->getCampus());

        $choices = $filtre->getChoice();

        if (in_array("organisateur", $choices)) {
            $queryBuilder->andWhere('s.organisateur = :val3');
            $queryBuilder->setParameter('val3', $user);
        }
        if (in_array("inscrit", $choices)) {
            $queryBuilder->andWhere(':val4 MEMBER OF s.participants ');
            $queryBuilder->setParameter('val4', $user);
        }
        if (in_array("/inscrit", $choices)) {
            $queryBuilder->andWhere(':val5 NOT MEMBER OF s.participants ');
            $queryBuilder->setParameter('val5', $user);
        }

        if (in_array("past", $choices)) {
            $queryBuilder->andWhere('state.libelle = :val6');
        } else {
            $queryBuilder->andWhere('state.libelle != :val6');
        }
        $queryBuilder->setParameter('val6', "passée");

        $query = $queryBuilder->getQuery();

        $query->setMaxResults(50);

        return $query->getResult();
    }

    public function findAllCurrentSorties()
    {
        $queryBuilder = $this->createQueryBuilder('s');

        $queryBuilder->leftJoin('s.campus', 'camp')
            ->addSelect('camp');
        $queryBuilder->leftJoin('s.lieu', 'addr')
            ->addSelect('addr');
        $queryBuilder->leftJoin('s.etat', 'state')
            ->addSelect('state');
        $queryBuilder->leftJoin('s.organisateur', 'orga')
            ->addSelect('orga');
        $queryBuilder->leftJoin('s.participants', 'parti')
            ->addSelect('parti');


        $queryBuilder->andWhere('state.libelle != :val');
        $queryBuilder->setParameter('val', "passée");
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
