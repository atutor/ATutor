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
 * Accessforall Export class.
 * Based on the specification at: 
 *		http://www.imsglobal.org/accessibility/index.html
 *
 * @date	Oct 3rd, 2008
 * @author	Harris Wong
 */
class A4aExport extends A4a {
	var $export_items = array();	//store all the resources in [id]=>[incrementer]=>[resource, language, type]

	/**
	 * Get information for this content
	 * @return the xml content
	 */	
	function getAlternative(){
		$resources = parent::getPrimaryResources();

		foreach($resources as $rid => $prop){
			$resources_types = parent::getPrimaryResourcesTypes($rid);
			$temp = array();
			foreach($resources_types as $rtid){
				$sec_resources['secondary_resources'] = parent::getSecondaryResources($rid);
				//determine secondary resource type
				foreach ($sec_resources['secondary_resources'] as $sec_id => $sec_resource){
					$sec_resources['secondary_resources'][$sec_id]['resource_type'] = parent::getSecondaryResourcesTypes($sec_id);
				}
				$res_type['primary_resource_type'] = $rtid;	//could be 1+
				$temp = array_merge($prop, $res_type, $sec_resources);
			}
			if (!empty($temp)){
				$this->export_items[] = $temp;
			}
		}
		return $this->export_items;
	}

	/**
	 * Get all secondary files
	 * @return array of secondary files that is being used in this->content.
	 */
	function getAllSecondaryFiles(){
		global $db;
		$secondary_files = array();

		$sql = "SELECT secondary_resource FROM ".TABLE_PREFIX."primary_resources a LEFT JOIN ".TABLE_PREFIX."secondary_resources s
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

	// 
	function exportA4a(){
		global $savant;
		$zipfile = new zipfile();

		// Get the alt content first.
		$this->getAlternative();
		$savant->assign('resources', $this->export_items);

		/* The AccessForAll Meta-data specification is intended to make it possible to identify resources 
		 * that match a user's stated preferences or needs.
		 * It is not clear how to map the primary resources onto the secondary resources(they call it equivalents).  
		 * Moreover, it is also not obvious how to include the different types(visual, auditory, etc) in the 
		 * secondary files.  
		 * @ref: http://www.imsglobal.org/accessibility/index.html
		 *
		 * Have decided to create our own XML file just for import/export as for now.  
		 */
		$xml = $savant->fetch(AT_INCLUDE_PATH.'classes/A4a/a4a.tmpl.php');
//			$zipfile->add_file($xml, 'a4a_'.$this->cid.'_'.$index.'.xml');	
		return $xml;
		//close and send
//		$zipfile->close();
//		$zipfile->send_file('AccessForAll');
		
	}
}

?>