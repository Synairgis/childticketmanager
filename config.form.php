<?php
require_once ('../../../inc/includes.php');

// Check if current user have config right
Session::checkRight("entity", UPDATE);

// Check if plugin is activated...
$plugin = new Plugin();
if (!$plugin->isActivated('childticketmanager')) {
   Html::displayNotFoundError();
}

Html::header(
	__('Tickets enfants', 'childticketmanager'),
	$_SERVER['PHP_SELF'],
	'admin',
	'PluginchildticketmanagerConfig'
);

//Search::show('PluginchildticketmanagerConfig');
Html::redirect($CFG_GLPI["root_doc"]."/front/config.form.php?forcetab=PluginchildticketmanagerConfig\$1");

Html::footer();
