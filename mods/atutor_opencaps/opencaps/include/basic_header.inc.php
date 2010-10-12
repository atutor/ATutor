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
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<title>OpenCaps - An online inclusive media editor</title>
	
	<script language="JavaScript" src="js/jquery/jquery-1.2.6.js" type="text/javascript"></script>
	<script language="JavaScript" src="js/json/json2.js" type="text/javascript"></script>
	<script language="JavaScript" src="js/utils.js" type="text/javascript"></script>
		
	<link rel="stylesheet" href="styles.css" type="text/css" />
	<link rel="stylesheet" href="styles_public.css" type="text/css" />
	<!--[if IE]>
		<link href="styles_ie.css" rel="stylesheet" type="text/css" />
	<![endif]-->	

</head>
<body>

	<?php if ($_SESSION['mid'] && $_SESSION['mid']!='99999' && !DISABLE_LOCAL) { ?>
	<div style="float:right; margin-top:-5px;font-size:smaller;">
		<img style="margin-bottom:-3px;" src="images/door_out.png" alt="" /> <a href="logout.php">Logout</a>
	</div>
		
	<?php 
	}
	
	if (isset($_SESSION['errors'])) {
		echo '<div class="error"><strong>Error:</strong><br />';
		foreach ($_SESSION['errors'] as $errmsg) {
			echo $errmsg.'<br />';	
		}
		echo '</div>';
		unset($_SESSION['errors']);
	}
	if (isset($_SESSION['feedback'])) {
		echo '<div class="feedback"><strong>Feedback:</strong><br />';
		foreach ($_SESSION['feedback'] as $fbmsg) {
			echo $fbmsg.'<br />';	
		}
		echo '</div>';
		unset($_SESSION['feedback']);
	}
	if (isset($_SESSION['notices'])) {
		echo '<div class="notice"><strong>Notice:</strong><br />';
		foreach ($_SESSION['notices'] as $nmsg) {
			echo $nmsg.'<br />';	
		}
		echo '</div>';
		unset($_SESSION['notices']);
	}	