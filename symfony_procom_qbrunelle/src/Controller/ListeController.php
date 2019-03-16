<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Employe;
use App\Entity\Projet;
use App\Entity\Metier;

/**
 * @Route("/liste", name="liste_")
 */
class ListeController extends AbstractController
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
        $this->metierRepository = $this->em->getRepository(Metier::class);
    }

    /**
     * @Route("/projets/{offset}", name="projets")
     */
    public function projets(int $offset)
    {
        $headers = ["Intitulé", "Description", "Type", "Est livré", "Date de création"];

        $projets = $this->projetRepository->findBy([],['date' => 'DESC'], 10, $offset);
        $nb_pages = ceil(count($this->projetRepository->findAll()) / 10) ;
        $current_page = ($offset /10) + 1;

        if($current_page == 1){
            $btns = ['disabled',''];
        }else if($current_page == $nb_pages){
            $btns = ['', 'disabled'];
        }else{
            $btns = ['', ''];
        }

        $active = ["dashboard" => "", "projets" => "active", "employes" => "", "metiers" => "" ];

        return $this->render('dashboard/list.html.twig', [
            'type_liste' => 'projet',
            'items' => $projets,
            'nb_pages' => $nb_pages,
            'current_page' => $current_page,
            'headers' => $headers,
            'active' => $active,
            'icon' => 'laptop',
            'btns' => $btns
        ]);
    }

    /**
     * @Route("/employes/{offset}", name="employes")
     */
    public function employes(int $offset)
    {
        $headers = ["Nom", "Email", "Métier", "Coût journalier", "Date d'embauche"];

        $employes = $this->employeRepository->findBy([], ['dateEmbauche' => 'DESC'], 10, $offset);
        $nb_pages = ceil(count($this->employeRepository->findAll()) / 10) ;
        $current_page = ($offset /10) + 1;

        if($current_page == 1){
            $btns = ['disabled',''];
        }else if($current_page == $nb_pages){
            $btns = ['', 'disabled'];
        }else{
            $btns = ['', ''];
        }

        $active = ["dashboard" => "", "projets" => "", "employes" => "active", "metiers" => "" ];

        return $this->render('dashboard/list.html.twig', [
            'type_liste' => 'employe',
            'items' => $employes,
            'nb_pages' => $nb_pages,
            'current_page' => $current_page,
            'headers' => $headers,
            'active' => $active,
            'icon' => 'users',
            'btns' => $btns
        ]);
    }

    /**
     * @Route("/metiers", name="metiers")
     */
    public function metiers()
    {

        $headers = ["Nom"];

        $metiers = $this->metierRepository->findAll();

        $active = ["dashboard" => "", "projets" => "", "employes" => "", "metiers" => "active" ];

        return $this->render('dashboard/list.html.twig', [
            'type_liste' => "metier",
            'items' => $metiers,
            'nb_pages' => 1,
            'current_page' => 1,
            'headers' => $headers,
            'active' => $active,
            'icon' => 'book',
            'btns' => ['disabled', 'disabled']
        ]);
    }

    /**
     * @Route("/employe/{id}", name="suppression_employe", requirements={"id" = "\d+"})
     */
    public function suppressionEmploye(int $id)
    {
        $employe = $this->employeRepository->find($id);

        $active = ["dashboard" => "", "projets" => "", "employes" => "active", "metiers" => "" ];

        return $this->render('dashboard/form.html.twig', [
            'entity' => $employe,
            'active' => $active
        ]);
    }

    /**
     * @Route("/projet/{id}", name="suppression_projet", requirements={"id" = "\d+"})
     */
    public function suppressionProjet(int $id)
    {
        $projet = $this->projetRepository->find($id);

        $active = ["dashboard" => "", "projets" => "active", "employes" => "", "metiers" => "" ];

        return $this->render('dashboard/form.html.twig', [
            'entity' => $projet,
            'active' => $active
        ]);
    }
}
