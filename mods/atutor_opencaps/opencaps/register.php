<?php 
/*
 * OpenCaps
 * http://opencaps.atrc.utoronto.ca
 * 
 * Copyright 2009 Heidi Hazelton
 * Adaptive Technology Resource Centre, University of Toronto
 * 
 * Licensed under the Educational Community License (ECL), Version 2.0. 
 * You may not use this file except in compliance with this License.
 * http://www.opensource.org/licenses/ecl2.php
 * 
 */

require('include/vitals.inc.php');

if (isset($_POST['newuser']) && $_POST['newuser']=="Register") {	

	$email = $addslashes($_POST['email']);
	$login = $addslashes($_POST['login']);

	$result = @mysql_query("SELECT * FROM members WHERE email='$email'",$db);
	if (@mysql_num_rows($result) != 0) {
		$_SESSION['errors'][] = 'Email address already in use. Try the Password Reminder.';
	}

	$result = @mysql_query("SELECT * FROM members WHERE login='$login'",$db);
	if (@mysql_num_rows($result) != 0) {
		$_SESSION['errors'][] = 'Login name already exists.';
	} 

	if (!isset($_SESSION['errors'])) {
		$realname	= $addslashes(trim($_POST['realname']));
		$email      = $addslashes(trim($_POST['email']));
		$login      	= $addslashes(trim($_POST['login']));
		$password  	= $addslashes(trim($_POST['password']));


		$sql = "INSERT INTO members VALUES (NULL, '$login', '$password', '$realname', '$email', NOW())";
		$result = @mysql_query($sql, $this_db->db);

		if (!$result) {
			$_SESSION['errors'][] = 'Database error - user not added.';
		} else {
			//send email to registrant
	
			$_SESSION['feedback'][] = 'Registration successful. Please login.';
			header('Location: index.php');
			exit;
		}
	}
}

require('include/basic_header.inc.php'); ?>

<script type="text/javascript">
<!--
	function validateRegForm() {
		var myform = document.forms[0];
		var errs = '';

		if (myform.realname.value == '') {
			errs = 'Name cannot be empty.\n';
		} else {
			var realname = myform.realname.value;
		}
		
		var at = myform.email.value.indexOf("@");
		var dot = myform.email.value.lastIndexOf(".");
		
		if (myform.email.value == '') {
			errs += 'Email cannot be empty.\n';
		} else if ( (at==-1 || dot==-1) || (dot<at) || (dot-at == 1) ) {
			errs += 'Incorrect email format.\n';
		} else {
			var email = myform.email.value;
		}
		
		if (myform.login.value == '') {
			errs += 'Login cannot be empty.\n';
		} else {
			var login = myform.login.value;
		}
		
		if (myform.password.value == '' || myform.password2.value == '' || myform.password.value != myform.password2.value) {
			errs += 'Passwords cannot be empty and must match.\n';
		} else {
			var password = myform.password.value;
		}		
		
		if (errs != '') {
			alert(errs);
			return false;
		} else {
			return true;
		}
	}

//-->
</script>
	
	<h2>Register</h2>
	<p>Create a new account with OpenCaps.</p>

	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="form" style="background-color:white;" onSubmit="javascript: return validateRegForm();">		
		
		<dl class="col-list" style="width:33%; margin-left:auto; margin-right:auto;">
			<dt><label for="realname">Name:</label></dt> 
				<dd><input name="realname" type="text" id="realname" value="<?php echo $_POST['realname']; ?>" /></dd>
			<dt><label for="email">Email:</label></dt> 
				<dd><input name="email" type="text" id="email" value="<?php echo $_POST['email']; ?>" /><br /><br /></dd>
	
			<dt><label for="login">Login:</label></dt> 
				<dd><input name="login" type="text" id="login" value="<?php echo $_POST['login']; ?>" /></dd>
			<dt><label for="pswd">Password:</label></dt> 
				<dd><input name="password" type="password" id="pswd" value="<?php echo $_POST['password']; ?>" /></dd>
			<dt><label for="pswd">Password Again:</label></dt> 
				<dd><input name="password2" type="password" id="pswd2" value="<?php echo $_POST['password2']; ?>" /></dd>
		</dl>
		<div style='text-align:right; margin-right:10px;'><input type="submit" name="newuser" value="Register" class="button" style="width:5em;" /> <input type="button" name="cancel" value="Cancel" class="button" style="width:5em;" onClick="javascript:history.back(1);" /> </div>
	</form>
</div>

</body>
</html>
