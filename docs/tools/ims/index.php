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
// $Id$

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require_once(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

$_section[0][0] = _AT('tools');
$_section[0][1] = 'tools/';
$_section[1][0] = _AT('content_packaging');
$_section[1][1] = 'tools/ims/';

global $savant;
$msg =& new Message($savant);

require(AT_INCLUDE_PATH.'header.inc.php');

	echo '<h2>';
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '<img src="images/icons/default/square-large-tools.gif" border="0" vspace="2"  class="menuimageh2" width="42" height="40" alt="" />';
	}
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
		echo ' <a href="tools/" class="hide" >'._AT('tools').'</a>';
	}
	echo '</h2>';

	echo '<h3>';
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '&nbsp;<img src="images/icons/default/package-large.gif" class="menuimageh3" width="42" height="38" alt="" /> ';
	}
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
		echo _AT('content_packaging');
	}
	echo '</h3>';
	
$msg->addHelp('EXPORT_PACKAGE');
if (authenticate(AT_PRIV_CONTENT, AT_PRIV_RETURN)) {
	$msg->addHelp('IMPORT_PACKAGE');
}
$msg->printAll();

if (!isset($_main_menu)) {
	$_main_menu = $contentManager->getContent();
}

function print_menu_sections(&$menu, $parent_content_id = 0, $depth = 0, $ordering = '') {
	$my_children = $menu[$parent_content_id];
	$cid = $_GET['cid'];

	if (!is_array($my_children)) {
		return;
	}
	foreach ($my_children as $children) {
		echo '<option value="'.$children['content_id'].'"';
		if ($cid == $children['content_id']) {
			echo ' selected="selected"';
		}
		echo '>';
		echo str_pad('', $depth, '-') . ' ';
		if ($parent_content_id == 0) {
			$new_ordering = $children['ordering'];
			echo $children['ordering'];
		} else {
			$new_ordering = $ordering.'.'.$children['ordering'];
			echo $ordering . '.'. $children['ordering'];
		}
		echo ' '.$children['title'].'</option>';

		print_menu_sections($menu, $children['content_id'], $depth+1, $new_ordering);
	}
}

?>

<?php
	if (!authenticate(AT_PRIV_CONTENT, AT_PRIV_RETURN) && ($_SESSION['packaging'] == 'none')) {
		echo '<p>'._AT('content_packaging_disabled').'</p>';
		require (AT_INCLUDE_PATH.'footer.inc.php'); 
		exit;
	} else if (!authenticate(AT_PRIV_CONTENT, AT_PRIV_RETURN) && ($_SESSION['packaging'] == 'top')) {
		$_main_menu = array($_main_menu[0]);
	}
?>
<form method="post" action="tools/ims/ims_export.php">
	<table cellspacing="1" cellpadding="0" border="0" class="bodyline" width="95%" summary="" align="center">
	<tr><th  colspan="2"><?php echo _AT('export_content_package'); ?></th></tr>
	<tr>
		<td class="row1"><strong><?php echo _AT('export_content_package_what'); ?>:</strong> <select name="cid">
							<option value="0"><?php echo _AT('export_entire_course_or_chap'); ?></option>
							<option>--------------------------</option>
							<?php
								print_menu_sections($_main_menu);
							?>
							</select><br /><br /></td>
	</tr>
	<tr><td height="1" class="row2"></td></tr>
	<?php if (authenticate(AT_PRIV_ADMIN, AT_PRIV_RETURN)): ?>
		<tr>
			<td class="row1"><input type="checkbox" name="to_tile" id="to_tile" value="1" /><label for="to_tile"><?php echo _AC('tile_export'); ?></label></td>
		</tr>
	<?php endif; ?>
	<tr><td height="1" class="row2"></td></tr>
	<tr>
		<td class="row1" align="center"><input type="submit" name="submit" value="<?php echo _AT('export'); ?>" class="button" /> - <input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" class="button" /></td>
	</tr>
	</table>
</form>

<?php if (!authenticate(AT_PRIV_CONTENT, AT_PRIV_RETURN)) {
		require (AT_INCLUDE_PATH.'footer.inc.php'); 
		exit;
}
?>
<br /><br />


<form name="form1" method="post" action="tools/ims/ims_import.php" enctype="multipart/form-data" onsubmit="openWindow('<?php echo $_base_href; ?>tools/prog.php');">
	<table cellspacing="1" cellpadding="0" border="0" class="bodyline" width="95%" summary="" align="center">
		<tr><th  colspan="2"><?php echo _AT('import_content_package'); ?></th></tr>
		<tr>
		<td class="row1"><strong><?php echo _AT('import_content_package_where'); ?>:</strong> <select name="cid">
							<option value="0"><?php echo _AT('import_content_package_bottom_subcontent'); ?></option>
							<option>--------------------------</option>
							<?php
								print_menu_sections($_main_menu);
							?>
							</select></td>
	</tr>
	<tr><td height="1" class="row2"></td></tr>
	<tr>
		<td class="row1"><strong><?php echo _AT('upload_content_package'); ?>:</strong> <input type="file" name="file" class="formfield" /><br /><br /></td>
	</tr>
	<tr><td height="1" class="row2"></td></tr>
	<tr>
		<td class="row1"><strong><?php echo _AT('specify_url_to_content_package'); ?>:</strong> <input type="input" name="url" value="http://" size="40" class="formfield" /><br /><br /></td>
	</tr>
	<tr><td height="1" class="row2"></td></tr>
	<tr><td height="1" class="row2"></td></tr>
	<tr>
		<td class="row1" align="center"><input type="submit" name="submit" value="<?php echo _AT('import'); ?>" class="button" /> - <input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" class="button" /></td>
	</tr>
	</table>
</form>

<script language="javascript" type="text/javascript">
function openWindow(page) {
	newWindow = window.open(page, "progWin", "width=400,height=200,toolbar=no,location=no");
	newWindow.focus();
}
</script>

<?php
	require (AT_INCLUDE_PATH.'footer.inc.php'); 
?>