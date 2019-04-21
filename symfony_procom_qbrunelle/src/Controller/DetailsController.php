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
     * @Route("/projet/{id}/{offset}/{erreur}", name="projet", requirements={"id" = "\d+"})
     */
    public function detailsProjet(Request $request, int $id, int $offset, bool $erreur = false)
    {
        $projet = $this->projetRepository->find($id); // On récupère le projet pour lequel on souhaite obtenir des détails

        // historique des temps de production par date décroissante et par tranche de 10
        $historique = $this->tempsDeProductionRepository->findBy(['projet' => $id],['dateSaisie' => 'DESC'], 10, $offset);

        $coutTotalProjet = $this->tempsDeProductionRepository->findCoutTotalProjet($id); // Query : coût total du projet
        $coutTotal = $coutTotalProjet[0]['coutTotal'] == null ? 0 : $coutTotalProjet[0]['coutTotal'];
        $nbEmployes = $this->tempsDeProductionRepository->findEmployesByProject($id); // Query : nombre d'employés affectés au projet

        // Nombre de pages nécessaires pour l'affichage de tous les temps de production par tranche de 10 résultats
        $nb_pages = ceil(count($this->tempsDeProductionRepository->findBy(['projet' => $id])) / 10);
        $nb_pages = $nb_pages < 1 ? 1 : $nb_pages;

        $current_page = ($offset /10) + 1; // Page actuelle où se situe l'utilisateur

        // $btns détermine si les chevrons sont désactivés ou non
        if($current_page == 1){
            $btns = ['disabled',''];
        }else if($current_page == $nb_pages){
            $btns = ['', 'disabled'];
        }else{
            $btns = ['', ''];
        }

        /* FORMULAIRE */

        // Création d'un nouveau temps de production et affectation du projet sur lequel on se trouve
        $newTime = new TempsProductionEmployeProjet();
        $newTime->setProjet($projet);

        // Choix de l'entité pour laquelle le formulaire va récupérer l'id (dans le cas d'un projet, il faut récupérer
        // l'employé). Utilisé dans la création du formulaire dans le template
        $entity_form = 'employe';

        $employes = $this->employeRepository->findBy(['archivage' => 0]); // Liste des employés non archivés

        // On récupère la concaténation "NOM Prénom" afin de l'afficher dans le select (les clés seulement étant affichées)
        // On injecte ensuite un employé en tant que valeur (car la valeur attendue en retour du formulaire est un employé)
        for($i = 0; $i < sizeof($employes); $i++){
            $label = strtoupper($employes[$i]->getNom()).' '.$employes[$i]->getPrenom();
            $liste[$label] = $employes[$i];
        }

        // Création du formulaire et ajout d'un champ de type 'select' utilisant la liste des employés non archivés
        $form = $this->createForm(AddProductionTimeType::class, $newTime)->add($entity_form, ChoiceType::class,[
            'label' => 'Employés',
            'choices' => $liste
        ]);
        $form->handleRequest($request);

        // Validation du formulaire
        if($form->isSubmitted() && $form->isValid()){
            $newTime->setCoutTotal($newTime->getEmploye()->getCoutJournalier() * $newTime->getDuree());

            $this->em->persist($newTime);
            $this->em->flush();

            return $this->redirectToRoute('details_projet',['id' => $id, 'offset' => 0]);
        }

        $chest = [
            'title' => $projet->getIntitule(),
            'icon' => 'laptop',
            'active' => ["dashboard" => "", "projets" => "active", "employes" => "", "metiers" => "" ]
        ];

        return $this->render('details/detail.html.twig', [
            'type_detail' => 'projet',
            'entity' => $projet,
            'coutTotal' => $coutTotal,
            'nbEmployes' => $nbEmployes[0]['employes'],
            'items' => $employes,
            'historiqueProduction' => $historique,
            'erreur_btn' => $erreur,
            'form' => $form->createView(),
            'entity_form' => $entity_form,
            'nb_pages' => $nb_pages,
            'current_page' => $current_page,
            'btns' => $btns,
            'chest' => $chest
        ]);
    }

    /**
     * @Route("/projet/livraison/{id}", name="livraison_projet", requirements={"id" = "\d+"})
     */

    public function livraisonProjet(int $id){

        // Récupération du projet puis set la livraison à true
        $projet = $this->projetRepository->find($id);
        $projet->setEstLivre(1);
        $this->em->persist($projet);
        $this->em->flush();

        return $this->redirectToRoute('details_projet',['id' => $id]);
    }

    /**
     * @Route("/employe/{id}/{offset}/{erreur}", name="employe", requirements={"id" = "\d+"})
     */
    public function detailsEmploye(int $id, int $offset, bool $erreur = false, Request $request)
    {
        // On récupère l'employé pour lequel on souhaite obtenir des détails
        $employe = $this->employeRepository->find($id);

        // historique des temps de production par date décroissante et par tranche de 10
        $historique = $this->tempsDeProductionRepository->findBy(['employe' => $id],['dateSaisie' => 'DESC'], 10, $offset);

        // Nombre de pages nécessaires pour l'affichage de tous les temps de production par tranche de 10 résultats
        $nb_pages = ceil(count($this->tempsDeProductionRepository->findBy(['employe' => $id])) / 10) ;
        $nb_pages = $nb_pages < 1 ? 1 : $nb_pages;

        $current_page = ($offset /10) + 1;// Page actuelle où se situe l'utilisateur

        // $btns détermine si les chevrons sont désactivés ou non
        if($current_page == 1){
            $btns = ['disabled',''];
        }else if($current_page == $nb_pages){
            $btns = ['', 'disabled'];
        }else{
            $btns = ['', ''];
        }

        /* FORMULAIRE */

        // Création d'un nouveau temps de production et affectation de l'employé sur lequel on se trouve
        $newTime = new TempsProductionEmployeProjet();
        $newTime->setEmploye($employe);

        // Choix de l'entité pour laquelle le formulaire va récupérer l'id (dans le cas d'un projet, il faut récupérer
        // l'employé). Utilisé dans la création du formulaire dans le template
        $entity_form = 'projet';

        $projets = $this->projetRepository->findBy(['estLivre' => 0]); // Liste des projets non livrés
        for($i = 0; $i < sizeof($projets); $i++){
            $label = strtoupper($projets[$i]->getIntitule());
            $liste[$label] = $projets[$i];
        }

        // Création du formulaire et ajout d'un champ de type 'select' utilisant la liste des employés non archivés
        $form = $this->createForm(AddProductionTimeType::class, $newTime)->add($entity_form, ChoiceType::class,[
            'label' => 'Projets',
            'choices' => $liste
        ]);
        $form->handleRequest($request);

        // Validation du formulaire
        if($form->isSubmitted() && $form->isValid()){
            $newTime->setCoutTotal($newTime->getEmploye()->getCoutJournalier() * $newTime->getDuree());

            $this->em->persist($newTime);
            $this->em->flush();

            return $this->redirectToRoute('details_employe',['id' => $id, 'offset' => 0]);
        }

        $chest = [
            'title' => $employe->getPrenom().' '.strtoupper($employe->getNom()),
            'icon' => 'users',
            'active' => ["dashboard" => "", "projets" => "", "employes" => "active", "metiers" => "" ]
        ];

        return $this->render('details/detail.html.twig', [
            'type_detail' => 'employe',
            'entity' => $employe,
            'items' => $projets,
            'historiqueProduction' => $historique,
            'erreur_btn' => $erreur,
            'form' => $form->createView(),
            'entity_form' => $entity_form,
            'nb_pages' => $nb_pages,
            'current_page' => $current_page,
            'btns' => $btns,
            'chest' => $chest
        ]);
    }

    /**
     * @Route("/production/delete/{id}/{idEntity}", name="delete_production", requirements={"id" = "\d+"})
     */

    public function deleteTempsDeProduction(int $id, int $idEntity){

        // Récupération du temps de production puis suppression de celui-ci
        $tempsDeProduction = $this->tempsDeProductionRepository->find($id);
        $this->em->remove($tempsDeProduction);
        $this->em->flush();

        return $this->redirectToRoute('details_employe',['id' => $idEntity]);
    }
}
