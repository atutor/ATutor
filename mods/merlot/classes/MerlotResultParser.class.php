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
// $Id: MerlotResultParser.class.php 7208 2008-02-08 16:07:24Z cindy $

/**
* MerlotResultParser
* Class for parsing XML result returned from merlot search
* @access	public
* @author	Cindy Qi Li
* @package Merlot Module
*/
class MerlotResultParser {

	// all private
	var $parser; // the XML handler
	var $result_rows = array(); // the module data
	var $character_data; // tmp variable for storing the data
	var $element_path; // array of element paths (basically a stack)
	var $row_num;
	var $history_num;

	function MerlotResultParser() {
		$this->parser = xml_parser_create(''); 

		xml_set_object($this->parser, $this);
		xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, false); /* conform to W3C specs */
		xml_set_element_handler($this->parser, 'startElement', 'endElement');
		xml_set_character_data_handler($this->parser, 'characterData');
	}

	// public
	function parse($xml_data) {
		$this->element_path   = array();
		$this->result_rows  = array();
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
		if ($this->element_path == array('merlotMaterialSearchWebService', 'status')) 
		{
			$this->result_rows['status'] = trim($this->character_data);
		} 
		else if ($this->element_path == array('merlotMaterialSearchWebService', 'error', 'message')) 
		{
			$this->result_rows['error'] = trim($this->character_data);
		} 
		else if ($this->element_path == array('merlotMaterialSearchWebService', 'summary', 'totalCount')) 
		{
			$this->result_rows['summary']['totalCount'] = trim($this->character_data);
		} 
		else if ($this->element_path == array('merlotMaterialSearchWebService', 'summary', 'resultCount'))
		{
			$this->result_rows['summary']['resultCount'] = trim($this->character_data);
		} 
		else if ($this->element_path == array('merlotMaterialSearchWebService', 'summary', 'lastRecNumber'))
		{
			$this->result_rows['summary']['lastRecNumber'] = trim($this->character_data);
		} 
		else if ($this->element_path === array('merlotMaterialSearchWebService', 'results', 'material', 'title')) 
		{
			$this->result_rows[$this->row_num]['title'] = trim($this->character_data);
		} 
		else if ($this->element_path === array('merlotMaterialSearchWebService', 'results', 'material', 'URL')) 
		{
			$this->result_rows[$this->row_num]['URL'] = trim($this->character_data);
		} 
		else if ($this->element_path === array('merlotMaterialSearchWebService', 'results', 'material', 'authorName')) 
		{
			$this->result_rows[$this->row_num]['authorName'] = trim($this->character_data);
		} 
		else if ($this->element_path === array('merlotMaterialSearchWebService', 'results', 'material', 'creationDate')) 
		{
			$this->result_rows[$this->row_num]['creationDate'] = trim($this->character_data);
		} 
		else if ($this->element_path === array('merlotMaterialSearchWebService', 'results', 'material', 'description')) 
		{
			$this->result_rows[$this->row_num]['description'] = trim($this->character_data);
		} 
		else if ($this->element_path === array('merlotMaterialSearchWebService', 'results', 'material', 'detailURL')) 
		{
			$this->result_rows[$this->row_num]['detailURL'] = trim($this->character_data);
		} 
		else if ($this->element_path === array('merlotMaterialSearchWebService', 'results', 'material', 'creativeCommons')) 
		{
			$this->result_rows[$this->row_num]['creativeCommons'] = trim($this->character_data);
		} 
		else if ($this->element_path === array('merlotMaterialSearchWebService', 'results', 'material')) 
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
	function getNumOfResults() 
	{
		return count($this->result_rows);
	}

	// public
	function getParsedArray() 
	{
		return $this->result_rows;
	}
}

?>