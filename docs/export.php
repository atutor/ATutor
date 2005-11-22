<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

define('AT_INCLUDE_PATH', './include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php');
	exit;
} else if (isset($_POST['submit'])) {
	header('Location: '.$_base_href.'tools/ims/ims_export.php?cid=' . intval($_POST['cid']));
	exit;
}

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

	if (!authenticate(AT_PRIV_CONTENT, AT_PRIV_RETURN) && ($_SESSION['packaging'] == 'none')) {
		echo '<p>'._AT('content_packaging_disabled').'</p>';
		require (AT_INCLUDE_PATH.'footer.inc.php'); 
		exit;
	} else if (!authenticate(AT_PRIV_CONTENT, AT_PRIV_RETURN) && ($_SESSION['packaging'] == 'top')) {
		$_main_menu = array($_main_menu[0]);
	}
?>


<form method="post" action="<?php $_SERVER['PHP_SELF']?>">
<div class="input-form">
	<div class="row">
		<h3><?php echo _AT('export_content'); ?></h3>
		<p><?php echo _AT('export_content_info'); ?></p>
	</div>

	<div class="row">
		<label for="select_cid"><?php echo _AT('export_content_package_what'); ?></label><br />
		<select name="cid" id="select_cid">
			<option value="0"><?php echo _AT('export_entire_course_or_chap'); ?></option>
			<option>--------------------------</option>
			<?php
				print_menu_sections($_main_menu);
			?>
		</select>
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('export'); ?>" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>