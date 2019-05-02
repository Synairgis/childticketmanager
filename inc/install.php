<?php
/**
 * Created by PhpStorm.
 * User: Tobbi
 * Date: 2018-04-24
 * Time: 10:09
 */

if (!defined('GLPI_ROOT')) {
	die("Sorry. You can't access this file directly");
}

class PluginChildticketmanagerInstall {
	protected $migration;
	protected $default_configs = [
		['plugin:childticketmanager', 'childticketmanager_close_child', 0],
		['plugin:childticketmanager', 'childticketmanager_resolve_child', 0],
		['plugin:childticketmanager', 'childticketmanager_display_tmpl_link', 0]
	];

	/**
	 * Install the plugin
	 * @param Migration $migration
	 *
	 * @return void
	 */
	public function install(Migration $migration) {
		global $DB;
		$this->migration = $migration;

		$query = "INSERT INTO glpi_configs(context, name, value) VALUES(?, ?, ?)";
		$stmt = $DB->prepare($query);

		foreach($this->default_configs as $config) {
			$stmt->bind_param('ssi', $config[0], $config[1], $config[2]);
			$stmt->execute();
		}

		return true;

	}

	public function upgrade(Migration $migration) {
		$this->migration = $migration;
		$this->migration->executeMigration();

		return true;
	}

	/**
	 * is the plugin already installed ?
	 *
	 * @return boolean
	 */
	public function isPluginInstalled() {
     $config = Config::getConfigurationValues('plugin:childticketmanager');

		return count($config);
	}
}