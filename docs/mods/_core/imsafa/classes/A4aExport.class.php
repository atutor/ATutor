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
// $Id: A4aExport.class.php 8804 2009-09-29 21:17:00Z hwong $

require_once(AT_INCLUDE_PATH.'../mods/_core/imsafa/classes/A4a.class.php');
//require(AT_INCLUDE_PATH.'classes/zipfile.class.php'); // for zipfile

/**
 * Accessforall Export class.
 * Based on the specification at: 
 *		http://www.imsglobal.org/accessibility/index.html
 *
 * @date	Oct 3rd, 2008
 * @author	Harris Wong
 */
class A4aExport extends A4a {
	var $original_files = array();	//store all the primary/original resources in [id]=>[incrementer]=>[resource, language, type]
//	var $alternative_files = array();	//secondary files aka. alternative, equivalent

	/**
	 * Get information for this content
	 * @return the xml content
	 */	
	function getAlternative(){
		$resources = parent::getPrimaryResources();
		foreach($resources as $rid => $prop){
			$resources_types = parent::getPrimaryResourcesTypes($rid);
			$temp = array();
			$secondary_array = array();
			foreach($resources_types as $rtid){
				$sec_resources['secondary_resources'] = parent::getSecondaryResources($rid);
				//determine secondary resource type
				foreach ($sec_resources['secondary_resources'] as $sec_id => $sec_resource){
					$current_sec_file = $sec_resource['resource'];
					$secondary_array['secondary_resources'][] = $current_sec_file ;
					//add to secondary file array if it's not there
					if (!isset($this->original_files[$current_sec_file]) || empty($this->original_files[$current_sec_file]) ){
						//TODO merge these values i think
						$this->original_files[$current_sec_file ] = $sec_resource;
						$this->original_files[$current_sec_file ]['resource_type'][$prop['resource']][] = parent::getSecondaryResourcesTypes($sec_id);
					} else {
						$this->original_files[$current_sec_file ]['resource_type'][$prop['resource']][] = parent::getSecondaryResourcesTypes($sec_id);
					}
					//add this primary file ref, and the resources type to the secondary file
					$this->original_files[$current_sec_file]['primary_resources'][$prop['resource']] = $rtid;
//					$this->original_files[$current_sec_file]['primary_resources'][$prop['resource']]['language_code'] = $sec_resource['language_code'];
				}
			}
			$res_type['resource_type'] = $rtid;	//could be 1+
			$temp = array_merge($prop, $res_type, $secondary_array);

			if (isset($this->original_files[$temp['resource']])){
				//use the existing temp array values, but merge in the secondary_array
				$temp = array_merge($this->original_files[$temp['resource']], $secondary_array);
			} 
			if(!empty($temp)){
				$this->original_files[$temp['resource']] = $temp;
	//			debug($this->original_files['7dolomiti_1a_como1e_como_road1b.jpg'], $rid);
			}
		}
		return $this->original_files;
	}

	/**
	 * Get all secondary files
	 * @return array of secondary files that is being used in this->content.
	 */
	function getAllSecondaryFiles(){
		global $db;
		$secondary_files = array();

		$sql = "SELECT DISTINCT secondary_resource FROM ".TABLE_PREFIX."primary_resources a LEFT JOIN ".TABLE_PREFIX."secondary_resources s
				ON a.primary_resource_id = s.primary_resource_id WHERE content_id=".$this->cid;
		$result = mysql_query ($sql);
		if ($result){
			while ($row = mysql_fetch_assoc($result)){
				if (!empty($row['secondary_resource'])){
					$secondary_files[] = $row['secondary_resource'];
				}
			}
		}
		return $secondary_files;
	}

	// Save all the xml into an array. 
	// key=filename, value=xml content
	function exportA4a(){
		global $savant;

		$xml_array = array();	//array of xml

		// Get the alt content first.
		$this->getAlternative();

		// Get original files' xml 
		foreach($this->original_files as $id=>$resource){
			$orig_access_mode = array();
			foreach($resource['resource_type'] as $type_id){
				if (!is_array($type_id)){
					//primary resource will always have just on type
					$orig_access_mode[] = $this->getResourceNameById($type_id);
				} else {
					foreach($type_id as $k=>$type_id2){
						$orig_access_mode[] = $this->getResourceNameById($type_id2[0]);
					}
				}
			}
			$savant->assign('relative_path', $this->relative_path);	//the template will need the relative path
			$savant->assign('orig_access_mode', $orig_access_mode);
			$savant->assign('language_code', $resource['language_code']);
			$savant->assign('secondary_resources', $resource['secondary_resources']);

			// If this is an alternative, and it is mapping to 
			// 1+ original files.  Each of these mapping requires
			// its own xml
			if(isset($resource['primary_resources'])){
				foreach($resource['primary_resources'] as $uri=>$pri_resource_types){
					$savant->assign('primary_resource_uri', $uri);
					$savant->assign('primary_resources', $pri_resource_types);
					//A file can be both original and alternative, and each could represent diff language
					//Tried to resolve but the A4a v.2 only accept 1 language
//					$savant->assign('language_code', $pri_resource_types['language_code']);
					//overwrite orig_access_mode					
					$orig_access_mode = array(); //reinitialize
					foreach($resource['resource_type'][$uri] as $type_id){
						$orig_access_mode[] = $this->getResourceNameById($type_id);
						$savant->assign('orig_access_mode', $orig_access_mode);
						$xml_array[$id.' to '.$uri][] = $savant->fetch(AT_INCLUDE_PATH.'../mods/_core/imsafa/classes/A4a.tmpl.php');
					}
					
				}
			} else {
				$savant->assign('primary_resource_uri', '');
				$savant->assign('primary_resources', '');
				$xml_array[$id] = $savant->fetch(AT_INCLUDE_PATH.'../mods/_core/imsafa/classes/A4a.tmpl.php');
			}
		}
		return $xml_array;
	}

	/**
	 * Get resource name by id
	 * @return	array
	 */
	function getResourceNameById($type_id){
		$orig_access_mode = '';
		if (is_array($type_id)) {
			$type_id = $type_id[0];
		}
		switch($type_id){
			case 1:
				$orig_access_mode = 'auditory';
				break;
			case 3:
				$orig_access_mode = 'textual';
				break;
			case 2:
				$orig_access_mode = 'sign_language';
				break;
			case 4:
				$orig_access_mode = 'visual';
				break;
			default:
				$orig_access_mode = '';
		}
		return $orig_access_mode;
	}

}

?>