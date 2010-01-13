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
// $Id: preview.php 7208 2008-01-09 16:07:24Z greg $

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

if (defined('AT_FORCE_GET_FILE') && AT_FORCE_GET_FILE) {
	$get_file = $_base_path . 'get.php/';
	$file = 'b64:'.base64_encode($_GET['file']);
} else {
	$get_file = $_base_path . 'content/' . $_SESSION['course_id'] . '/';
	$file = $_GET['file'];
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Frameset//EN" "http://www.w3.org/TR/REC-html40/frameset.dtd" />
<html lang="<?php echo $myLang->getCode(); ?>">
<head>
	<title><?php echo _AT('file_manager_frame'); ?></title>
	<meta http-equiv="Content-Type" content="text/html; <?php echo $myLang->getCharacterSet(); ?>" />
</head>

<frameset rows="50,*">

<frame src="preview_top.php?file=<?php echo $file.SEP.'pathext='. $_GET['pathext'] . SEP . 'popup=' . $_GET['popup']; ?>" scrolling="no" marginwidth="0" marginheight="0" />
<frame src="<?php echo $get_file; ?><?php echo $file; ?>" />

<noframes>
  <p><?php echo _AT('frame_contains'); ?><br />
  * <a href="../mods/_core/file_manager/file_manager.php"><?php echo _AT('file_manager'); ?></a>
  </p>
</noframes>

</frameset>
</html>