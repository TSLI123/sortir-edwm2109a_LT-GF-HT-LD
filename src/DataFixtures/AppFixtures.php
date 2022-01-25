<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Participant;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {

        $this->userPasswordHasher = $userPasswordHasher;
    }


    /**
     * @throws \Exception
     */
    public function load(ObjectManager $manager): void
    {
        $etats = ["Créée", "Ouverte", "Clôturée", "Activité en cours", "passée", "Annulée"];

        for ($count = 0; $count < count($etats); $count++) {
            $etat = new Etat();
            $etat->setLibelle($etats[$count]);
            $manager->persist($etat);
        }

        $campus = ["Nantes", "Rennes", "Niort"];
        $campusEntities= [];

        for ($count = 0; $count < count($campus); $count++) {
            $c = new Campus();
            $c->setNom($campus[$count]);
            $manager->persist($c);
            $campusEntities[$count]=$c;
        }

        $plainPassword = "root123456";

        for ($count = 0; $count < 20; $count++) {
            $participant = new Participant();
            $participant->setNom("Nom " . $count);
            $participant->setPrenom("Prenom" . $count);
            $participant->setPseudo("pseudo".$count);
            $participant->setTelephone("0011223344");
            $participant->setEmail("mail". $count."@eni.fr");

            $participant->setMotPasse($this->userPasswordHasher->hashPassword($participant, $plainPassword));

            $participant->setAdministrateur(false);
            $participant->setActif(true);
            $participant->setCampus($campusEntities[random_int(0, 2)]);
            $manager->persist($participant);
        }

        $manager->flush();
    }
}
