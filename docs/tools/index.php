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
// $Id$

	$page = 'tools';
	define('AT_INCLUDE_PATH', '../include/');
	require(AT_INCLUDE_PATH.'vitals.inc.php');
	require(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

	$_section[0][0] = _AT('tools');
	
	global $savant;
	$msg =& new Message($savant);

	require(AT_INCLUDE_PATH.'header.inc.php');

	$msg->printAll();

?>
	<h2><?php 
		if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
			echo '<img src="images/icons/default/square-large-tools.gif" class="menuimageh2" width="42" height="40" alt="" /> ';
		}
		if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
			echo _AT('tools');
		}
	?></h2><br />
<h3><?php echo _AT('student').' '._AT('tools'); ?></h3><br />
<table border="0" cellspacing="0" cellpadding="3" summary="">
<tr>
	<?php
		if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
			echo '<td rowspan="2" valign="top"><img src="images/icons/default/search-small.gif" border="0"  class="menuimage" width="28" height="25" alt="*" /></td>';
		}
		echo '<td>';
		if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
			echo ' <a href="users/search.php?g=20">'._AT('search').'</a>';
		}
		echo '</td></tr><tr><td>';
		echo _AT('search_text');
		?>
	</td>
</tr>
<tr>
	<?php 
			if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
				echo '<td rowspan="2" valign="top"><img src="images/icons/default/sitemap-small.gif" border="0"  class="menuimage" width="28" height="25" alt="*" /></td>';
			}
			echo '<td>';
			if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
				echo ' <a href="tools/sitemap/index.php?g=23">'._AT('sitemap').'</a>';
			}
			echo '</td></tr><tr><td>';
			echo _AT('sitemap_text');
			?>
	</td>
</tr>
<tr>
	<?php 
			if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
				echo '<td rowspan="2" valign="top"><img src="images/icons/default/glossary-small.gif" border="0"  class="menuimage" width="28" height="25" alt="*" /></td>';
			}
			echo '<td>';
			if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
				echo ' <a href="glossary/index.php?g=25">'._AT('glossary').'</a>';
			}
			echo '</td></tr><tr><td>';
			echo _AT('glossary_text');
		?>
	</td>
</tr>
<tr>
	<?php
				if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
					echo '<td rowspan="2" valign="top"><img src="images/icons/default/package-small.gif" border="0" class="menuimage" width="28" height="25" alt="*" /></td>';
				}
				echo '<td>';
				if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
					echo ' <a href="tools/ims/index.php?g=27">'._AT('export_content').'</a>';
				}
				echo '</td></tr><tr><td>';
				echo _AT('export_content_text');
			?>
	</td>
</tr>
<tr>
	<?php 
			if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
				echo '<td rowspan="2" valign="top"><img src="images/icons/default/course-tracker-small.gif" border="0"  class="menuimage" width="28" height="25" alt="*" /></td>';
			}
			echo '<td>';
			if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
				echo ' <a href="tools/tracker.php?g=28">'._AT('my_tracker').'</a>';
			}
			echo '</td></tr><tr><td>';
			echo _AT('my_tracker_text');
		?>
	</td>
</tr>
<tr>
	<?php 
			if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
				echo '<td rowspan="2" valign="top"><img src="images/icons/default/my-tests-small.gif" border="0"  class="menuimage" width="28" height="25" alt="*" /></td>';
			}
			echo '<td>';
			if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
				echo ' <a href="tools/my_tests.php?g=32">'._AT('my_tests').'</a>';
			}
			echo '</td></tr><tr><td>';
			echo _AT('my_tests_text');
		?>
	</td>
</tr>
</table> 
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
	
	if (show_tool_header()) {
		echo '<br /><a name="ins-tools"></a><h3>'._AT('instructor_tools').'</h3><br />';
	}
?>
<table border="0" cellspacing="0" cellpadding="3" summary="">
<?php if (authenticate(AT_PRIV_COURSE_EMAIL, AT_PRIV_RETURN)) { ?>
<tr>
	<?php 
				if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
					echo '<td rowspan="2" valign="top"><img src="images/icons/default/course_mail-small.gif" border="0"  class="menuimage" width="28" height="25" alt="*" /></td>';
				}
				echo '<td>';
				if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
					echo ' <a href="tools/course_email.php">'._AT('course_email').'</a>';
				}
				echo '</td></tr><tr><td>';
				echo _AT('send_to', '');
			?>
	</td>
</tr>
<?php } ?>
<?php if (authenticate(AT_PRIV_ENROLLMENT, AT_PRIV_RETURN)) { ?>
<tr>
	<?php 
				if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
					echo '<td rowspan="2" valign="top"><img src="images/icons/default/enrol_mng-small.gif" border="0"  class="menuimage" width="28" height="25" alt="*" /></td>';
				}
				echo '<td>';
				if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
					echo ' <a href="tools/enroll_admin.php">'._AT('course_enrolment').'</a>';
				}
				echo '</td></tr><tr><td>';
				echo _AT('course_enrollment_text');
			?>
	</td>
