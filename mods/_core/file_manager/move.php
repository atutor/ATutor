<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2010                                      */
/* Inclusive Design Institute                                   */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require_once(AT_INCLUDE_PATH.'../mods/_core/file_manager/filemanager.inc.php');

if (!authenticate(AT_PRIV_FILES,AT_PRIV_RETURN)) {
	authenticate(AT_PRIV_CONTENT);
}


$current_path = AT_CONTENT_DIR.$_SESSION['course_id'].'/';

$popup = $_REQUEST['popup'];
$framed = $_REQUEST['framed'];

if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php?pathext='.$_POST['pathext'].SEP.'framed='.$_POST['framed'].SEP.'popup='.$_POST['popup'].SEP.'cp='.$_POST['cp'].SEP.'cid='.$_POST['cid'].SEP.'pid='.$_POST['pid'].SEP.'a_type='.$_POST['a_type']);
	exit;
}

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php?pathext='.$_POST['pathext'].SEP.'framed='.$_REQUEST['framed'].SEP.'popup='.$_REQUEST['popup'].SEP.'cp='.$_POST['cp'].SEP.'cid='.$_POST['cid'].SEP.'pid='.$_POST['pid'].SEP.'a_type='.$_POST['a_type']);
	exit;
}

if (isset($_POST['submit_yes'])) {
	$dest = $_POST['dest'] .'/';
	$pathext = $_POST['pathext'];

	if (isset($_POST['listofdirs'])) {

		$_dirs = explode(',',$_POST['listofdirs']);
		$count = count($_dirs);
		
		for ($i = 0; $i < $count; $i++) {
			$source = $_dirs[$i];
			
			if (course_realpath($current_path . $pathext . $source) == FALSE) {
				// error: File does not exist
				$msg->addError('DIR_NOT_EXIST');
				header('Location: index.php?pathext='.$pathext.SEP.'framed='.$framed.SEP.'popup='.$popup.SEP.'cp='.$_POST['cp'].SEP.'cid='.$_POST['cid'].SEP.'pid='.$_POST['pid'].SEP.'a_type='.$_POST['a_type']);
				exit;
			}
			else if (course_realpath($current_path . $dest) == FALSE) {
				// error: File does not exist
				$msg->addError('UNKNOWN');
				header('Location: index.php?pathext='.$pathext.SEP.'framed='.$framed.SEP.'popup='.$popup.SEP.'cp='.$_POST['cp'].SEP.'cid='.$_POST['cid'].SEP.'pid='.$_POST['pid'].SEP.'a_type='.$_POST['a_type']);
				exit;
			}
			else if (strpos($source, '..') !== false) {
				$msg->addError('UNKNOWN');
				header('Location: index.php?pathext='.$pathext.SEP.'framed='.$framed.SEP.'popup='.$popup.SEP.'cp='.$_POST['cp'].SEP.'cid='.$_POST['cid'].SEP.'pid='.$_POST['pid'].SEP.'a_type='.$_POST['a_type']);
				exit;
			}	
			else {
				@rename($current_path.$pathext.$source, $current_path.$dest.$source);
			}
		}
		$msg->addFeedback('DIRS_MOVED');
	}
	if (isset($_POST['listoffiles'])) {

		$_files = explode(',',$_POST['listoffiles']);
		$count = count($_files);

		for ($i = 0; $i < $count; $i++) {
			$source = $_files[$i];
			
			if (course_realpath($current_path . $pathext . $source) == FALSE) {
				// error: File does not exist
				$msg->addError('FILE_NOT_EXIST');
				header('Location: index.php?pathext='.$pathext.SEP.'framed='.$framed.SEP.'popup='.$popup.SEP.'cp='.$_POST['cp'].SEP.'cid='.$_POST['cid'].SEP.'pid='.$_POST['pid'].SEP.'a_type='.$_POST['a_type']);
				exit;
			}
			else if (course_realpath($current_path . $dest) == FALSE) {
				// error: File does not exist
				$msg->addError('UNKNOWN');
				header('Location: index.php?pathext='.$pathext.SEP.'framed='.$framed.SEP.'popup='.$popup.SEP.'cp='.$_POST['cp'].SEP.'cid='.$_POST['cid'].SEP.'pid='.$_POST['pid'].SEP.'a_type='.$_POST['a_type']);
				exit;
			}
			else if (strpos($source, '..') !== false) {
				$msg->addError('UNKNOWN');
				header('Location: index.php?pathext='.$pathext.SEP.'framed='.$framed.SEP.'popup='.$popup.SEP.'cp='.$_POST['cp'].SEP.'cid='.$_POST['cid'].SEP.'pid='.$_POST['pid'].SEP.'a_type='.$_POST['a_type']);
				exit;
			}	
			else {
				@rename($current_path.$pathext.$source, $current_path.$dest.$source);
			}
		}
		$msg->addFeedback('MOVED_FILES');
	}
	header('Location: index.php?pathext='.$_POST['pathext'].SEP.'framed='.$_POST['framed'].SEP.'popup='.$_POST['popup'].SEP.'cp='.$_POST['cp'].SEP.'cid='.$_POST['cid'].SEP.'pid='.$_POST['pid'].SEP.'a_type='.$_POST['a_type']);
	exit;
}

