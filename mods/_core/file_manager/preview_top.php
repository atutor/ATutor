<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id: preview_top.php 10142 2010-08-17 19:17:26Z hwong $

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

if (defined('AT_FORCE_GET_FILE') && AT_FORCE_GET_FILE) {
	$get_file = AT_BASE_HREF.'get.php/';
} else {
	$get_file = AT_BASE_HREF.'content/' . $_SESSION['course_id'] . '/';
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />
<html lang="<?php echo $myLang->getCode(); ?>">
<head>
	<title><?php echo _AT('file_manager_frame'); ?></title>
	<meta http-equiv="Content-Type" content="text/html; <?php echo $myLang->getCharacterSet(); ?>" />
</head>

<body>
<p align="bottom">

<a href="index.php?framed=<?php echo SEP; ?>popup=<?php echo SEP; ?>pathext=<?php echo $_GET['pathext'].SEP . 'popup=' . $_GET['popup'] . SEP . 'framed=' . $_GET['framed']; ?>" target="_top"><?php echo _AT('return_file_manager'); ?></a> 
<?php if (defined('AT_FORCE_GET_FILE') && AT_FORCE_GET_FILE): ?>
	 | 
	<a href="<?php echo $get_file; ?>@/<?php echo $_GET['file']; ?>" target="_top"><?php echo _AT('download_file'); ?></a>
<?php endif; ?> |
<a href="<?php echo $get_file; ?><?php echo $_GET['file']; ?>" target="_top"><?php echo _AT('remove_frame'); ?></a>
</p>

</body>
</html>