<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id: $

define('AT_INCLUDE_PATH', '../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_ADMIN);

if (isset($_GET['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: users.php');
	exit;
}

if (isset($_GET['submit'])) {
	
	$_GET['mnot'] = intval($_GET['mnot']);
	$_GET['numbering'] = intval($_GET['numbering']);
	$_GET['use_jump_redirect'] = intval($_GET['use_jump_redirect']);
	$_GET['form_focus'] = intval($_GET['form_focus']);

	$default_theme = get_default_theme();
	//$default_lang = $_config['default_language'];

	$pref_defaults = array('PREF_THEME'=>$default_theme['dir_name'], 'PREF_NUMBERING'=>$_GET['numbering'], 'PREF_JUMP_REDIRECT'=>$_GET['use_jump_redirect'], 'PREF_FORM_FOCUS'=>$_GET['form_focus']);

	$pref_defaults = serialize($pref_defaults);

	if (!($_config_defaults['pref_defaults'] == $pref_defaults) && (strlen($pref_defaults) < 256)) {
		$sql    = "REPLACE INTO ".TABLE_PREFIX."config VALUES ('pref_defaults','$pref_defaults')";
	} else if ($_config_defaults['pref_defaults'] == $pref_defaults) {
		$sql    = "DELETE FROM ".TABLE_PREFIX."config WHERE name='pref_defaults'";
	}
	$result = mysql_query($sql, $db);

	$sql    = "REPLACE INTO ".TABLE_PREFIX."config VALUES ('pref_inbox_notify','$_GET[mnot]')";
	$result = mysql_query($sql, $db);

	$msg->addFeedback('PREFS_SAVED2');
	header('Location: '.$_SERVER['PHP_SELF']);
	exit;
}

$pref_defaults = unserialize($_config['pref_defaults']);

require(AT_INCLUDE_PATH.'header.inc.php');

?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" name="prefs">
<div class="input-form">

	<div class="row">
		<?php echo _AT('inbox_notification'); ?><br />
		<?php
			$yes = '';
			$no  = '';
			if ($_config['pref_inbox_notify']) {
				$yes = ' checked="checked"';
			} else {
				$no  = ' checked="checked"';
			}
		?>
		<input type="radio" name="mnot" id="mnot_yes" value="1" <?php echo $yes; ?> /><label for="mnot_yes"><?php echo _AT('enable'); ?></label> 
		<input type="radio" name="mnot" id="mnot_no" value="0" <?php echo $no; ?> /><label for="mnot_no"><?php echo _AT('disable'); ?></label>		
	</div>

	<div class="row">
		<?php echo _AT('show_numbers');  ?><br />
		<?php
			$num = '';  $num2 = '';
			if ($pref_defaults['PREF_NUMBERING'] == 1) {
				$num = ' checked="checked"';
			} else {
				$num2 = ' checked="checked"';
			}
			?><input type="radio" name ="numbering" id="num_en" value="1" <?php echo $num; ?> /><label for="num_en"><?php echo _AT('enable');  ?></label> 
			<input type="radio" name ="numbering" id="num_dis" value="0" <?php echo $num2; ?> /><label for="num_dis"><?php echo _AT('disable');  ?></label>
	</div>

	<div class="row">
		<?php echo _AT('jump_redirect'); ?><br />
		<?php
			$num = '';  $num2 = '';
			if ($pref_defaults['PREF_JUMP_REDIRECT'] == 1) {
				$num = ' checked="checked"';
			} else {
				$num2 = ' checked="checked"';
			}
			?><input type="radio" name ="use_jump_redirect" id="jump_en" value="1" <?php echo $num; ?> /><label for="jump_en"><?php echo _AT('enable');  ?></label> 
			<input type="radio" name ="use_jump_redirect" id="jump_dis" value="0" <?php echo $num2; ?> /><label for="jump_dis"><?php echo _AT('disable');  ?></label>
	</div>

	<!--div class="row">
		<?php echo _AT('auto_login1');  ?><br /><?php
			if ($pref_defaults['PREF_JUMP_REDIRECT'] == 1 ) {
				$auto_en = 'checked="checked"';
			} else {
				$auto_dis = 'checked="checked"';
			}
		?><input type="radio" name ="auto" id="auto_en" value="enable" <?php echo $auto_en; ?> /><label for="auto_en"><?php echo _AT('enable');  ?></label> 
		<input type="radio" name ="auto" id="auto_dis" value="disable" <?php echo $auto_dis; ?> /><label for="auto_dis"><?php echo _AT('disable');  ?></label>
	</div-->

	<div class="row">
		<?php echo _AT('form_focus');  ?><br />
		<?php
			$num = '';  $num2 = '';
			if ($pref_defaults['PREF_FORM_FOCUS'] == 1) {
				$num = ' checked="checked"';
			} else {
				$num2 = ' checked="checked"';
			}
			?><input type="radio" name ="form_focus" id="focus_on" value="1" <?php echo $num; ?> /><label for="focus_on"><?php echo _AT('enable');  ?></label> 
			<input type="radio" name ="form_focus" id="focus_off" value="0" <?php echo $num2; ?> /><label for="focus_off"><?php echo _AT('disable');  ?></label>
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" accesskey="s" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>"  />
	</div>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>