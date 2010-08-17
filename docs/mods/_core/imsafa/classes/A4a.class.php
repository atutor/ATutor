<?php
/********************************************************************/
/* ATutor															*/
/********************************************************************/
/* Copyright (c) 2002-2010                                          */
/* Inclusive Design Institute                                       */
/* http://atutor.ca													*/
/*																	*/
/* This program is free software. You can redistribute it and/or	*/
/* modify it under the terms of the GNU General Public License		*/
/* as published by the Free Software Foundation.					*/
/********************************************************************/
// $Id$


/**
 * Accessforall General class.
 * Based on the specification at: 
 *		http://www.imsglobal.org/accessibility/index.html
 *
 * @date	Oct 3rd, 2008
 * @author	Harris Wong
 */
class A4a {
	//variables
	var $cid = 0;						//content id
	var $resource_types = array();		//resource types hash mapping
	var $relative_path = '';			//relative path to the file 

	//Constructor
	function A4a($cid){
		$this->cid = intval($cid);
	}


	// Return resources type hash mapping.
	function getResourcesType($type_id=0){
		global $db;

		$type_id = intval($type_id);

		//if this is the first time calling this function, grab the list from db
		if (empty($resource_types)){
			$sql = 'SELECT * FROM '.TABLE_PREFIX.'resource_types';
			$result = mysql_query($sql, $db);
			while ($row = mysql_fetch_assoc($result)){
				$this->resource_types[$row['type_id']] = $row['type'];
			}
		}

		if (!empty($this->resource_types[$type_id])){
			return $this->resource_types[$type_id];		
		}
		return $this->resource_types;
	}

	
	// Get primary resources
	function getPrimaryResources(){
		global $db;

		$pri_resources = array(); // cid=>[resource, language code]
		$sql = 'SELECT * FROM '.TABLE_PREFIX.'primary_resources WHERE content_id='.$this->cid;
		$result = mysql_query($sql, $db);
		if (mysql_numrows($result) > 0){
			while ($row = mysql_fetch_assoc($result)){
				$pri_resources[$row['primary_resource_id']]['resource'] = $row['resource'];
				if ($row['language_code'] != ''){
					$pri_resources[$row['primary_resource_id']]['language_code'] = $row['language_code'];
				}
			}
		}
		return $pri_resources;
	}


	// Get primary resources types
	function getPrimaryResourcesTypes($pri_resource_id=0){
		global $db;

		$pri_resource_id = intval($pri_resource_id);

		//quit if id not specified
		if ($pri_resource_id == 0) {
			return array();
		}

		$pri_resources_types = array();	// cid=>[type id]+
		$sql = 'SELECT * FROM '.TABLE_PREFIX.'primary_resources_types WHERE primary_resource_id='.$pri_resource_id;
		$result = mysql_query($sql, $db);
		if ($result){
			while ($row = mysql_fetch_assoc($result)){
				$pri_resources_types[$pri_resource_id][] = $row['type_id'];
			}
		}
		return $pri_resources_types;
	}


	// Get secondary resources 
	function getSecondaryResources($pri_resource_id=0){
		global $db;

		$pri_resource_id = intval($pri_resource_id);

		//quit if id not specified
		if ($pri_resource_id == 0) {
			return array();
		}

		$sec_resources = array(); // cid=>[resource, language code]
		$sql = 'SELECT * FROM '.TABLE_PREFIX.'secondary_resources WHERE primary_resource_id='.$pri_resource_id;
		$result = mysql_query($sql, $db);
		if ($result){
			while ($row = mysql_fetch_assoc($result)){
				$sec_resources[$row['secondary_resource_id']]['resource'] = $row['secondary_resource'];
				if ($row['language_code'] != ''){
					$sec_resources[$row['secondary_resource_id']]['language_code'] = $row['language_code'];
				}
			}
		}
		return $sec_resources;
	}


