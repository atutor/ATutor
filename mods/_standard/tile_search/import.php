<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2012                                      */
/* Inclusive Design Institute                                   */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: import.php 10155 2010-09-08 18:05:52Z greg $

	define('AT_INCLUDE_PATH', '../../../include/');
	define('TR_INCLUDE_PATH', '../../../include/');

	if(isset($_POST['tile_course_id'], $_POST['aclcl'])){

		error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
		ini_set("display_errors", 1);

		require(AT_INCLUDE_PATH.'vitals.inc.php');

		require_once(AT_INCLUDE_PATH . 'classes/AContent_lcl/AContent_LiveContentLink.class.php');

		$ret = new AContent_LiveContentLink();
	
		if($ret->status){
			$msg->addError('IMPORT_FAILED');
		}else
			$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	
		header('Location: index.php');
		exit();
	}

//define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_CONTENT);

require(AT_INCLUDE_PATH.'header.inc.php');

if (!isset($_main_menu)) {
	$_main_menu = $contentManager->getContent();
}

function print_menu_sections(&$menu, $parent_content_id = 0, $depth = 0, $ordering = '') {
	$my_children = $menu[$parent_content_id];
	$cid = $_GET['cid'];

	if (!is_array($my_children)) {
		return;
	}
	foreach ($my_children as $children) {
		echo '<option value="'.$children['content_id'].'"';
		if ($cid == $children['content_id']) {
			echo ' selected="selected"';
		}
		echo '>';
		echo str_pad('', $depth, '-') . ' ';
		if ($parent_content_id == 0) {
			$new_ordering = $children['ordering'];
			echo $children['ordering'];
		} else {
			$new_ordering = $ordering.'.'.$children['ordering'];
			echo $ordering . '.'. $children['ordering'];
		}
		echo ' '.$children['title'].'</option>';

		print_menu_sections($menu, $children['content_id'], $depth+1, $new_ordering);
	}
}

?>

<?php

	$action	= $_SERVER['PHP_SELF'];

	$vars	= array('tile_course_id'	=> htmlentities($_GET['tile_course_id']),
					'cid'				=> '',
					'title'				=> htmlentities($_GET['title']),
					'desc'				=> htmlentities($_GET['desc']),
					'ordering'			=> '',
					'pid'				=> 0,
					'day'				=> date('d'),
					'month'				=> date('m'),
					'year'				=> date('Y'),
					'hour'				=> date('H'),
					'minute'			=> date('i'),
					'min'				=> 0,
					'alternatives'		=> '',
					'current_tab'		=> 0,
					'keywords'			=> '',
					'test_message'		=> '',
					'allow_test_export'	=> 0,
					'submit'			=> 'Save',
					'displayhead'		=> 0,
					'displaypaste'		=> 0,
					'complexeditor'		=> 0,
					'formatting'		=> 2,
					'head'				=> htmlentities($_GET['tile_course_id']),
					'body_text'			=> '',
					'weblink_text'		=> htmlentities($_GET['url']));

	// ***
	// ACC
	// changes the form action url
	// Lesson
	// Pages

	// LCL = Live Content Link

	// if I need to Link (AContent Live Content Link) the content
	if(isset($_GET['mode']) AND $_GET['mode'] == 'LCL')
		$vars['aclcl']		= 1;
	else
		$action	= 'mods/_core/imscp/ims_import.php?tile=1';

?>

<form name="form1" method="post" action="<?php echo $action; ?>" onsubmit="openWindow('<?php echo AT_BASE_HREF; ?>tools/prog.php?tile=1');">
	<?php
		foreach($vars as $name => $value){
			echo '<input type="hidden" value="'.$value.'" name="'.$name.'" />';
		}
	?>
	<input type="hidden" name="url" value="<?php echo AT_TILE_EXPORT_CC_URL.$_GET['tile_course_id']; ?>" />
	<input type="hidden" name="allow_a4a_import" value="1" />
<div class="input-form">

	<div class="row">
		<?php echo _AT('tile_import_content_package_about'); ?>
	</div>

	<div class="row">
	<strong><?php echo _AT('import_content_package_where'); ?>:</strong>
	<select name="cid">
		<option value="0"><?php echo _AT('import_content_package_bottom_subcontent'); ?></option>
		<option>--------------------------</option>
		<?php print_menu_sections($_main_menu); ?>
	</select>
	</div>
	<div class="row">
		<strong><?php echo _AT('import_content_package'); ?>:</strong> <?php echo urldecode($stripslashes($_GET['title'])); ?>
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('import'); ?>" /> <input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
</div>
</form>

<script type="text/javascript">
function openWindow(page) {
	newWindow = window.open(page, "progWin", "width=400,height=200,toolbar=no,location=no");
	newWindow.focus();
}
</script>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>