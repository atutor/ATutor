<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2009										*/
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: index_admin.php 8485 2009-05-25 20:50:55Z hwong $

define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_SOCIAL);
$_custom_css = $_base_path . AT_SOCIAL_BASENAME . 'module.css'; // use a custom stylesheet

if($_POST['save']){
	$shindig_url = $addslashes($_POST['shindig_url']);
	$sql = "REPLACE into ".TABLE_PREFIX."config (name,value) VALUES('shindig_url','$shindig_url')";
	if($result = mysql_query($sql, $db)){
		 $msg->addFeedback('SOCIAL_SETTINGS_SAVED');
	}else{
 		$msg->addError('SOCIAL_SETTINGS_NOT_SAVED');
	}
	header("Location: ".$_SERVER['PHP_SELF']);
	exit;
}

require (AT_INCLUDE_PATH.'header.inc.php');
?>


<div class="headingbox"><h3><?php echo _AT('admin_social'); ?></h3></a></div>
<div class="contentbox">
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
	<p><?php echo _AT('shindig_blurb'); ?></p>
		<dl id="public-profile">
			<dt><label for="shindig"><?php echo _AT('shindig_url'); ?></dt>
			<dl><input type="text" id="shindig" name="shindig_url" size="60" value="<?php echo $_config['shindig_url']; ?>" /></dd>
		</dl>
	<input type="submit" name="save" value="<?php echo _AT('save'); ?>">
</form>
</div>
<?php 

require (AT_INCLUDE_PATH.'footer.inc.php'); ?>