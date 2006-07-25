<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2003 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: language_import.php 6526 2006-07-24 20:32:43Z joel $

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_LANGUAGES);
require(AT_INCLUDE_PATH.'header.inc.php');


$button_state = '';
if (AT_DEVEL_TRANSLATE == 0) {
	$button_state = 'disabled="disabled"';
}

?>

<form method="get">
<div class="input-form">
	<div class="row">
		<h3><?php echo _AT('translate'); ?></h3>
	</div>

	<div class="row">
		<p><?php echo _AT('translate_lang_howto'); ?></p>
	</div>

	<div class="row buttons">
		<input type="button" onclick="javascript:window.open('<?php echo $_base_href; ?>admin/translate_atutor.php', 'newWin1', 'toolbar=0, location=0, directories=0, status=0, menubar=0, scrollbars=1, resizable=1, copyhistory=0, width=640, height=480')" value="<?php echo _AT('translate'); ?>" <?php echo $button_state; ?> />
	</div>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>