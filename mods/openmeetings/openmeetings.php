<?php
/*
This is the ATutor Openmeetings module page. It allows an admin user
to set or edit  the URL for the Openmeetings installation for ATutor, and define an optional guest password.

*/
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
$_custom_css = $_base_path . 'mods/openmeetings/module.css'; // use a custom stylesheet
admin_authenticate(AT_ADMIN_PRIV_OPENMEETINGS);

if (isset($_POST['submit'])) {
	$_POST['om_uri'] = $addslashes(trim($_POST['om_uri']));
	$_POST['om_username'] = $addslashes(trim($_POST['om_username']));
	$_POST['om_userpass'] = $addslashes(trim($_POST['om_userpass']));

	if (!$_POST['om_uri']){
		$msg->addError('OPENMEETINGS_URL_ADD_EMPTY');
	}
	if (!$_POST['om_username']){
		$msg->addError('OPENMEETINGS_USERNAME_ADD_EMPTY');
	}
	if (!$_POST['om_userpass']){
		$msg->addError('OPENMEETINGS_USERPASS_ADD_EMPTY');
	}	
			
	if (!$msg->containsErrors()) {
		$_POST['om_uri'] = $addslashes($_POST['om_uri']);
		$sql = "REPLACE INTO ".TABLE_PREFIX."config VALUES ('openmeetings_location', '$_POST[om_uri]'), ('openmeetings_username', '$_POST[om_username]'), ('openmeetings_userpass', '$_POST[om_userpass]')";
		mysql_query($sql, $db);
		$msg->addFeedback('OPENMEETINGS_URL_ADD_SAVED');

		header('Location: '.$_SERVER['PHP_SELF']);
		exit;
	}
}

require (AT_INCLUDE_PATH.'header.inc.php');

?>
<form action="<?php  $_SERVER['PHP_SELF']; ?>" method="post">
	<div class="input-form">
		<div class="row">
			<p><label for="om_uri"><?php echo _AT('openmeetings_location'); ?></label></p>	
			<input type="text" name="om_uri" value="<?php echo $_config['openmeetings_location']; ?>" id="om_uri" size="80" style="min-width: 95%;" />
		</div>
		<div class="row">
			<p><label for="om_username"><?php echo _AT('openmeetings_username'); ?></label></p>	
			<input type="text" name="om_username" value="<?php echo $_config['openmeetings_username']; ?>" id="om_username" size="20" />

			<p><label for="om_userpass"><?php echo _AT('openmeetings_userpass'); ?></label></p>	
			<input type="text" name="om_userpass" value="<?php echo $_config['openmeetings_userpass']; ?>" id="om_userpass" size="20" />
		</div>
		<div class="row buttons">
			<input type="submit" name="submit" value="<?php echo _AT('save'); ?>"  />
		</div>
	</div>
</form>
<div>
<a href="<?php echo $_SERVER['PHP_SELF']; ?>" onclick="window.open('<?php echo $_config['openmeetings_location']; ?>','openmeetingswin','width=800,height=720,scrollbars=yes, resizable=yes'); return false"><?php echo  _AT('openmeetings_own_window'); ?></a> </li>

<?php exit; if ($_config['openmeetings_location'] != ''): ?>
<iframe name="openmeetings" id="openmeetings" title="Openmeetings" frameborder="1" scrolling="auto" src="<?php echo $_config['openmeetings_location']; ?>/index.jsp" height="500" width="90%" align="center" style="border:thin white solid; align:center;" allowautotransparency="true"></iframe>
<?php endif; ?>

</div>
<?php  require (AT_INCLUDE_PATH.'footer.inc.php'); ?>