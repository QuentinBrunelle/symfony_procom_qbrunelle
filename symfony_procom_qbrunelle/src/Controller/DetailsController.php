<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Employe;
use App\Entity\Projet;
use App\Entity\Metier;

/**
 * @Route("/details", name="details_")
 */
class DetailsController extends AbstractController
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
     * @Route("/projet/{id}", name="projet", requirements={"id" = "\d+"})
     */
    public function detailsProjet(int $id)
    {
        $projet = $this->projetRepository->find($id);
        $employes = $this->employeRepository->findBy(['archivage' => 0]);

        $active = ["dashboard" => "", "projets" => "active", "employes" => "", "metiers" => "" ];

        return $this->render('dashboard/detail.html.twig', [
            'type_detail' => 'projet',
            'entity' => $projet,
            'items' => $employes,
            'erreur_btn' => false,
            'active' => $active
        ]);
    }

    /**
     * @Route("/employe/{id}/{erreur}", name="employe", requirements={"id" = "\d+"})
     */
    public function detailsEmploye(int $id, bool $erreur = false)
    {

        $employe = $this->employeRepository->find($id);
        $projets = $this->projetRepository->findAll();

        $active = ["dashboard" => "", "projets" => "", "employes" => "active", "metiers" => "" ];

        return $this->render('dashboard/detail.html.twig', [
            'type_detail' => 'employe',
            'entity' => $employe,
            'items' => $projets,
            'erreur_btn' => $erreur,
            'active' => $active,
        ]);
    }


}
