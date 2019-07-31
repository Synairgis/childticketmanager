<?php
/**
 * Created by PhpStorm.
 * User: Tobbi
 * Date: 2018-04-06
 * Time: 13:23
 */

include ("../../../inc/includes.php");

$template = PluginChildticketmanagerAction::getTemplate($_POST);
echo json_encode(["template_id" => $template->getID()]);
