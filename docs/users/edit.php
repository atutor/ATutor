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

$page = 'profile';
$_user_location	= 'users';
define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

$_section[0][0] = _AT('profile');

$title = _AT('edit_profile'); 

if ($_SESSION['valid_user'] !== true) {
	require(AT_INCLUDE_PATH.'header.inc.php');

	$info[] = array(AT_INFOS_INVALID_USER, $_SESSION['course_id']);
	print_infos($info);

	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}


if (isset($_POST['cancel'])) {
	Header('Location: index.php?f='.AT_FEEDBACK_CANCELLED);
	exit;
}

if (isset($_GET['auto']) && ($_GET['auto'] == 'disable')) {

	$parts = parse_url($_base_href);

	setcookie('ATLogin', '', time()-172800, $parts['path'], $parts['host'], 0);
	setcookie('ATPass',  '', time()-172800, $parts['path'], $parts['host'], 0);
	Header('Location: index.php?f='.urlencode_feedback(AT_FEEDBACK_AUTO_DISABLED));
	exit;
} else if (isset($_GET['auto']) && ($_GET['auto'] == 'enable')) {
	$parts = parse_url($_base_href);

	$sql	= "SELECT PASSWORD(password) AS pass FROM ".TABLE_PREFIX."members WHERE member_id=$_SESSION[member_id]";
	$result = mysql_query($sql, $db);
	$row	= mysql_fetch_array($result);

	setcookie('ATLogin', $_SESSION['login'], time()+172800, $parts['path'], $parts['host'], 0);
	setcookie('ATPass',  $row['pass'], time()+172800, $parts['path'], $parts['host'], 0);

	header('Location: index.php?f='.urlencode_feedback(AT_FEEDBACK_AUTO_ENABLED));
	exit;
}


if ($_POST['submit']) {
	$error = '';

	// email check
	if ($_POST['email'] == '') {
		$errors[]=AT_ERROR_EMAIL_MISSING;
	} else {
		if(!eregi("^[a-z0-9\._-]+@+[a-z0-9\._-]+\.+[a-z]{2,3}$", $_POST['email'])) {
			$errors[]=AT_ERROR_EMAIL_INVALID;
		}
		$result = mysql_query("SELECT * FROM ".TABLE_PREFIX."members WHERE email='$_POST[email]' AND member_id<>$_SESSION[member_id]",$db);
		if(mysql_num_rows($result) != 0) {
			$errors[]=AT_ERROR_EMAIL_EXISTS;
		}
	}

	// password check
	if ($_POST['password'] == '') { 
		$errors[] = AT_ERROR_PASSWORD_MISSING;
	}
	// check for valid passwords
	if ($_POST['password'] != $_POST['password2']) {
		$errors[] = AT_ERROR_PASSWORD_MISMATCH;
	}
		
	//check date of birth
	$mo = intval($_POST['month']);
	$day = intval($_POST['day']);
	$yr = intval($_POST['year']);

	if ($yr < date('y')) { 
		$yr += 2000; 
	} else if ($yr < 1900) { 
		$yr += 1900; 
	} 

	$dob = $yr.'-'.$mo.'-'.$day;
	if ($mo && $day && $yr && !checkdate($mo, $day, $yr)) {	
		$errors[]=AT_ERROR_DOB_INVALID;
	} else if (!$mo || !$day || !$yr) {
		$dob = '0000-00-00';
		$yr = $mo = $day = 0;
	}
		
	$login = strtolower($_POST['login']);
	if (!$errors) {			
		if (($_POST['web_site']) && (!ereg('://',$_POST['web_site']))) { $_POST['web_site'] = 'http://'.$_POST['web_site']; }
		if ($_POST['web_site'] == 'http://') { $_POST['web_site'] = ''; }

		// insert into the db.
		$_POST['password'] = $addslashes($_POST['password']);
		$_POST['website'] = $addslashes($_POST['website']);
		$_POST['first_name'] = $addslashes($_POST['first_name']);
		$_POST['last_name'] = $addslashes($_POST['last_name']);
		$_POST['address'] = $addslashes($_POST['address']);
		$_POST['postal'] = $addslashes($_POST['postal']);
		$_POST['city'] = $addslashes($_POST['city']);
		$_POST['province'] = $addslashes($_POST['province']);
		$_POST['country'] = $addslashes($_POST['country']);
		$_POST['phone'] = $addslashes($_POST['phone']);

		$sql = "UPDATE ".TABLE_PREFIX."members SET password='$_POST[password]', email='$_POST[email]', website='$_POST[website]', first_name='$_POST[first_name]', last_name='$_POST[last_name]', dob='$dob', gender='$_POST[gender]', address='$_POST[address]', postal='$_POST[postal]', city='$_POST[city]', province='$_POST[province]', country='$_POST[country]', phone='$_POST[phone]', language='$_SESSION[lang]' WHERE member_id=$_SESSION[member_id]";

		$result = mysql_query($sql,$db);
		if (!$result) {
			$errors[]=AT_ERROR_DB_NOT_UPDATED;
			print_errors($errors);
			exit;
		}

		header('Location: ./index.php?f='.urlencode_feedback(AT_FEEDBACK_PROFILE_UPDATED));
		exit;
	}
}

