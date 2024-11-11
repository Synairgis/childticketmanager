# ChildTicket Manager GLPI Plugin
![GitHub Downloads (all assets, all releases)](https://img.shields.io/github/downloads/synairgis/childticketmanager/total?style=plastic)
![Static Badge](https://img.shields.io/badge/GLPI-v10-blue?style=plastic)

![Logo](logo.png)



This plugin is meant to ease the management of parent-child tickets in GLPI. It adds an option to the standard linked ticket GLPI feature which can generate a new child ticket directly from the current one.

It allows to:

1) Easily create a child ticket directly from a parent ticket
2) Cascade the resolution/closure of child ticket upon parent's status change
3) Easily apply a template to newly created child tickets

## Installation & Configuration

**Please take note that v3 now requires GLPI v10.0. To continue using the plugin with GLPI 9, use v2.**

Once installed, you can configure wether the plugin:

- closes child tickets upon parent's closure;
- resolves child tickets upon parent's resolution;
- diplay a link to the selected category's template.

## Usage

From any ticket, go to the "Linked tickets" section and click the "+ Add" button. This will show the "Child Ticket" options below the standard ticket linkage options.

The options allows to select the category of the child ticket to create and also provides a link to the selected category's template, if there is one.

----

# Plugin GLPI ChildTicket Manager

Ce plugin vise à simplifier la gestion des tickets parent-enfant dans GLPI. Il ajoute une option à la fonctionnalité de liaison de tickets native à GLPI permettant de créer un nouveau ticket enfant directement depuis le ticket courant.

## Fonctionnalités

1) Permet de facilement créer un ticket enfant à partir d'un ticket parent
2) Résolution/fermeture en cascade des tickets enfant au changement de statut du parent
3) Application d'un gabarit aux enfants nouvellement créés

## Configuration

**Veuillez noter que la version 3 du plugin requiert GLPI v10.0. Pour continuer d'utiliser le plugin avec GLPI v9, veuillez utiliser la version 2.**

Une fois installé, vous pouvez configurer le plugin afin qu'il

- ferme les tickets enfants lorsque le parent est clos;
- résolve les tickets enfants lorsque le parent est résolu;
- affiche un lien vers le gabarit de la catégorie sélectionnée.

## Utilisation

Dpuis un ticket, aller à la section "Ticket lié" et cliquer sur le bouton "+ Ajouter". Ceci affichera de nouvelles options sous les options native de liaison identifiées par un symbole de ticket.

Ces options permettant de sélectionner la catégorie du ticket enfant à créer ainsi que de consulter le garabrit lié à cette catégorie, s'il y en a un.
