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

$msg->printAll();

echo '<br/><h3>' . _AT('error_profiles') . '</h3>';

?>

<br/><form name="form1" method="post" action="<?php echo 'admin/error_logging_details.php'; ?>">

<table cellspacing="1" cellpadding="0" border="0" class="bodyline" width="95%" summary="" align="center">
	<tr>
		<th class="cat"><?php echo _AT('profile'); ?></th>
		<th class="cat"><?php echo _AT('date'); ?></th>
		<th class="cat"><?php echo _AT('bug_count'); ?></th>
	</tr>
	<tr><td height="1" class="row2" colspan="3"></td></tr>
<?php
		
		$dir_ = AT_CONTENT_DIR . 'logs';
		
		if (!($dir = opendir($dir_))) {
			$msg->printNoLookupFeedback('Could not access /content/logs. Check that the permission for the <strong>Server</string> user are r+w to it');
			require(AT_INCLUDE_PATH.'footer.inc.php'); 
			
			exit;
		}
		
		/**
		 * Run through the logs directory and lets get all the profiles of all the logs of all the dates, sort
		 * by primary key as date, secondary key is profile name
		 */ 
		$logdirs;
		 
		// loop through folder to get files and directory listing
		while (($file = readdir($dir)) !== false) {

			/* if the name is not a directory */
			if( ($file == '.') || ($file == '..')) {
				continue;
			}

			if (is_dir($dir_ . '/' . $file)) {
				$logdirs{$file} = $file; // store the day log dir
			}
		}
		closedir($dir); // clean it up

		if (empty($logdirs)) { ?>
			<tr>
				<td class="row1" align="center" colspan="3"><small><?php echo _AT('none_found'); ?></small></td>
			</tr>
			<tr><td height="1" class="row2" colspan="3"></td></tr>
		<?php
		} else {
		
			$count_ = 1;
			foreach ($logdirs as $row => $val) {
				$log_profiles; // store all the profiles under the dir /content/logs/$val
				$log_profiles_bug_count; // store the amount of bugs per profile
				
				if (!($dir = opendir($dir_ . '/' . $val))) {
					$msg->printNoLookupFeedback('Could not access /content/logs/' . $val . '. Check that the permission for the <strong>Server</string> user are r+w to it');
					require(AT_INCLUDE_PATH.'footer.inc.php'); 
			
					exit;
				}
				// Open a read pointer to run through each log date directory getting all the profiles
				while (($file = readdir($dir)) !== false) {
		
					if (($file == '.') || ($file == '..') || is_dir($file)) {
						continue;
					}
		
					if (strpos($file, 'profile')	!== false) { // found a profile, store its md5 key identifier
						$tmp_ = substr($file, strpos($file, '_') + 1);
						$tmp_ = substr($tmp_, 0, strpos($tmp_, '.log.php'));
						$log_profiles{$file} = $tmp_;
					}
					
				}
				closedir($dir); // clean it up
				
				/**
				 * Open a read pointer to run through each log date directory getting all the bugs associated
				 * all the profiles in $log_profiles
				 */
				if (empty($log_profiles)) { 
					$msg->printNoLookupFeedback('Warning. No profile found in ' . $dir_ . '/' . $val);
					require(AT_INCLUDE_PATH.'footer.inc.php'); 
			
					exit;
				}
				
				foreach ($log_profiles as $elem => $val_) {
					$count = 0;
					
					/* for each profile get the number of bugs associated with it */
					if (!($dir = opendir($dir_ . '/' . $val))) {
						$msg->printNoLookupFeedback('Could not access /content/logs' . $val . '. Check that the permission for the <strong>Server</string> user are r+w to it');
						require(AT_INCLUDE_PATH.'footer.inc.php'); 
			
						exit;
					}
					
					while (($file = readdir($dir)) !== false) {
			
						// make sure we ignore profiles too!, just look at bug files
						if( ($file == '.') || ($file == '..') || is_dir($file) || (strpos($file, 'profile') !== false)) {
							continue;
						}

						if (strpos($file, $val_)	!== false) { // found a bug that maps to $val_ md5 profile identifer
							$count++;
						}
					}
					closedir($dir);
					
					$log_profiles_bug_count{$val_} = $count; // store the amount of bugs associated with profile
				}

				/**
				 * At this point ($log_profiles => key) = ($log_profiles_bug_count => key).
				 *
				 * Lets print out <td> rows corresponding to all profiles found in the following format:
				 *
				 * Profile name, profile date, profile bug count. 
				 */		
				 
				foreach ($log_profiles_bug_count as $elem => $lm) {
					echo '<tr><td class="row1" style="padding-left: 10px;"><small><label><input type="radio" value="'. $elem . ':' . $row .'" name="data" />';
					echo ''. $count_ .'</label></small></td>';
					echo '<td class="row1" align="center"><small>' . $row .'</small></td>';
					echo '<td class="row1" align="center"><small>' . $lm .'</small></td>';
					echo '</tr>';
					echo '<tr><td height="1" class="row2" colspan="3"></td></tr>';
					$count_++;
				}
			}
		}
	
?>
	<tr><td height="1" class="row2" colspan="3"></td></tr>
	<tr>
		<td class="row1" align="center" colspan="3">
			<br /><input type="submit" name="view" value="<?php echo _AT('view_profile_bugs'); ?>" class="button" /> - 
				  <input type="submit" name="delete" value="<?php echo _AT('delete_profile'); ?>" class="button" /><br/><br/> 				  
		</td>
	</tr>
	</form>
	<tr><td height="1" class="row2" colspan="3"></td></tr>
	<tr>
		<td class="row1" align="center" colspan="3">
			<br />
				<form name="form2" method="post" action="<?php echo 'admin/error_logging_bundle.php'; ?>">
				<input type="submit" name="bundle" value="<?php echo _AT('report_errors'); ?>" class="button" /><br/><br/> 				  
		</td>
	</tr> 
	</table>



<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>