require(AT_INCLUDE_PATH.'header.inc.php');
	
echo '<h2>'._AT('profile').'</h2>';

/* verify that this user owns this profile */
$sql	= "SELECT status FROM ".TABLE_PREFIX."members WHERE member_id=$_SESSION[member_id]";
$result = mysql_query($sql, $db);

if (!($row = mysql_fetch_array($result))) {
	$errors[]=AT_ERROR_CREATE_NOPERM;
	print_errors($errors);
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}
print_errors($errors);
?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<table align="center" cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="">
<?php
	$sql	= 'SELECT * FROM '.TABLE_PREFIX.'members WHERE member_id='.$_SESSION['member_id'];
	$result = mysql_query($sql,$db);
	$row = mysql_fetch_array($result);

	if ($_POST['submit']){
		$row['password']	= $_POST['password'];
		$row['email']		= $_POST['email'];
		$row['first_name']	= $_POST['first_name'];
		$row['last_name']	= $_POST['last_name'];
		$row['dob']			= $dob;
		$row['address']		= $_POST['address'];
		$row['postal']		= $_POST['postal'];
		$row['city']		= $_POST['city'];
		$row['province']	= $_POST['province'];
		$row['country']		= $_POST['country'];
		$row['phone']		= $_POST['phone'];
		$row['website']		= $_POST['website'];
	}

?>
<tr>
	<th colspan="2" class="cyan"><?php   echo _AT('account_information'); ?></th>
</tr>
<tr>
	<td class="row1" align="right"><?php echo _AT('login_name'); ?>:</td>
	<td class="row1"><?php echo $row['login'];?></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right" valign="top"><label for="password"><?php   echo _AT('password'); ?>:</label></td>
	<td class="row1" valign="top"><input id="password" class="formfield" name="password" type="password"  size="15" maxlength="15" value="<?php echo stripslashes(htmlspecialchars($row['password'])); ?>" /><br />
	<small class="spacer">&middot; <?php echo _AT('combination'); ?><br />
	&middot; <?php echo _AT('15_max_chars'); ?></small></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><label for="password2"><?php echo _AT('password_again'); ?>:</label></td>
	<td class="row1"><input id="password2" class="formfield" name="password2" type="password" size="15" maxlength="15" value="<?php if ($_POST['submit']){ echo stripslashes(htmlspecialchars($_POST['password2'])); } else { echo stripslashes(htmlspecialchars($row['password'])); }?>" /></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right" valign="top"><label for="email"><?php   echo _AT('email_address'); ?>:</label></td>
	<td class="row1"><input id="email" class="formfield" name="email" type="text" size="30" maxlength="60"  value="<?php echo stripslashes(htmlspecialchars($row['email']));?>" /></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right" valign="top"><label for="langs"><?php echo _AT('language'); ?>:</label></td>
	<td class="row1"><?php $languageManager->printDropdown($_SESSION['lang'], 'lang', 'pri_langs'); ?></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right" valign="top"><label for="language"><?php echo _AT('status'); ?>:</label></td>
	<td class="row1" align="left">
<?php
	if ($row['status']) { 
		echo _AT('instructor'); 
	} else { 
		echo _AT('student'); 
		if (ALLOW_INSTRUCTOR_REQUESTS) {
			echo ' <br /><a href="users/request_instructor.php">'._AT('request_instructor_account').'</a>';
		} else {
			echo '<br /><small>'._AT('request_instructor_disabled').'</small>';
		}
	}
?>
	</td>
</tr>
<?php
echo '<tr><td height="1" class="row2" colspan="2"></td></tr>';
	echo '<tr><td class="row1" align="right">'._AT('auto_login1').':</td><td class="row1">';
	if ( ($_COOKIE['ATLogin'] != '') && ($_COOKIE['ATPass'] != '') ) {
		echo _AT('auto_enable');
	} else {
		echo _AT('auto_disable');
	}
	
	echo '<br /><br /></td></tr>';
?>
<tr>
	<th colspan="2" class="cyan"><?php echo _AT('personal_information'); ?></th> 
