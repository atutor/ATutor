<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2009                                              */
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_ADMIN);

if (isset($_POST['up'])) {
	$up = key($_POST['up']);
	$_new_modules  = array();
	if (isset($_POST['main'])) {
		foreach ($_POST['main'] as $m) {
			if ($m == $up) {
				$last_m = array_pop($_new_modules);
				$_new_modules[] = $m;
				$_new_modules[] = $last_m;
			} else {
				$_new_modules[] = $m;
			}
		}

		$_POST['main'] = $_new_modules;
	}

	$_POST['submit'] = TRUE;
} else if (isset($_POST['down'])) {
	$_new_modules  = array();

	$down = key($_POST['down']);

	if (isset($_POST['main'])) {
		foreach ($_POST['main'] as $m) {
			if ($m == $down) {
				$found = TRUE;
				continue;
			}
			$_new_modules[] = $m;
			if ($found) {
				$_new_modules[] = $down;
				$found = FALSE;
			}
		}

		$_POST['main'] = $_new_modules;
	}

	$_POST['submit'] = TRUE;
}

if (isset($_POST['submit'])) {
	if (isset($_POST['main'])) {
		$_POST['main'] = array_intersect($_POST['main'], $_modules);
		$_POST['main'] = array_unique($_POST['main']);
		$main_links = implode('|', $_POST['main']);
	} else {
		$main_links = '';
	}

	$sql    = "REPLACE INTO ".TABLE_PREFIX."fha_student_tools VALUES ($_SESSION[course_id], '$main_links', 1)";
	$result = mysql_query($sql, $db);

	$msg->addFeedback('STUDENT_TOOLS_SAVED');
	header('Location: '.$_SERVER['PHP_SELF']);
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

$fha_student_tools = array();

$sql = "SELECT links FROM ".TABLE_PREFIX."fha_student_tools WHERE course_id=$_SESSION[course_id] AND links <> ''";
$result = mysql_query($sql, $db);
if ($row = mysql_fetch_assoc($result)) {
	$fha_student_tools = explode('|', $row['links']);
}

$_current_modules = array_merge($fha_student_tools, array_diff($_modules, $fha_student_tools));

$num_modules = count($fha_student_tools);
?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<table class="data" rules="rows" summary="">
<thead>
<tr>
	<th scope="cols"><?php echo _AT('section'); ?></th>
	<th><?php echo _AT('order'); ?></th>
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="2"><input type="submit" name="submit" value="<?php echo _AT('save'); ?>" accesskey="s" /></td>
</tr>
</tfoot>
<tbody>

<?php foreach ($_current_modules as $module): ?>
<?php if ($module == 'mods/_standard/student_tools/index.php') { continue; } ?>
<?php  ?>
<tr>
	<td>
		<?php if (in_array($module, $fha_student_tools)): ?>
			<input type="checkbox" name="main[]" value="<?php echo $module; ?>" id="m<?php echo $count; ?>" checked="checked" />
		<?php else: ?>
			<input type="checkbox" name="main[]" value="<?php echo $module; ?>" id="m<?php echo $count; ?>" />
		<?php endif; ?>
		<label for="m<?php echo $count; ?>"><?php 
			if (isset($_pages[$module]['title'])) {
				echo $_pages[$module]['title'];
			} else {
				echo _AT($_pages[$module]['title_var']);
		} ?></label>
	</td>

	<td align="right">
		<?php if (!in_array($module, $fha_student_tools)): ?>
			&nbsp;
		<?php else: ?>
			<?php if (($count != $num_main+1) && ($count > 1)): ?>
				<input type="submit" name="up[<?php echo $module; ?>]" value="<?php echo _AT('move_up'); ?>" title="<?php echo _AT('move_up'); ?>" style="background-color: white; border: 1px solid; padding: 0px;" />
			<?php else: ?>
				<img src="images/clr.gif" alt="" width="12" />
			<?php endif; ?>
			<?php if (($count != $num_main) && ($count < $num_modules)): ?>
				<input type="submit" name="down[<?php echo $module; ?>]" value="<?php echo _AT('move_down'); ?>" title="<?php echo _AT('move_down'); ?>" style="background-color: white; border: 1px solid; padding: 0px;"/>
			<?php else: ?>
				<img src="images/clr.gif" alt="" width="12" />
			<?php endif; ?>
		<?php endif; ?>
	</td>
</tr>
<?php 
$count++;
endforeach; ?>
</tbody>
</table>
</form>


<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>