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
        $projet = $this->projetRepository->find($id);
        $employes = $this->employeRepository->findBy(['archivage' => 0]);

        $historique = $this->tempsDeProductionRepository->findBy(['projet' => $id],['dateSaisie' => 'DESC'], 10, $offset);

        $coutTotalProjet = $this->tempsDeProductionRepository->findCoutTotalProjet($id);
        $nbEmployes = $this->tempsDeProductionRepository->findEmployesByProject($id);

        $coutTotal = $coutTotalProjet[0]['coutTotal'] == null ? 0 : $coutTotalProjet[0]['coutTotal'];

        $nb_pages = ceil(count($this->tempsDeProductionRepository->findBy(['projet' => $id])) / 10);
        $nb_pages = $nb_pages < 1 ? 1 : $nb_pages;
        $current_page = ($offset /10) + 1;

        if($current_page == 1){
            $btns = ['disabled',''];
        }else if($current_page == $nb_pages){
            $btns = ['', 'disabled'];
        }else{
            $btns = ['', ''];
        }

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

        $form = $this->createForm(AddProductionTimeType::class, $newTime)->add($entity_form, ChoiceType::class,[
            'label' => 'Employés',
            'choices' => $liste
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $newTime->setCoutTotal($newTime->getEmploye()->getCoutJournalier() * $newTime->getDuree());

            $this->em->persist($newTime);
            $this->em->flush();

            return $this->redirectToRoute('details_projet',['id' => $id, 'offset' => 0]);
        }

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

    public function livraisonProjet(Request $request, int $id){
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

        $employe = $this->employeRepository->find($id);
        $projets = $this->projetRepository->findBy(['estLivre' => 0]);

        $historique = $this->tempsDeProductionRepository->findBy(['employe' => $id],['dateSaisie' => 'DESC'], 10, $offset);

        $nb_pages = ceil(count($this->tempsDeProductionRepository->findBy(['employe' => $id])) / 10) ;
        $nb_pages = $nb_pages < 1 ? 1 : $nb_pages;
        $current_page = ($offset /10) + 1;

        if($current_page == 1){
            $btns = ['disabled',''];
        }else if($current_page == $nb_pages){
            $btns = ['', 'disabled'];
        }else{
            $btns = ['', ''];
        }

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

            return $this->redirectToRoute('details_employe',['id' => $id, 'offset' => 0]);
        }

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
        $tempsDeProduction = $this->tempsDeProductionRepository->find($id);
        $this->em->remove($tempsDeProduction);
        $this->em->flush();

        return $this->redirectToRoute('details_employe',['id' => $idEntity]);
    }
}
