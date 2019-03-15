<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Employe;
use App\Entity\Projet;

class AppFixtures extends Fixture
{
    /**
     * @var ObjectManager
     */
    private $manager;

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        $this->loadEmployes();

        $this->loadProjets();
    }

    private function loadEmployes(){

        $noms = ["O'brien", "Davidson", "Denis", "Dupond", "Moore", "Rowland", "Travis", "Henson", "Higgins", "Lucas"];
        $prenoms = ["Quentin", "Thibaut", "Kevin", "Balou", "Manon", "Lara", "Serge", "Amélie", "Marine", "Stéphanie"];
        $coutJournalier = [50.64, 65.89, 74.13, 49.63, 95.65, 84.00, 89.64, 49.68];

        for($i = 0; $i < 25; $i++){

            //$random_date = mt_rand(1262055681,1262055681);
            //$date = date("d/m/Y",$random_date);

            $employe = (new Employe())
                ->setNom($noms[mt_rand(0,9)])
                ->setPrenom($prenoms[mt_rand(0,9)]);

            $employe
                ->setEmail($employe->getPrenom().".".$employe->getNom()."@gmail.com")
                ->setCoutJournalier($coutJournalier[mt_rand(0, 7)])
                ->setDateEmbauche(new \DateTime("now"));

            $this->manager->persist($employe);
        }

        $this->manager->flush();
    }

    private function loadProjets(){

        $type = ['Capex','Opex'];


        for($i = 0; $i < 25; $i++){

            $projet = (new Projet())
                ->setIntitule("Projet n°".$i)
                ->setDescription('Description du projet n°'.$i)
                ->setType($type[mt_rand(0,1)])
                ->setEstLivre($this->randomBool());

            $this->manager->persist($projet);
        }

        $this->manager->flush();
    }

    private function randomBool(){
        return (bool) random_int(0,1);
    }
}
