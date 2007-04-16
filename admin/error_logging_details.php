<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_ADMIN);

if (!isset($_POST['data'])) {
	$msg->addError('NO_PROFILE_SELECTED');
	header('Location: error_logging.php');
	exit;
} // else we have a profile we can work with

if (isset($_POST['delete'])) {

	$key = substr($_POST['data'], 0, strpos($_POST['data'], ':'));
	$date = substr($_POST['data'], strpos($_POST['data'], ':') + 1);
	$dir_ = AT_CONTENT_DIR . 'logs/' . $date;
	$delete_store;
	
	if (!($dir = opendir($dir_))) {
			$msg->printNoLookupFeedback('Could not access /content/logs/' . $date . '. Check that the permission for the <strong>Server</string> user are r+w to it');
			require(AT_INCLUDE_PATH.'footer.inc.php'); 
			
			exit;
		}
	
	$cnt = 0;	
	// Open a read pointer to run through each log date directory getting all the profiles
	while (($file = readdir($dir)) !== false) {
		
		if (($file == '.') || ($file == '..') || is_dir($file)) {
			continue;
		}
		
		if (strpos($file, $key) !== false) { // found a bug associated with our profile key
			$delete_store{$file} = $file;
		} else {
			$cnt++;
		}
					
	}
	closedir($dir); // clean it up
	
	if (count($delete_store) > 0) {
		// Now run through the files and unlink them all
		foreach($delete_store as $elem => $val) 
			unlink($dir_ . '/' . $elem);
	}
		
	// remove the directory as well if there are no oother profiles in it
	if ($cnt == 0) {
		rmdir($dir_);
	}
	
	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	header('Location: error_logging.php');
	exit;
} 

require(AT_INCLUDE_PATH.'header.inc.php');

if (isset($_POST['view'])) {
	// Grab all the bugs associated with this $_POST['data'] corresponding md5 key
	$key = substr($_POST['data'], 0, strpos($_POST['data'], ':'));
	$date = substr($_POST['data'], strpos($_POST['data'], ':') + 1);
	$dir_ = AT_CONTENT_DIR . 'logs/' . $date;
	$log_profiles_bugs;
		
	?>

	<form name="form" method="post" action="<?php echo 'admin/error_logging_view.php'; ?>">
	
	<table class="data" summary="" rules="cols">
	<thead>
	<tr>
		<th scope="col"><?php echo _AT('bug_identifier'); ?></th>
		<th scope="col"><?php echo _AT('timestamp');      ?></th>
	</tr>
	</thead>
	<tfoot>
	<tr>
		<td colspan="2">
			<input type="hidden" name="profile_id" value="<?php echo $key; ?>"/>
			<input type="hidden" name="profile_date" value="<?php echo $date; ?>"/>
			<input type="submit" name="view" value="<?php echo _AT('view_selected_bugs'); ?>" />
			<input type="submit" name="back" value="<?php echo _AT('back_to_main'); ?>" />
		</td>
	</tr>
	</tfoot>
	<tbody>
	<?php
		if (!($dir = opendir($dir_))) {
			$msg->printNoLookupFeedback('Could not access /content/logs/' . $date . '. Check that the permission for the <strong>Server</string> user are r+w to it');
			require(AT_INCLUDE_PATH.'footer.inc.php'); 
			
			exit;
		}
		
		// Open a read pointer to run through each log date directory getting all the profiles
		while (($file = readdir($dir)) !== false) {
		
			if (($file == '.') || ($file == '..') || is_dir($file) || (strpos($file, 'profile')	!== false)) {
				continue;
			}
		
			if (strpos($file, $key) !== false) { // found a bug associated with our profile key
				$log_profile_bugs{$file} = $file;
			}
					
		}
		closedir($dir); // clean it up		
		
		if (empty($log_profile_bugs)) { ?>
			<tr>
				<td align="center" colspan="2"><small><?php echo _AT('none_found'); ?></small></td>
			</tr>
			<tr><td height="1" class="row2" colspan="2"></td></tr>
		<?php
		} else {
			$count = 0;
			
			$id_cnt = 1; // give each bug an easier to understand id onscreen
 			foreach ($log_profile_bugs as $elem => $lm) {
				// construct timestamp from millis since epoch in bug identifier
				$timestamp = substr($lm, strpos($lm, '_') + 1);
				$timestamp = substr($timestamp, 0, strpos($lm, '_') + 2);
			
				$timestamp = AT_Date(_AT('inbox_date_format'), $timestamp, AT_DATE_UNIX_TIMESTAMP);
			
				$str_prefix = substr($lm, 0, strpos($lm, '_'));
			?>
				<tr onmousedown="document.form['q<?php echo $lm; ?>'].checked = !document.form['q<?php echo $lm; ?>'].checked;">
					<td><input type="checkbox" value="<?php echo $date . '/' . $lm; ?>" name="file<?php echo $count; ?>" id="q<?php echo $lm; ?>" onmouseup="this.checked=!this.checked" /><?php echo $id_cnt . '_' . $str_prefix; ?></td>
					<td><?php echo $timestamp; ?></td>
				</tr>
				<?php $count++; $id_cnt++;
			}
		}
		?>
		</tbody>
		</table>

		</form>
	<?php
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
		
} else if (isset($_POST['delete'])) {
	$key = substr($_POST['data'], 0, strpos($_POST['data'], ':'));
	$date = substr($_POST['data'], strpos($_POST['data'], ':') + 1);
	$dir_ = AT_CONTENT_DIR . 'logs/' . $date;
	$delete_store;
	
	if (!($dir = opendir($dir_))) {
			$msg->printNoLookupFeedback('Could not access /content/logs/' . $date . '. Check that the permission for the <strong>Server</string> user are r+w to it');
			require(AT_INCLUDE_PATH.'footer.inc.php'); 
			
			exit;
		}
		
	// Open a read pointer to run through each log date directory getting all the profiles
	while (($file = readdir($dir)) !== false) {
		
		if (($file == '.') || ($file == '..') || is_dir($file)) {
			continue;
		}
		
		if (strpos($file, $key) !== false) { // found a bug associated with our profile key
			$delete_store{$file} = $file;
		}
					
	}
	closedir($dir); // clean it up
	
	// Now run through the files and unlink them all
	foreach($delete_store as $elem => $val) 
		unlink($dir_ . '/' . $elem);
		
	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	header('Location: ' . $_SERVER['PHP_SELF']);
}
?>