if (isset($_POST['dir_chosen'])) {
	$hidden_vars['framed']  = $_REQUEST['framed'];
	$hidden_vars['popup']   = $_REQUEST['popup'];
	$hidden_vars['pathext'] = $_REQUEST['pathext'];
	$hidden_vars['dest']    = $_REQUEST['dir_name'];
	$hidden_vars['cp']  = $_REQUEST['cp'];
	$hidden_vars['cid']   = $_REQUEST['cid'];
	$hidden_vars['pid'] = $_REQUEST['pid'];
	$hidden_vars['a_type']    = $_REQUEST['a_type'];
	
	if (isset($_POST['files'])) {
		$list_of_files = implode(' <br />', $_POST['files']);
		$hidden_vars['listoffiles'] = $list_of_files;
		$msg->addConfirm(array('FILE_MOVE', "<br />".$list_of_files."<br />", $_POST['dir_name']), $hidden_vars);
	}
	if (isset($_POST['dirs'])) {
		$list_of_dirs = implode(',', $_POST['dirs']);
		$hidden_vars['listoffiles'] = $list_of_dirs;
		$msg->addConfirm(array('DIR_MOVE', $list_of_dirs, $_POST['dir_name']), $hidden_vars);
	}
	require(AT_INCLUDE_PATH.'header.inc.php');
	$msg->printConfirm();
	require(AT_INCLUDE_PATH.'footer.inc.php');
} 
else {
	require(AT_INCLUDE_PATH.'header.inc.php');
	
	$tree = AT_CONTENT_DIR.$_SESSION['course_id'].'/';
	$file    = $_GET['file'];
	$pathext = $_GET['pathext']; 
	$popup   = $_GET['popup'];
	$framed  = $_GET['framed'];
	$cp  = $_GET['cp'];
	$cid  = $_GET['cid'];
	$pid  = $_GET['pid'];
	$a_type  = $_GET['a_type'];
	
	/* find the files and directories to be copied */
	$total_list = explode(',', $_GET['list']);

	$count = count($total_list);
	$countd = 0;
	$countf = 0;
	for ($i=0; $i<$count; $i++) {
		if (is_dir($current_path.$pathext.$total_list[$i])) {
			$_dirs[$countd] = $total_list[$i];
			$hidden_dirs  .= '<input type="hidden" name="dirs['.$countd.']"   value="'.$_dirs[$countd].'" />';
			$countd++;
		} else {
			$_files[$countf] = $total_list[$i];
			$hidden_files .= '<input type="hidden" name="files['.$countf.']" value="'.$_files[$countf].'" />';
			$countf++;
		}
	}
?>

<form name="move_form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<div class="input-form">
	<div class="row">
		<p><?php echo _AT('select_directory'); ?></p>
	</div>
	
	<div class="row">
		<ul>
			<li class="folders"><label><input type="radio" name="dir_name" value="<?php echo _AT('home'); ?>"<?php
				if ($pathext == '') {
					echo ' checked="checked"';
					$here = ' ' . _AT('current_location');
				} 
				echo '/>'._AT('home').' ' .$here.'</label>';
			
				echo display_tree($current_path, '', $pathext);
			?></li>
		</ul>
	</div>

	<div class="row buttons">
		<input type="submit" name="dir_chosen" value="<?php echo _AT('move'); ?>" accesskey="s" /> 
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
</div>

<input type="hidden" name="pathext" value="<?php echo $pathext; ?>" />
<input type="hidden" name="framed" value="<?php echo $framed; ?>" />
<input type="hidden" name="popup" value="<?php echo $popup; ?>" />
<input type="hidden" name="cp" value="<?php echo $cp; ?>" />
<input type="hidden" name="cid" value="<?php echo $cid; ?>" />
<input type="hidden" name="pid" value="<?php echo $pid; ?>" />
<input type="hidden" name="a_type" value="<?php echo $a_type; ?>" />
			<?php
	echo $hidden_dirs;
	echo $hidden_files;
?>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php');
}
?>