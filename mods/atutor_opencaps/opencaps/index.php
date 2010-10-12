<?php
/*
 * OpenCaps
 * http://opencaps.atrc.utoronto.ca
 * 
 * Copyright 2009 Heidi Hazelton
 * Adaptive Technology Resource Centre, University of Toronto
 * 
 * Licensed under the Educational Community License (ECL), Version 2.0. 
 * You may not use this file except in compliance with this License.
 * http://www.opensource.org/licenses/ecl2.php
 * 
 */

define('INCLUDE_PATH', 'include/');
require(INCLUDE_PATH.'vitals.inc.php');

//remote system request - send captions back
if (isset($_GET['id'])) {
	$this_proj->id = $_GET['id'];
	$_SESSION['pid'] = $this_proj->id;
	
	//check which system is pinging opencaps
	if (isset($systems) && ACTIVE_SYSTEM != '') {
		
		/*$ref_url = substr($_SERVER['HTTP_REFERER'], 0, -(strlen($_SERVER['SCRIPT_FILENAME'])));
		
		echo $_SERVER['HTTP_REFERER'].' '.$_SERVER['HTTP_HOST'].'<br>';
		echo $systems[2]['url'].' '.$ref_url;
		exit; 
		
		foreach ($systems as $key=>$sys) {			
			if (strpos($sys['url'], getenv("HTTP_REFERER"))) {
				$sys_id = $key;
			}
		}*/
		
		$this_system->openProject($this_proj->id);
			

	} else {
		$_SESSION['errors'][] = "External system URL not recognised. Make sure to add this system to the OpenCaps config file.";		
		header("Location:start.php");
		exit;
	}
	//$_SESSION['rid'] = $rid;
 	//$this_proj->openRemote($this_proj->id, true);

} else {
	header("Location:start.php");
	exit;
}

?>