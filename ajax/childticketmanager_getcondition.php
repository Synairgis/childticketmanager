<?php

include ("../../../inc/includes.php");

echo json_encode(PluginChildticketmanagerAction::getCondition($_POST));
