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

require(AT_INCLUDE_PATH.'header.inc.php');

if ($_POST['count'] == 0) {
	$msg->printErrors('NO_LOG_SELECTED');
}

echo '<br/><h3> Viewing Error Log(s) </h3>';

?>

<br/><form name="form1" method="post" action="<?php echo 'admin/error_logging_details.php'; ?>">

<table cellspacing="1" cellpadding="0" border="0" class="bodyline" width="95%" summary="" align="center">

	<tr><td height="1" class="row2" colspan="1"></td></tr>

		<?php
		
		for ($i = 0;$i < $_POST['count'];$i++) {
			$dump = file_get_contents(AT_CONTENT_DIR . 'logs/' . $_POST['file' . $i]);			
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
				$msg->printErrors(array('CANNOT_READ_FILE', AT_CONTENT_DIR . 'logs/' . $_POST['file' . $count]));
			}
		}
		
		$back_ref = '';
		$rest = '';
		// construct back reference to error_logging_details.php  with appropriate key and date passed through POST
		foreach($_POST as $elem => $tmp) {
			if (strpos($elem, 'file') !== false) {
				$back_ref = substr($tmp, 0, strpos($tmp, '/'));
				$rest = substr($tmp, strpos($tmp, '/')+ 1);
				break;
			}
		}
		
		$second = $back_ref;
		$back_ref = substr($rest, strrpos($rest, '_pr') + 3);
		$back_ref = substr($back_ref, 0, strpos($back_ref, '.log.php'));
		$back_ref .= ':' . $second;
		?>
		
	<tr><td height="1" class="row2" colspan="1"></td></tr>
	<tr>
		<td class="row1" align="center" colspan="1">
			<input type="hidden" name="data" value="<?php echo $back_ref; ?>"/>
			<input type="hidden" name="view" value="<?php echo ''; ?>"/>
			<br /><input type="submit" name="back" value="<?php echo 'Back to Profile'; ?>" class="button" /><br/><br/> 				  
		</td>
	</tr>
	</table>

	</form>
	<?php
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;