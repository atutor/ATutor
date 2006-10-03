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

if (isset($_POST['back'])) {
	header('Location: error_logging.php');
	exit;
}

if (isset($_POST['step2'])) { // e-mail bundle

	if ($_POST['email_add'] == '') {
		$msg->addError('NO_RECIPIENT');
		header('Location: ' . $_SERVER['PHP_SELF']);
		exit;
	}
	/* First lets check if they selected any profiles to bundle, run through $POST['file(\d)'] */
	foreach($_POST as $elem => $val) {
		if (strpos($elem, 'file') !== false) {
			$found = true;
			
			$work = $val;
			
			$date = substr($work, 0, strpos($work, ':'));
			$id = substr($work, strpos($work, ':') + 1);
			/* Parse the variable */
			$profiles{$id} = $date;
		}
	}
	
	if ($found === true) {
		require(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');
		require(AT_INCLUDE_PATH.'classes/zipfile.class.php');
	
		$mail = new ATutorMailer;
	
		$zipfile = new zipfile();

		$dir_ = AT_CONTENT_DIR . 'logs';

		foreach($profiles as $elem => $val) {
			$store_some;

			// read the dir where this profile and its associated log files are located
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
		
					// any files mathcing the $elem key correspond to this profile
					if (strpos($file, $elem)	!== false) { 
						$store_some{$dir_ . '/'.  $val . '/' . $file} = $file;
					}
					
				}
				closedir($dir); // clean it up
				
				// The dir pointer is closed lets add to the zip
				foreach($store_some as $val_ => $e)
					$zipfile->add_file(file_get_contents($val_), $e);
		}
		
		$zipfile->close();

		if ($file_handle = fopen($dir_ . '/bundle.log', "w")) {
				if (!fwrite($file_handle, $zipfile->get_file())) { }
		} else { }
		fclose($file_handle);

		$mail->From = $_config['contact_email'];
		$mail->addAddress($_POST['email_add']);
		$mail->Subject = _AT('log_file_bundle');
		$mail->Body    = _AT('see_attached');
		$mail->AddAttachment($dir_ . '/bundle.log');
	
		// clean up the file at the redirection point
		if(!$mail->Send()) {
		   $msg->addError('SENDING_ERROR');
		   /* Make sure the tmp bundle file never exists past the lifetime of the bundle manager page */
		   unlink($dir_ . '/bundle.log');
		   header('Location: ' . $_SERVER['PHP_SELF']);
		   exit;
		}
		unset($mail);

		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		/* Make sure the tmp bundle file never exists past the lifetime of the bundle manager page */
		unlink($dir_ . '/bundle.log');
		header('Location: error_logging.php');
		exit;
	} else {
		$msg->addError('NO_LOGS_SELECTED');
		header('Location: ' . $_SERVER['PHP_SELF']);
		exit;
	}
} // else step 1

require(AT_INCLUDE_PATH.'header.inc.php');

$msg->printAll();


?>
<h3><?php echo _AT('profile_bundle_select'); ?></h3>

<p><?php echo _AT('admin_bundle_instructions'); ?></p>

<form name="form1" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<table class="data" summary="" rules="cols">
<thead>
<tr>
	<th span="col"><?php echo _AT('profile');   ?></th>
	<th span="col"><?php echo _AT('date');      ?></th>
	<th span="col"><?php echo _AT('bug_count'); ?></th>
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="3">
		<label for="email" ><?php echo _AT('recipient_address'); ?></label><br />
		<input type="text" id="email" name="email_add" value="" />
		<input type="submit" name="step2" value="<?php echo _AT('send_bundle'); ?>" />
		<input type="submit" name="back" value="<?php echo _AT('back_to_main'); ?>" />		
	</td>
</tr>
</tfoot>
<tbody>
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
					<tr onmousedown="document.form1['<?php echo $stamp.$day; ?>'].checked = !document.form1['<?php echo $stamp.$day; ?>'].checked;">
						<td><input type="checkbox" id="<?php echo $stamp.$day; ?>" value="<?php echo $day.':'.$stamp; ?>" name="file<?php echo $count_; ?>" onmouseup="this.checked=!this.checked" /><?php echo $count_; ?></td>					
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