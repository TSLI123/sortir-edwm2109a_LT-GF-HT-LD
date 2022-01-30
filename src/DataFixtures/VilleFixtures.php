<?php

namespace App\DataFixtures;

use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class VilleFixtures extends Fixture
{
    public const VILLE_REFERENCE = 'ville_';

    public function load(ObjectManager $manager): void
    {
        $villes = [["Nantes", "44000"], ["Rennes", "35000"], ["Niort", "79000"]];
        for ($count = 0; $count < count($villes); $count++) {
            $ville = new Ville();
            $ville->setNom($villes[$count][0]);
            $ville->setCodePostal($villes[$count][1]);
            $this->addReference(self::VILLE_REFERENCE.$count, $ville);
            $manager->persist($ville);
        }
        $manager->flush();
    }
}
