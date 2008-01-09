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
// $Id$

define('AT_INCLUDE_PATH', '../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_STYLES);

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

	if (isset($_POST['home'])) {
		$_new_modules  = array();
		foreach ($_POST['home'] as $m) {
			if ($m == $up) {
				$last_m = array_pop($_new_modules);
				$_new_modules[] = $m;
				$_new_modules[] = $last_m;
			} else {
				$_new_modules[] = $m;
			}
		}

		$_POST['home'] = $_new_modules;
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

	if (isset($_POST['home'])) {
		$_new_modules  = array();
		foreach ($_POST['home'] as $m) {
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

		$_POST['home'] = $_new_modules;
	}

	$_POST['submit'] = TRUE;
}

// 'search.php',  removed
if (isset($_POST['submit'])) {

	if (isset($_POST['main'])) {
		$_POST['main'] = array_intersect($_POST['main'], $_modules);
		$_POST['main'] = array_unique($_POST['main']);
		$main_links = implode('|', $_POST['main']);
	} else {
		$main_links = '';
	}

	if (isset($_POST['home'])) {
		$_POST['home'] = array_intersect($_POST['home'], $_modules);
		$_POST['home'] = array_unique($_POST['home']);
		$home_links = implode('|', $_POST['home']);
	} else {
		$home_links = '';
	}

	$sql    = "UPDATE ".TABLE_PREFIX."courses SET home_links='$home_links', main_links='$main_links' WHERE course_id=$_SESSION[course_id]";
	$result = mysql_query($sql, $db);

	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	header('Location: modules.php');
	exit;
}


require(AT_INCLUDE_PATH.'header.inc.php');


//being displayed
$_current_modules = array_slice($_pages[AT_NAV_COURSE], 1, -1); // removes index.php and tools/index.php
$num_main    = count($_current_modules);
//main and home merged
$_current_modules = array_merge( (array) $_current_modules, array_diff($_pages[AT_NAV_HOME],$_pages[AT_NAV_COURSE]) );
$num_modules = count($_current_modules);
//all other mods
$_current_modules = array_merge( (array) $_current_modules, array_diff($_modules, $_current_modules));

$count = 0;

?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<table class="data static" rules="rows" summary="">
<thead>
<tr>
	<th scope="cols"><?php echo _AT('section'); ?></th>
	<th><?php echo _AT('location'); ?></th>
	<th><?php echo _AT('order'); ?></th>
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="3" style="text-align:right;"><input type="submit" name="submit" value="<?php echo _AT('save'); ?>" accesskey="s" /></td>
</tr>
</tfoot>
<tbody>
<?php foreach ($_current_modules as $module): ?>
<?php $count++; ?>
<tr>
	<td><?php 
		if (isset($_pages[$module]['title'])) {
			echo $_pages[$module]['title'];
		} else {
			echo _AT($_pages[$module]['title_var']);
		} ?></td>
	<td>
		<?php if (in_array($module, $_pages[AT_NAV_COURSE])): ?>
			<input type="checkbox" name="main[]" value="<?php echo $module; ?>" id="m<?php echo $count; ?>" checked="checked" /><label for="m<?php echo $count; ?>"><?php echo _AT('main_navigation'); ?></label>
		<?php else: ?>
			<input type="checkbox" name="main[]" value="<?php echo $module; ?>" id="m<?php echo $count; ?>" /><label for="m<?php echo $count; ?>"><?php echo _AT('main_navigation'); ?></label>
		<?php endif; ?>

		<?php if (in_array($module, $_pages[AT_NAV_HOME])): ?>
			<input type="checkbox" name="home[]" value="<?php echo $module; ?>" id="h<?php echo $count; ?>" checked="checked" /><label for="h<?php echo $count; ?>"><?php echo _AT('home'); ?></label>
		<?php else: ?>
			<input type="checkbox" name="home[]" value="<?php echo $module; ?>" id="h<?php echo $count; ?>" /><label for="h<?php echo $count; ?>"><?php echo _AT('home'); ?></label>
		<?php endif; ?>
	</td>
	<td align="right">
		<?php if (!in_array($module, $_pages[AT_NAV_HOME]) && !in_array($module, $_pages[AT_NAV_COURSE])): ?>
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
<?php endforeach; ?>
</tbody>
</table>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>