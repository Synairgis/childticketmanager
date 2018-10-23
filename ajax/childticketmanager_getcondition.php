<?php

include ("../../../inc/includes.php");

if($_POST['type'] == Ticket::INCIDENT_TYPE)
	echo sha1("`is_incident`='1'");
elseif($_POST['type'] == Ticket::DEMAND_TYPE)
	echo sha1("`is_request`='1'");
else
	echo sha1("");

?>