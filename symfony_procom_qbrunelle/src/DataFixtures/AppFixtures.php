<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Employe;
use App\Entity\Projet;
use App\Entity\Metier;
use App\Entity\TempsProductionEmployeProjet;

class AppFixtures extends Fixture
{
    /**
     * @var ObjectManager
     */
    private $manager;

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        $this->loadEntities();
    }

    private function loadEntities(){

        // Paramètres Employés

        $noms = ["O'brien", "Davidson", "Denis", "Dupond", "Moore", "Rowland", "Travis", "Henson", "Higgins", "Lucas"];
        $prenoms = ["Quentin", "Thibaut", "Kevin", "Balou", "Manon", "Lara", "Serge", "Amélie", "Marine", "Stéphanie"];
        $coutJournalier = [50.64, 65.89, 74.13, 49.63, 95.65, 84.00, 89.64, 49.68];

        $metiers_string = ["Web designer", "Manager SEO", "Manager Marketing", "Chef de projet", "Développeur Front-End", "Intégrateur", "Développeur Back-end"];
        $metiers = [];
        for($i = 0 ; $i < count($metiers_string) ; $i++){
            array_push($metiers,(new Metier())->setNom($metiers_string[$i]));
        }

        // Paramètres Projets

        $type = ['Capex','Opex'];

        // Création des Entités
        for($i = 0; $i < 25; $i++){

            // Création d'un employé
            $employe = (new Employe())
                ->setNom($noms[mt_rand(0,9)])
                ->setPrenom($prenoms[mt_rand(0,9)])
                ->setMetier($metiers[mt_rand(0,6)])
                ->setArchivage($this->randomBool());

            $employe
                ->setEmail(mb_strtolower($employe->getPrenom()).'.'.mb_strtolower($employe->getNom()).'@gmail.com')
                ->setCoutJournalier($coutJournalier[mt_rand(0, 7)])
                ->setDateEmbauche(new \DateTime($this->randomDate()));

            $this->manager->persist($employe);

            // Création d'un projet
            $projet = (new Projet())
                ->setIntitule("Projet n°".$i)
                ->setDescription('Description du projet n°'.$i)
                ->setType($type[mt_rand(0,1)])
                ->setEstLivre($this->randomBool())
                ->setDate(new \DateTime($this->randomDate()));

            $this->manager->persist($projet);

            // Création d'un temps de production
            $tempsDeProduction = (new TempsProductionEmployeProjet())
                ->setDuree(mt_rand(1,16));

            $tempsDeProduction
                ->setCoutTotal($employe->getCoutJournalier() * $tempsDeProduction->getDuree())
                ->setEmploye($employe)
                ->setProjet($projet)
                ->setDateSaisie(new \DateTime($this->randomDate()));

            $this->manager->persist($tempsDeProduction);
        }

        $this->manager->flush();
    }

    // Création d'un booléen aléatoire
    private function randomBool(){
        return (bool) random_int(0,1);
    }

    // Création d'une date aléatoire entre le 01 Janvier 2015 et la date du jour
    private function randomDate(){
        $start = strtotime('01 January 2015');
        $end = time();

        $timestamp = mt_rand($start,$end);

        return date('Y-m-d', $timestamp);
    }
}
