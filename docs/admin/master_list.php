<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_USERS);

require(AT_INCLUDE_PATH.'header.inc.php');

if (!defined('AT_MASTER_LIST') || !AT_MASTER_LIST) {
	echo '... master list is disabled. enable it using the <a href="">config editor thing..</a>';
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

?>
what kind of options do we want?
view list? filter by created accounts, delete list, override list upon upload..

<form name="importForm" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
<div class="input-form">
	<div class="row">
		format.. encoding options
	</div>

	<div class="row">
		<label for="file"><?php echo _AT('file'); ?></label><br />
		<input type="file" name="file" size="40" id="file" />
	</div>
	
	<div class="row">
		<?php echo _AT('replace current list'); ?><br />
		<input type="radio" name="override" id="oy" value="1" /><label for="oy"><?php echo _AT('yes'); ?></label>
		<input type="radio" name="override" id="on" value="0" /><label for="on"><?php echo _AT('no'); ?></label>
	</div>

	<div class="row buttons">
		<input type= "submit" name="submit" value="<?php echo _AT('upload'); ?>" />
	</div>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>