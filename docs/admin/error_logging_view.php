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

if (isset($_POST['back'])) {
	header('Location: error_logging.php');
	exit;
}

if (!isset($_POST['view_profile'])) {
	if ($_POST['count'] == 0) {
		$msg->addError('NO_LOG_SELECTED');
		header('Location: error_logging.php');
	}
}

$ok = false;
if (isset($_POST['view'])) { // check if a bug was selected
	foreach($_POST as $elem => $val) {
		if (strpos($elem, 'file') == 0) {
			$ok = true;
			break;
		}
	}
}

require(AT_INCLUDE_PATH.'header.inc.php');
echo '<br/><h3>' . _AT('viewing_errors') .  '</h3>';

?>

<br/><form name="form1" method="post" action="<?php echo 'admin/error_logging_details.php'; ?>">

<table cellspacing="1" cellpadding="0" border="0" class="bodyline" width="95%" summary="" align="center">

	<tr><td height="1" class="row2" colspan="1"></td></tr>

		<?php
		if (isset($_POST['view_profile'])) {
			// lets just dump the profile file
			$dump = file_get_contents(AT_CONTENT_DIR . 'logs/' . $_POST['profile_date'] . '/' . 'profile_' . $_POST['profile_id'] . '.log.php');
			if ($dump !== false) {
				?>
					<tr>
						<td class="row1" align="left" colspan="1"><span style="font-family:arial">

					<?php
						echo $dump;
					?>
					</span></td>
				</tr>
				<?php
			} else {
				$msg->printErrors(array('CANNOT_READ_FILE', AT_CONTENT_DIR . 'logs/' . $_POST['profile_date'] . '/' . 'profile_' . $_POST['profile_id'] . '.log.php'));
			}
		} else {

			if ($ok === false) {
				$msg->printErrors('NO_LOG_SELECTED');
			}
			
			for ($i = 0;$i < $_POST['count'];$i++) {
				$dump = file_get_contents(AT_CONTENT_DIR . 'logs/' . $_POST['file' . $i]);			
				
				if ($dump == '') continue;
				if ($dump !== false) {
					?>
						<tr>
							<td class="row1" align="left" colspan="1"><span style="font-family:arial">
		
						<?php
							echo $dump;
							echo '<hr/>';
						?>
						</span></td>
					</tr>
					<?php
				} else {
					$msg->printErrors(array('CANNOT_READ_FILE', AT_CONTENT_DIR . 'logs/' . $_POST['file' . $count]));
				}	
			}
		}
		
		$back_ref = $_POST['profile_id'] . ':' . $_POST['profile_date'];
		?>
		
	<tr><td height="1" class="row2" colspan="1"></td></tr>
	<tr>
		<td class="row1" align="center" colspan="1">
			<input type="hidden" name="data" value="<?php echo $back_ref; ?>"/>
			<input type="hidden" name="view" value="<?php echo ''; ?>"/>
			<br /><input type="submit" name="back" value="<?php echo _AT('back_to_profile'); ?>" class="button" /><br/><br/> 				  
		</td>
	</tr>
	</table>

	</form>
	<?php
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;