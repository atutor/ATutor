<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: merlot.php 6614 2006-09-27 19:32:29Z greg $


define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_MERLOT);


if($_POST['submit']){
	$_POST['merlot_location'] = trim($_POST['merlot_location']);
	$_POST['merlot_key'] = trim($_POST['merlot_key']);

	if (!$_POST['merlot_location']){
		$msg->addError('MERLOTURL_ADD_EMPTY');
	}

	if (!$_POST['merlot_key']){
		$msg->addError('MERLOTKEY_ADD_EMPTY');
	}		

	if (!$msg->containsErrors()) {
		$_POST['merlot_location'] = $addslashes($_POST['merlot_location']);
		$sql = "REPLACE INTO ".TABLE_PREFIX."config VALUES ('merlot_location', '$_POST[merlot_location]')";
		mysql_query($sql, $db);

		$_POST['merlot_key'] = $addslashes($_POST['merlot_key']);
		$sql = "REPLACE INTO ".TABLE_PREFIX."config VALUES ('merlot_key', '$_POST[merlot_key]')";
		mysql_query($sql, $db);
		
		$msg->addFeedback('MERLOT_CONFIG_SAVED');
	
		header('Location: '.$_SERVER['PHP_SELF']);
		exit;
	}
}
require (AT_INCLUDE_PATH.'header.inc.php');
?>
    <div class="input-form">
        <div class="row">
            <p><?php echo _AT('merlot_config');  ?></p>
        </div>
    </div>
<form action="<?php  $_SERVER['PHP_SELF']; ?>" method="post">
    <div class="input-form">
        <div class="row">
         	<p><label for="uri"><?php echo _AT('merlot_location'); ?></label></p>
            	<input type="text" name="merlot_location" value="<?php echo $_config['merlot_location']; ?>" id="uri" size="80" style="min-width: 95%;" />
    	     
		<p><label for="key"><?php echo _AT('merlot_key'); ?></label></p>
           	 <input type="text" name="merlot_key" value="<?php echo $_config['merlot_key']; ?>" id="key" size="80" style="min-width: 95%;" />
        </div>

        <div class="row buttons">
            <input type="submit" name="submit" value="<?php echo _AT('merlot_save'); ?>"  />
        </div>
    </div>
</form> 

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>