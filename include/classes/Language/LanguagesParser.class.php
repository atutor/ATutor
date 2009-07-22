<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$

require_once(dirname(__FILE__) . '/LanguageParser.class.php');

/**
* LanguagesParser
* Class for parsing XML languages info and returning a Language Objects
* @access	public
* @author	Joel Kronenberg
* @package	Language
*/
class LanguagesParser extends LanguageParser {

	// private
	function startElement($parser, $name, $attributes) {
		if ($name == 'languages') {
			// strip off the initial 'languages'
			$this->element_path = array();
		} else {
			parent::startElement($this->parser, $name, $attributes);
		}
   }
 
}

?>