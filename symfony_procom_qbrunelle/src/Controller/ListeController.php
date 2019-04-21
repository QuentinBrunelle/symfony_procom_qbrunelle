<?php

namespace App\Controller;

use App\Entity\TempsProductionEmployeProjet;
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

    /**
     * @var \Swift_Mailer
     */
    private $mailer;
    private $contactEmailAdress;

    private $employeRepository;
    private $projetRepository;
    private $metierRepository;
    private $tempsDeProductionRepository;


    public function __construct(\Swift_Mailer $mailer,EntityManagerInterface $em)
    {
        $this->mailer = $mailer;
        $this->contactEmailAdress = 'quentin.brunelle@gmail.com';
        $this->em = $em;
        $this->employeRepository = $this->em->getRepository(Employe::class);
        $this->projetRepository = $this->em->getRepository(Projet::class);
        $this->metierRepository = $this->em->getRepository(Metier::class);
        $this->tempsDeProductionRepository = $this->em->getRepository(TempsProductionEmployeProjet::class);
    }

    /**
     * @Route("/projets/{offset}/{erreur}", name="projets")
     */
    public function projets(int $offset, bool $erreur = false, array $filteredProjects = [])
    {
        // Liste des projets à afficher : Si $filteredProjects est définie, c'est cette variable qui sera utilisée.
        // Sinon, on récupère les projets par date décroissante par tranche de 10.
        $projets = $filteredProjects === [] ? $this->projetRepository->findBy([],['date' => 'DESC'], 10, $offset) : $filteredProjects;

        // Nombre de pages nécessaires pour l'affichage de tous les temps de production par tranche de 10 résultats
        $nb_pages = ceil(count($this->projetRepository->findAll()) / 10) ;
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

        $chest = [
            'title' => 'Projets',
            'icon' => 'laptop',
            'active' => ["dashboard" => "", "projets" => "active", "employes" => "", "metiers" => "" ],
            'headers' => ["Intitulé", "Description", "Type", "Est livré", "Date de création"],
            'error_message' => "Le projet ne peut être modifié ou supprimé car il a été livré..."
        ];

        return $this->render('liste/list.html.twig', [
            'type_liste' => 'projet',
            'items' => $projets,
            'nb_pages' => $nb_pages,
            'current_page' => $current_page,
            'btns' => $btns,
            'erreur_btn' => $erreur,
            'chest' => $chest
        ]);
    }

    /**
     * @Route("/projet/{id}", name="suppression_projet", requirements={"id" = "\d+"})
     */
    public function suppressionProjet(int $id)
    {
        // L'ensemble des éléments fournis dans le mail sont placés dans l'array $projet
        $projet['projet'] = $this->projetRepository->find($id);
        $projet['destinataire'] = 'quentin.brunelle@gmail.com';
        $projet['historique'] = $this->tempsDeProductionRepository->findBy(['projet' => $id],['dateSaisie' => 'DESC']);
        $coutTotalProjet = $this->tempsDeProductionRepository->findCoutTotalProjet($id);
        $projet['coutTotal'] = $coutTotalProjet[0]['coutTotal'] == null ? 0 : $coutTotalProjet[0]['coutTotal'];
        $projet['nbEmployes'] = ($this->tempsDeProductionRepository->findEmployesByProject($id))[0]['employes'];

        // On prépare le message en utilisant SwiftMailer via la view adéquate
        $message = (new \Swift_Message('Un message de contact sur Shoefony'))
            ->setFrom('quentin.brunelle@gmail.com')
            ->setTo($this->contactEmailAdress)
            ->setBody(
                $this->renderView('email/projet.html.twig',['projet' => $projet]),'text/html'
            );

        $this->mailer->send($message); // Envoi du message

        // Suppression du projet
        $this->em->remove($projet['projet']);
        $this->em->flush();

        return $this->projets(0,false);
    }

    /**
     * @Route("/employes/{offset}/{erreur}", name="employes")
     */
    public function employes(int $offset, bool $erreur = false)
    {
        $employes = $this->employeRepository->findBy([], ['dateEmbauche' => 'DESC'], 10, $offset);

        // Gestion de la pagination
        $nb_pages = ceil(count($this->employeRepository->findAll()) / 10) ;
        $current_page = ($offset /10) + 1;

        // Gestion de l'affichage des boutons de pagination
        if($current_page == 1){
            $btns = ['disabled',''];
        }else if($current_page == $nb_pages){
            $btns = ['', 'disabled'];
        }else{
            $btns = ['', ''];
        }

        $chest = [
            'title' => 'Employés',
            'icon' => 'users',
            'active' => ["dashboard" => "", "projets" => "", "employes" => "active", "metiers" => "" ],
            'headers' => ["Nom", "Email", "Métier", "Coût journalier", "Date d'embauche"],
            'error_message' => "L'utilisateur ne peut être modifié ou archivé car il est déjà archivé..."
        ];

        return $this->render('liste/list.html.twig', [
            'type_liste' => 'employe',
            'items' => $employes,
            'nb_pages' => $nb_pages,
            'current_page' => $current_page,
            'btns' => $btns,
            'erreur_btn' => $erreur,
            'chest' => $chest
        ]);
    }

    /**
     * @Route("/employe/{id}", name="suppression_employe", requirements={"id" = "\d+"})
     */
    public function suppressionEmploye(int $id)
    {
        $employe = $this->employeRepository->find($id);
        $employe->setArchivage(true);
        $this->em->persist($employe);
        $this->em->flush();

        return $this->employes(0,false);
    }

    /**
     * @Route("/metiers", name="metiers")
     */
    public function metiers(bool $erreur = false, string $erreur_message = '')
    {
        $metiers = $this->metierRepository->findAll();

        $chest = [
            'title' => 'Métiers',
            'icon' => 'book',
            'active' => ["dashboard" => "", "projets" => "", "employes" => "", "metiers" => "active" ],
            'headers' => ["Nom"],
            'error_message' => $erreur_message
        ];

        return $this->render('liste/list.html.twig', [
            'type_liste' => "metier",
            'items' => $metiers,
            'nb_pages' => 1,
            'current_page' => 1,
            'btns' => ['disabled', 'disabled'],
            'erreur_btn' => $erreur,
            'chest' => $chest
        ]);
    }

    /**
     * @Route("/metier/{id}", name="suppression_metier", requirements={"id" = "\d+"})
     */
    public function suppressionMetier(int $id)
    {
        $metier = $this->metierRepository->find($id); // On récupère le métier que l'on souhaite supprimer

        /**
         * On vérifie si un ou plusieurs employés ont ce métier
         */
        $employes_work = $this->employeRepository->findBy(['metier' => $id]);

        if(empty($employes_work)){ // Si le métier n'est pas utilisé, on le supprime
            $this->em->remove($metier);
            $this->em->flush();

            $erreur = false;
            $error_message = "";
        }else{ // Sinon on envoi un message d'erreur
            $erreur = true;
            $error_message = "Ce métier est utilisé par un ou plusieurs employés. Il ne peut être supprimé.";
        }

        return $this->metiers($erreur, $error_message);
    }

    /**
     * @Route("/recherche", name="recherche")
     */
    public function recherche(Request $request)
    {
        $recherche = $request->get('recherche'); // On récupère la valeur de l'input

        $projets = $this->projetRepository->findAll(); // On récupère l'ensemble des projets

        // Si l'intitulé ou la description d'un projet contient la chaine issue du formulaire, on l'ajoute dans filteredProjects
        $filteredProjects = [];
        foreach ($projets as $projet){
            if(stristr($projet->getIntitule(), $recherche) != false || stristr($projet->getDescription(),$recherche) != false){
                array_push($filteredProjects, $projet);
            }
        }

        return $this->projets(0, false, $filteredProjects);
    }
}
