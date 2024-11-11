<?php

$AJAX_INCLUDE = 1;
include('../../../inc/includes.php');
header("Content-Type: text/html; charset=UTF-8");
Html::header_nocache();

Session::checkLoginUser();

if (($ticket = Ticket::getById($_POST['ticket']??0)) === false) exit('{}');
$template = $ticket->getITILTemplateToUse(0, $_POST['type']??$ticket->getField('type'), $_POST['category']??0);

exit(json_encode(['template_id' => $template->getID()]));
