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

authenticate(AT_PRIV_STYLES);

if (isset($_POST['submit'])) {

	$side_menu = "";
	foreach($_POST['stack'] as $dropdown) {
		$side_menu .= $dropdown . "|";
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
<p><?php echo _AT('save_default_prefs_how'); ?></p>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="prefs">
<div class="input-form" style="width:50%">
	<div class="row">
		<?php
			$num_stack = count($_stacks);

			for ($i = 0; $i< 8; $i++) {
				echo '<select name="stack['.$i.']">'."\n";
				echo '<option value="">'._AT('empty').'</option>'."\n";
				for ($j = 0; $j<$num_stack; $j++) {
					echo '<option value="'.$j.'"';
					if (isset($_SESSION['prefs'][PREF_STACK][$i]) && ($j == $_SESSION[prefs][PREF_STACK][$i])) {
						echo ' selected="selected"';
					}
					echo '>'._AT($_stacks[$j]).'</option>'."\n";
				}
				echo '</select>'."\n";
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