<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2010                                      */
/* Inclusive Design Institute                                   */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

$_user_location = 'admin';

define('AT_INCLUDE_PATH', '../../../include/');

require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'../mods/_core/themes/lib/themes.inc.php');
admin_authenticate(AT_ADMIN_PRIV_THEMES);

$theme   = $addslashes($_GET['theme_dir']);
$version = $addslashes($_GET[$theme.'_version']);

if (isset($_GET['export'], $_GET['theme_dir'])) {
	export_theme($theme);
} else if (isset($_GET['delete'], $_GET['theme_dir'])) {
	header('Location: delete.php?theme_code='.urlencode($theme));
	exit;
} else if (isset($_GET['default'], $_GET['theme_dir'])) {
	set_theme_as_default($theme, $_GET['type']);
	$_config['pref_defaults'] = unserialize($_config['pref_defaults']);
	if ($_GET['type']==MOBILE_DEVICE) {
		$_config['pref_defaults']['PREF_MOBILE_THEME'] = $theme;
	} else {
		$_config['pref_defaults']['PREF_THEME'] = $theme;
	}
	$_config['pref_defaults'] = serialize($_config['pref_defaults']);

	$sql    = "REPLACE INTO ".TABLE_PREFIX."config VALUES ('pref_defaults','{$_config['pref_defaults']}')";
	$result = mysql_query($sql, $db);

	header('Location: '.$_SERVER['PHP_SELF']);
	exit;
} else if (isset($_GET['enable'], $_GET['theme_dir'])) {
	if ($version != VERSION) {
		$str = $theme . ' - version: ' . $version;
		$warnings = array('THEME_VERSION_DIFF', $str);
		$msg->addWarning($warnings);
	}
	enable_theme($theme);
	header('Location: '.$_SERVER['PHP_SELF']);
	exit;
} else if (isset($_GET['disable'], $_GET['theme_dir'])) {
	disable_theme($theme);
	header('Location: '.$_SERVER['PHP_SELF']);
	exit;
} else if (isset($_GET['preview'], $_GET['theme_dir'])) {
	$_SESSION['prefs']['PREF_THEME'] = $_GET['theme_dir'];
	header('Location: '.$_SERVER['PHP_SELF']);
	exit;
} else if (isset($_GET['disable']) || isset($_GET['enable']) || isset($_GET['default']) || isset($_GET['delete']) || isset($_GET['export'])) {
	$msg->addError('NO_ITEM_SELECTED');
}

require(AT_INCLUDE_PATH.'header.inc.php');
?>

<?php if (!is_writeable(realpath('./../../../themes'))): ?>
	<div class="input-form">
		<div class="row">
			<?php echo _AT('install_themes_text', realpath('./../../../themes')); ?>		
		</div>
	</div>
<?php else: ?>
	<form name="importForm" method="post" action="mods/_core/themes/import.php" enctype="multipart/form-data">
	<div class="input-form" style="width:95%;">
		<div class="row">
			<h3><?php echo _AT('import_theme'); ?></h3>
		</div>

		<div class="row">
			<label for="file"><?php echo _AT('upload_theme_package'); ?></label><br />
			<input type="file" name="file" size="40" id="file" />
		</div>

		<div class="row">
			<label for="url"><?php echo _AT('specify_url_to_theme_package'); ?></label><br />
			<input type="text" name="url" value="http://" size="40" id="url" />
		</div>
			
		<div class="row buttons">
			<input type= "submit" name="import" value="<?php echo _AT('import'); ?>" />
		</div>
	</div>
	</form>
	<br />
<?php endif; 

$sql    = "SELECT * FROM " . TABLE_PREFIX . "themes WHERE type='".DESKTOP_DEVICE."' ORDER BY title ASC";
$result = mysql_query($sql, $db);
print_data_table($result, DESKTOP_DEVICE);
echo '<br /><br />';
$sql    = "SELECT * FROM " . TABLE_PREFIX . "themes WHERE type='".MOBILE_DEVICE."' ORDER BY title ASC";
$result = mysql_query($sql, $db);
print_data_table($result, MOBILE_DEVICE);
?>

