<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca                                             */
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/

$_ignore_page = true; /* without this we wouldn't know where we're supposed to go */
define('AT_INCLUDE_PATH', 'include/');
require(AT_INCLUDE_PATH.'vitals.inc.php')
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN"
   "http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
	<title><?php echo _AT('file_manager_frame'); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
</head>
<frameset cols="20%, *" border="1" frameborder="0" framespacing="0">
	<frame marginwidth="0" marginheight="0" frameborder="0" src="tools/file_manager.php?frame=1" name="frame" title="<?php echo _AT('file_manager_frame'); ?>">
	<frame marginwidth="0" marginheight="0" frameborder="0" src="<?php echo urldecode($_GET['p']); ?>" name="content" title="<?php echo _AT('file_content_frame'); ?>">
	<noframes>
      <p><?php echo _AT('frame_contains'); ?><br />
	  * <a href="tools/file_manager.php"><?php echo _AT('file_manager'); ?></a>
	  * <a href="<?php echo urldecode($_GET['p']); ?>"><?php echo _AT('atutor_content'); ?>/a>
	  </p>
  </noframes>
</frameset>
</html>