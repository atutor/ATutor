<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id: print.php 5866 2005-12-15 16:16:03Z joel $

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>ATutor Handbook</title>
	<link rel="stylesheet" href="styles.css" type="text/css" />
</head>
<body>
<?php
if (isset($_GET['admin'])) {
	$section = 'admin';
} elseif (isset($_GET['instructor'])) {
	$section = 'instructor';
} elseif (isset($_GET['general'])){
	$section = 'general';
} else {
	$section = 'general';
}

require('../'.$section.'/pages.inc.php');


echo '<a href="../'.$section.'/index.php" target="_top">Back to Chapters</a>';

foreach ($_pages as $file => $title) {
	readfile('../'.$section.'/'.$file);
}
?>
</body>
</html>