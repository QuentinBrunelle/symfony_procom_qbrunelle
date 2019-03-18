<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Employe;
use App\Entity\Projet;
use App\Entity\Metier;
use App\Entity\TempsProductionEmployeProjet;
use App\Form\AddProductionTimeType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

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
    private $tempsDeProductionRepository;


    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->employeRepository = $this->em->getRepository(Employe::class);
        $this->projetRepository = $this->em->getRepository(Projet::class);
        $this->metierRepository = $this->em->getRepository(Metier::class);
        $this->tempsDeProductionRepository = $this->em->getRepository(TempsProductionEmployeProjet::class);
    }

    /**
     * @Route("/projet/{id}", name="projet", requirements={"id" = "\d+"})
     */
    public function detailsProjet(Request $request, int $id)
    {
        $projet = $this->projetRepository->find($id);
        $employes = $this->employeRepository->findBy(['archivage' => 0]);

        $historique = $this->tempsDeProductionRepository->findBy(['projet' => $id]);

        $chest = [
            'title' => $projet->getIntitule(),
            'icon' => 'laptop',
            'active' => ["dashboard" => "", "projets" => "active", "employes" => "", "metiers" => "" ]
        ];

        /*
         *  FORMULAIRE
         */

        $newTime = new TempsProductionEmployeProjet();
        $newTime->setProjet($projet);

        /**
         * On récupère la concaténation "NOM Prénom" afin de l'afficher dans le select (les clés seulement étant affichées)
         * On injecte ensuite un employé en tant que valeu (car la valeur attendue en retour du formulaire est un employé)
         */

        for($i = 0; $i < sizeof($employes); $i++){
            $label = strtoupper($employes[$i]->getNom()).' '.$employes[$i]->getPrenom();
            $liste[$label] = $employes[$i];
        }

        $entity_form = 'employe'; // Choix de l'entité pour laquelle le formulaire va récupérer l'id

        // Création du formulaire ; Array_combine permet de faire en sorte que les clés soit "NOM Prénom" étant donné
        // que ce sont les clés qui sont affichées à l'utilisateur

        $form = $this->createForm(AddProductionTimeType::class, $newTime)->add($entity_form, ChoiceType::class,[
            'label' => 'Employés',
            'choices' => $liste
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $newTime->setCoutTotal($newTime->getEmploye()->getCoutJournalier() * $newTime->getDuree());

            $this->em->persist($newTime);
            $this->em->flush();

            return $this->redirectToRoute('details_projet',['id' => $id]);
        }

        return $this->render('dashboard/detail.html.twig', [
            'type_detail' => 'projet',
            'entity' => $projet,
            'items' => $employes,
            'historiqueProduction' => $historique,
            'erreur_btn' => false,
            'form' => $form->createView(),
            'entity_form' => $entity_form,
            'chest' => $chest
        ]);
    }

    /**
     * @Route("/employe/{id}/{erreur}", name="employe", requirements={"id" = "\d+"})
     */
    public function detailsEmploye(int $id, bool $erreur = false, Request $request)
    {

        $employe = $this->employeRepository->find($id);
        $projets = $this->projetRepository->findAll();

        $historique = $this->tempsDeProductionRepository->findBy(['employe' => $id]);

        $active = ["dashboard" => "", "projets" => "", "employes" => "active", "metiers" => "" ];

        $chest = [
            'title' => $employe->getPrenom().' '.strtoupper($employe->getNom()),
            'icon' => 'users',
            'active' => ["dashboard" => "", "projets" => "", "employes" => "active", "metiers" => "" ]
        ];

        // Formulaire

        $newTime = new TempsProductionEmployeProjet();
        $newTime->setEmploye($employe);

            // Choix de l'entité pour laquelle le formulaire va récupérer l'id

        $entity_form = 'projet';

            // Création du formulaire

        for($i = 0; $i < sizeof($projets); $i++){
            $label = strtoupper($projets[$i]->getIntitule());
            $liste[$label] = $projets[$i];
        }

        $form = $this->createForm(AddProductionTimeType::class, $newTime)->add($entity_form, ChoiceType::class,[
            'label' => 'Projets',
            'choices' => $liste
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $newTime->setCoutTotal($newTime->getEmploye()->getCoutJournalier() * $newTime->getDuree());

            $this->em->persist($newTime);
            $this->em->flush();

            return $this->redirectToRoute('details_employe',['id' => $id]);
        }

        return $this->render('dashboard/detail.html.twig', [
            'type_detail' => 'employe',
            'entity' => $employe,
            'items' => $projets,
            'historiqueProduction' => $historique,
            'erreur_btn' => $erreur,
            'active' => $active,
            'form' => $form->createView(),
            'entity_form' => $entity_form,
            'chest' => $chest
        ]);
    }
}
