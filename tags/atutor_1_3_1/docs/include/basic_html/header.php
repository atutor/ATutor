<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2003 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
if (!defined('AT_INCLUDE_PATH')) { exit; }

Header('Content-Type: text/html; charset='.$available_languages[$_SESSION['lang']][1]);

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="<?php echo $available_languages[$_SESSION['lang']][2]; ?>">
<head>
	<title><?php echo SITE_NAME; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $available_languages[$_SESSION['lang']][1]; ?>" />

	<link rel="stylesheet" href="stylesheet.css" type="text/css" />
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
	<?php
		if (in_array($_SESSION['lang'], $_rtl_languages)) {
			echo '<link rel="stylesheet" href="'.$_base_path.'rtl.css" type="text/css" />'."\n";
		}
	?>
</head>
<body <?php echo $onload; ?>><?php
require(AT_INCLUDE_PATH.'basic_html/public_menu.inc.php');


?><small class="spacer"><br /></small><table width="98%" align="center" border="0" cellpadding="2" cellspacing="3" class="bodyline" summary=""><tr><td><?php

if (isset($errors)) {
	print_errors($errors);
	unset($errors);
}
?>