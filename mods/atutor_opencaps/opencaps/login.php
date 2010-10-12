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

if (isset($_POST['submit'])) {
	
	$this_user->login($_POST['login'], $_POST['password']);

}

include('include/basic_header.inc.php');
?>
<style>
	body { background-color:white; }
</style>
	<script language="JavaScript" src="../jscripts/sha-1factory.js" type="text/javascript"></script>

	<script language="JavaScript" type="text/javascript">
	//<!--
	  function crypt_sha1() {
		document.form.password_hidden.value = hex_sha1(document.form.password.value + "<?php echo $_SESSION['token']; ?>");
		document.form.password.value = "";
		return true;
	  }
	 //-->
	</script>	
	<h2>Login</h2>

	<p>To start a new captioning project or to return to an ongoing project, please login below. If you are new here, quickly <a href="register.php">register</a> with us!</p>
	
	
	<form action="login.php" method="post" id="form">
	
		<h1 style="float:left; margin-left:10px;"><img src="images/logo_sm.png" alt="Capscribe logo" /></h1>
	
		<dl class="col-list" style="width:33%; margin-left:auto; margin-right:auto;">
			<dt><label for="login">Login:</label></dt> 
				<dd><input name="login" type="text" id="login" value="" /></dd>
			<dt><label for="pswd">Password:</label></dt> 
				<dd><input name="password" type="password" id="pswd" value="" /></dd>
		</dl>
		<div style="text-align:right">
			<input type="submit" name="submit" value="Submit" class="button" style="width:5em; margin-right:10px;" />
		</div>
	</form>

</body>
</html>