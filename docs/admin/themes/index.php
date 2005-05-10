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

$_user_location = 'admin';

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'lib/themes.inc.php');
admin_authenticate(AT_ADMIN_PRIV_THEMES);

$theme = $_POST['theme_dir'];
$version = $_POST[$theme.'_version'];

if (isset($_POST['export'])) {
	export_theme($theme);
} else if (isset($_POST['delete'])) {
	header('Location: delete.php?theme_code='.urlencode($theme));
	exit;
} else if (isset($_POST['default'])) {
	set_theme_as_default ($theme);
	header('Location: '.$_SERVER['PHP_SELF']);
	exit;
} else if (isset($_POST['enable'])) {
	if ($version != VERSION) {
		$str = $theme . ' - version: ' . $version;
		$warnings = array('THEME_VERSION_DIFF', $str);
		$msg->addWarning($warnings);
	}
	enable_theme($theme);
	header('Location: '.$_SERVER['PHP_SELF']);
	exit;
} else if(isset($_POST['disable'])) {
	disable_theme($theme);
	header('Location: '.$_SERVER['PHP_SELF']);
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

$sql    = "SELECT * FROM " . TABLE_PREFIX . "themes ORDER BY title ASC";
$result = mysql_query($sql, $db);
?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<table class="data" summary="">

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
		<input type="submit" name="enable"   value="<?php echo _AT('enable'); ?>" />
		<input type="submit" name="disable"   value="<?php echo _AT('disable'); ?>" />
		<input type="submit" name="default" value="<?php echo _AT('set_default'); ?>" />
		<input type="submit" name="export" value="<?php echo _AT('export'); ?>" />
		<input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" />
	</td>
</tr>
</tfoot>
<tbody>
<?php while($row = mysql_fetch_assoc($result)) : ?>
	<tr onmousedown="document.form['t_<?php echo $row['dir_name']; ?>'].checked = true;">
		<td><input type="radio" id="t_<?php echo $row['dir_name']; ?>" name="theme_dir" value="<?php echo $row['dir_name']; ?>" />
			<input type="hidden" name="<?php echo $row['dir_name']; ?>_version" value="<?php echo $row['version']; ?>" />
		</td>
		<td><label for="t_<?php echo $row['dir_name']; ?>"><?php echo AT_print($row['title'], 'themes.title'); ?></label></td>
		<td><?php 
			if ($row['status'] == 0) { 
				echo _AT('disabled'); 
			} else if ($row['status'] == 1) { 
				echo _AT('enabled'); 
			} else if ($row['status'] == 2) { 
				echo '<strong>'._AT('default').'</strong>'; 
			}  
			?>
		</td>
		<td><?php echo $row['version']; ?></td>
		<td><?php echo $row['dir_name']; ?></td>
		<td><?php echo $row['extra_info']; ?></td>
		<td><?php
			if (file_exists('../../themes/'.$row['dir_name'].'/screenshot.jpg')) { ?>
				  <img src="<?php echo $_base_href; ?>themes/<?php echo $row['dir_name']; ?>/screenshot.jpg" />
			<?php		
			} else if (file_exists('../../themes/'.$row['dir_name'].'/screenshot.gif')) { ?>
				 <img src="<?php echo $_base_href; ?>themes/<?php echo $row['dir_name']; ?>/screenshot.gif" />
			<?php } ?>
		</td>
	</tr>
<?php endwhile; ?>
</tbody>
</table>
</form>
<br /><br />
<form name="importForm" method="post" action="admin/themes/import.php" enctype="multipart/form-data">
<div class="input-form" style="width:50%;">
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
	<input type= "submit" name="import" value="<?php echo _AT('import_theme'); ?>" />
	</div>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>