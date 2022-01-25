<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\Ville;
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
        $villes = [["Nantes", "44000"], ["Rennes", "35000"], ["Niort", "79000"]];
        for ($count = 0; $count < count($villes); $count++) {
            $ville = new Ville();
            $ville->setNom($villes[$count][0]);
            $ville->setCodePostal($villes[$count][1]);
            $manager->persist($ville);
        }

        $lieu = new Lieu();
        $lieu->setNom("lieu1");
        $lieu->setRue("Rue1");
        $lieu->setLatitude(48.862725);
        $lieu->setLongitude(2.287592);
        $lieu->setVille($ville);
        $manager->persist($lieu);

        $etats = ["Créée", "Ouverte", "Clôturée", "Activité en cours", "passée", "Annulée"];
        $etatEntities = [];
        for ($count = 0; $count < count($etats); $count++) {
            $etat = new Etat();
            $etat->setLibelle($etats[$count]);
            $manager->persist($etat);
            $etatEntities[$count] = $etat;
        }

        $campus = ["SAINT-HERBLAIN", "CHARTRES DE BRETAGNE", "LA ROCHE SUR YON"];
        $campusEntities = [];

        for ($count = 0; $count < count($campus); $count++) {
            $c = new Campus();
            $c->setNom($campus[$count]);
            $manager->persist($c);
            $campusEntities[$count] = $c;
        }

        $plainPassword = "root123456";

        $participantsEntities = [];
        for ($count = 0; $count < 20; $count++) {
            $participant = new Participant();
            $participant->setNom("Nom " . $count);
            $participant->setPrenom("Prenom" . $count);
            $participant->setPseudo("pseudo" . $count);
            $participant->setTelephone("2123456789");
            $participant->setEmail("mail" . $count . "@eni.fr");

            $participant->setMotPasse($this->userPasswordHasher->hashPassword($participant, $plainPassword));

            $participant->setAdministrateur(false);
            $participant->setActif(true);
            $participant->setCampus($campusEntities[random_int(0, 2)]);
            $manager->persist($participant);
            $participantsEntities[$count] = $participant;
        }

        // sorties en cours
        for ($count = 0; $count < 10; $count++) {
            $sortie = new Sortie();
            $sortie->setNom("sortie" . $count);

            $organisateur = $participantsEntities[$count];
            $sortie->setOrganisateur($organisateur);
            $sortie->setCampus($organisateur->getCampus());

            $sortie->setDuree(120);
            $sortie->setInfosSortie("Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod 
            tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation");

            $sortie->setLieu($lieu);
            $sortie->setEtat($etatEntities[1]);

            $sortie->setDateHeureDebut(new \DateTime());
            $sortie->setDateLimiteInscription(new \DateTime("+".$count." day"));

            $sortie->setNbInscriptionsMax(10);

            $manager->persist($sortie);
        }

        $manager->flush();
    }
}
