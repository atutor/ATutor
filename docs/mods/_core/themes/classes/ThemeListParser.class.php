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
// $Id: ThemeListParser.class.php 7208 2008-02-08 16:07:24Z cindy $

/**
* ThemeListParser
* Class for parsing XML theme list info
* @access	public
* @author	Cindy Qi Li
* @package Admin Theme
*/
class ThemeListParser {

	// all private
	var $parser; // the XML handler
	var $theme_rows = array(); // the theme data
	var $character_data; // tmp variable for storing the data
	var $element_path; // array of element paths (basically a stack)
	var $row_num;
	var $history_num;

	function ThemeListParser() {
		$this->parser = xml_parser_create(''); 

		xml_set_object($this->parser, $this);
		xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, false); /* conform to W3C specs */
		xml_set_element_handler($this->parser, 'startElement', 'endElement');
		xml_set_character_data_handler($this->parser, 'characterData');
	}

	// public
	function parse($xml_data) {
		$this->element_path   = array();
		$this->theme_rows  = array();
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
		if ($this->element_path == array('theme_list', 'theme', 'name')) 
		{
			$this->theme_rows[$this->row_num]['name'] = trim($this->character_data);
		} 
		else if ($this->element_path === array('theme_list', 'theme', 'atutor_version')) 
		{
			$this->theme_rows[$this->row_num]['atutor_version'] = trim($this->character_data);
		} 
		else if ($this->element_path === array('theme_list', 'theme', 'description')) 
		{
			$this->theme_rows[$this->row_num]['description'] = trim($this->character_data);
		} 
		else if ($this->element_path === array('theme_list', 'theme', 'history')) 
		{
			$this->history_num = 0;
		} 
		else if ($this->element_path === array('theme_list', 'theme', 'history', 'release')) 
		{
			$this->history_num++;
		} 
		else if ($this->element_path === array('theme_list', 'theme', 'history', 'release', 'version')) 
		{
			$this->theme_rows[$this->row_num]['history'][$this->history_num]['version'] = trim($this->character_data);
		} 
		else if ($this->element_path === array('theme_list', 'theme', 'history', 'release', 'atutor_version')) 
		{
			$this->theme_rows[$this->row_num]['history'][$this->history_num]['atutor_version'] = trim($this->character_data);
		} 
		else if ($this->element_path === array('theme_list', 'theme', 'history', 'release', 'filename')) 
		{
			$this->theme_rows[$this->row_num]['history'][$this->history_num]['filename'] = trim($this->character_data);
		} 
		else if ($this->element_path === array('theme_list', 'theme', 'history', 'release', 'location')) 
		{
			$this->theme_rows[$this->row_num]['history'][$this->history_num]['location'] = trim($this->character_data);
		} 
		else if ($this->element_path === array('theme_list', 'theme', 'history', 'release', 'screenshot_file')) 
		{
			$this->theme_rows[$this->row_num]['history'][$this->history_num]['screenshot_file'] = trim($this->character_data);
		} 
		else if ($this->element_path === array('theme_list', 'theme', 'history', 'release', 'install_folder')) 
		{
			$this->theme_rows[$this->row_num]['history'][$this->history_num]['install_folder'] = trim($this->character_data);
		} 
		else if ($this->element_path === array('theme_list', 'theme', 'history', 'release', 'date')) 
		{
			$this->theme_rows[$this->row_num]['history'][$this->history_num]['date'] = trim($this->character_data);
		} 
		else if ($this->element_path === array('theme_list', 'theme', 'history', 'release', 'state')) 
		{
			$this->theme_rows[$this->row_num]['history'][$this->history_num]['state'] = trim($this->character_data);
		} 
		else if ($this->element_path === array('theme_list', 'theme', 'history', 'release', 'maintainer')) 
		{
			$this->theme_rows[$this->row_num]['history'][$this->history_num]['maintainer'] = trim($this->character_data);
		} 
		else if ($this->element_path === array('theme_list', 'theme', 'history', 'release', 'notes')) 
		{
			$this->theme_rows[$this->row_num]['history'][$this->history_num]['notes'] = trim($this->character_data);
		} 
		else if ($this->element_path === array('theme_list', 'theme')) 
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
	function getNumOfThemes() 
	{
		return count($this->theme_rows);
	}

	// public
	function getParsedArray() 
	{
		return $this->theme_rows;
	}
}

?>