<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca                                                     */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_ADMIN);

require(AT_INCLUDE_PATH.'header.inc.php');

?>

<form name="form" method="post" action="admin/error_logging_details.php">
<table class="data" summary="" rules="cols">
<thead>
<tr>
	<th><?php echo _AT('profile');   ?></th>
	<th><?php echo _AT('date');      ?></th>
	<th><?php echo _AT('bug_count'); ?></th>
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="3">
		<input type="submit" name="view" value="<?php echo _AT('view_profile_bugs'); ?>" /> 
		<input type="submit" name="delete" value="<?php echo _AT('delete_profile'); ?>" />
	</td>
</tr>
</tfoot>
<tbody>
<?php
		
		$dir_ = AT_CONTENT_DIR . 'logs';
		if (!is_dir($dir_)) {
			mkdir($dir_);
		}

		if (!($dir = @opendir($dir_))) {
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
				<td colspan="3"><?php echo _AT('none_found'); ?></td>
			</tr>
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

						// found a bug that maps to $val_ md5 profile identifer
						if (strpos($file, $val_)	!== false) { 
							$count++;
						}
					}
					closedir($dir);

					// store the amount of bugs associated with profile
					$log_profiles_bug_count{$val}[$val_] = $count;
				}
				$log_profiles = array();
			}
			/**
			 * At this point ($log_profiles => key) = ($log_profiles_bug_count => key).
			 *
			 * Lets print out <td> rows corresponding to all profiles found in the following format:
			 *
			 * Profile name, profile date, profile bug count. 
			 */		
			foreach ($log_profiles_bug_count as $day => $profile) :
				 foreach ($profile as $stamp => $total) :
			?>
					<tr onmousedown="document.form['<?php echo $stamp.$day; ?>'].checked = true;rowselect(this);" id="r_<?php echo $stamp.$day; ?>">
						<td><input type="radio" id="<?php echo $stamp.$day; ?>" value="<?php echo $stamp.':'.$day; ?>" name="data" /><label for="<?php echo $stamp.$day; ?>"><?php echo $count_; ?></label></td>
						<td><?php echo $day; ?></td>
						<td><?php echo $total; ?></td>
					</tr>
			<?php
					$count_++;
				endforeach;
			endforeach;
		}
	
?>
</tbody>
</table>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>