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

	define('AT_INCLUDE_PATH', '../include/');
	require(AT_INCLUDE_PATH.'vitals.inc.php');

	require(AT_INCLUDE_PATH.'header.inc.php');

?>
<ol>
	<li><a href="tools/content/index.php"><?php echo _AT('content'); ?></a> (add, content packaging)</li>
	<li><a href="tools/news/index.php"><?php echo _AT('announcements'); ?></a></li>
	<li><a href="tools/forums/index.php"><?php echo _AT('forums'); ?></a></li>
	<li><a href="tools/course_properties.php"><?php echo _AT('properties'); ?></a></li>
	<li><a href="tools/backup/index.php"><?php echo _AT('backups'); ?></a></li>
	<li><a href="tools/enrollment/index.php"><?php echo _AT('enrolment'); ?></a> ( send email, enrollment manager, tracker)</li>
	<li><a href="tools/polls/index.php"><?php echo _AT('polls'); ?></a></li>
	<li><a href="tools/tile/index.php"><?php echo _AT('tile_search'); ?></a></li>
	<li><a href="tools/links/index.php"><?php echo _AT('links'); ?></a></li>
	<li><a href="tools/filemanager/index.php"><?php echo _AT('file_manager'); ?></a></li>
	<li><a href="tools/tests/index.php"><?php echo _AT('test_manager'); ?></a></li>
	<li><a href="tools/course_tracker.php"><?php echo _AT('course_tracker'); ?></a></li>
	<li><a href="tools/course_stats.php"><?php echo _AT('course_stats'); ?></a></li>
	<li><a href="tools/modules.php"><?php echo _AT('modules'); ?></a></li>
	<li><a href="tools/glossary/index.php"><?php echo _AT('glossary'); ?></a></li>
	<li><a href="tools/tracker/index.php"><?php echo _AT('tracker'); ?></a> *new*</li>
	<li><a href="tools/side_menu.php"><?php echo _AT('side_menu'); ?></a></li>
</ol>
<?php
	
if (defined('AC_PATH') && AC_PATH) {
	echo '<br /><h3>ACollab '._AT('tools').'</h3><br />';

?>
	<table border="0" cellspacing="0" cellpadding="3" summary="">
	<?php if (authenticate(AT_PRIV_AC_CREATE, AT_PRIV_RETURN)) { ?>
	<tr>
		<?php 
					if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
						echo '<td rowspan="2" valign="top"><img src="images/icons/default/ac_group_mng-small.gif" border="0"  class="menuimage" width="28" height="25" alt="*" /></td>';
					}
					echo '<td>';
					if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
						echo ' <a href="acollab/bounce.php?p='.urlencode('admin/groups_create.php').'">'._AT('ac_create').'</a>';
					}
					echo '</td></tr><tr><td>';
					echo _AT('ac_create_text');
		?>
		</td>
	</tr>
	<?php } ?>
	<tr>
		<?php 
					if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
						echo '<td rowspan="2" valign="top"><img src="images/icons/default/ac_group-small.gif" border="0"  class="menuimage" width="28" height="25" alt="*" /></td>';
					}
					echo '<td>';
					if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
						echo ' <a href="acollab/bounce.php">'._AT('ac_access_groups').'</a>';
					}
					echo '</td></tr><tr><td>';
					echo _AT('ac_access_text');
				?>
		</td>
	</tr>
	</table>
<?php
}
	if (!$_SESSION['privileges'] && !authenticate(AT_PRIV_ADMIN, AT_PRIV_RETURN)) {
		require(AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}
?>
<table border="0" cellspacing="0" cellpadding="3" summary="">
<?php if (authenticate(AT_PRIV_STYLES, AT_PRIV_RETURN)) { ?>

<tr>
	<?php 
				if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
					echo '<td rowspan="2" valign="top"><img src="images/icons/default/banner-small.gif" border="0"  class="menuimage" width="28" height="25" alt="*" /></td>';
				}
				echo '<td>';
				if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
					echo ' <a href="tools/banner.php">'._AT('course_banner').'</a>';
				}
				echo '</td></tr><tr><td>';
				echo _AT('banner_text');
			?>
	</td>
</tr>

<?php } ?>
</table>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>