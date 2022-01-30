<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CampusFixtures extends Fixture
{
    public const CAMPUS_REFERENCE = 'campus_';

    public function load(ObjectManager $manager): void
    {
        $campus = ["SAINT-HERBLAIN", "CHARTRES DE BRETAGNE", "LA ROCHE SUR YON"];

        for ($count = 0; $count < count($campus); $count++) {
            $c = new Campus();
            $c->setNom($campus[$count]);
            $manager->persist($c);
            $this->addReference(self::CAMPUS_REFERENCE.$count, $c);
        }

        $manager->flush();
    }
}
