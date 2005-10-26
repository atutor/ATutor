<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$

define('AT_HANDBOOK', true);

session_start();

$parts = pathinfo($_SERVER['PHP_SELF']);
$this_page = $parts['basename'];

if (strpos(@ini_get('arg_separator.input'), ';') !== false) {
	define('SEP', ';');
} else {
	define('SEP', '&');
}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>ATutor Administrator Documentation</title>
	<link rel="stylesheet" href="../common/styles.css" type="text/css" />
</head>

<body onload="doparent();">
<script type="text/javascript">
// <!--
function doparent() {
	if (parent.toc && parent.toc.highlight) {
		parent.toc.highlight('id<?php echo $this_page; ?>');
	}
}
// -->
</script>
<?php
if(strstr($parts['dirname'], "admin")){
	$section = 'admin';
}elseif(strstr($parts['dirname'], "instructor")){
	$section = 'instructor';
}elseif(strstr($parts['dirname'], "general")){
	$section = 'general';
} else {
	$section = 'general';
}
require('../'.$section.'/pages.inc.php');

while (current($_pages) !== FALSE) {
	if (key($_pages) == $this_page) {
		next($_pages);
		$next_page = key($_pages);
		break;
	}
	$previous_page = key($_pages);
	next($_pages);
}
?>
<div class="seq">
	<?php if (isset($previous_page)): ?>
		Previous Chapter: <a href="<?php echo $previous_page; ?>" accesskey="," title="<?php echo $_pages[$previous_page]; ?> Alt+,"><?php echo $_pages[$previous_page]; ?></a><br />
	<?php endif; ?>

	<?php if (isset($next_page)): ?>
		Next Chapter: <a href="<?php echo $next_page; ?>" accesskey="." title="<?php echo $_pages[$next_page]; ?> Alt+."><?php echo $_pages[$next_page]; ?></a>
	<?php endif; ?>
</div>
