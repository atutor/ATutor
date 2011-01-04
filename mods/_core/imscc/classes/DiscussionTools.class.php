<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2009										*/
/* Inclusive Design Institute                                   */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: DiscussionTools.class.php 10141 2010-08-17 17:56:03Z hwong $

/**
 * A class for DiscussionTools
 * based on:
 *  http://www.imsglobal.org/profile/cc/ccv1p0/derived_schema/domainProfile_5/imsdt_v1p0_localised.xsd
 */
class DiscussionTools {
	//global variables
	var $title = '';	//The Forum title
	var $text = '';		//The description of the discussion tools.

	//constructor
	function DiscussionTools($title, $text){
		$this->title = $title;
		$this->text = $text;
	}

	function getTitle(){
		return htmlspecialchars(trim($this->title));
	}

	function getText(){
		//change the $IMS-CC-FILEBASE$ to the base of this directory
		//TODO: The returned value may contains HTML, ATutor doesn't check 
		//		if it contains malicious javascript at this point.
		$this->text = preg_replace('/\$IMS\-CC\-FILEBASE\$/', '', $this->text);
		return trim(html_entity_decode($this->text));
	}
}
?>