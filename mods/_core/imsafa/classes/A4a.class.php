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

		$type_id = intval($type_id);

		//if this is the first time calling this function, grab the list from db
		if (empty($resource_types)){

			$sql = 'SELECT * FROM %sresource_types';
			$rows_types = queryDB($sql, array(TABLE_PREFIX));
			foreach($rows_types as $row){
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

		$pri_resources = array(); // cid=>[resource, language code]

		$sql = 'SELECT * FROM %sprimary_resources WHERE content_id=%d ORDER BY primary_resource_id';
		$rows_primary_resouces = queryDB($sql, array(TABLE_PREFIX, $this->cid));

		if (count($rows_primary_resouces) > 0){
			foreach($rows_primary_resouces as $row){
				$pri_resources[$row['primary_resource_id']]['resource'] = $row['resource'];
				if ($row['language_code'] != ''){
					$pri_resources[$row['primary_resource_id']]['language_code'] = $row['language_code'];
				}
			}
		}
		return $pri_resources;
	}


	// Get primary resources by resource name
	function getPrimaryResourceByName($primary_resource){

				$sql = "SELECT * FROM %sprimary_resources 
		        WHERE content_id=%d
		          AND language_code = '%s'
		          AND resource='%s'";
		$rows_primary = queryDB($sql, array(TABLE_PREFIX, $this->cid, $_SESSION['lang'], $primary_resource));
		
		if(count($rows_primary) > 0){
			return $rows_primary;
		} else {
			return false;
		}
	}


	// Get primary resources types
	function getPrimaryResourcesTypes($pri_resource_id=0){

		$pri_resource_id = intval($pri_resource_id);

		//quit if id not specified
		if ($pri_resource_id == 0) {
			return array();
		}

		$pri_resources_types = array();	// cid=>[type id]+

		$sql = 'SELECT * FROM %sprimary_resources_types WHERE primary_resource_id=%d';
		$rows_primary = queryDB($sql, array(TABLE_PREFIX, $pri_resource_id));

		if(count($rows_primary) > 0){
		    foreach($rows_primary as $row){
				$pri_resources_types[$pri_resource_id][] = $row['type_id'];
			}
		}
		return $pri_resources_types;
	}


	// Get secondary resources 
	function getSecondaryResources($pri_resource_id=0){

		$pri_resource_id = intval($pri_resource_id);

		//quit if id not specified
		if ($pri_resource_id == 0) {
			return array();
		}

		$sec_resources = array(); // cid=>[resource, language code]

		$sql = 'SELECT * FROM %ssecondary_resources WHERE primary_resource_id=%d';
		$rows_secondary = queryDB($sql, array(TABLE_PREFIX, $pri_resource_id));

		if(count($rows_secondary) > 0){
			foreach($rows_secondary as $row){
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

		$sec_resource_id = intval($sec_resource_id);

		//quit if id not specified
		if ($sec_resource_id == 0) {
			return array();
		}

		$sec_resources_types = array();	// cid=>[type id]+

		$sql = 'SELECT * FROM %ssecondary_resources_types WHERE secondary_resource_id=%d';
		$rows_second_types = queryDB($sql, array(TABLE_PREFIX, $sec_resource_id));
		
		if(count($rows_second_types) > 0){
		    foreach($rows_second_types as $row){
				$sec_resources_types[] = $row['type_id'];
			}
		}
		return $sec_resources_types;
	}


	// Insert primary resources into the db
	// @return primary resource id.
	function setPrimaryResource($content_id, $file_name, $lang){
		global $addslashes; 

		$content_id = intval($content_id);
		$file_name = $addslashes(convert_amp($file_name));
		$lang = $addslashes($lang);

		$sql = "INSERT INTO %sprimary_resources SET content_id=%d, resource='%s', language_code='%s'";
		$result = queryDB($sql, array(TABLE_PREFIX, $content_id, $file_name, $lang));	
		
		if($result > 0){
			return at_insert_id();
		}
		return false;
	}

	// Insert primary resource type
	function setPrimaryResourceType($primary_resource_id, $type_id){

		$primary_resource_id= intval($primary_resource_id);
		$type_id = intval($type_id);

		$sql = "INSERT INTO %sprimary_resources_types SET primary_resource_id=%d, type_id=%d";
		$result = queryDB($sql, array(TABLE_PREFIX, $primary_resource_id, $type_id));

	}

	// Insert secondary resource
	// @return secondary resource id
	function setSecondaryResource($primary_resource_id, $file_name, $lang){
		global $addslashes; 

		$primary_resource_id = intval($primary_resource_id);
		$file_name = $addslashes(convert_amp($file_name));
		$lang = $addslashes($lang);

		$sql = "INSERT INTO %ssecondary_resources SET primary_resource_id=%d, secondary_resource='%s', language_code='%s'";
		$result = queryDB($sql, array(TABLE_PREFIX, $primary_resource_id, $file_name, $lang));

		if ($result > 0){
			return at_insert_id();
		}
		return false;
	}

	// Insert secondary resource
	function setSecondaryResourceType($secondary_resource, $type_id){
	
		$secondary_resource = intval($secondary_resource);
		$type_id = intval($type_id);

		$sql = "INSERT INTO %ssecondary_resources_types SET secondary_resource_id=%d, type_id=%d";
		$result = queryDB($sql, array(TABLE_PREFIX, $secondary_resource, $type_id));
	}

	
	// Set the relative path to all files
	function setRelativePath($path){
		$this->relative_path = $path . '/';
	}


    /**
     * Delete this primary resource and all its associated secondary resources
     * @param   int     primary resournce id
     */
    function deletePrimaryResource($primary_rid){
        // Delete all secondary a4a
        $sql = "DELETE c, d FROM %ssecondary_resources c LEFT JOIN %ssecondary_resources_types d ON c.secondary_resource_id=d.secondary_resource_id WHERE primary_resource_id=%d";
        $result = queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX, $primary_rid));        
        // If successful, remove all primary resources
        if ($result > 0){

            $sql = "DELETE a, b FROM %sprimary_resources a LEFT JOIN %sprimary_resources_types b ON a.primary_resource_id=b.primary_resource_id WHERE a.primary_resource_id=%d";
            queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX, $primary_rid));            
        }
    }

	// Delete all materials associated with this content
	function deleteA4a(){

		$pri_resource_ids = array();

		// Get all primary resources ID out that're associated with this content
		$sql = 'SELECT a.primary_resource_id FROM %sprimary_resources a LEFT JOIN %sprimary_resources_types b ON a.primary_resource_id=b.primary_resource_id WHERE content_id=%d';
		$rows_primary = queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX, $this->cid));
		
		foreach($rows_primary as $row){
			$pri_resource_ids[] = $row['primary_resource_id'];
		}

		//If the are primary resources found
		if (!empty($pri_resource_ids)){
			$glued_pri_ids = implode(",", $pri_resource_ids);

			// Delete all secondary a4a
			$sql = 'DELETE c, d FROM %ssecondary_resources c LEFT JOIN %ssecondary_resources_types d ON c.secondary_resource_id=d.secondary_resource_id WHERE primary_resource_id IN (%s)';
			$result = queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX, $glued_pri_ids));
			// If successful, remove all primary resources
			if ($result > 0){
				$sql = 'DELETE a, b FROM %sprimary_resources a LEFT JOIN %sprimary_resources_types b ON a.primary_resource_id=b.primary_resource_id WHERE content_id=%d';
				queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX, $this->cid));
			}
		}
	}
}

?>