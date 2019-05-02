<?php
/**
 * Created by PhpStorm.
 * User: Tobbi
 * Date: 2018-04-09
 * Time: 10:29
 */

include ("../../../inc/includes.php");

echo json_encode(PluginChildticketmanagerAction::childtix($_POST));