</tr>
<tr>
	<td class="row1" align="right"><label for="first_name"><?php   echo _AT('first_name'); ?>:</label></td>
	<td class="row1"><input id="first_name" class="formfield" name="first_name" type="text" value="<?php echo stripslashes(htmlspecialchars($row['first_name']));?>" /></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><label for="last_name"><?php   echo _AT('last_name'); ?>:</label></td>
	<td class="row1"><input id="last_name" class="formfield" name="last_name" type="text"  value="<?php echo stripslashes(htmlspecialchars($row['last_name']));?>" /></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><?php echo _AT('date_of_birth'); ?>:</td>
	<td class="row1">
	<?php
	$dob = explode('-',$row['dob']); 

	if (!isset($yr) && ($dob[0] > 0)) { $yr = $dob[0]; }
	if (!isset($mo) && ($dob[1] > 0)) { $mo = $dob[1]; }
	if (!isset($day) && ($dob[2] > 0)) { $day = $dob[2]; }
	?>
	<input title="<?php echo _AT('day'); ?>" id="day" class="formfield" name="day" type="text" size="2" maxlength="2" value="<?php echo $day; ?>" />-<input title="<?php echo _AT('month'); ?>" id="month" class="formfield" name="month" type="text" size="2" maxlength="2" value="<?php echo $mo; ?>" />-<input title="<?php echo _AT('year'); ?>" id="year" class="formfield" name="year" type="text" size="4" maxlength="4" value="<?php echo $yr; ?>" /><small> <?php echo _AT('dd_mm_yyyy'); ?></small>
	</td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><?php   echo _AT('gender'); ?>:</td>
	<td class="row1"><?php
	if ($row['gender'] == 'm'){
		$m = ' checked="checked"';
	}
	if ($row['gender'] == 'f'){
		$f = ' checked="checked"';
	}

	?><input type="radio" name="gender" id="m" <?php echo $m;?> value="m" /><label for="m"><?php   echo _AT('male'); ?></label> <input type="radio" value="f" name="gender" id="f" <?php echo $f;?>  size="2" maxlength="2" /><label for="f"><?php   echo _AT('female'); ?></label> <input type="radio" value="ns" name="gender" id="ns" <?php if (($row['gender'] == 'ns') || ($row['gender'] == '')) { echo 'checked="checked"'; } ?> /><label for="ns"><?php echo _AT('not_specified'); ?></label></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><label for="address"><?php   echo _AT('street_address'); ?>:</label></td>
	<td class="row1"><input id="address" class="formfield" name="address" size="40" type="text"   value="<?php echo stripslashes(htmlspecialchars($row['address']));?>" /></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><label for="postal"><?php   echo _AT('postal_code'); ?>:</label></td>
	<td class="row1"><input id="postal" class="formfield" name="postal" size="7" type="text"   value="<?php echo stripslashes(htmlspecialchars($row['postal']));?>" /></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><label for="city"><?php   echo _AT('city'); ?>:</label></td>
	<td class="row1"><input id="city" class="formfield" name="city" type="text" value="<?php echo stripslashes(htmlspecialchars($row['city'])); ?>" /><br /></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><label for="province"><?php   echo _AT('province'); ?>:</label></td>
	<td class="row1"><input id="province" class="formfield" name="province" type="text"   value="<?php echo stripslashes(htmlspecialchars($row['province']));?>" /></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><label for="country"><?php   echo _AT('country'); ?>:</label></td>
	<td class="row1"><input id="country" class="formfield" name="country" type="text"   value="<?php echo stripslashes(htmlspecialchars($row['country']));?>" /></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right" valign="top"><label for="phone"><?php   echo _AT('phone'); ?>:</label></td>
	<td class="row1"><input class="formfield" size="11" name="phone" id="phone" type="text" value="<?php echo stripslashes(htmlspecialchars($row['phone']));?>" /> <small>(Eg. 123-456-7890)</small></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right" valign="top"><label for="website"><?php   echo _AT('web_site'); ?>:</label></td>
	<td class="row1"><input id="website" class="formfield" name="website" size="40" type="text" value="<?php echo stripslashes(htmlspecialchars($row['website']));?>" /><br /><br /></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="center" colspan="2"><input type="submit" class="button" value=" <?php   echo _AT('update_profile'); ?> [Alt-s]" name="submit" accesskey="s" /> <input type="submit" name="cancel" class="button" value=" <?php echo  _AT('cancel'); ?>" /></td>
</tr>
</table>
</form>
<?php
	require(AT_INCLUDE_PATH.'footer.inc.php');
?>