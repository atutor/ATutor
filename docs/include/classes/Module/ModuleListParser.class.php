<?php
/************************************************************************/
/* ATutor                                                               */
/************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto          */
/* http://atutor.ca                                                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id: ModuleListParser.class.php 7208 2008-02-08 16:07:24Z cindy $

/**
* ModuleListParser
* Class for parsing XML module list info
* @access	public
* @author	Cindy Qi Li
* @package Admin Module
*/
class ModuleListParser {

	// all private
	var $parser; // the XML handler
	var $module_rows = array(); // the module data
	var $character_data; // tmp variable for storing the data
	var $element_path; // array of element paths (basically a stack)
	var $row_num;
	var $history_num;

	function ModuleListParser() {
		$this->parser = xml_parser_create(''); 

		xml_set_object($this->parser, $this);
		xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, false); /* conform to W3C specs */
		xml_set_element_handler($this->parser, 'startElement', 'endElement');
		xml_set_character_data_handler($this->parser, 'characterData');
	}

	// public
	function parse($xml_data) {
		$this->element_path   = array();
		$this->module_rows  = array();
		$this->character_data = '';
		$this->row_num        = 0;
		$this->history_num    = 0;
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
		if ($this->element_path == array('module_list', 'module', 'name')) 
		{
			$this->module_rows[$this->row_num]['name'] = trim($this->character_data);
		} 
		else if ($this->element_path === array('module_list', 'module', 'description')) 
		{
			$this->module_rows[$this->row_num]['description'] = trim($this->character_data);
		} 
		else if ($this->element_path === array('module_list', 'module', 'atutor_version_to_work_with')) 
		{
			$this->module_rows[$this->row_num]['atutor_version_to_work_with'] = trim($this->character_data);
		} 
		else if ($this->element_path === array('module_list', 'module', 'atutor_version_tested_with')) 
		{
			$this->module_rows[$this->row_num]['atutor_version_tested_with'] = trim($this->character_data);
		} 
		else if ($this->element_path === array('module_list', 'module', 'history')) 
		{
			$this->history_num = 0;
		} 
		else if ($this->element_path === array('module_list', 'module', 'history', 'release')) 
		{
			$this->history_num++;
		} 
		else if ($this->element_path === array('module_list', 'module', 'history', 'release', 'version')) 
		{
			$this->module_rows[$this->row_num]['history'][$this->history_num]['version'] = trim($this->character_data);
		} 
		else if ($this->element_path === array('module_list', 'module', 'history', 'release', 'filename')) 
		{
			$this->module_rows[$this->row_num]['history'][$this->history_num]['filename'] = trim($this->character_data);
		} 
		else if ($this->element_path === array('module_list', 'module', 'history', 'release', 'location')) 
		{
			$this->module_rows[$this->row_num]['history'][$this->history_num]['location'] = trim($this->character_data);
		} 
		else if ($this->element_path === array('module_list', 'module', 'history', 'release', 'install_folder')) 
		{
			$this->module_rows[$this->row_num]['history'][$this->history_num]['install_folder'] = trim($this->character_data);
		} 
		else if ($this->element_path === array('module_list', 'module', 'history', 'release', 'date')) 
		{
			$this->module_rows[$this->row_num]['history'][$this->history_num]['date'] = trim($this->character_data);
		} 
		else if ($this->element_path === array('module_list', 'module', 'history', 'release', 'state')) 
		{
			$this->module_rows[$this->row_num]['history'][$this->history_num]['state'] = trim($this->character_data);
		} 
		else if ($this->element_path === array('module_list', 'module', 'history', 'release', 'maintainer')) 
		{
			$this->module_rows[$this->row_num]['history'][$this->history_num]['maintainer'] = trim($this->character_data);
		} 
		else if ($this->element_path === array('module_list', 'module', 'history', 'release', 'notes')) 
		{
			$this->module_rows[$this->row_num]['history'][$this->history_num]['notes'] = trim($this->character_data);
		} 
		else if ($this->element_path === array('module_list', 'module')) 
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
	function getNumOfModules() 
	{
		return count($this->module_rows);
	}

	// public
	function getParsedArray() 
	{
		return $this->module_rows;
	}
}

?>