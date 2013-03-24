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

    public static function up_set($post, $up) {
        $_new_modules  = array();
        foreach ($post as $m) {
            if ($m == $up) {
                $last_m = array_pop($_new_modules);
                $_new_modules[] = $m;
                $_new_modules[] = $last_m;
            } else {
                $_new_modules[] = $m;
            }
        }
        $post = $_new_modules;
        return $post;
    }


    public static function down_set($post, $down) {
        $_new_modules  = array();
        foreach ($post as $m) {
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
        $post = $_new_modules;
        return $post;
    }

    public static function submit_set($post, $defaults) {
        if (isset($post)) {
            $post = array_unique($post);
            $post = array_filter($post); // remove empties
            $defaults = implode('|', $post);
        } else {
            $defaults = '';
        }
        return $defaults;
    }

	public static function set_config_values($config_name, $name) {
        if (!($_config_defaults[$config_name] == $name)) {
            $result = queryDB("REPLACE INTO %sconfig VALUES('%s', '%s')", array(TABLE_PREFIX, $config_name, $name));
		} else if ($_config_defaults[$config_name] == $name) {
            $result = queryDB("DELETE FROM %sconfig WHERE name='%s'", array(TABLE_PREFIX, $config_name));
		}
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
            if(isset($post_home)) {
                $post_home = self::up_set($post_home, $up);
            }
            if(isset($post_main)) {
                $post_main = self::up_set($post_main, $up);
            }
			$post_submit = TRUE;
		} else if (isset($post_down)) {
            $down = key($post_down);
            if(isset($post_home)) {
                $post_home = self::down_set($post_home, $down);
            }
            if(isset($post_main)) {
                $post_main = self::down_set($post_main, $down);
            }
			$post_submit = TRUE;
		}
		if (isset($post_submit)) {
            $main_defaults = self::submit_set($post_main, $main_defaults);
            $home_defaults = self::submit_set($post_home, $home_defaults);

			self::set_config_values('main_defaults', $main_defaults);
			self::set_config_values('home_defaults', $home_defaults);
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
