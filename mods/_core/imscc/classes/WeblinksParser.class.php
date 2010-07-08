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
// $Id: WeblinksParser.class.php 8782 2009-09-04 17:33:07Z hwong $

/**
* WeblinksParser
* Class for parsing XML language info and returning a Weblink Object
* @access	public
* @author	Harris Wong
*/
class WeblinksParser {
	//private
	var $parser; // the XML handler
	var $character_data; // tmp variable for storing the data
	var $element_path; // array of element paths (basically a stack)
	var $title;	//link's title
	var $url; //url

	//constructor
	function WeblinksParser() {
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
			case 'url':
				$this->url = $attributes['href'];
				break;
		}
		array_push($this->element_path, $name);
   }

	// private
	/* called when an element ends */
	/* removed the current element from the $path */
	function endElement($parser, $name) {
		//check element path
		$current_pos = count($this->element_path) - 1;
		$last_element = $this->element_path[$current_pos - 1];

		switch($name) {
			case 'title':
				$this->title = $this->character_data;
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

	//gets
	function getTitle(){
		return $this->title;
	}
	function getUrl(){
		return $this->url;
	}
}

?>
