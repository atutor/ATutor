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

if (!isset($_SESSION['mid']))
	$_SESSION['mid'] = '99999'; //guest for remote


if (!isset($systems[$_GET['r']]['url'])) {
	
	if (ACTIVE_SYSTEM) {
		header("Location:start_remote.php?r=".ACTIVE_SYSTEM);
		exit;
	} 
	
	$_SESSION['errors'][] = "No remote systems have been configured. Go to the <a href='start.php'>start</a> page.";	
	include(INCLUDE_PATH.'basic_header.inc.php'); 
	include(INCLUDE_PATH.'footer.inc.php'); 
	exit;
} 

if (!isset($_SESSION['mid']))
	$_SESSION['mid'] = '99999'; //guest for remote

	
require('include/basic_header.inc.php'); 

?>
<script language="JavaScript" src="js/start_remote.js" type="text/javascript"></script>

<h1 style="margin-top:10px;"><img src="images/logo.png" alt="OpenCaps - a free, online caption editor" title="OpenCaps - a free, online caption editor" style="margin-top:7px;" /></h1>

<p>Start captioning! Please select a project to work on:</p>

<div id="start-tabs"></div>
<div id="start-container">	
	<div>
		<h2 style="font-weight:bold">Open Project</h2>
		<p>Add captions to a remote project.</p>
			
		<form action="javascript:processOpenRemote();" method="post" id="form_open" enctype="multipart/form-data" onsubmit="javascript: return validateOpenForm();">
			<div id="projects"></div>
		</form>
		
	<br style="clear:both" /></div>
	
</div>

</body>
</html>
