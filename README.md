# ChildTicket Manager GLPI Plugin
![GitHub Downloads (all assets, all releases)](https://img.shields.io/github/downloads/synairgis/childticketmanager/total?style=plastic)
![Static Badge](https://img.shields.io/badge/GLPI-v11-blue?style=plastic)

![Logo](logo.png)

This plugin is meant to ease the management of parent-child tickets in GLPI. It adds an option to the standard linked ticket GLPI feature which can generate a new child ticket directly from the current one.

It allows to:

1. Easily create a child ticket directly from a parent ticket
2. Cascade the resolution/closure of child ticket upon parent's status change
3. Easily apply a template to newly created child tickets

## Installation & Configuration

> **v4 requires GLPI >= 11.0.** For GLPI 10, use v3.0.2. For GLPI 9, use v2.

Once installed, you can configure whether the plugin:

- closes child tickets upon parent's closure;
- resolves child tickets upon parent's resolution;
- displays a link to the selected category's template.

## Usage

From any ticket, go to the "Linked tickets" section and click the "+ Add" button. This will show the "Child Ticket" options below the standard ticket linkage options.

The options allow you to select the category of the child ticket to create and also provide a link to the selected category's template, if there is one.

----

# Plugin GLPI ChildTicket Manager

Ce plugin vise à simplifier la gestion des tickets parent-enfant dans GLPI. Il ajoute une option à la fonctionnalité de liaison de tickets native à GLPI permettant de créer un nouveau ticket enfant directement depuis le ticket courant.

## Fonctionnalités

1. Permet de facilement créer un ticket enfant à partir d'un ticket parent
2. Résolution/fermeture en cascade des tickets enfant au changement de statut du parent
3. Application d'un gabarit aux enfants nouvellement créés

## Configuration

> **La version 4 requiert GLPI >= 11.0.** Pour GLPI 10, utilisez la v3.0.2. Pour GLPI 9, utilisez la v2.

Une fois installé, vous pouvez configurer le plugin afin qu'il :

- ferme les tickets enfants lorsque le parent est clos;
- résolve les tickets enfants lorsque le parent est résolu;
- affiche un lien vers le gabarit de la catégorie sélectionnée.

## Utilisation

Depuis un ticket, aller à la section "Ticket lié" et cliquer sur le bouton "+ Ajouter". Ceci affichera de nouvelles options sous les options natives de liaison identifiées par un symbole de ticket.

Ces options permettent de sélectionner la catégorie du ticket enfant à créer ainsi que de consulter le gabarit lié à cette catégorie, s'il y en a un.

----

# Plugin GLPI ChildTicket Manager (Español)

Este plugin facilita la gestión de tickets padre-hijo en GLPI. Añade una opción a la funcionalidad nativa de tickets vinculados que permite crear un nuevo ticket hijo directamente desde el ticket actual.

## Funcionalidades

1. Crear fácilmente un ticket hijo desde un ticket padre
2. Resolución/cierre en cascada de tickets hijos al cambiar el estado del padre
3. Aplicar una plantilla a los tickets hijos recién creados

## Configuración

> **La versión 4 requiere GLPI >= 11.0.** Para GLPI 10, usa la v3.0.2. Para GLPI 9, usa la v2.

Una vez instalado, puedes configurar si el plugin:

- cierra los tickets hijos cuando se cierra el padre;
- resuelve los tickets hijos cuando se resuelve el padre;
- muestra un enlace a la plantilla de la categoría seleccionada.

## Uso

Desde cualquier ticket, ve a la sección "Tickets vinculados" y haz clic en el botón "+ Agregar". Esto mostrará las opciones de "Ticket hijo" debajo de las opciones nativas de vinculación.

Las opciones permiten seleccionar la categoría del ticket hijo a crear y también proporcionan un enlace a la plantilla de esa categoría, si existe.
