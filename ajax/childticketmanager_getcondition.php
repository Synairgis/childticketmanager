<?php

include ("../../../inc/includes.php");

$retour = array();

$ticket = new Ticket();
$ticket->getFromDB($_POST['id']);


$condition = "";
if ($_POST['type'] == Ticket::INCIDENT_TYPE) {
   $condition = "`is_incident` = 1";
} else if ($_POST['type'] == Ticket::DEMAND_TYPE) {
   $condition = "`is_request` = 1";
}

$retour['condition'] = sha1($condition);
$_SESSION['glpicondition'][$retour['condition']] = $condition;

$retour['entity'] = $ticket->fields["entities_id"];

echo json_encode($retour);
