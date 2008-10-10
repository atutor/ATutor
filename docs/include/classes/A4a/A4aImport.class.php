<?php
/********************************************************************/
/* ATutor															*/
/********************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Cindy Qi Li, & Harris Wong	*/
/* Adaptive Technology Resource Centre / University of Toronto		*/
/* http://atutor.ca													*/
/*																	*/
/* This program is free software. You can redistribute it and/or	*/
/* modify it under the terms of the GNU General Public License		*/
/* as published by the Free Software Foundation.					*/
/********************************************************************/
// $Id$

require_once(AT_INCLUDE_PATH.'classes/A4a/A4a.class.php');
//require(AT_INCLUDE_PATH.'classes/zipfile.class.php'); // for zipfile

/**
 * Accessforall Import  class.
 * Based on the specification at: 
 *		http://www.imsglobal.org/accessibility/index.html
 *
 * @date	Oct 9th, 2008
 * @author	Harris Wong
 */
class A4aImport extends A4a {
	var $element_path = array();	//store the current element path
	var $character_data = '';
	var $parser;
	var $items = array();			//stores all XML data.
	var $current_identifier = 0;	//current resource id
	var $secondary_identifier = 0;

	function A4aImport($cid){
		parent::A4a($cid);		//call its parent

		$this->parser = xml_parser_create();
		xml_set_object($this->parser, $this);
		xml_parser_set_option($this->parser , XML_OPTION_CASE_FOLDING, false); /* conform to W3C specs */
		xml_set_element_handler($this->parser , 'startElement', 'endElement');
		xml_set_character_data_handler($this->parser , 'characterData');

//		xml_parser_free($this->parser);
	}

	/**
	 * @param	string	the actual content of the xml, not the link
	 * @private
	 */
	function parse($xml){
		if (!xml_parse($this->parser , $xml, true)) {
			die(sprintf("XML error: %s at line %d, col %d",
						xml_error_string(xml_get_error_code($this->parser)),
						xml_get_current_line_number($this->parser),
						xml_get_current_column_number($this->parser)));
		}
	}

	/* called when an xml element starts */
	function startElement($parser, $name, $attrs){
		$id =& $this->current_identifier;
		$sec_id =& $this->secondary_identifier;

		switch ($name){
			case 'primaryResource':
				$this->items[$id]['primaryResource']['attrs'] = $attrs;
				break;
			case 'secondaryResource':
				$this->items[$id]['secondaryResource'][$sec_id]['attrs'] = $attrs;
				break;
		}

		array_push($this->element_path, $name);
	}

	/* called when an xml element ends */
	/* removed the current element from the $path */
	function endElement($parser, $name) {
		$id =& $this->current_identifier;
		$sec_id =& $this->secondary_identifier;

		switch ($name){
			case 'primaryResource':
				$this->items[$id]['primaryResource']['file'] = $this->character_data;
				break;
			case 'secondaryResource':
				$this->items[$id]['secondaryResource'][$sec_id]['file'] = $this->character_data;
				$sec_id++;	//increment secondary counter once it's closed
				break;
			case 'resource':			
				$sec_id = 0;	//reset secondary counter after a resource is closed
				$id++;			//increment the next entry
				break;
		}
		array_pop($this->element_path);		
		$this->character_data = '';
	}	

	/* called when there is character data within elements */
	/* constructs the $items array using the last entry in $path as the parent element */
	function characterData($parser, $data){
		$this->character_data .= trim($data);
	}


	/** 
	 * Import AccessForAll
	 * @param	string	the xml file
	 */
	function importA4a($xml_file){		
		$xml = file_get_contents($xml_file);	//read file

		$this->parse($xml);	//parse the xml content, and generate the item array.

		//use the items array data and insert it into the database.
		foreach ($this->items as $resource){
			$primary_resource = $resource['primaryResource'];
			$secondary_resources = $resource['secondaryResource'];

			$file_name = $primary_resource['file'];
			$lang = $primary_resource['attrs']['lang'];
			$attrs = $this->toResourceTypeId($primary_resource['attrs']);

			//insert primary resource
			$primary_id = $this->setPrimaryResource($this->cid, $file_name, $lang);

			//insert primary resource type associations
			foreach ($attrs as $resource_type_id){
				$this->setPrimaryResourceType($primary_id, $resource_type_id);
			}
			//insert secondary resource
			foreach ($secondary_resources as $secondary_resource){
				$secondary_file = $secondary_resource['file'];
				$secondary_lang = $secondary_resource['attrs']['lang'];
				$secondary_attr = $this->toResourceTypeId($secondary_resource['attrs']);

				$secondary_id = $this->setSecondaryResource($primary_id, $secondary_file, $secondary_lang);

				//insert secondary resource type associations
				foreach ($secondary_attr as $secondary_resource_type_id){
					$this->setSecondaryResourceType($secondary_id, $secondary_resource_type_id);
				}
			}
		}
	}

	/**
	 * By the given attrs array, decide which resource type it is
	 *	auditory = type 1
	 *	textual	 = type 3
	 *	visual	 = type 4
	 * @param	array
	 * return type id array
	 */
	 function toResourceTypeId($attrs){
		 $result = array();
		 if ($attrs['is_auditory']=='true'){
			 $result[] = 1;
		 }
		 if ($attrs['is_textual']=='true'){
			 $result[] = 3;
		 }
		 if ($attrs['is_visual']=='true'){
			 $result[] = 4;
		 }
		 return $result;
	 }
}

?>