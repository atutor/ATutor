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
// $Id: ResultParser.class.php 10142 2010-08-17 19:17:26Z hwong $

/**
* ResultParser
* Class for parsing XML result returned from transformable search
* @access	public
* @author	Cindy Qi Li
* @package tile_search Module
*/
class ResultParser {

	// all private
	var $parser; // the XML handler
	var $result_rows = array(); // the module data
	var $character_data; // tmp variable for storing the data
	var $element_path; // array of element paths (basically a stack)
	var $row_num;
	var $history_num;

	function ResultParser() {
		$this->parser = xml_parser_create(''); 

		xml_set_object($this->parser, $this);
		xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, false); /* conform to W3C specs */
		xml_parser_set_option($this->parser,XML_OPTION_TARGET_ENCODING, "ISO-8859-1");
		xml_set_element_handler($this->parser, 'startElement', 'endElement');
		xml_set_character_data_handler($this->parser, 'characterData');
	}

	// public
	function parse($xml_data) {
		$xml_data = trim($xml_data);
		
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
		// parse error message
		if ($this->element_path == array('errors', 'totalCount')) 
		{
			$this->result_rows['status'] = 'fail';
			$this->result_rows['totalCount'] = trim($this->character_data);
		} 
		else if ($this->element_path == array('errors', 'error', 'message')) 
		{
			$this->result_rows['error'][] = trim($this->character_data);
		} 
		
		// parse search results
		else if ($this->element_path == array('resultset', 'summary', 'numOfTotalResults')) 
		{
			$this->result_rows['status'] = 'success';
			$this->result_rows['summary']['numOfTotalResults'] = trim($this->character_data);
		} 
		else if ($this->element_path == array('resultset', 'summary', 'nmOfResults'))
		{
			$this->result_rows['summary']['nmOfResults'] = trim($this->character_data);
		} 
		else if ($this->element_path == array('resultset', 'summary', 'lastResultNumber'))
		{
			$this->result_rows['summary']['lastResultNumber'] = trim($this->character_data);
		} 
		else if ($this->element_path === array('resultset', 'results', 'result', 'courseID')) 
		{
			$this->result_rows[$this->row_num]['courseID'] = trim($this->character_data);
		} 
		else if ($this->element_path === array('resultset', 'results', 'result', 'title')) 
		{
			$this->result_rows[$this->row_num]['title'] = trim($this->character_data);
		} 
//		else if ($this->element_path === array('resultset', 'results', 'result', 'authorName')) 
//		{
//			$this->result_rows[$this->row_num]['authorName'] = trim($this->character_data);
//		} 
		else if ($this->element_path === array('resultset', 'results', 'result', 'creationDate')) 
		{
			$this->result_rows[$this->row_num]['creationDate'] = trim($this->character_data);
		} 
		else if ($this->element_path === array('resultset', 'results', 'result', 'description')) 
		{
			$this->result_rows[$this->row_num]['description'] = trim($this->character_data);
		} 
//		else if ($this->element_path === array('resultset', 'results', 'result', 'detailURL')) 
//		{
//			$this->result_rows[$this->row_num]['detailURL'] = trim($this->character_data);
//		} 
//		else if ($this->element_path === array('resultset', 'results', 'result', 'creativeCommons')) 
//		{
//			$this->result_rows[$this->row_num]['creativeCommons'] = trim($this->character_data);
//		} 
		else if ($this->element_path === array('resultset', 'results', 'result')) 
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