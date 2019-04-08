<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Employe;
use App\Entity\Projet;
use App\Entity\TempsProductionEmployeProjet;

class DashboardController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    private $employeRepository;
    private $projetRepository;
    private $tempsDeProductionRepository;


    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->employeRepository = $this->em->getRepository(Employe::class);
        $this->projetRepository = $this->em->getRepository(Projet::class);
        $this->tempsDeProductionRepository = $this->em->getRepository(TempsProductionEmployeProjet::class);
    }

    /**
     * @Route("/", name="dashboard")
     */
    public function index()
    {
        // Projets en cours et projets livrés (+ taux de livraison)

        $nb_total_projets = count($this->projetRepository->findAll());
        $nb_current_projects = count($this->projetRepository->findBy(['estLivre' => 0]));
        $nb_delivered_projects = $nb_total_projets - $nb_current_projects;
        $pourcentage_delivered = ($nb_delivered_projects / $nb_total_projets)* 100;


        // Rentabilité

        $nb_capex = count($this->projetRepository->findBy(['type' => "Capex"]));
        $nb_opex = count($this->projetRepository->findBy(['type' => "Opex"]));
        $pourcentage_capex = ($nb_capex / $nb_total_projets)* 100;

        // Top Employé

        $employes = $this->employeRepository->findAll();

        $max = 0;
        $top_employe = null;

        foreach ($employes as $employe){
            $duree = $this->tempsDeProductionRepository->findTopEmploye($employe->getId()); // @Todo : Optimiser la requête
            $cout = $duree[0]['somme'] * $employe->getCoutJournalier();
            if($max < $cout){
                $max = $cout;
                $top_employe = $employe;
            }
        }

        $top = [
            'coutTotal' => $max,
            'employe' => $top_employe
        ];

        // Jours de production

        $production_days = 0;
        $historique = $this->tempsDeProductionRepository->findAll();

        for($i = 0; $i < sizeof($historique); $i++){
            $production_days += $historique[$i]->getDuree();
        }

        // 5  derniers projets

        $five_last_projects = $this->projetRepository->findBy([],['date' => 'DESC'],5);
        $five_projects = [];

        foreach($five_last_projects as $projet){
            $cout = $this->tempsDeProductionRepository->findCoutTotalProjet($projet->getId());
            $coutTotal = $cout[0]['coutTotal'] == null ? 0 : $cout[0]['coutTotal'];
            $currentProject = [$projet, $coutTotal];
            array_push($five_projects, $currentProject);
        }

        // 10 derniers temps de production

        $ten_last_time = $this->tempsDeProductionRepository->findBy([],['dateSaisie' => 'DESC'],10);

        $chest = [
            'title' => 'Dashboard',
            'active' => ["dashboard" => "active", "projets" => "", "employes" => "", "metiers" => "" ]
        ];

        return $this->render('dashboard/index.html.twig', [
            'nb_employes' => count($employes),
            'nb_current_projects' => $nb_current_projects,
            'nb_delivered_projects' => $nb_delivered_projects,
            'pourcentage_delivered' => $pourcentage_delivered,
            'nb_production_days' => $production_days,
            'nb_capex' => $nb_capex,
            'nb_opex' => $nb_opex,
            'pourcentage_capex' => $pourcentage_capex,
            'five_projects' => $five_projects,
            'top' => $top,
            'ten_time' => $ten_last_time,
            'chest' => $chest
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