</tr>
<?php } ?>

<?php if (authenticate(AT_PRIV_FILES, AT_PRIV_RETURN)) { ?>
<tr>
	<?php
				if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
					echo '<td rowspan="2" valign="top"><img src="images/icons/default/file-manager-small.gif" border="0"  class="menuimage" width="28" height="25" alt="*" /></td>';
				}
				echo '<td>';
				if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
					echo ' <a href="tools/file_manager.php">'._AT('file_manager').'</a>';
				}
				echo '</td></tr><tr><td>';
				echo _AT('file_manager_text');
			?>
	</td>
</tr>
<?php } ?>
<?php if (authenticate(AT_PRIV_TEST_CREATE, AT_PRIV_RETURN) || authenticate(AT_PRIV_TEST_MARK, AT_PRIV_RETURN)) { ?>
<tr>
	<?php
				if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
					echo '<td rowspan="2" valign="top"><img src="images/icons/default/test-manager-small.gif" border="0"  class="menuimage" width="28" height="25" alt="*" /></td>';
				}
				echo '<td>';
				if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
					echo ' <a href="tools/tests/">'._AT('test_manager').'</a>';
				}
				echo '</td></tr><tr><td>';
				echo _AT('test_manager_text');
			?>
	</td>
</tr>
<?php } ?>
<?php if (authenticate(AT_PRIV_ADMIN, AT_PRIV_RETURN)) { ?>
<tr>
	<?php
				if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
					echo '<td rowspan="2" valign="top"><img src="images/icons/default/package-small.gif" border="0" class="menuimage" width="28" height="25" alt="*" /></td>';
				}
				echo '<td>';
				if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
					echo ' <a href="tools/ims/">'._AT('content_packaging').'</a>';
				}
				echo '</td></tr><tr><td>';
				echo _AT('content_packaging_text');
			?>
	</td>
</tr>
<tr>
	<?php
				if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
					echo '<td rowspan="2" valign="top"><img src="images/icons/default/backup-small.gif" border="0" class="menuimage" width="28" height="25" alt="*" /></td>';
				}
				echo '<td>';
				if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
					echo ' <a href="tools/backup/">'._AT('backup_manager').'</a>';
				}
				echo '</td></tr><tr><td>';
				echo _AT('backup_course_text');
			?>
	</td>
</tr>
<tr>
	<?php
				if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
					echo '<td rowspan="2" valign="top"><img src="images/icons/default/course-tracker-small.gif" border="0"  class="menuimage" width="28" height="25" alt="*" /></td>';
				}
				echo '<td>';
				if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
					echo ' <a href="tools/course_tracker.php">'._AT('course_tracker').'</a>';
				}
				echo '</td></tr><tr><td>';
				echo _AT('course_tracker_text');
			?>
	</td>
</tr>
<?php } ?>
<?php if (authenticate(AT_PRIV_ADMIN, AT_PRIV_RETURN)) { ?>
<tr>
	<?php
				if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
					echo '<td rowspan="2" valign="top"><img src="images/icons/default/course-properties-small.gif" border="0"  class="menuimage" width="28" height="25" alt="*" /></td>';
				}
				echo '<td>';
				if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
					echo ' <a href="tools/course_properties.php">'._AT('course_properties').'</a>';
				}
				echo '</td></tr><tr><td>';
				echo _AT('course_properties_text');
			?>
	</td>
</tr>
<?php } ?>
<?php if (authenticate(AT_PRIV_STYLES, AT_PRIV_RETURN)) { ?>
<tr>
	<?php
				if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
					echo '<td rowspan="2" valign="top"><img src="images/icons/default/edit-preferences-small.gif" border="0"  class="menuimage" width="28" height="25" alt="*" /></td>';
				}
				echo '<td>';
				if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
					echo ' <a href="tools/course_preferences.php">'._AT('course_default_prefs').'</a>';
				}
				echo '</td></tr><tr><td>';
				echo _AT('course_default_prefs_text');
			?>
	</td>
</tr>
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
<tr>
	<?php
				if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
					echo '<td rowspan="2" valign="top"><img src="images/icons/default/copyright-small.gif" border="0" width="28"  class="menuimage" height="25" alt="*" /></td>';
				}
				echo '<td>';
				if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
					echo ' <a href="tools/edit_header.php">'._AT('course_copyright2').'</a>';
				}
				echo '</td></tr><tr><td>';
				echo _AT('copyright_text');
			?>
	</td>
</tr>
<tr>
	<?php 
				if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
					echo '<td rowspan="2" valign="top"><img src="images/icons/default/css-editor-small.gif" border="0"  class="menuimage" width="28" height="25" alt="*" /></td>';
				}
				echo '<td>';
				if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
					echo ' <a href="tools/edit_styles.php">'._AT('style_editor').'</a>';
				}
				echo '</td></tr><tr><td>';
				echo _AT('style_editor_text');
			?>
	</td>
</tr>
<?php } ?>
</table>

<?php
	require(AT_INCLUDE_PATH.'footer.inc.php');
?>