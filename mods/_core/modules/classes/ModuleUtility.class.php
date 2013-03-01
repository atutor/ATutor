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

	public static function set_config_values($config_name, $name) {
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
	public static function set_default_tools($post_up, $post_down, $post_main, $post_home, $post_submit) {
		global $msg;
		if (isset($post_up)) {
			$up = key($post_up);
			$_new_modules  = array();
			if (isset($post_main)) {
				foreach ($post_main as $m) {
					if ($m == $up) {
						$last_m = array_pop($_new_modules);
						$_new_modules[] = $m;
						$_new_modules[] = $last_m;
					} else {
					$_new_modules[] = $m;
					}
				}

				$post_main = $_new_modules;
			}
			if (isset($post_home)) {
				$_new_modules  = array();
				foreach ($post_home as $m) {
					if ($m == $up) {
						$last_m = array_pop($_new_modules);
						$_new_modules[] = $m;
						$_new_modules[] = $last_m;
					} else {
						$_new_modules[] = $m;
					}
				}
				$post_home = $_new_modules;
			}

			$post_submit = TRUE;
		} else if (isset($post_down)) {
			$_new_modules  = array();

			$down = key($post_down);

			if (isset($post_main)) {
				foreach ($post_main as $m) {
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

				$post_main = $_new_modules;
			}

			if (isset($post_home)) {
				$_new_modules  = array();
				foreach ($post_home as $m) {
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

				$post_home = $_new_modules;
			}

			$post_submit = TRUE;
		}
		if (isset($post_submit)) {
			if (isset($post_main)) {
				$post_main = array_unique($post_main);
				$post_main = array_filter($post_main); // remove empties
				$main_defaults = implode('|', $post_main);

			} else {
				$main_defaults = '';
			}


			if (isset($post_home)) {
				$post_home = array_unique($post_home);
				$post_home = array_filter($post_home); // remove empties
				$home_defaults = implode('|', $post_home);
			} else {
				$home_defaults = '';
			}

			self::set_config_values('main_defaults', $main_defaults);
			self::set_config_values('home_defaults', $home_defaults);

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
