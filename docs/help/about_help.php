<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
$page = 'help';
$_user_location	= 'users';


define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

$_section[0][0] = _AT('help');
$_section[0][1] = 'help/';
$_section[1][0] = _AT('about_atutor_help');


require(AT_INCLUDE_PATH.'header.inc.php');
$temp_mini = $_SESSION['prefs'][PREF_MINI_HELP];
?>

<p><?php echo _AT('help_forms'); ?></p>

<ul>
	<li><?php
		echo _AT('atutor_help_boxes');
		$msg->printHelps('DEMO_HELP2'); 

	?><br /></li>

	<li><?php
		echo _AT('context_sensative_help_text').' ';

	/* turn on mini help temporarily if its not on */
	$old_pref_value = $_SESSION['prefs'][PREF_MINI_HELP];
	$_SESSION['prefs'][PREF_MINI_HELP] = 1;
	print_popup_help('DEMO_HELP', '');
	$_SESSION['prefs'][PREF_MINI_HELP] = $old_pref_value;

	?>.</li>
</ul>

<?php
 	$_SESSION['prefs'][PREF_MINI_HELP] = $temp_mini;
	require(AT_INCLUDE_PATH.'footer.inc.php');
?>