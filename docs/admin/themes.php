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

$_user_location = 'admin';

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
if ($_SESSION['course_id'] > -1) { exit; }

require(AT_INCLUDE_PATH.'classes/pclzip.lib.php');
require(AT_INCLUDE_PATH.'lib/filemanager.inc.php');

/* to avoid timing out on large files */
set_time_limit(0);


$_section[0][0] = _AT('admin');
$_section[0][1] = 'admin/';
$_section[1][0] = _AT('themes');


require(AT_INCLUDE_PATH.'header.inc.php'); 

echo '<h2>'._AT('themes').'</h2>';

include(AT_INCLUDE_PATH . 'html/feedback.inc.php');

if (isset($_GET['e'])) {
	$e = intval($_GET['e']);
	if ($e <= 0) {
		/* it's probably an array */
		$e = unserialize(urldecode($_GET['e']));
	}
	print_errors($e);
}


/** Display list of themes in directory **/
require(AT_INCLUDE_PATH.'lib/themes.inc.php');
$available_themes = get_available_themes();

echo '<p>'._AT('themes_on_this_system').'</p>';
foreach($available_themes as $theme){
	echo '<li>';
	echo $theme['name'].' ('.$theme['filename'].')';
	echo '</li>';
}

?>

<br /><br />
<form name="form1" method="post" action="admin/theme_import.php" enctype="multipart/form-data" onsubmit="openWindow('<?php echo $_base_href; ?>tools/prog.php');">
	<table cellspacing="1" cellpadding="0" border="0" class="bodyline" width="95%" summary="">
	<tr>
		<th class="cyan" colspan="2"><?php echo _AT('import_a_new_theme'); ?></th>
	</tr>
	<tr>
		<td class="row1" colspan="2"><?php echo _AT('import_theme_howto'); ?></td>
	</tr>
	<tr><td height="1" class="row2"></td></tr>
	<tr>
		<td class="row1"><strong><?php echo _AT('theme_file'); ?>:</strong> <input type="file" name="file" class="formfield" /><br /><br /></td>
	</tr>
	<tr><td height="1" class="row2"></td></tr>
	<tr>
		<td class="row1"><strong><?php echo _AT('specify_url_to_theme'); ?>:</strong> <input type="input" name="url" value="http://" size="40" class="formfield" /><br /><br /></td>
	</tr>
	<tr><td height="1" class="row2"></td></tr>
	<tr><td height="1" class="row2"></td></tr>
	<tr>
		<td class="row1" align="center"><br />
		<input type="submit" name="submit" value="<?php echo _AT('import'); ?>" class="button" /> - <input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" class="button" /></td>
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