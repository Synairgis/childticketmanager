<?php
/**
 * Created by PhpStorm.
 * User: Tobbi
 * Date: 2018-04-03
 * Time: 16:00
 */

include ("../../../inc/includes.php");

$parent_ticket = new Ticket();
$parent_ticket->getFromDB($_POST['tickets_id']);

$new_ticket = new Ticket();
$template = $new_ticket->getTicketTemplateToUse(0, $parent_ticket->getField('type'), $_POST['category']);

$new_ticket_values = $template->predefined;

$new_ticket_values['entities_id'] = $parent_ticket->getField('entities_id');

// La date d'ouverture... si elle vient du template on n'a rien à faire et si
// le template ne la définit pas, glpi prend la date courante alors dans tous les cas, on n'a rien à faire.
// $new_ticket_values['date'] =

$new_ticket_values['type'] = isset($new_ticket_values['type']) ? $new_ticket_values['type'] : $parent_ticket->getField('type');
$new_ticket_values['status'] = isset($new_ticket_values['status']) ? $new_ticket_values['status'] : 1; // 1 => Nouveau
$new_ticket_values['urgency'] = isset($new_ticket_values['urgency']) ? $new_ticket_values['urgency'] : $parent_ticket->getField('urgency');
$new_ticket_values['impact'] = isset($new_ticket_values['impact']) ? $new_ticket_values['impact'] : $parent_ticket->getField('impact');

//Priorité, voir si on peut la calculer
//$new_ticket_values['impact'] = isset($new_ticket_values['impact']) ? $new_ticket_values['impact'] : $parent_ticket->getField('impact');

$new_ticket_values['itilcategories_id'] = $_POST['category'];

$new_ticket_values['requesttypes_id'] = isset($new_ticket_values['requesttypes_id']) ? $new_ticket_values['requesttypes_id'] : 1; // 1 => Helpdesk

//$new_ticket_values['global_validation'] = Rien à faire puisqu'on prend soit la valeur du gabarit ou la valeur par défaut

$new_ticket_values['locations_id'] = isset($new_ticket_values['locations_id']) ? $new_ticket_values['locations_id'] : $parent_ticket->getField('locations_id');
$new_ticket_values['name'] = isset($new_ticket_values['name']) ? $new_ticket_values['name'] : $parent_ticket->getField('name');
$new_ticket_values['content'] = isset($new_ticket_values['content']) ? $new_ticket_values['content'] : $parent_ticket->getField('content');

foreach($new_ticket_values as $field => $val)
{
   if($field != '_documents_id')
      $new_ticket_values[$field] = $DB->escape($val);

      // $new_ticket_values[$field] = mysqli_real_escape_string($DB->dbh, $val);
}

$new_ticket->add(  $new_ticket_values  );

$relation = new Ticket_Ticket();

$relation->add( ['tickets_id_1' => $new_ticket->getID(), 'tickets_id_2' => $parent_ticket->getID(), 'link' => 3] );

if( isset( $new_ticket_values['_documents_id']  )   )
{
   foreach($new_ticket_values['_documents_id'] as $docID)
   {
      $docItem = new Document_Item();
      $docItem->add([
         'documents_id' => $docID,
         'itemtype' => 'Ticket',
         'items_id' => $new_ticket->getID()
      ]);
   }
}

echo json_encode(["new_ticket_id" => $new_ticket->getID()]);