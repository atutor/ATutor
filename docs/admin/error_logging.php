<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/

$page = 'server_configuration';
$_user_location = 'admin';

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
if ($_SESSION['course_id'] > -1) { exit; }

require_once(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

global $savant;
$msg =& new Message($savant);

require(AT_INCLUDE_PATH.'header.inc.php'); 
echo '<h3>Log Files</h3>';

$msg->printAll();
?>

<form name="form1" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<input type="hidden" name="course" value="<?php echo $_REQUEST['course']; ?>" />

<table cellspacing="1" cellpadding="0" border="0" class="bodyline" width="95%" summary="" align="center">
	<tr>
		<th class="cat"><?php echo _AT('file_name'); ?></th>
		<th class="cat"><?php echo _AT('file_size'); ?></th>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
<?php
		
		$dir_ = AT_CONTENT_DIR . 'logs';
		
		if (!($dir = opendir($dir_))) {
			$msg->printNoLookupFeedback('Could not access /content/logs. Check that the permission for the <strong>Server</string> user are r+w to it');
			require(AT_INCLUDE_PATH . $_footer_file);
			exit;
		}
		
		/**
		 * Run through the logs directory and lets get all the log file names
		 */ 
		$logfiles;
		 
		// loop through folder to get files and directory listing
		while (($file = readdir($dir)) !== false) {
		
			/* if the name is not a directory */
			if( ($file == '.') || ($file == '..') || is_dir($file) ) {
				continue;
			}
		
			// get some info about the file
			$filedata = stat($dir_ . '/' . $file);
		
			$logfiles{$file} = $filedata[7] . ' KB'; // store the file size
		}
		
		closedir($dir); // clean it up

		if (empty($logfiles)) { ?>
			<tr>
				<td class="row1" align="center" colspan="2"><small><?php echo _AT('none_found'); ?></small></td>
			</tr>
			<tr><td height="1" class="row2" colspan="2"></td></tr>
		<?php
		} else {

			foreach ($logfiles as $row => $val) {
				echo '<tr><td class="row1" style="padding-left: 10px;"><small><label><input type="radio" value="'.$row['backup_id'].'_'.$row['course_id'].'" name="backup_id" />';
				echo ''.$row.'</label></small></td>';
				echo '<td class="row1" align="right"><small>' . $val .'</small></td>';
				echo '</tr>';
				echo '<tr><td height="1" class="row2" colspan="2"></td></tr>';
			}
		}
	

?>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1" align="center" colspan="2">
			<br /><input type="submit" name="restore" value="<?php echo 'View' ?>" class="button" /> - 
				  <input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" class="button" /><br/><br/> 				  
		</td>
	</tr>
	</table>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>