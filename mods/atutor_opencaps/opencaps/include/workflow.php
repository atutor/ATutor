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


/*
 * This file receives a request from the js and processes it using existing classes.
 * It's the AJAX-request manager. Should it be a class too?
 * 
 * everything here should call object functions. no real code here.
 * 
 */

define('INCLUDE_PATH', '');
require(INCLUDE_PATH.'vitals.inc.php');

/* get a project loaded into the system: start new, open existing, load chosen */
if ($_GET['task'] == "print_projs") {
	if (isset($_GET['page']))	
		$this_proj->printUserProjects($_GET['page']);
	else
		$this_proj->printUserProjects(1);
	
} else if ($_GET['task'] == "print_projs_remote") {	
	if (isset($_GET['page']))
		$this_system->printProjects($_GET['page']);
	else
		$this_system->printProjects();
} else if ($_GET['task'] == "open_proj") {
	$this_proj->open($_GET['pid']);

} else if ($_GET['task'] == "open_proj_remote") {
	$this_proj->openRemote($_GET['pid']);
	
} else if ($_GET['task'] == "proj_delete") {
	$this_proj->delete($_GET['pid']);
} 


/*
 * load json file into a project
 */
if ($_GET['task'] == 'get_json') {
	$json_path = '../projects/'.$this_proj->id.'/';
	echo $stripslashes(@file_get_contents($json_path.'opencaps.json'));
	
/*
 * save project into json file
 */	
} else if ($_POST['task'] == 'save_json') {
	$this_proj->saveJson($_POST['json'], $_POST['pid']);	
	
/*
 * preview - create smil & qttext files based on layout
 */	
} else if ($_GET['task'] == "preview") {
	$this_proj->preview($_GET['layout']);
	
/* export */	
} else if ($_GET['task'] == "export") {
	$exfile = $this_proj->exportCaption($_GET['format']);
	export_file($exfile);

/* start tabs */
} else if ($_GET['task'] == "get_tabs") {
	
	if (isset($systems)) { 
		echo '<ul>';
		if(!DISABLE_LOCAL)		
			echo '<li id="home"><a href="start.php">Home</a></li>';	
			
		if (ACTIVE_SYSTEM)	
			echo'<li id="remote-'.ACTIVE_SYSTEM.'"><a href="start_remote.php?r='.ACTIVE_SYSTEM.'">'.$systems[ACTIVE_SYSTEM]['name'].'</a></li>';
			
	/*	foreach ($systems as $key=>$remote) {
			echo'<li id="remote-'.$key.'"><a href="start_remote.php?r='.$key.'">'.$remote['name'].'</a></li>';
		}
	*/	
		echo '</ul>';		
	}  
}


?>