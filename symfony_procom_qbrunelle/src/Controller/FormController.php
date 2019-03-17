<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Employe;
use App\Entity\Projet;
use App\Entity\Metier;

/**
 * @Route("/formulaire", name="formulaire_")
 */
class FormController extends AbstractController
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
     * @Route("/employe", name="ajout_employe")
     */
    public function ajoutEmploye()
    {

        $employe = '';

        $chest = [
            'active' => ["dashboard" => "", "projets" => "", "employes" => "active", "metiers" => "" ]
        ];

        return $this->render('dashboard/form.html.twig', [
            'entity' => $employe,
            'chest' => $chest
        ]);
    }

    /**
     * @Route("/employe/{id}", name="modification_employe", requirements={"id" = "\d+"})
     */
    public function modificationEmploye(int $id)
    {
        $employe = $this->employeRepository->find($id);

        $active = ["dashboard" => "", "projets" => "", "employes" => "active", "metiers" => "" ];

        return $this->render('dashboard/form.html.twig', [
            'entity' => $employe,
            'active' => $active
        ]);
    }

    /**
     * @Route("/projet", name="ajout_projet")
     */
    public function ajoutProjet()
    {

        $projet = '';

        $chest = [
            'active' => ["dashboard" => "", "projets" => "active", "employes" => "", "metiers" => "" ]
        ];

        return $this->render('dashboard/form.html.twig', [
            'entity' => $projet,
            'chest' => $chest
        ]);
    }

    /**
     * @Route("/projet/{id}", name="modification_projet", requirements={"id" = "\d+"})
     */
    public function modificationProjet(int $id)
    {
        $projet = $this->projetRepository->find($id);

        $active = ["dashboard" => "", "projets" => "active", "employes" => "", "metiers" => "" ];

        return $this->render('dashboard/form.html.twig', [
            'entity' => $projet,
            'active' => $active
        ]);
    }
}