	// Get secondary resources types
	function getSecondaryResourcesTypes($sec_resource_id=0){
		global $db;

		$sec_resource_id = intval($sec_resource_id);

		//quit if id not specified
		if ($sec_resource_id == 0) {
			return array();
		}

		$sec_resources_types = array();	// cid=>[type id]+
		$sql = 'SELECT * FROM '.TABLE_PREFIX.'secondary_resources_types WHERE secondary_resource_id='.$sec_resource_id;
		$result = mysql_query($sql, $db);
		if ($result){
			while ($row = mysql_fetch_assoc($result)){
				$sec_resources_types[] = $row['type_id'];
			}
		}
		return $sec_resources_types;
	}


	// Insert primary resources into the db
	// @return primary resource id.
	function setPrimaryResource($content_id, $file_name, $lang){
		global $addslashes, $db; 

		$content_id = intval($content_id);
		$file_name = $addslashes($file_name);
		$lang = $addslashes($lang);

		$sql = "INSERT INTO ".TABLE_PREFIX."primary_resources SET content_id=$content_id, resource='$file_name', language_code='$lang'";
		$result = mysql_query($sql, $db);
		if ($result){
			return mysql_insert_id();
		}
		return false;
	}

	// Insert primary resource type
	function setPrimaryResourceType($primary_resource_id, $type_id){
		global $db; 

		$primary_resource_id= intval($primary_resource_id);
		$type_id = intval($type_id);

		$sql = "INSERT INTO ".TABLE_PREFIX."primary_resources_types SET primary_resource_id=$primary_resource_id, type_id=$type_id";
		$result = mysql_query($sql, $db);
	}

	// Insert secondary resource
	// @return secondary resource id
	function setSecondaryResource($primary_resource_id, $file_name, $lang){
		global $addslashes, $db; 

		$primary_resource_id = intval($primary_resource_id);
		$file_name = $addslashes($file_name);
		$lang = $addslashes($lang);

		$sql = "INSERT INTO ".TABLE_PREFIX."secondary_resources SET primary_resource_id=$primary_resource_id, secondary_resource='$file_name', language_code='$lang'";
		$result = mysql_query($sql, $db);
		if ($result){
			return mysql_insert_id();
		}
		return false;
	}

	// Insert secondary resource
	function setSecondaryResourceType($secondary_resource, $type_id){
		global $db;

		$secondary_resource = intval($secondary_resource);
		$type_id = intval($type_id);

		$sql = "INSERT INTO ".TABLE_PREFIX."secondary_resources_types SET secondary_resource_id=$secondary_resource, type_id=$type_id";
		$result = mysql_query($sql, $db);
	}

	
	// Set the relative path to all files
	function setRelativePath($path){
		$this->relative_path = $path . '/';
	}


	// Delete all materials associated with this content
	function deleteA4a(){
		global $db; 

		$pri_resource_ids = array();

		// Get all primary resources ID out that're associated with this content
		$sql = 'SELECT a.primary_resource_id FROM '.TABLE_PREFIX.'primary_resources a LEFT JOIN '.TABLE_PREFIX.'primary_resources_types b ON a.primary_resource_id=b.primary_resource_id WHERE content_id='.$this->cid;
		$result = mysql_query($sql);

		while($row=mysql_fetch_assoc($result)){
			$pri_resource_ids[] = $row['primary_resource_id'];
		}

		//If the are primary resources found
		if (!empty($pri_resource_ids)){
			$glued_pri_ids = implode(",", $pri_resource_ids);

			// Delete all secondary a4a
			$sql = 'DELETE c, d FROM '.TABLE_PREFIX.'secondary_resources c LEFT JOIN '.TABLE_PREFIX.'secondary_resources_types d ON c.secondary_resource_id=d.secondary_resource_id WHERE primary_resource_id IN ('.$glued_pri_ids.')';
			$result = mysql_query($sql);

			// If successful, remove all primary resources
			if ($result){
				$sql = 'DELETE a, b FROM '.TABLE_PREFIX.'primary_resources a LEFT JOIN '.TABLE_PREFIX.'primary_resources_types b ON a.primary_resource_id=b.primary_resource_id WHERE content_id='.$this->cid;
				mysql_query($sql);
			}
		}
	}
}

?>