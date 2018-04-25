<?php
/**
 * Created by PhpStorm.
 * User: Tobbi
 * Date: 2018-04-06
 * Time: 13:23
 */

include ("../../../inc/includes.php");

$ticket = new Ticket();
$ticket->getFromDB($_POST['tickets_id']);

$template = $ticket->getTicketTemplateToUse(0, $ticket->getField('type'), $_POST['category']);

echo json_encode(["template_id" => $template->getID()]);