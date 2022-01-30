<?php

namespace App\DataFixtures;

use App\Entity\Etat;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class EtatFixtures extends Fixture
{

    public const ETAT_REFERENCE = 'etat_';

    public function load(ObjectManager $manager): void
    {
        $etats = ["Créée", "Ouverte", "Clôturée", "Activité en cours", "passée", "Annulée"];

        for ($count = 0; $count < count($etats); $count++) {
            $etat = new Etat();
            $etat->setLibelle($etats[$count]);
            $manager->persist($etat);
            $this->addReference(self::ETAT_REFERENCE.$count, $etat);
        }

        $manager->flush();
    }
}
