<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2010                                      */
/* Inclusive Design Institute                                   */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$


/**
* ModuleUtility
* Class with utility functions to be used in modules
* @access public
* @package Module
*/
class ModuleUtility {

	private static $main_defaults;
	private static $home_defaults;

	function ModuleUtility() {
		// constructor
	}

	public function set_config_values($config_name, $name) {
		global $db;
		if (!($_config_defaults[$config_name] == $name)) {
			$sql    = "REPLACE INTO ".TABLE_PREFIX."config VALUES('".$config_name."', '$name')";
		} else if ($_config_defaults[$config_name] == $name) {
			$sql    = "DELETE FROM ".TABLE_PREFIX."config WHERE name='".$config_name."'";
		}
		$result = mysql_query($sql, $db);
	}
	/**
	*
	* Set default course tools
	* @access public
	* @static
	*
	*/
	public static function set_default_tools() {
		global $msg;
		if (isset($_POST['up'])) {
			$up = key($_POST['up']);
			$_new_modules  = array();
			if (isset($_POST['main'])) {
				foreach ($_POST['main'] as $m) {
					if ($m == $up) {
						$last_m = array_pop($_new_modules);
						$_new_modules[] = $m;
						$_new_modules[] = $last_m;
					} else {
					$_new_modules[] = $m;
					}
				}

				$_POST['main'] = $_new_modules;
			}
			if (isset($_POST['home'])) {
				$_new_modules  = array();
				foreach ($_POST['home'] as $m) {
					if ($m == $up) {
						$last_m = array_pop($_new_modules);
						$_new_modules[] = $m;
						$_new_modules[] = $last_m;
					} else {
						$_new_modules[] = $m;
					}
				}
				$_POST['home'] = $_new_modules;
			}

			$_POST['submit'] = TRUE;
		} else if (isset($_POST['down'])) {
			$_new_modules  = array();

			$down = key($_POST['down']);

			if (isset($_POST['main'])) {
				foreach ($_POST['main'] as $m) {
					if ($m == $down) {
						$found = TRUE;
						continue;
					}
					$_new_modules[] = $m;
					if ($found) {
						$_new_modules[] = $down;
						$found = FALSE;
					}
				}

				$_POST['main'] = $_new_modules;
			}

			if (isset($_POST['home'])) {
				$_new_modules  = array();
				foreach ($_POST['home'] as $m) {
					if ($m == $down) {
						$found = TRUE;
						continue;
					}
					$_new_modules[] = $m;
					if ($found) {
						$_new_modules[] = $down;
						$found = FALSE;
					}
				}

				$_POST['home'] = $_new_modules;
			}

			$_POST['submit'] = TRUE;
		}
		if (isset($_POST['submit'])) {
			if (isset($_POST['main'])) {
				$_POST['main'] = array_unique($_POST['main']);
				$_POST['main'] = array_filter($_POST['main']); // remove empties
				$main_defaults = implode('|', $_POST['main']);

			} else {
				$main_defaults = '';
			}


			if (isset($_POST['home'])) {
				$_POST['home'] = array_unique($_POST['home']);
				$_POST['home'] = array_filter($_POST['home']); // remove empties
				$home_defaults = implode('|', $_POST['home']);
			} else {
				$home_defaults = '';
			}
			$obj = new ModuleUtility();
			$obj->set_config_values('main_defaults', $main_defaults);
			$obj->set_config_values('home_defaults', $home_defaults);

			$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
			header('Location: '.$_SERVER['PHP_SELF']);
			exit;
		}
	}

	/**
	* Return set config values
	* @access public
	* @return array
	* @static
	*/
	public static function get_main_defaults()
	{
		return $main_defaults;
	}

	public static function get_home_defaults()
	{
		return $home_defaults;
	}

}

?>
