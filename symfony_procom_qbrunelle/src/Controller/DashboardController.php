<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Employe;
use App\Entity\Projet;
//use App\Entity\Metier;

class DashboardController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    private $employeRepository;
    private $projetRepository;
    private $metierRepository;


    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->employeRepository = $this->em->getRepository(Employe::class);
        $this->projetRepository = $this->em->getRepository(Projet::class);
        //$this->metierRepository = $this->em->getRepository(Metier::class);
    }

    /**
     * @Route("/", name="dashboard")
     */
    public function index()
    {
        $projets = $this->projetRepository->findAll();
        $nb_total_projets = count($projets);
        $production_days  = 251; // A modifier

        $nb_capex = count($this->projetRepository->findBy(['type' => "Capex"]));
        $nb_opex = count($this->projetRepository->findBy(['type' => "Opex"]));
        $pourcentage_capex = ($nb_capex / $nb_total_projets)* 100;

        $nb_current_projects = count($this->projetRepository->findBy(['estLivre' => 0]));
        $nb_delivered_projects = count($this->projetRepository->findBy(['estLivre' => 1]));
        $pourcentage_delivered = ($nb_delivered_projects / $nb_total_projets)* 100;


        // 5  derniers projets

        $five_last_projects = $this->projetRepository->findBy([],['date' => 'DESC'],5);

        $active = ["dashboard" => "active", "projets" => "", "employes" => "", "metiers" => "" ];
        return $this->render('dashboard/index.html.twig', [
            'nb_employes' => count($this->employeRepository->findAll()),
            'nb_current_projects' => $nb_current_projects,
            'nb_delivered_projects' => $nb_delivered_projects,
            'pourcentage_delivered' => $pourcentage_delivered,
            'nb_production_days' => $production_days,
            'nb_capex' => $nb_capex,
            'nb_opex' => $nb_opex,
            'pourcentage_capex' => $pourcentage_capex,
            'five_last_projects' => $five_last_projects,
            'active' => $active
        ]);
    }

    /**
     * @Route("/projets", name="projets")
     */
    public function projets()
    {
        $active = ["dashboard" => "", "projets" => "active", "employes" => "", "metiers" => "" ];
        return $this->render('dashboard/list.html.twig', [
            'active' => $active
        ]);
    }

    /**
     * @Route("/employes", name="employes")
     */
    public function employes()
    {
        $active = ["dashboard" => "", "projets" => "", "employes" => "active", "metiers" => "" ];
        return $this->render('dashboard/list.html.twig', [
            'active' => $active
        ]);
    }

    /**
     * @Route("/metiers", name="metiers")
     */
    public function metiers()
    {
        $active = ["dashboard" => "", "projets" => "", "employes" => "", "metiers" => "active" ];
        return $this->render('dashboard/list.html.twig', [
            'active' => $active
        ]);
    }
}
