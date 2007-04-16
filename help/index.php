<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2007 by Greg Gay & Joel Kronenberg        */
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
	<li style="padding-bottom: 20px;"><a href="documentation/index_list.php?lang=<?php echo $_SESSION['lang']; ?>" onclick="poptastic('<?php echo AT_BASE_HREF; ?>documentation/index_list.php?lang=<?php echo $_SESSION['lang']; ?>'); return false;" target="_new"><?php echo _AT('atutor_handbook');?></a><br />
		<?php echo _AT('general_help', AT_GUIDES_PATH); ?></li>

	<li style="padding-bottom: 20px;"><a href="help/accessibility.php"><?php echo _AT('accessibility_features'); ?></a>
		<br /><?php echo _AT('accessibility_features_text'); ?></li>

	<li><a href="help/contact_support.php"><?php echo _AT('contact_support'); ?></a></li>
</ul>

<h3><?php echo _AT('external_help'); ?></h3>
<ul>

	<li style="padding-bottom: 20px;"><?php echo _AT('howto_course'); ?>
		<br /><?php echo _AT('howto_course_text'); ?></li>

	<li><a href="http://www.atutor.ca/forums/forum.php?fid=7"><?php echo _AT('tech_support_forum'); ?></a>
		<br /><?php echo _AT('tech_support_forum_text'); ?></li>
</ul>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>