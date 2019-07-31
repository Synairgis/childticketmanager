<?php
/**
 * Created by PhpStorm.
 * User: Tobbi
 * Date: 2018-04-03
 * Time: 16:00
 */

include ("../../../inc/includes.php");

$new_ticket = PluginChildticketmanagerAction::save($_POST);
echo json_encode([
   "new_ticket_id" => $new_ticket->getID()
]);
