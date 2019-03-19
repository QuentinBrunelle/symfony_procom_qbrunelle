<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Employe;
use App\Entity\Projet;
use App\Entity\Metier;
use App\Form\EmployeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;

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
    public function ajoutEmploye(Request $request)
    {

        $employe = new Employe();

        $metiers = $this->metierRepository->findAll();

        for($i = 0; $i < sizeof($metiers); $i++){
            $label = $metiers[$i]->getNom();
            $liste[$label] = $metiers[$i];
        }

        $form = $this->createForm(EmployeType::class, $employe)->add('metier', ChoiceType::class,[
            'label' => 'Métier',
            'choices' => $liste
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $employe->setArchivage(0);
            $this->em->persist($employe);
            $this->em->flush();

            $this->addFlash('success','Un nouvel employé a bien été crée !');

            return $this->redirectToRoute('formulaire_ajout_employe');
        }

        $chest = [
            'active' => ["dashboard" => "", "projets" => "", "employes" => "active", "metiers" => "" ]
        ];

        return $this->render('dashboard/form.html.twig', [
            'type_form' => "employe",
            'entity' => $employe,
            'form' => $form->createView(),
            'chest' => $chest
        ]);
    }

    /**
     * @Route("/employe/{id}", name="modification_employe", requirements={"id" = "\d+"})
     */
    public function modificationEmploye(int $id, Request $request)
    {
        $employe = $this->employeRepository->find($id);

        $metiers = $this->metierRepository->findAll();

        for($i = 0; $i < sizeof($metiers); $i++){
            $label = $metiers[$i]->getNom();
            $liste[$label] = $metiers[$i];
        }

        $form = $this->createForm(EmployeType::class, $employe)->add('metier', ChoiceType::class,[
            'label' => 'Métier',
            'choices' => $liste
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $employe->setArchivage(0);
            $this->em->persist($employe);
            $this->em->flush();

            $this->addFlash('success','Modification réussie !');

            return $this->redirectToRoute('formulaire_ajout_employe');
        }

        $chest = [
            'active' => ["dashboard" => "", "projets" => "", "employes" => "active", "metiers" => "" ]
        ];

        return $this->render('dashboard/form.html.twig', [
            'type_form' => "employe",
            'entity' => $employe,
            'form' => $form->createView(),
            'chest' => $chest
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

    /**
     * @Route("/projet", name="ajout_metier")
     */
    public function ajoutMetier()
    {

        $metier = '';

        $chest = [
            'active' => ["dashboard" => "", "projets" => "", "employes" => "", "metiers" => "active" ]
        ];

        return $this->render('dashboard/form.html.twig', [
            'entity' => $metier,
            'chest' => $chest
        ]);
    }
}
