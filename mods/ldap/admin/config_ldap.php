<?php
/*
Admin LDAP-auth config page
Based on ATutor create/edit pages

Maintainer smal (Serhiy Voyt)
smalgroup@gmail.com

Version 0.2
10.11.2008

Distributed under GPL (c)Sehiy Voyt 2005-2009
*/

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_ADMIN);


if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php');
	exit;
} else if (isset($_POST['submit'])) {
	$_POST['ldap_name']               = trim($_POST['ldap_name']);
	$_POST['ldap_port']               = intval($_POST['ldap_port']);
	$_POST['ldap_base_tree']          = trim($_POST['ldap_base_tree']);
	$_POST['ldap_attr_login']         = trim($_POST['ldap_attr_login']);
// 	$_POST['ldap_attr_password']      = trim($_POST['ldap_attr_password']);
	$_POST['ldap_attr_password']      = "__unused_ldap_field__";
	$_POST['ldap_attr_mail']          = trim($_POST['ldap_attr_mail']);
	$_POST['ldap_attr_last_name']     = trim($_POST['ldap_attr_last_name']);
	$_POST['ldap_attr_first_name']    = trim($_POST['ldap_attr_first_name']);
	$_POST['ldap_attr_second_name']   = trim($_POST['ldap_attr_second_name']);
	$_POST['ldap_attr_dob']           = trim($_POST['ldap_attr_dob']);
	$_POST['ldap_attr_gender']        = trim($_POST['ldap_attr_gender']);
	$_POST['ldap_attr_address']       = trim($_POST['ldap_attr_address']);
	$_POST['ldap_attr_postal']        = trim($_POST['ldap_attr_postal']);
	$_POST['ldap_attr_city']          = trim($_POST['ldap_attr_city']);
	$_POST['ldap_attr_province']      = trim($_POST['ldap_attr_province']);
	$_POST['ldap_attr_country']       = trim($_POST['ldap_attr_country']);
	$_POST['ldap_attr_phone']         = trim($_POST['ldap_attr_phone']);
	$_POST['ldap_attr_website']       = trim($_POST['ldap_attr_website']);
	
	//check that all required values have been set	
	if (!$_POST['ldap_name']) {
		$msg->addError('NO_LDAP_SERVER_NAME');
	}
	
	
	if (!$_POST['ldap_base_tree']) {
		$msg->addError('NO_LDAP_BASE_TREE');
	}
	
	
	if (!$_POST['ldap_attr_login']) {
		$msg->addError('NO_LDAP_ATTR_LOGIN');
	}
	
	if (!$_POST['ldap_attr_password']) {
		$msg->addError('NO_LDAP_ATTR_PASS');
	}
	
	if (!$_POST['ldap_attr_mail']) {
		$msg->addError('NO_LDAP_ATTR_MAIL');
	}
	
	if (!$_POST['ldap_attr_last_name']) {
		$msg->addError('NO_LDAP_ATTR_LAST_NAME');
	}
	
	if (!$_POST['ldap_attr_first_name']) {
		$msg->addError('NO_LDAP_ATTR_FIRST_NAME');
	}
	
	if (!$_POST['ldap_attr_second_name']) {
		$msg->addError('NO_LDAP_ATTR_SECOND_NAME');
	}
	
	if (!$msg->containsErrors()) {
		$_POST['ldap_name']               = $addslashes($_POST['ldap_name']);
		$_POST['ldap_base_tree']          = $addslashes($_POST['ldap_base_tree']);
		$_POST['ldap_attr_login']         = $addslashes($_POST['ldap_attr_login']);
		$_POST['ldap_attr_password']      = $addslashes($_POST['ldap_attr_password']);
		$_POST['ldap_attr_mail']          = $addslashes($_POST['ldap_attr_mail']);
		$_POST['ldap_attr_last_name']     = $addslashes($_POST['ldap_attr_last_name']);
		$_POST['ldap_attr_first_name']    = $addslashes($_POST['ldap_attr_first_name']);
		$_POST['ldap_attr_second_name']   = $addslashes($_POST['ldap_attr_second_name']);
		$_POST['ldap_attr_dob']           = $addslashes($_POST['ldap_attr_dob']);
		$_POST['ldap_attr_gender']        = $addslashes($_POST['ldap_attr_gender']);
		$_POST['ldap_attr_address']       = $addslashes($_POST['ldap_attr_address']);
		$_POST['ldap_attr_postal']        = $addslashes($_POST['ldap_attr_postal']);
		$_POST['ldap_attr_city']          = $addslashes($_POST['ldap_attr_city']);
		$_POST['ldap_attr_province']      = $addslashes($_POST['ldap_attr_province']);
		$_POST['ldap_attr_country']       = $addslashes($_POST['ldap_attr_country']);
		$_POST['ldap_attr_phone']         = $addslashes($_POST['ldap_attr_phone']);
		$_POST['ldap_attr_website']       = $addslashes($_POST['ldap_attr_website']);
				 
		foreach ($_POST as $name => $value) {
			$sql    = "SELECT * FROM ".TABLE_PREFIX."config_ldap WHERE name='$name'";
			$result = mysql_query($sql, $db);
			$row = mysql_fetch_array($result);
			echo $row['name']." -> ". $row['value']."<br>";
			if (($name != 'submit') && isset($_POST[$name]) && ($_POST[$name] != $row['value'])) {
				$sql = "UPDATE ".TABLE_PREFIX."config_ldap SET value='$_POST[$name]' WHERE name='$name'";
				mysql_query($sql,$db);
				}
			}
		
		$msg->addFeedback('SYSTEM_PREFS_SAVED');
		header('Location:index.php');
		exit;
		
		
	}
}

