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
// $Id: PatchListParser.class.php 7208 2008-02-08 16:07:24Z greg $

/**
* PatchListParser
* Class for parsing XML patch list info
* @access	public
* @author	Cindy Qi Li
* @package	Patch
*/
class PatchListParser {

	// all private
	var $parser; // the XML handler
	var $patch_rows = array(); // the patch data
	var $character_data; // tmp variable for storing the data
	var $element_path; // array of element paths (basically a stack)
	var $row_num;

	function PatchListParser() {
		$this->parser = xml_parser_create(''); 

		xml_set_object($this->parser, $this);
		xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, false); /* conform to W3C specs */
		xml_set_element_handler($this->parser, 'startElement', 'endElement');
		xml_set_character_data_handler($this->parser, 'characterData');
	}

	// public
	function parse($xml_data) {
		$this->element_path   = array();
		$this->patch_rows  = array();
		$this->character_data = '';
		$this->row_num        = 0;
		xml_parse($this->parser, $xml_data, TRUE);
	}

	// private
	function startElement($parser, $name, $attributes) 
	{
		array_push($this->element_path, $name);
   }

	// private
	/* called when an element ends */
	/* removed the current element from the $path */
	function endElement($parser, $name) {
		if ($this->element_path == array('patch_list', 'patch', 'atutor_patch_id')) 
		{
			$this->patch_rows[$this->row_num]['atutor_patch_id'] = trim($this->character_data);
		} 
		else if ($this->element_path === array('patch_list', 'patch', 'applied_version')) 
		{
			$this->patch_rows[$this->row_num]['applied_version'] = trim($this->character_data);
		} 
		else if ($this->element_path === array('patch_list', 'patch', 'sequence')) 
		{
			$this->patch_rows[$this->row_num]['sequence'] = trim($this->character_data);
		} 
		else if ($this->element_path === array('patch_list', 'patch', 'patch_folder')) 
		{
			$this->patch_rows[$this->row_num]['patch_folder'] = trim($this->character_data);
		} 
		else if ($this->element_path === array('patch_list', 'patch', 'description')) 
		{
			$this->patch_rows[$this->row_num]['description'] = trim($this->character_data);
		} 
		else if ($this->element_path === array('patch_list', 'patch', 'available_to')) 
		{
			$this->patch_rows[$this->row_num]['available_to'] = trim($this->character_data);
		} 
		else if ($this->element_path === array('patch_list', 'patch')) 
		{
			$this->row_num++;
		}

		array_pop($this->element_path);
		$this->character_data = '';
	}

	// private
   	function characterData($parser, $data){
		$this->character_data .= $data;
	}

	// public
	function getNumPathes() 
	{
		return count($this->patch_rows);
	}

	// public
	function getParsedArray() 
	{
		return $this->patch_rows;
	}

	// public
	// return parsed array only for given version 
	// and the array is sorted by sequence
	function getMySortedParsedArrayForVersion($who, $version) 
	{
		$sorted_parsed_array = array();
		$sequence = array();

		// filter out the patch for given version
		foreach ($this->patch_rows as $key => $row) 
		{
	    if ($row['applied_version'] == $version && $row['available_to']==$who)
	    {
	    	array_push($sorted_parsed_array, $row);
	    	array_push($sequence, $row['sequence']);
	    }
		}
		// sort array by patch sequence
		array_multisort($sequence, SORT_ASC, $sorted_parsed_array);
		
		return $sorted_parsed_array;
	}
}



?>