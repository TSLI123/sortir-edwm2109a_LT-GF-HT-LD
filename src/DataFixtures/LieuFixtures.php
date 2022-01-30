<?php

namespace App\DataFixtures;

use App\Entity\Lieu;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LieuFixtures extends Fixture implements DependentFixtureInterface
{
    public const LIEU_REFERENCE = 'lieu_';

    public function load(ObjectManager $manager): void
    {
        $lieux = [['bar', 'rue des arches', 23.8789, 25.32323],
            ['jardin', 'rue des fleurs', 18.8789, 25.32323],
            ['musée de arts', 'rue des lilas', 19.8789, 25.32323],
            ['pub australien', 'impasse du raisin', 20.8789, 25.32323],
            ['square', 'rue des pierres', 21.8789, 25.32323],
            ['bowling', 'rue des balles', 22.8789, 25.32323],
            ['parc random', 'allée des tilleus', 2.8789, 25.989323],
            ['bistro', 'rue des mimosas', 13.12712, 2.32323]
        ];
        for ($count = 0; $count < count($lieux); $count++) {
            $lieu = new Lieu();
            $lieu->setNom($lieux[$count][0]);
            $lieu->setRue($lieux[$count][1]);
            $lieu->setLatitude($lieux[$count][2]);
            $lieu->setLongitude($lieux[$count][3]);
            $ville = $this->getReference(VilleFixtures::VILLE_REFERENCE . rand(0, 2));
            $lieu->setVille($ville);
            $manager->persist($lieu);
            $this->addReference(self::LIEU_REFERENCE.$count, $lieu);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            VilleFixtures::class,
        );
    }
}
