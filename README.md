# Projet Symfony : _Procom_

## Auteur

Ce projet a été développé par Quentin BRUNELLE (quentin.brunelle@gmail.com).

## Description

_Procom_ est une interface web permettant de calculer les coûts de développement des projets réalisés par les équipes 
de production d'une entreprise.

Elle permet notamment : 
* de bénéficier d'un dashboard complet permettant de visualiser sommairement la situation
de l'entreprise
* une gestion des employés (ajout / modification / archivage) et une visualisation 
des projets sur lesquels ils travaillent.
* une gestion des projets (ajout / modification / livraison / suppression) et une
visualisation des employés affectés.
* une gestion des métiers (ajout / modification / suppression)

### Arborescence 

![alt text](http://lp.florianhermann.fr/img/procom/arbo.png)

## Bundles utilisés

[TwigExtension](https://numa-bord.com/miniblog/symfony-afficher-date-francais-twig/) : Permet l'utilisation du filtre __localizeddate__ afin d'afficher la date selon la variable
locale établie (fr) et selon l'ordre souhaité (ici "_Mars 2018"_).

```bash
<td>{{ employe.dateEmbauche | localizeddate('none', 'none', null, null, 'MMMM Y') | capitalize }}</td>
```

[SwiftMailer](https://swiftmailer.symfony.com/) : Permet de gérer l'envoi du mail au directeur lors de la suppresion d'un projet.


## Fixtures

J'ai choisi de mettre en place des données entièrement différentes les unes
par rapport aux autres afin que celles-ci soient uniques et reflètent la réalité
d'une entreprise.

Les employés sont créés de manière aléatoire par l'utilisation de différents tableaux
contenant des données brutes.

Les projets sont de la forme _Projet n°X_, _description du projet n°X_.

Les métiers sont issus d'un tableau en dur.

L'ensemble des dates (dateEmbauche, dateCreation, dateSaisie) sont créées de manières 
aléatoires dans l'intervalle [01 Janvier 2015 - Date du jour].

## Améliorations

Je souhaite améliorer la requête concernant l'employé du mois afin de réduire le nombre
de requêtes à une seule. Néanmoins, je n'ai pas réussi à créer une telle requête dans
le Query Builder.
