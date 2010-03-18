<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id: menu_pages.php 4799 2005-06-06 13:19:09Z heidi $


$parts = pathinfo($_SERVER['PHP_SELF']);
if (substr($parts['dirname'], -5) == 'admin') {
	$section = 'admin';
} else if (substr($parts['dirname'], -10) == 'instructor') {
	$section = 'instructor';
} else if (substr($parts['dirname'], -7) == 'general') {
	$section = 'general';
} else {
	header('Location: index/index.php');
	exit;
}

if ( get_magic_quotes_gpc() == 1 ) {
	$addslashes = 'my_add_null_slashes';
} else {
	$addslashes = 'mysql_real_escape_string';
}

$req_lang = 'en';
if (!empty($_GET)) {
	$req_lang = key($_GET);
}

$path = '../common/';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html lang="en">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>ATutor Handbook</title>

<head>
<script type="text/javascript">

var i = 0;

function show2() {
	var fs = document.getElementById('frameset1');
	if (fs) {
		i += 5;
		if (i > 28) {
			i = 28;
		}
		fs.cols = i + '%, *';
	}
	if (i < 28) {
		window.setTimeout('show2()', 1);
	}
	return true;
}
function show() {
	i = 0;
	window.setTimeout('show2()', 1);
	return true;
}

function hide2() {
	var fs = document.getElementById('frameset1');
	if (fs) {
		i -= 5;
		if (i < 0) {
			i =0;
		}
		fs.cols = i + '%, *';
	}
	if (i > 0) {
		window.setTimeout('hide2()', 1);
	}
	return false;
}

function hide() {
	i= 28;
	window.setTimeout('hide2()', 1);
	return false;
}
</script>
</head>

<?php 
if (isset($_GET['p'])) {
	$body = htmlentities($_GET['p']);
	$accessible_pages = scandir('.');
	
	if (!in_array($body, $accessible_pages))
	{

?>
<div style="width: 50%;
	margin-left: auto;
	margin-right: auto;
	margin-top: 15px;
	margin-bottom: 15px;
	border: 1px solid #DD0000;
	padding: 5px;
	background-color: #F4DCDC;
	color: #A50707;
	background-color: #F4DCDC;
	padding-left: 25px;
	font-weight: bold;">
	INACCESSIBLE PAGE
</div>

<?php exit;
	}
} else {
	$body = 'introduction.php';
} 
?>

<frameset rows="24,*">
	<frame src="<?php echo $path; ?>frame_header.php?<?php echo $section; ?>&amp;<?php echo $req_lang; ?>" frameborder="0" name="header" title="header" scrolling="no" noresize="noresize">
	<frameset cols="22%, *" id="frameset1">
		<frame frameborder="0" scrolling="auto" marginwidth="0" marginheight="0" src="<?php echo $path; ?>frame_toc.php?<?php echo $section; ?>&amp;<?php echo $req_lang; ?>" name="toc" id="toc" title="Table of Contents">
		<frame frameborder="0" src="<?php echo $body.'?'.$req_lang; ?>" name="body" title="Content">
	</frameset>

	<noframes>
		<h1>Administrator Documentation</h1>
		<p><a href="frame_toc.html">Table of Contents</a></p>
	 </noframes>
</frameset>

</html>