$onload = 'document.form.sitename.focus();';

require(AT_INCLUDE_PATH.'header.inc.php');

if (!isset($_POST['submit'])) {
	$sql    = "SELECT * FROM ".TABLE_PREFIX."config_ldap";
	$result = mysql_query($sql,$db);
	
	if (!($row = mysql_fetch_assoc($result))){
		$msg->printErrors('SOME_ERROR');
		require (AT_INCLUDE_PATH.'footer.inc.php');
		exit;
		}
	$result = mysql_query($sql,$db);
	while ($row = mysql_fetch_assoc($result)){
		$_POST[$row['name']] = $row['value'];
		}	

}
?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">
<div class="input-form">
	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="sitename"><?php echo _AT('ldap_name'); ?></label><br />
		<input type="text" name="ldap_name" size="40" maxlength="60" id="ldapname" value="<?php echo $stripslashes(htmlspecialchars($_POST['ldap_name'])); ?>" />
	</div>

	<div class="row">
		<div class="required" title="<?php echo _AT('required_field');
		?>">*</div><label for="ldap_port"><?php echo _AT('ldap_port'); ?></label><br />
		<select name="ldap_port" id="ldap_port" value=<?php echo $stripslashes(htmlspecialchars($_POST['ldap_port'])); ?> >
		<option value="389"<?php if ($_POST['ldap_port'] == 389) {echo 'selected="selected"'; }?>>389</option>
		<option value="636"<?php if ($_POST['ldap_port'] == 636) {echo 'selected="selected"'; }?>>636</option>
		</select>
	</div>

	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="ldap_base_tree"><?php echo _AT('ldap_base_tree'); ?></label><br />
		<input type="text" name="ldap_base_tree" size="40" maxlength="60" id="ldap_base_tree" value="<?php echo $stripslashes(htmlspecialchars($_POST['ldap_base_tree'])); ?>" />
	</div>
		
	<div class="row">
		<label><?php echo _AT('ldap_attr');?></label></br>
		<div class="row" title="<?php echo _AT('ldap_attr_login'); ?>">
		<div class="required" title="<?php echo _AT('required_field'); ?>"><small><small>*</small></small></div>
		<label for="ldap_attr_login"><small><small><?php echo _AT('ldap_attr_login'); ?></small></small></label><br />
		<input type="text" name="ldap_attr_login" size="39" maxlength="50" id="ldap_attr_login" value="<?php echo $stripslashes(htmlspecialchars($_POST['ldap_attr_login']));?>" />
		</div>
	
		
		<!--<div type ="hidden" class="row" title="<?php echo _AT('ldap_attr_password'); ?>">
		<div class="required" title="<?php /*echo _AT('required_field'); ?>"><small><small>*</small></small></div>
		<label for="ldap_attr_password"><small><small><?php echo _AT('ldap_attr_password'); ?></small></small></label><br />
		<input type="text" name="ldap_attr_password" size="39" maxlength="50" id="ldap_attr_password" value="<?php echo $stripslashes(htmlspecialchars($_POST['ldap_attr_password']));*/?>" />
		</div>
	-->
		<div class="row" title="<?php echo _AT('ldap_attr_mail'); ?>">
		<div class="required" title="<?php echo _AT('required_field'); ?>"><small><small>*</small></small></div>
		<label for="ldap_attr_mail"><small><small><?php echo _AT('ldap_attr_mail'); ?></small></small></label><br />
		<input type="text" name="ldap_attr_mail" size="39" maxlength="50" id="ldap_attr_mail" value="<?php echo $stripslashes(htmlspecialchars($_POST['ldap_attr_mail']));?>" />
		</div>
		
		<div class="row" title="<?php echo _AT('ldap_attr_last_name'); ?>">
		<div class="required" title="<?php echo _AT('required_field'); ?>"><small><small>*</small></small></div>
		<label for="ldap_attr_last_name"><small><small><?php echo _AT('ldap_attr_last_name'); ?></small></small></label><br />
		<input type="text" name="ldap_attr_last_name" size="39" maxlength="50" id="ldap_attr_last_name" value="<?php echo $stripslashes(htmlspecialchars($_POST['ldap_attr_last_name']));?>" />
		</div>

		<div class="row" title="<?php echo _AT('ldap_attr_first_name'); ?>">
		<div class="required" title="<?php echo _AT('required_field'); ?>"><small><small>*</small></small></div>
		<label for="ldap_attr_first_name"><small><small><?php echo _AT('ldap_attr_first_name'); ?></small></small></label><br />
		<input type="text" name="ldap_attr_first_name" size="39" maxlength="50" id="ldap_attr_first_name" value="<?php echo $stripslashes(htmlspecialchars($_POST['ldap_attr_first_name']));?>" />
		</div>
		
		<div class="row" title="<?php echo _AT('ldap_attr_second_name'); ?>">
		<div class="required" title="<?php echo _AT('required_field'); ?>"><small><small>*</small></small></div>
		<label for="ldap_attr_second_name"><small><small><?php echo _AT('ldap_attr_second_name'); ?></small></small></label><br />
		<input type="text" name="ldap_attr_second_name" size="39" maxlength="50" id="ldap_attr_second_name" value="<?php echo $stripslashes(htmlspecialchars($_POST['ldap_attr_second_name']));?>" />
		</div>
		
		<div class="row" title="<?php echo _AT('ldap_attr_dob'); ?>">
		<label for="ldap_attr_dob"><small><small><?php echo _AT('ldap_attr_dob'); ?></small></small></label><br />
		<input type="text" name="ldap_attr_dob" size="39" maxlength="50" id="ldap_attr_dob" value="<?php echo $stripslashes(htmlspecialchars($_POST['ldap_attr_dob']));?>" />
		</div>
		
		<div class="row" title="<?php echo _AT('ldap_attr_gender'); ?>">
		<label for="ldap_attr_gender"><small><small><?php echo _AT('ldap_attr_gender'); ?></small></small></label><br />
		<input type="text" name="ldap_attr_gender" size="39" maxlength="50" id="ldap_attr_gender" value="<?php echo $stripslashes(htmlspecialchars($_POST['ldap_attr_gender'])); ?>" />
		</div>
		
		<div class="row" title="<?php echo _AT('ldap_attr_address'); ?>">
		<label for="ldap_attr_address"><small><small><?php echo _AT('ldap_attr_address'); ?></small></small></label><br />
		<input type="text" name="ldap_attr_address" size="39" maxlength="50" id="ldap_attr_address" value="<?php echo $stripslashes(htmlspecialchars($_POST['ldap_attr_address']));?>" />
		</div>
		
		<div class="row" title="<?php echo _AT('ldap_attr_postal'); ?>">
		<label for="ldap_attr_postal"><small><small><?php echo _AT('ldap_attr_postal'); ?></small></small></label><br />
		<input type="text" name="ldap_attr_postal" size="39" maxlength="50" id="ldap_attr_postal" value="<?php echo $stripslashes(htmlspecialchars($_POST['ldap_attr_postal'])); ?>" />
		</div>
		
		<div class="row" title="<?php echo _AT('ldap_attr_city'); ?>">
		<label for="ldap_attr_city"><small><small><?php echo _AT('ldap_attr_city'); ?></small></small></label><br />
		<input type="text" name="ldap_attr_city" size="39" maxlength="50" id="ldap_attr_city" value="<?php echo $stripslashes(htmlspecialchars($_POST['ldap_attr_city'])); ?>" />
		</div>
		
		<div class="row" title="<?php echo _AT('ldap_attr_province'); ?>">
		<label for="ldap_attr_province"><small><small><?php echo _AT('ldap_attr_province'); ?></small></small></label><br />
		<input type="text" name="ldap_attr_province" size="39" maxlength="50" id="ldap_attr_province" value="<?php echo $stripslashes(htmlspecialchars($_POST['ldap_attr_province'])); ?>" />
		</div>
		
		<div class="row" title="<?php echo _AT('ldap_attr_country'); ?>">
		<label for="ldap_attr_country"><small><small><?php echo _AT('ldap_attr_country'); ?></small></small></label><br />
		<input type="text" name="ldap_attr_country" size="39" maxlength="50" id="ldap_attr_country" value="<?php echo $stripslashes(htmlspecialchars($_POST['ldap_attr_country'])); ?>" />
		</div>
		
		<div class="row" title="<?php echo _AT('ldap_attr_phone'); ?>">
		<label for="ldap_attr_phone"><small><small><?php echo _AT('ldap_attr_phone'); ?></small></small></label><br />
		<input type="text" name="ldap_attr_phone" size="39" maxlength="50" id="ldap_attr_phone" value="<?php echo $stripslashes(htmlspecialchars($_POST['ldap_attr_phone'])); ?>" />
		</div>
		
		<div class="row" title="<?php echo _AT('ldap_attr_website'); ?>">
		<label for="ldap_attr_website"><small><small><?php echo _AT('ldap_attr_website'); ?></small></small></label><br />
		<input type="text" name="ldap_attr_website" size="39" maxlength="50" id="ldap_attr_website" value="<?php echo $stripslashes(htmlspecialchars($_POST['ldap_attr_website'])); ?>" />
		</div>
		
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" accesskey="s"  />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>"  />
	
	</div>
	
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>