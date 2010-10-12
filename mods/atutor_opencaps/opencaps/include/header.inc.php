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
	<meta http-equiv="content-script-type" content="text/javascript" />
	
	<title>OpenCaps - An online inclusive media editor</title>
	<link rel="stylesheet" href="styles.css" type="text/css" />
	<?php
	/* browser detection for css */
	$userAgent = $_SERVER['HTTP_USER_AGENT'];
	
	//Firefox on Win
	if (preg_match('/firefox/i', $userAgent) && preg_match('/windows|win32/i', $userAgent)) { 
		echo '<link rel="stylesheet" href="styles_ff.css" type="text/css" />';
		
	//IE
	} else if (preg_match('/msie/i', $userAgent)) {
		echo '<link rel="stylesheet" href="styles_ie.css" type="text/css" />';			
	
	//Opera
	} else if (preg_match('/opera/i', $userAgent)) {
		echo '<link rel="stylesheet" href="styles_opera.css" type="text/css" />';			
	} 
	?>

	<script language="javascript" type="text/javascript" src="js/AC_QuickTime.js"></script>
	<script language="javascript" type="text/javascript" src="js/jquery/jquery-1.3.2.min.js"></script>
	<script language="javascript" type="text/javascript" src="js/jquery/ui.core.js"></script>
	<script language="javascript" type="text/javascript" src="js/jquery/ui.slider.js"></script>
	<script language="javascript" type="text/javascript" src="js/json/json2.js"></script>
	
	<script language="javascript" type="text/javascript" src="js/utils.js"></script>

</head>

<body>
<div id="container">		
	<h1 style="margin:10px; float:left;"><img src="images/logo.png" alt="OpenCaps - a free, online caption editor" title="OpenCaps - a free, online caption editor" style="margin-top:7px;" /></h1>
	
	<div style="float:right; margin-top:5px; margin-right:10px;">
		<span style="font-style:italic;"><?php echo $this_proj->name; ?></span>
		<div id="last-saved"></div>
	</div>

	<div id="menubar">		
		<ul>
			<li id="editor-tab"><a href="editor.php"><img style="margin-bottom:-3px;" src="images/application_edit.png" alt="arrow pointing right" /> Caption</a></li>
			<?php if(!DISABLE_LOCAL && !isset($_SESSION['rid'])) { ?><li id="preview-tab"><a href="preview.php"><img src="images/arrow_right.png" alt="arrow pointing right" style="margin-bottom:-3px;" /> Preview</a></li><?php } ?>
			<li id="settings-tab"><a href="settings.php"><img style="margin-bottom:-3px;" src="images/page_gear.png" alt="" /> Settings</a></li>
			<li id="export-tab"><a href="export.php"><img style="margin-bottom:-3px;" src="images/application_put.png" alt="" /> Finish</a></li>
			
			<?php if (!isset($_SESSION['rid'])) { ?>			
			<!--  li id="close-tab"><a href="start.php"><img style="margin-bottom:-3px;" src="images/cross.png" alt="" /> Close</a></li -->
			<?php } ?>

		</ul>
	</div>
	<?php 
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
	?>	