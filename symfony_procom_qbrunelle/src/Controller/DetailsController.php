<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Employe;
use App\Entity\Projet;

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
        //$this->metierRepository = $this->em->getRepository(Metier::class);
    }

    /**
     * @Route("/projet/{id}", name="projet", requirements={"id" = "\d+"})
     */
    public function detailsProjet(Request $request, int $id)
    {
        $projet = $this->projetRepository->find($id);

        return $this->render('dashboard/detail.html.twig', [
            'entity' => $projet,
        ]);
    }
}
