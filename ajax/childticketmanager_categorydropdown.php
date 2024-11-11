<?php

$AJAX_INCLUDE = 1;
include('../../../inc/includes.php');
header("Content-Type: text/html; charset=UTF-8");
Html::header_nocache();

Session::checkLoginUser();

$entity = -1;
if (($ticket = Ticket::getById($_POST['ticket'])) != false) {
    $entity = $ticket->fields['entities_id'];
}

$options = [
    'entity' => $entity,
    'condition' => [],
    'width' => '100%',
    'addicon' => false,
    'comments' => false, // Avant de ne plus avoir de cheveux à m'arracher de sur la tête...
    'value' => 0,
    'name' => 'childticketmanager_category',
    'on_change' => (($_POST['template']??0) ? "glpi_plugin_childticketmanager_updateTemplateId($(this).val());" : ''),
];

$type = $_POST['type'] ?? Ticket::DEMAND_TYPE;
$cat = new ITILCategory();
$cat->getFromDB($_POST['value'] ?? null);

switch ($type) {
    case Ticket::INCIDENT_TYPE:
        $options['condition']['is_incident'] = 1;
        if ($cat->getField('is_incident') == 1) $options['value'] = $_POST['value'];
        break;
    case Ticket::DEMAND_TYPE:
        $options['condition']['is_request'] = 1;
        if ($cat->getField('is_request') == 1) $options['value'] = $_POST['value'];
        break;
}

ITILCategory::dropdown($options);
exit;