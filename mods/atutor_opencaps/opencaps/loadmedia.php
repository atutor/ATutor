<?php
/*
 * OpenCaps
 * http://opencaps.atrc.utoronto.ca
 * 
 * Copyright 2010 Heidi Hazelton
 * Adaptive Technology Resource Centre, University of Toronto
 * 
 * Licensed under the Educational Community License (ECL), Version 2.0. 
 * You may not use this file except in compliance with this License.
 * http://www.opensource.org/licenses/ecl2.php
 * 
 */

define('INCLUDE_PATH', 'include/');

require(INCLUDE_PATH.'vitals.inc.php');


$file = $_GET['loc'];

if (isset($_SESSION['rid'])) {
	$uri = explode($remote_systems[$_SESSION["rid"]]['url'], $file);
	$uri = $uri[1];
	$file = matterhornAuth($_SESSION["rid"], $uri, "media");
}

header('Pragma: public');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check');
header('Cache-Control: private', false);
header('Content-Type: video/quicktime');
header('Content-Transfer-Encoding: binary');
header('Content-Length: '.filesize($file));

readfile($file);
exit;









/*

loadMedia(urldecode($_GET['loc']));

function loadMedia($loc) {
	global $remote_systems;
			
	//if matterhorn
	if (isset($_SESSION["rid"])) {
		$uri = explode($remote_systems[$_SESSION["rid"]]['url'], $loc);
		$uri = $uri[1];
		return matterhornAuth($rid, $uri, "media");
	
	} else {
		return readfile($loc);
	}
}*/


?>