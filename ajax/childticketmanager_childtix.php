<?php
/**
 * Created by PhpStorm.
 * User: Tobbi
 * Date: 2018-04-09
 * Time: 10:29
 */

include ("../../../inc/includes.php");

$configs = Config::getConfigurationValues('plugin:childticketmanager' , ['childticketmanager_close_child', 'childticketmanager_resolve_child', 'childticketmanager_hide_tmpl_link']);


if($configs['childticketmanager_close_child'] == 1 && $_POST['tickets_status'] == CommonITILObject::CLOSED)
{
	echo json_encode(updateChildTickets($_POST['tickets_status'], $_POST['tickets_id'], null));
//	Session::addMessageAfterRedirect(  __(' mis à jour', 'childticketmanager')  );
}
elseif($configs['childticketmanager_resolve_child'] == 1 && $_POST['tickets_status'] == CommonITILObject::SOLVED)
{
	echo json_encode(updateChildTickets($_POST['tickets_status'], $_POST['tickets_id'], null));
//	Session::addMessageAfterRedirect(  __(' mis à jour', 'childticketmanager')  );
}
else
{
	echo json_encode("");
}

function updateChildTickets($status, $current, $parent)
{
	global $DB;
	$children = getChildTickets($current);
	$retour = [];

	if($children != null)
	{
		foreach($children as $tix)
			$retour = array_merge($retour, updateChildTickets($status, $tix, $current));
	}

	$currentDate = new datetime();
	$parent_ticket = new Ticket();
	$parent_ticket->getFromDB($current);
	$updatedFields = ['status', 'solvedate', 'closedate'];
	$oldValues = [];

	if($parent_ticket->fields['status'] >= $status)
		return $retour;

	$parent_ticket->input = [];
	$parent_ticket->updates = [];

	Plugin::doHook("pre_item_update", $parent_ticket);

	foreach($updatedFields as $fld)
		$oldValues[$fld] = $parent_ticket->fields[$fld];

	$parent_ticket->fields['status'] = $status;
	
	if($status == CommonITILObject::SOLVED) 
	{
		$parent_ticket->fields['solvedate'] = $currentDate->format("Y-m-d H:i:s");

		if( $parent != null)
		{
			$solution = new ITILSolution();

			$fields = [
				'itemtype' => 'Ticket',
				'items_id' => $parent_ticket->fields['id'],
				'solutiontypes_id' => 0,
				'solutiontype_name' => null,
				'content' => "Résolu par le biais du billet " . $parent,
				'date_creation' => $currentDate->format("Y-m-d H:i:s"),
				'date_mod' => $currentDate->format("Y-m-d H:i:s"),
				'date_approval' => null,
				'users_id' => Session::getLoginUserID(),
				'user_name' => null,
				'users_id_editor' => 0,
				'users_id_approval' => $parent_ticket->fields['users_id_recipient'],
				'user_name_approval' => null,
				'status' => 2,
				'ticketfollowups_id' => null
			];

			$solution->add($fields);
		}

		$mailtype = "solved";
	}
	elseif($status == CommonITILObject::CLOSED)
	{
		$parent_ticket->fields['closedate'] = $currentDate->format("Y-m-d H:i:s");

		if($parent_ticket->fields['solvedate'] == null)
			$parent_ticket->fields['solvedate'] = $currentDate->format("Y-m-d H:i:s");

		$mailtype = "closed";
		$parent_ticket->updateInDB($updatedFields, $oldValues);
	}


	Plugin::doHook("item_update", $parent_ticket);
	
	Session::addMessageAfterRedirect(  __("Ticket ", "childticketmanager") . $parent_ticket->getID() . __(" mis à jour", "childticketmanager")  );
	NotificationEvent::raiseEvent($mailtype, $parent_ticket);

	return array_merge( [$current], $retour );
}

function getChildTickets($parent_id)
{
	global $DB;
	
	$query = "SELECT tickets_id_1 as ticket_id FROM glpi_tickets_tickets WHERE tickets_id_2 = ? AND LINK = 3";
	$stmt = $DB->prepare($query);

	$stmt->bind_param('i', $parent_id);
	$stmt->execute();

	$res = $stmt->get_result();

	if($res->num_rows == 0)
		return null;

	return array_column($res->fetch_all(MYSQLI_ASSOC), 'ticket_id');
	
}
