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

$page = 'themes';
$_user_location = 'admin';

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'lib/themes.inc.php');


if (isset($_POST['export'])) {
	export_theme($_POST['theme_name']);
} else if(isset($_POST['delete'])) {
	header('Location: theme_delete.php?theme_code='.$_POST['theme_name']);
	exit;
} else if(isset($_POST['default'])) {
	set_theme_as_default ($_POST['theme_name']);
} else if(isset($_POST['enable'])) {
	enable_theme($_POST['theme_name']);
	//feedback that theme was enabled, however, version is not compatible
	if (check_version($_POST['theme_name']) == 0) {
		header('Location: index.php?f='.urlencode_feedback(AT_FEEDBACK_LANG_DELETED));
		exit;
	}
} else if(isset($_POST['disable'])) {
	disable_theme($_POST['theme_name']);

} else if(isset($_POST['import'])) {
	import_theme();
}

require(AT_INCLUDE_PATH.'header.inc.php');


echo '<br /> <h2>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '<img src="images/icons/default/square-large-tools.gif" border="0" vspace="2"  class="menuimageh2" width="42" height="40" alt="" />';
} 
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
	echo ' <a href="admin/themes/" >'._AT('themes').'</a>';
}
echo '</h2>';

echo '<h3>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '&nbsp;<img src="images/icons/default/enrol_mng-large.gif"  class="menuimageh3" width="42" height="38" alt="" /> ';
}
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
	echo _AT('theme_manager');
}
echo '</h3>';

require(AT_INCLUDE_PATH . 'html/feedback.inc.php');
$themes = get_all_themes();

foreach ($themes as $t): ?>
<form name="themes" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
	<input type="hidden" value="<?php echo $t; ?>" name="theme_name" />
	<table cellspacing="1" cellpadding="0" border="1" class="bodyline" width="80%" height ="126" summary="" align="center" />
		<tr>
			<td class="row1" width="185">
				<img src="<?php 
								$src = get_image_path($t);
								echo 'themes/' . $src;
						  ?>"
						  width="185" height="126" border="0" alt="Theme Screenshot">
			</td>
			
			<td class="row1" height="126">
			<table cellspacing="0" cellpadding="0" border="0" class="bodyline" width="100%" height="130" summary="">
				<tr ><th height="15" class="cyan"><?php echo ' ' . $t ?></th></tr>
				<tr >
					<td height="20"class="row1"><small>
						<?php 
							$info = get_themes_info($t);
							echo ' ' . $info['extra_info'];
						?></small>
					</td>
				</tr>
				<tr >
					<td height="15"class="row1"><small><b>Version:</b><i>
						<?php 
							echo ' ' . $info['version'];
						?><i></small>
					</td>
				</tr>
				<tr >
					<td height="15" class="row1"><small><b>Last Updated:</b><i>
						<?php 
							echo ' ' . $info['last_updated'];
						?></i></small>
					</td></tr>
				<tr >
					<td height="20" class="row1">
						<input type= "submit" name="export"  value="<?php echo _AT('export_theme'); ?>" class="button" />
							<?php 
								if (intval(check_status($t)) == 0) {
									echo ' | <input type= "submit" name="delete"  value="'. _AT('delete_theme') .'" class="button" />';
									echo '| <input type= "submit" name="enable" value="'. _AT('enable') .'" class="button" />';
									echo ' | <input type= "submit" name="default" value="'. _AT('set_default') .'" class="button" />';
								}
		
								else if (intval(check_status($t)) == 1) {
									echo ' | <input type= "submit" name="delete"  value="'. _AT('delete_theme') .'" class="button" />';
									echo '| <input type= "submit" name="disable" value="'. _AT('disable') .'" class="button" />';
									echo ' | <input type= "submit" name="default" value="'. _AT('set_default') .'" class="button" />';
								}
	
								else {
									echo ' | ' . _AT('current_default_theme');
								}
							?>
					</td>
				</tr>
			</table>
			</td>
		</tr>
	</table><br>
</form>
<?php
endforeach;
?>

<form name="importForm" method="post" action="themes/themes_import.php"  enctype="multipart/form-data" >
	<table cellspacing="1" cellpadding="0" border="0" class="bodyline" width="80%" summary="" align="center">
		<tr>
			<th class="cyan"><?php echo _AT('import_theme'); ?></th>
		</tr>
		<tr><td height="1" class="row2"></td></tr>
		<tr>
			<td class="row1"><strong><?php echo _AT('upload_theme_package'); ?>:</strong>
			<input type="file" name="file" class="formfield" size = "40" /></td>
		</tr>	
		<tr><td height="1" class="row2"></td></tr>
		<tr>
			<td class="row1"><strong><?php echo _AT('specify_url_to_theme_package'); ?>:</strong>
			<input type="input" name="url" value="http://" size="40" class="formfield" /></td>
		</tr>
		<tr><td height="1" class="row2"></td></tr>
		<tr>
			<td class="row1" align="center">
			<input type= "submit" name="import" value="<?php echo _AT('import_theme'); ?>" class="button">
			</td>
		</tr>
	</table>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>