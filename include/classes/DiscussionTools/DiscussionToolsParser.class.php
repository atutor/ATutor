<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2009										*/
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: DiscussionToolsParser.class.php 8894 2009-11-09 20:03:12Z hwong $
include('DiscussionTools.class.php');

/**
 * A class for DiscussionToolsParser
 * based on:
 *  http://www.imsglobal.org/profile/cc/ccv1p0/derived_schema/domainProfile_5/imsdt_v1p0_localised.xsd
 */
class DiscussionToolsParser {
	//global variables
	//private
	var $parser; // the XML handler
	var $character_data; // tmp variable for storing the data
	var $element_path; // array of element paths (basically a stack)
	var $title;	//link's title
	var $text; //description

	//constructor
	function DiscussionToolsParser(){
		$this->parser = xml_parser_create(); 

		xml_set_object($this->parser, $this);
		xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, false); /* conform to W3C specs */
		xml_set_element_handler($this->parser, 'startElement', 'endElement');
		xml_set_character_data_handler($this->parser, 'characterData');
	}

	// public
	// @return	true if parsed successfully, false otherwise
	function parse($xml_data) {
		$this->element_path   = array();
		$this->character_data = '';
		xml_parse($this->parser, $xml_data, TRUE);		
	}

	// private
	function startElement($parser, $name, $attributes) {
		//save attributes.
		switch($name) {
			case 'text':
				$this->text_type = $attributes['texttype'];
				break;
		}
		array_push($this->element_path, $name);
   }

	// private
	/* called when an element ends */
	/* removed the current element from the $path */
	function endElement($parser, $name) {
		switch($name) {
			case 'title':
				$this->title = $this->character_data;
				break;
			case 'text':
				$this->text = $this->character_data;
				break;
		}

		//pop stack and reset character data, o/w it will stack up
		array_pop($this->element_path);
		$this->character_data = '';
	}

	// private	
   	function characterData($parser, $data){
		global $addslashes;
		if (trim($data)!=''){
			$this->character_data .= preg_replace('/[\t\0\x0B(\r\n)]*/', '', $data);
//			$this->character_data .= trim($data);
		}
	}

	//public
	function close(){
		//Free the XML parser
		xml_parser_free($this->parser);
	}

	//get title
	function getDT(){
		return new DiscussionTools($this->title, $this->text);
	}
}
?>