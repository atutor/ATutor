<?php
/************************************************************************/
/* ATutor                                                               */
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca                                                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id: PatchParser.class.php 10142 2010-08-17 19:17:26Z hwong $

/**
* PatchParser
* Class for parsing XML patch info (patch.xml)
* @access	public
* @author	Cindy Qi Li
* @package	Patch
*/
class PatchParser {

	// all private
	var $parser; // the XML handler
	var $patch_row = array(); // the patch data
	var $character_data; // tmp variable for storing the data
	var $element_path; // array of element paths (basically a stack)
	var $file_num;
	var $action_detail_num;
	var $dependent_patches_num;

	function PatchParser() {
		$this->parser = xml_parser_create(''); 

		xml_set_object($this->parser, $this);
		xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, false); /* conform to W3C specs */
		xml_set_element_handler($this->parser, 'startElement', 'endElement');
		xml_set_character_data_handler($this->parser, 'characterData');
	}

	// public
	function parse($xml_data) {
		$this->element_path   = array();
		$this->patch_row  = array();
		$this->character_data = '';
		$this->file_num = 0;
		$this->action_detail_num = 0;
		$this->dependent_patches_num = 0;
		
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
	function endElement($parser, $name)
	{
		if ($this->element_path == array('patch', 'atutor_patch_id')) 
		{
			$this->patch_row['atutor_patch_id'] = trim($this->character_data);
		}
		if ($this->element_path == array('patch', 'applied_version')) 
		{
			$this->patch_row['applied_version'] = trim($this->character_data);
		}
		if ($this->element_path == array('patch', 'sequence')) 
		{
			$this->patch_row['sequence'] = trim($this->character_data);
		}
		if ($this->element_path == array('patch', 'description')) 
		{
			$this->patch_row['description'] = trim($this->character_data);
		}
		if ($this->element_path === array('patch', 'dependent_patches', 'dependent_patch')) 
		{
			$this->patch_row['dependent_patches'][$this->dependent_patches_num++] = trim($this->character_data);
		}
		if ($this->element_path == array('patch', 'sql')) 
		{
			$this->patch_row['sql'] = trim($this->character_data);
		}
		else if ($this->element_path === array('patch', 'files', 'file', 'action')) 
		{
			$this->patch_row['files'][$this->file_num]['action'] = trim($this->character_data);
		} 
		else if ($this->element_path === array('patch', 'files', 'file', 'name')) 
		{
			$this->patch_row['files'][$this->file_num]['name'] = trim($this->character_data);
		} 
		else if ($this->element_path === array('patch', 'files', 'file', 'location')) 
		{
			$this->patch_row['files'][$this->file_num]['location'] = trim($this->character_data);
		} 
		else if ($this->element_path === array('patch', 'files', 'file', 'action_detail', 'type')) 
		{
			$this->patch_row['files'][$this->file_num]['action_detail'][$this->action_detail_num]['type'] = trim($this->character_data);
		} 
		else if ($this->element_path === array('patch', 'files', 'file', 'action_detail', 'code_from')) 
		{
			$this->patch_row['files'][$this->file_num]['action_detail'][$this->action_detail_num]['code_from'] = trim($this->character_data);
		} 
		else if ($this->element_path === array('patch', 'files', 'file', 'action_detail', 'code_to')) 
		{
			$this->patch_row['files'][$this->file_num]['action_detail'][$this->action_detail_num]['code_to'] = trim($this->character_data);
		} 
		else if ($this->element_path === array('patch', 'files', 'file')) 
		{
			$this->file_num++;
		}
		else if ($this->element_path === array('patch', 'files', 'file', 'action_detail')) 
		{
			$this->action_detail_num++;
		}

		array_pop($this->element_path);
		$this->character_data = '';
	}

	// private
  function characterData($parser, $data)
  {
		$this->character_data .= $data;
	}

	// public
	function getParsedArray() 
	{
		return $this->patch_row;
	}
}

?>