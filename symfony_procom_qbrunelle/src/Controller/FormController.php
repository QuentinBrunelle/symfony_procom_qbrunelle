<?php

namespace App\Controller;

use App\Form\ProjetType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Employe;
use App\Entity\Projet;
use App\Entity\Metier;
use App\Form\EmployeType;
use App\Form\MetierType;
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
            'active' => ["dashboard" => "", "projets" => "", "employes" => "active", "metiers" => "" ],
            'titre' => "Ajout d'un employé",
            'icon' => 'users',
        ];

        return $this->render('form/form.html.twig', [
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
            'active' => ["dashboard" => "", "projets" => "", "employes" => "active", "metiers" => "" ],
            'titre' => "Edition de ".$employe->getPrenom()." ".$employe->getNom()."",
            'icon' => 'users',
        ];

        return $this->render('form/form.html.twig', [
            'type_form' => "employe",
            'entity' => $employe,
            'form' => $form->createView(),
            'chest' => $chest
        ]);
    }

    /**
     * @Route("/projet", name="ajout_projet")
     */
    public function ajoutProjet(Request $request)
    {

        $projet = new Projet();

        $liste = ['Capex' => 'Capex', 'Opex' => 'Opex'];
        $form = $this->createForm(ProjetType::class, $projet)->add('type', ChoiceType::class,[
            'label' => 'Type',
            'choices' => $liste
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $projet->setEstLivre(false);
            $this->em->persist($projet);
            $this->em->flush();

            $this->addFlash('success','Un nouveau projet a bien été crée !');

            return $this->redirectToRoute('formulaire_ajout_projet');
        }

        $chest = [
            'active' => ["dashboard" => "", "projets" => "active", "employes" => "", "metiers" => "" ],
            'titre' => "Ajout d'un projet",
            'icon' => 'laptop',
        ];

        return $this->render('form/form.html.twig', [
            'type_form' => "projet",
            'entity' => $projet,
            'form' => $form->createView(),
            'chest' => $chest
        ]);
    }

    /**
     * @Route("/projet/{id}", name="modification_projet", requirements={"id" = "\d+"})
     */
    public function modificationProjet(int $id, Request $request)
    {
        $projet = $this->projetRepository->find($id);
        $liste = ['Capex' => 'Capex', 'Opex' => 'Opex'];
        $form = $this->createForm(ProjetType::class, $projet)->add('type', ChoiceType::class,[
            'label' => 'Type',
            'choices' => $liste
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $projet->setEstLivre(false);
            $this->em->persist($projet);
            $this->em->flush();

            $this->addFlash('success','Modification réussie !');

            return $this->redirectToRoute('formulaire_ajout_projet');
        }

        $chest = [
            'active' => ["dashboard" => "", "projets" => "active", "employes" => "", "metiers" => "" ],
            'titre' => "Edition de ".$projet->getIntitule()."",
            'icon' => 'laptop',
        ];

        return $this->render('form/form.html.twig', [
            'type_form' => "projet",
            'entity' => $projet,
            'form' => $form->createView(),
            'chest' => $chest
        ]);
    }

    /**
     * @Route("/metier", name="ajout_metier")
     */
    public function ajoutMetier(Request $request)
    {

        $metier = new Metier();

        $form = $this->createForm(MetierType::class, $metier);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->em->persist($metier);
            $this->em->flush();

            $this->addFlash('success','Un nouveau métier a bien été crée !');

            return $this->redirectToRoute('formulaire_ajout_metier');
        }

        $chest = [
            'active' => ["dashboard" => "", "projets" => "", "employes" => "", "metiers" => "active" ],
            'titre' => "Ajout d'un métier",
            'icon' => 'book',
        ];

        return $this->render('form/form.html.twig', [
            'type_form' => "metier",
            'entity' => $metier,
            'form' => $form->createView(),
            'chest' => $chest
        ]);
    }

    /**
     * @Route("/metier/{id}", name="modification_metier", requirements={"id" = "\d+"})
     */
    public function modificationMetier(int $id, Request $request)
    {
        $metier = $this->metierRepository->find($id);

        $form = $this->createForm(MetierType::class, $metier);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->em->persist($metier);
            $this->em->flush();

            $this->addFlash('success','Modification réussie !');

            return $this->redirectToRoute('formulaire_ajout_metier');
        }

        $chest = [
            'active' => ["dashboard" => "", "projets" => "", "employes" => "", "metiers" => "active" ],
            'titre' => "Edition de ".$metier->getNom(),
            'icon' => 'book',
        ];

        return $this->render('form/form.html.twig', [
            'type_form' => "metier",
            'entity' => $metier,
            'form' => $form->createView(),
            'chest' => $chest
        ]);
    }
}