<?php function print_data_table($result, $type) {
	if (@mysql_num_rows($result) == 0) return;
?>
<h3><?php if ($type == DESKTOP_DEVICE) echo _AT('themes_for_desktop'); else echo _AT('themes_for_mobile');?></h3><br />
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" name="form_<?php echo $type; ?>">
<input type="hidden" name="type" value="<?php echo $type; ?>" />
<table class="data" summary="" rules="cols">
<thead>
<tr>
	<th scope="col">&nbsp;</th>
	<th scope="col"><?php echo _AT('title'); ?></th>
	<th scope="col"><?php echo _AT('status'); ?></th>
	<th scope="col"><?php echo _AT('version'); ?></th>
	<th scope="col"><?php echo _AT('directory_name'); ?></th>
	<th scope="col"><?php echo _AT('description'); ?></th>
	<th scope="col"><?php echo _AT('theme_screenshot'); ?></th>
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="7">
		<input type="submit" name="preview"  value="<?php echo _AT('preview'); ?>" />
		<input type="submit" name="enable"  value="<?php echo _AT('enable'); ?>" />
		<input type="submit" name="disable" value="<?php echo _AT('disable'); ?>" />
		<input type="submit" name="default" value="<?php echo _AT('set_default').'&nbsp;'; if ($type == DESKTOP_DEVICE) echo _AT('desktop_theme'); else echo _AT('mobile_theme'); ?>" />
		<input type="submit" name="export"  value="<?php echo _AT('export'); ?>" />
		<input type="submit" name="delete"  value="<?php echo _AT('delete'); ?>" />
	</td>
</tr>
</tfoot>
<?php while($row = mysql_fetch_assoc($result)) : ?>
	<tbody>
	<tr onmousedown="document.form['t_<?php echo $row['dir_name']; ?>'].checked = true; rowselect(this);" id="r_<?php echo $row['dir_name']; ?>">
		<td valign="top">
			<input type="radio" id="t_<?php echo $row['dir_name']; ?>" name="theme_dir" value="<?php echo $row['dir_name']; ?>" />
			<input type="hidden" name="<?php echo $row['dir_name']; ?>_version" value="<?php echo $row['version']; ?>" />
		</td>
		<td nowrap="nowrap" valign="top"><label for="t_<?php echo $row['dir_name']; ?>"><?php echo AT_print($row['title'], 'themes.title'); ?></label></td>
		<td valign="top"><?php
			if ($row['status'] == 0) {
				echo _AT('disabled');
			} else if ($row['status'] == 1) {
				echo _AT('enabled');
			} else if (($type == DESKTOP_DEVICE && $row['status'] == 2) || ($type == MOBILE_DEVICE && $row['status'] == 3)) {
				echo '<strong>'._AT('default').'</strong>'; 
			}
			?>
		</td>
		<td valign="top"><?php echo $row['version']; ?></td>
		<td valign="top"><code><?php echo $row['dir_name']; ?>/</code></td>
		<td valign="top"><?php echo $row['extra_info']; ?></td>
		<td valign="top"><?php
			if (file_exists('../../../themes/'.$row['dir_name'].'/screenshot.jpg')) { ?>
				  <img src="<?php echo AT_BASE_HREF; ?>themes/<?php echo $row['dir_name']; ?>/screenshot.jpg" border="1" alt="<?php echo _AT('theme_screenshot'); ?>" />
			<?php		
			} else if (file_exists('../../../themes/'.$row['dir_name'].'/screenshot.gif')) { ?>
				 <img src="<?php echo AT_BASE_HREF; ?>themes/<?php echo $row['dir_name']; ?>/screenshot.gif" border="1" alt="<?php echo _AT('theme_screenshot'); ?>" />
			<?php } ?>
		</td>
	</tr>
	</tbody>
<?php endwhile; ?>
</table>
</form>
<?php
}
 
require(AT_INCLUDE_PATH.'footer.inc.php'); 
?>