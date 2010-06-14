<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: index.php 8901 2009-11-11 19:10:19Z cindy $

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

require(AT_INCLUDE_PATH.'header.inc.php');

if (!isset($_main_menu)) {
	$_main_menu = $contentManager->getContent();
}

function print_menu_sections(&$menu, $only_print_content_folder = false, $parent_content_id = 0, $depth = 0, $ordering = '') {
	$my_children = $menu[$parent_content_id];
	$cid = $_GET['cid'];

	if (!is_array($my_children)) {
		return;
	}
	foreach ($my_children as $children) {
		/* test content association, we don't want to display the test pages
		 * as part of the menu section.  If test, skip it.
		 */
		if (isset($children['test_id'])){
			continue;
		}
		if ($only_print_content_folder && $children['content_type'] != CONTENT_TYPE_FOLDER) {
			continue;
		}

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

		print_menu_sections($menu, $only_print_content_folder, $children['content_id'], $depth+1, $new_ordering);
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
<form name="exportForm" method="post" action="mods/_core/imscp/ims_export.php">
<div class="input-form">
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('export_content'); ?></legend>
	<div class="row">
		<p><?php echo _AT('export_content_info'); ?></p>
	</div>

<?php if ($_main_menu[0]): ?>
	<div class="row">
		<label for="select_cid"><?php echo _AT('export_content_package_what'); ?></label><br />
		<select name="cid" id="select_cid">
			<option value="0"><?php echo _AT('export_entire_course_or_chap'); ?></option>
			<option value="0"></option>
			<?php
				print_menu_sections($_main_menu);
			?>
		</select>
	</div>

	<?php if (authenticate(AT_PRIV_ADMIN, AT_PRIV_RETURN)): ?>
			<div class="row">
				<input type="radio" name="export_as" id="to_cp" value="1" checked="checked" onclick="changeFormAction('cp');" />
				<label for="to_cp"><?php echo _AT('content_package'); ?></label> <br />
				<input type="radio" name="export_as" id="to_cc" value="1" onclick="changeFormAction('cc');" />
				<label for="to_cc"><?php echo _AT('common_cartridge'); ?> </label>
			</div>
			<div class="row">
				<input type="checkbox" name="to_tile" id="to_tile" value="1" />
				<label for="to_tile"><?php echo _AT('tile_export'); ?></label> <br />
				<input type="checkbox" name="to_a4a" id="to_a4a" value="1" />
				<label for="to_a4a"><?php echo _AT('a4a_export'); ?></label>
			</div>
	<?php endif; ?>
	
	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('export'); ?>" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
	</fieldset>
<?php else: ?>
	<div class="row">
		<strong><?php echo _AT('none_found'); ?></strong>
	</div>
<?php endif; ?>

</div>
</form>

<?php if (!authenticate(AT_PRIV_CONTENT, AT_PRIV_RETURN)) {
		require (AT_INCLUDE_PATH.'footer.inc.php'); 
		exit;
}
?>
<br /><br />


<form name="form1" method="post" action="mods/_core/imscp/ims_import.php" enctype="multipart/form-data" onsubmit="openWindow('<?php echo AT_BASE_HREF; ?>tools/prog.php');">
<div class="input-form">
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('import_content'); ?></legend>
	<div class="row">

		<p><?php echo _AT('import_content_info'); ?></p>
	</div>

	<div class="row">
		<label for="select_cid2"><?php echo _AT('import_content_package_where'); ?></label><br />
		<select name="cid" id="select_cid2">
			<option value="0"><?php echo _AT('import_content_package_bottom_subcontent'); ?></option>
			<option value="0"></option>
			<?php
				print_menu_sections($_main_menu, true);
			?>
		</select>
	</div>

	<div class="row">
		<input type="checkbox" name="allow_test_import" id="allow_test_import" checked="checked" />
		<label for="allow_test_import"><?php echo _AT('test_import_package'); ?></label> <br />
		<input type="checkbox" name="allow_a4a_import" id="allow_a4a_import" checked="checked" />
		<label for="allow_a4a_import"><?php echo _AT('a4a_import_package'); ?></label> <br />
		<input type="checkbox" name="ignore_validation" id="ignore_validation" value="1" />
		<label for="ignore_validation"><?php echo _AT('ignore_validation'); ?></label> <br />
	</div>
	
	<div class="row">
		<label for="to_file"><?php echo _AT('upload_content_package'); ?></label><br />
		<input type="file" name="file" id="to_file" />
	</div>

	<div class="row">
		<label for="to_url"><?php echo _AT('specify_url_to_content_package'); ?></label><br />
		<input type="text" name="url" value="http://" size="40" id="to_url" />
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" onclick="setClickSource('submit');" value="<?php echo _AT('import'); ?>" />
		<input type="submit" name="cancel" onclick="document.form1.enctype='';setClickSource('cancel');" value="<?php echo _AT('cancel'); ?>" />
	</div>
</div>
</form>

<script language="javascript" type="text/javascript">

var but_src;
function setClickSource(name) {
	but_src = name;
}

function openWindow(page) {
	if (but_src != "cancel") {
		newWindow = window.open(page, "progWin", "width=400,height=200,toolbar=no,location=no");
		newWindow.focus();
	}
}

//Change form action 
function changeFormAction(type){
	var obj = document.exportForm;
	if (type=="cc"){
		obj.action = "mods/_core/imscc/ims_export.php";
	} else if (type=="cp"){
		obj.action = "mods/_core/imscp/ims_export.php";
	}
}

</script>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>