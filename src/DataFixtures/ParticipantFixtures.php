<?php

namespace App\DataFixtures;

use App\Entity\Participant;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ParticipantFixtures extends Fixture implements DependentFixtureInterface
{
    private UserPasswordHasherInterface $userPasswordHasher;

    public const PARTICIPANT_REFERENCE = 'participant_';

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {

        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $plainPassword = "root123456";

        for ($count = 0; $count < 30; $count++) {
            $participant = new Participant();
            $participant->setNom("Nom" . $count);
            $participant->setPrenom("Prenom" . $count);
            $participant->setPseudo("pseudo" . $count);
            $participant->setTelephone("0123456789");
            $participant->setEmail("mail" . $count . "@eni.fr");

            $participant->setMotPasse($this->userPasswordHasher->hashPassword($participant, $plainPassword));

            $participant->setAdministrateur(false);
            $participant->setActif(true);

            $campus = $this->getReference(CampusFixtures::CAMPUS_REFERENCE . rand(0, 2));
            $participant->setCampus($campus);
            $manager->persist($participant);
            $this->addReference(self::PARTICIPANT_REFERENCE.$count, $participant);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            CampusFixtures::class,
        );
    }
}
