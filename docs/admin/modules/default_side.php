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
// $Id$ $

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

admin_authenticate(AT_ADMIN_PRIV_ADMIN);


if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: ../courses.php');
	exit;
}

if (isset($_POST['submit'])) {

	$side_menu = '';
	$_stack_names = array();

	foreach($_stacks as $name=>$file) {
		$_stack_names[] = $name;
	}

	$_POST['stack'] = array_unique($_POST['stack']);
	$_POST['stack'] = array_intersect($_POST['stack'], $_stack_names);

	foreach($_POST['stack'] as $dropdown) {
		if($dropdown != '') {
			$side_menu .= $dropdown . '|';
		}
	}
	$side_menu = substr($side_menu, 0, -1);

	if (!($_config_defaults['side_defaults'] == $side_menu) && (strlen($side_menu) < 256)) {
		$sql    = "REPLACE INTO ".TABLE_PREFIX."config VALUES('side_defaults', '$side_menu')";
	} else if ($_config_defaults['side_defaults'] == $side_menu) {
		$sql    = "DELETE FROM ".TABLE_PREFIX."config WHERE name='side_defaults'";
	}

	$result = mysql_query($sql, $db);
	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	header('Location:'. $_SERVER[PHP_SELF]);
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="prefs">
<div class="input-form" style="width:50%">
	<div class="row">
		<p><?php echo _AT('side_menu_text'); ?></p>
	</div>

	<div class="row">
		<?php
			$num_stack = count($_stacks);	

			$side_menu = explode('|', $_config['side_defaults']);

			for ($i=0; $i<$num_stack; $i++) {				
				echo '<select name="stack['.$i.']">';
				echo '<option value=""></option>';
				foreach ($_stacks as $name=>$info) {
					if (isset($info['title'])) {
						$title = $info['title'];
					} else {
						$title = _AT($info['title_var']);
					}
					echo '<option value="'.$name.'"';
					if (isset($side_menu[$i]) && ($name == $side_menu[$i])) {
						echo ' selected="selected"';
					}
					echo '>'.$title.'</option>';
				}
				echo '</select>';
				echo '<br />'; 
			} ?>
	</div>

	<div class="buttons">
		<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" accesskey="s" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>"  />
	</div>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>