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
define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_COURSE_TOOLS);


if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php');
	exit;
	
}

if (isset($_POST['submit'])) {

	$side_menu = '';
	$_stack_names = array();

	foreach($_stacks as $name=>$info) {
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

	$sql    = "UPDATE ".TABLE_PREFIX."courses SET side_menu='$side_menu' WHERE course_id=$_SESSION[course_id]";
	$result = mysql_query($sql, $db);
	$msg->addFeedback('COURSE_PREFS_SAVED');
	header('Location: index.php');
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
			$side_menu = explode("|", $system_courses[$_SESSION['course_id']]['side_menu']);			

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
		<input type="submit" name="submit" value="<?php echo _AT('apply'); ?>" accesskey="s" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>