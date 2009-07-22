<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

/**
* It provides a link to a resource, which opens such a resource in a new window.
* @access	public
* @param	$resource: 			the name of the file.
* @return	
* @see		
* @author	Silvia Mirri
*/
function link_name_resource($resource){
	echo '<a href="'.$resource.'" target="_blank">'.$resource.'</a>';
}
 
/**
* It provides in the page the appropriated checkbox, in order to allow type resource and type alternative specification.
* @access	public
* @param	$resource_id:		the id of the resource.
* @param	$alt: 				it identifies if the resource is a primary or an alternative one.
* @param	$kind:		 		it identifies the resource type: if the resource is textual or not.
* @return	
* @see		$db			        in include/vitals.inc.php
* @author	Silvia Mirri
*/
function checkbox_types($resource_id, $alt, $kind){
	global $db;
	
	$legend = $alt.'_resource_type';
	echo '<fieldset>';
	echo '<legend>'. _AT($legend).'</legend>';

	$sql_types		= "SELECT * FROM ".TABLE_PREFIX."resource_types";
	$types			= mysql_query($sql_types, $db);

	$sql_set_types	= "SELECT type_id FROM ".TABLE_PREFIX.$alt."_resources_types where ".$alt."_resource_id=".$resource_id;
	$set_types		= mysql_query($sql_set_types, $db);
	
	$resource_types	= false;
	$j 				= 0;
	
	if (mysql_num_rows($set_types) > 0){
		while ($set_type = mysql_fetch_assoc($set_types)) {
			$resource_types[$j] = $set_type[type_id];
			$j++;
		}
	}
	else echo '<p class="unsaved">'. _AT('define_resource_type').'</p>';
	
	while ($type = mysql_fetch_assoc($types)) {
		if ((($alt == 'primary')) && ($kind == 'non_textual') && (($type['type'] == 'sign_language')))
			continue;
		else {
			echo '<input type="checkbox" name="checkbox_'.$type['type'].'_'.$resource_id.'_'.$alt.'" value="'.$type['type'].'_'.$resource_id.'_'.$alt.'" id="'.$type['type'].'_'.$resource_id.'_'.$alt.'"';
		
			$m = count($resource_types);
			for ($j=0; $j < $m; $j++){
				if (trim($resource_types[$j]) == trim($type[type_id])){
					echo 'checked="checked"';
					continue;
				}
			}
			echo '/>';
			echo '<label for="'.$type['type'].'_'.$resource_id.'_'.$alt.'">';
			if ($type['type']=='auditory')
				echo _AT('auditory');
 			if ($type['type']=='visual')
				echo _AT('visual');
			if ($type['type']=='textual')
				echo _AT('textual');
			if ($type['type']=='sign_language')
				echo _AT('sign_language');
			echo '</label><br/>';	
		}	
	}	
	echo '</fieldset>';
}
	
/**
* It provides, for each alternative, a link through which it is possible to delete such an alternative resource declared for a particular original one.
* @access	public
* @param	$cid: 				content id.
* @param	$resource: 			resource id.
* @param	$current_tab:		the current tab id .
* @return	
* @see		
* @author	Silvia Mirri
*/
function delete_alternative($resource, $cid, $current_tab){
	echo '<a href="'.$_SERVER['PHP_SELF'].'?cid='.$cid. SEP .'tab='.$current_tab . SEP . 'act=delete'. SEP .'id_alt='.$resource[secondary_resource_id].' ">'. _AT('delete').' <strong>'. $resource[secondary_resource].'</strong></a>';
	
}						
						
					