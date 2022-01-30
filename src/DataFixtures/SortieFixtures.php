<?php

namespace App\DataFixtures;

use App\Entity\Sortie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SortieFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        for ($count = 0; $count < 100; $count++) {
            $sortie = new Sortie();
            $sortie->setNom("sortie" . $count);

            $organisateur = $this->getReference(ParticipantFixtures::PARTICIPANT_REFERENCE.rand(0,29));
            $sortie->setOrganisateur($organisateur);
            $sortie->setCampus($organisateur->getCampus());

            $sortie->setDuree(rand(6,24)*10);
            $sortie->setInfosSortie("Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod 
            tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation");

            $lieu = $this->getReference(LieuFixtures::LIEU_REFERENCE .rand(0,7));
            $sortie->setLieu($lieu);

            $etat = $this->getReference(EtatFixtures::ETAT_REFERENCE .'1'); //Ouverte
            $sortie->setEtat($etat);

            $sortie->setDateLimiteInscription(new \DateTime("+".$count." day"));
            $sortie->setDateHeureDebut(new \DateTime("+".($count+rand(1,7))." day"));

            $sortie->setNbInscriptionsMax(rand(2,12));

            $manager->persist($sortie);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            LieuFixtures::class,
            ParticipantFixtures::class,
            CampusFixtures::class,
            EtatFixtures::class
        );
    }
}
