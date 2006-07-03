<?php 
/* start output buffering: */
global $savant, $_config;
ob_start(); ?>

<?php if($_GET['guest'] != '1'){ ?>
	<form name="loginform" method="post" action="<?php echo $_config['elluminate']; ?>">
		<label for="userName" class="normalbody"><?php echo _AT('elluminate_name'); ?>:</label><br> <input type="text" name="username"  id="userName" class="fieldOff" onFocus="doBg('username');" onBlur="dontBg('username');"><br>
		<label for="password" class="normalbody"><?php echo _AT('elluminate_password'); ?>:</label><br> <input type="password" name="password" class="fieldOff" onFocus="doBg('password');" onBlur="dontBg('password');"><br>
		<br><br>
		<input button type="submit" name="submit" value="Login" class="button">
	</form><br />
<?php if($_config['elluminate_pw'] != ''){ ?>
	<a href="<?php echo $_SERVER['PHP_SELF']; ?>?guest=1">Login as a guest</a>
<?php } ?>
<?php }else { ?>
	<form name="loginform" method="post" action="<?php echo $_config['elluminate']; ?>">
		<label for="userName" class="normalbody"><?php echo _AT('elluminate_name'); ?>:</label><br/> 
		<br /> <input type="text" name="username" id="userName" class="fieldOff" onFocus="doBg('username');" onBlur="dontBg('username');"><br>
		<input type="hidden" name="password" value="<?php echo  $_config['elluminate_pw']; ?>">
		<br><br>
		<input button type="submit" name="submit" value="Login" class="button">
	</form>
<?php } ?>

<?php
$savant->assign('dropdown_contents', ob_get_contents());
ob_end_clean();

$savant->assign('title', _AT('elluminate')); // the box title
$savant->display('include/box.tmpl.php');
?>