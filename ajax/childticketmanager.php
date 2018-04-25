<?php
/**
 * Created by PhpStorm.
 * User: Tobbi
 * Date: 2018-04-03
 * Time: 08:55
 */

include ("../../../inc/includes.php");

$configs = Config::getConfigurationValues('plugin:childticketmanager' , ['childticketmanager_close_child', 'childticketmanager_resolve_child', 'childticketmanager_display_tmpl_link']);

$ticket_id = (int) $_REQUEST['tickets_id'];

$ticket = new Ticket();
$ticket->getFromDB($ticket_id);

$JS = <<<JAVASCRIPT
	if( $(this).val() == 0 )
		$("#childticketmanager_templ").hide();
	else
		$("#childticketmanager_templ").show();

JAVASCRIPT;

ITILCategory::dropdown([
	'comments' => false, 
	'name' => 'childticketmanager_category',
	'on_change' => $JS
]);
echo Html::hidden("ticket_id", ['value' => $ticket_id]);

if($configs['childticketmanager_display_tmpl_link'] == 1 ) {
	echo "&nbsp;";
	echo "&nbsp;";
	echo Html::submit( __("Voir gabarit", 'childticketmanager'), ['id' => 'childticketmanager_templ', 'style' => 'display:none']);
}
echo "&nbsp;";
echo "&nbsp;";
echo html::submit( __('Créer enfant', 'childticketmanager'), ['id' => 'childticketmanager_submit']);
echo "<br>";