<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
$page = 'help';
define('AT_INCLUDE_PATH', '../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

$_section[0][0] = _AT('help');

require (AT_INCLUDE_PATH.'header.inc.php');

echo '<h2>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '<img src="images/icons/default/square-large-help.gif" width="42" height="38" class="menuimage" border="0" alt="" /> ';
}
echo _AT('help').'</h2>';

?>

<ul>
	<li>
	<?php
		if($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2){
			echo '<img src="images/icons/default/access.gif" width="29" height="27" hspace="3" vspace="3" class="menuimage"  border="0" alt="" />';
		}
	?>
	<a href="help/accessibility.php?g=18"><?php echo _AT('accessibility_features'); ?></a>
		<br /><?php echo _AT('accessibility_features_text'); ?><br /><br /></li>

	<li><a href="help/about_help.php?g=18"><?php echo _AT('about_atutor_help'); ?></a>
		<br /><?php echo _AT('about_atutor_help_text'); ?><br /><br /></li>

	<li><a href="http://www.atutor.ca/howto.php"><?php echo _AT('howto_course'); ?></a>
		<br /><?php echo _AT('howto_course_text'); ?><br /><br /></li>

	<li><a href="http://www.atutor.ca/forums/forum.php?fid=7"><?php echo _AT('tech_support_forum'); ?></a>
		<br /><?php echo _AT('tech_support_forum_text'); ?><br /><br /></li>
</ul>

<h3><?php echo _AT('contacts'); ?></h3>
<ul>
	<?php if (authenticate(AT_PRIV_ADMIN, AT_PRIV_RETURN)) { ?>
		<li><?php echo _AT('for_instructors'); ?><br />
			<ul>
				<li><a href="help/contact_admin.php"><?php echo _AT('system_contact'); ?></a></li>
			</ul>
		</li>
	<?php } else {
		echo '<li><a href="help/contact_instructor.php?g=18">'._AT('instructor_contact').'</a></li>';
	} ?>
</ul>

<?php
	require(AT_INCLUDE_PATH.'footer.inc.php');
?>