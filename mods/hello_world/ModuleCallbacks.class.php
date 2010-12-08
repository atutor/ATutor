<?php

/**
 * This class contains the callback functions that are called in the core scripts to manipulate content etc. 
 *
 * Note:
 * 1. CHANGE the class name and ensure its uniqueness by prefixing with the module name
 * 2. DO NOT change the script name. Leave as "ModuleCallbacks.class.php"
 * 3. DO NOT change the names of the methods.
 * 4. REGISTER the unique class name in module.php
 *
 * @access	public
 */
if (!defined('AT_INCLUDE_PATH')) exit;

class HelloWorldCallbacks {
	/*
	 * To append output onto course content page 
	 * @param: None
	 * @return: a string, plain or html, to be appended to course content page
	 */ 
	public static function appendContent() {
		return "Output from hello world module!";
	}
}

?>