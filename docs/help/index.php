<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$
$_user_location	= 'public';

define('AT_INCLUDE_PATH', '../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

require (AT_INCLUDE_PATH.'header.inc.php');

?>
<ul>
	<li>
	<a href="help/accessibility.php"><?php echo _AT('accessibility_features'); ?></a>
		<br /><?php echo _AT('accessibility_features_text'); ?></li>
</ul>

<br />

<h3><?php echo _AT('help_contact'); ?></h3>
<ul>
	<li><a href="help/contact_admin.php"><?php echo _AT('system_contact'); ?></a></li>
</ul>

<br />

<h3><?php echo _AT('external_help'); ?></h3>
<ul>

	<li><a href="http://www.atutor.ca/howto.php"><?php echo _AT('howto_course'); ?></a>
		<br /><?php echo _AT('howto_course_text'); ?><br /><br /></li>

	<li><a href="http://www.atutor.ca/forums/forum.php?fid=7"><?php echo _AT('tech_support_forum'); ?></a>
		<br /><?php echo _AT('tech_support_forum_text'); ?></li>
</ul>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>