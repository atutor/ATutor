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

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'lib/filemanager.inc.php');

authenticate(AT_PRIV_FILES);

$current_path = AT_CONTENT_DIR.$_SESSION['course_id'].'/';

if (($_REQUEST['popup'] == TRUE) || ($_REQUEST['framed'] == TRUE)) {
	$_header_file = AT_INCLUDE_PATH.'fm_header.php';
	$_footer_file = AT_INCLUDE_PATH.'fm_footer.php';
} else {
	$_header_file = AT_INCLUDE_PATH.'header.inc.php';
	$_footer_file = AT_INCLUDE_PATH.'footer.inc.php';
}
$popup  = $_REQUEST['popup'];
$framed = $_REQUEST['framed'];

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php?pathext='.$_POST['pathext'].SEP.'framed='.$_POST['framed'].SEP.'popup='.$_POST['popup']);
	exit;
}

if (isset($_POST['overwritenewfile'])) {

	$filename = preg_replace("{[^a-zA-Z0-9_]}","_", trim($_POST['filename']));

	if (($f = @fopen($current_path.$pathext.$filename.'.'.$_POST['extension'],'w')) && @fwrite($f,$_POST['body_text']) != false && @fclose($f)){
		$msg->addFeedback('FILE_OVERWRITE');
	} else {
		$msg->addError('CANNOT_OVERWRITE_FILE');
	}
	unset($_POST['newfile']);
	header('Location: index.php?pathext='.$pathext.SEP.'framed='.$framed.SEP.'popup='.$popup);
	exit;
}

if(isset($_POST['savenewfile'])) {

	if (isset($_POST['filename']) && ($_POST['filename'] != "")) {
		$filename     = preg_replace("{[^a-zA-Z0-9_]}","_", trim($_POST['filename']));
		$pathext      = $_POST['pathext'];
		$current_path = AT_CONTENT_DIR.$_SESSION['course_id'].'/';

		if (!@file_exists($current_path.$pathext.$filename.'.'.$_POST['extension'])) {
			$content = str_replace("\r\n", "\n", $_POST['body_text']);

			if (($f = fopen($current_path.$pathext.$filename.'.'.$_POST['extension'], 'w')) && (@fwrite($f, $content)!== false)  && (@fclose($f))) {
				$msg->addFeedback(array('FILE_SAVED', $filename.'.'.$_POST['extension']));
				header('Location: index.php?pathext='.urlencode($_POST['pathext']).SEP.'popup='.$_POST['popup']);
				exit;
			} else {
				$msg->addError('FILE_NOT_SAVED');
			}
		}
		else if (strpos($pathext, '..') !== false) {
			$msg->addError('UNKNOWN');
			header('Location: index.php?pathext='.$pathext.SEP.'framed='.$framed.SEP.'popup='.$popup);
			exit;
		}	
		
		else {
			require($_header_file);
			$pathext = $_POST['pathext']; 
			$popup   = $_POST['popup'];

			if ($popup == TRUE) {
				echo '<div align="right"><a href="javascript:window.close()">' . _AT('close_file_manager') . '</a></div>';
			}
			
			$msg->printWarnings(array('FILE_EXISTS', $filename));
			echo '<form name="form1" action="'.$_SERVER['PHP_SELF'].'" method="post">'."\n";
			echo '<input type="hidden" name="pathext"   value="'.$pathext.'" />'."\n";
			echo '<input type="hidden" name="popup"     value="'.$_POST['popup'].'" />'."\n";
			echo '<input type="hidden" name="filename"  value="'.$filename.'" />'."\n";
			echo '<input type="hidden" name="extension" value="'.$_POST['extension'].'" />'."\n";
			echo '<input type="hidden" name="body_text" value="'.$_POST['body_text'].'" />'."\n";
			echo '<div class="input-form">';
			echo '<div class="row buttons">';
			echo '<input type="submit" name="overwritenewfile" value="'._AT('overwrite').'" class="button"/>';
			echo '<input type="submit" name="cancel" value="'._AT('cancel').'" class="button"/></p>'."\n";
			echo '</div>';
			echo '</form>';
			$_POST['newfile'] = "new";
			require($_footer_file);
			exit;
		}
	} else {
		$msg->addError('NEED_FILENAME');
	}
}

require($_header_file);
$pathext = $_GET['pathext']; 
$popup   = $_GET['popup'];

if ($popup == TRUE) {
	echo '<div align="right"><a href="javascript:window.close()">' . _AT('close_file_manager') . '</a></div>';
}
	
$msg->printWarnings();
$msg->printErrors();
$msg->printFeedbacks();

?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
		<input type="hidden" name="pathext" value="<?php echo $_GET['pathext'] ?>" />
		<input type="hidden" name="popup" value="<?php echo $popup; ?>" />

		<div class="input-form">
			<div class="row">
				<label for="ctitle"><?php echo _AT('file_name');  ?></label><br />
				<input type="text" name="filename" id="ctitle" size="40" <?php if (isset($_POST['filename'])) echo 'value="'.$_POST['filename'].'"'?> />
			</div>

			<div class="row">
				<label for="extension"><?php echo _AT('type'); ?></label><br />
					<label><input type="radio" name="extension" value="html" checked="checked" /><?php echo _AT('html'); ?></label>
					<label><input type="radio" name="extension" value="txt" /><?php echo _AT('text'); ?></label>
			</div>

			<div class="row">
				<label for="body_text"><?php echo _AT('body');  ?></label><br />
				<textarea name="body_text" id="body_text" rows="25"><?php echo ContentManager::cleanOutput($_POST['body_text']); ?></textarea>
			</div>

			<div class="row buttons">
				<input type="submit" name="savenewfile" value="<?php echo _AT('save'); ?>" accesskey="s" />
				<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>"  />		
			</div>

			</div>
		</form>

<?php
	require($_footer_file);
?>
