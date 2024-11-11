<?php
$AJAX_INCLUDE = 1;
include('../../../inc/includes.php');

use Glpi\Toolbox\Sanitizer;

header("Content-Type: text/html; charset=UTF-8");
Html::header_nocache();

Session::checkLoginUser();

if (($parent = Ticket::getById($_POST['ticket'])) === false) exit();

$child = new Ticket;
$input = $child->getITILTemplateToUse(0, $_POST['type'], $_POST['category'])->predefined;

foreach ([
    'entities_id',
    'type',
    'urgency',
    'impact',
    'requesttypes_id',
    'locations_id',
    'name',
    'content',
] as $key) {
    $input[$key] = $input[$key] ?? $parent->getField($key);
}
$input['itilcategories_id'] = $_POST['category'];
$input['status'] = $input['status'] ?? Ticket::INCOMING;
$input['requesttypes_id'] = $input['requesttypes_id'] ?? 1; // Helpdesk
$input['_add'] = true; // This adds the standard redirect message

foreach ($input as $key => $val) {
    $input[$key] = ($key == '_documents_id' ? $val : Sanitizer::dbEscape($val));
}

$child->add($input);
(new Ticket_Ticket)->add([
    'tickets_id_1'  => $child->getID(),
    'tickets_id_2'  => $parent->getID(),
    'link'          => Ticket_Ticket::SON_OF,
]);

foreach ($input['_documents_id']??[] as $doc) {
    (new Document_Item)->add([
        'documents_id'  => $doc,
        'itemtype'      => Ticket::class,
        'items_id'      => $child->getID(),
    ]);
}

exit(json_encode(['tickets_id' => $child->getID()]));