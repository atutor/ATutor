<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2003 by Greg Gay, Joel Kronenberg, Heidi Hazelton	*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/

print_progress($step);

if (isset($_POST['submit']) && (trim($_POST['old_path']) != '')) {
	if ((strpos($_POST['old_path'], '/') === false) && is_dir('../../'.$_POST['old_path'])) {
		if ( file_exists('../../'.$_POST['old_path'] . '/include/config.inc.php') ) {
			
			require('../../'.$_POST['old_path'] . '/include/lib/constants.inc.php');
			if (!defined('VERSION')) {
				$errors[] = 'Cannot detect version number. Only ATutor versions greater than 1.0 can be upgraded. Upgrade to 1.1 manually then try upgrading to the latest version again.';
			} else {
				$progress[] = 'Found ATutor version <code><b>'.VERSION . '</b></code> in path <code><b>'.$_POST['old_path'].'</b></code>.';
			}
			if (!version_compare(VERSION, $new_version, '<')) {
				$errors[] = 'The version upgrading (<code><b>'.VERSION.'</b></code>) is not older than the new version (<code><b>'.$new_version.'</b></code>).';
			}

			if (!$errors) {
				$progress[] = 'Upgrading from version <code><b>'.VERSION.'</b></code> to version <code><b>'.$new_version.'</b></code>.';
				print_feedback($progress);

				require('../../'.$_POST['old_path'] . '/include/config.inc.php');
				
				$IllegalExtentions = "'".implode("','", $IllegalExtentions)."'";

				echo '<form action="'.$_SERVER['PHP_SELF'].'" method="post" name="form">';
				echo '<input type="hidden" name="step" value="2" />';
				echo '<input type="hidden" name="old_path" value="'.$_POST['old_path'].'" />';

				echo '<input type="hidden" name="db_login" value="'.urlencode(DB_USER).'" />';
				echo '<input type="hidden" name="db_password" value="'.urlencode(DB_PASSWORD).'" />';
				echo '<input type="hidden" name="db_host" value="'.DB_HOST.'" />';
				if (defined('DB_PORT')) {
					echo '<input type="hidden" name="db_port" value="'.DB_PORT.'" />';
				} else {
					echo '<input type="hidden" name="db_port" value="3306" />';
				}
				echo '<input type="hidden" name="db_name" value="'.DB_NAME.'" />';

				if (defined('TABLE_PREFIX')) {
					echo '<input type="hidden" name="tb_prefix" value="'.TABLE_PREFIX.'" />';
				} else {
					echo '<input type="hidden" name="tb_prefix" value="" />';
				}
			
				echo '<input type="hidden" name="new_version" value="'.$new_version.'" />';
				echo '<input type="hidden" name="old_version" value="'.VERSION.'" />';
				echo '<input type="submit" class="button" value=" Next » " name="submit" /></form>';
				return;
			}
		} else {
			$errors[] = 'Directory was found, but the configuration file cannot be found.';
		}
	} else {
		$errors[] = 'Directory does not exist relative to the new installation.';
	}
}

if (isset($progress)) {
	print_feedback($progress);
}

if (isset($errors)) {
	print_errors($errors);
}

?>

<p>Please tell me where the old version of atutor is relative to the current installation:</p>
<p>Example: If the old ATutor installation directory was renamed to <code>atutor_old</code> then enter that name below. The old version must be at the same directory level as the new version.</p>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<input type="hidden" name="new_version" value="<?php echo $new_version; ?>" />
<input type="hidden" name="step" value="1" />

<input type="text" name="old_path" value="<?php if (!empty($_POST['old_path'])) { echo $_POST['old_path']; } ?>" class="formfield" />

<br /><br /><p align="center"><input type="submit" class="button" value="Next » " name="submit" /></p>

</form>