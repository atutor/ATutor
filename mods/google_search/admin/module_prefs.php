<?php

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_ADMIN);

$key = $_config['gsearch'];

if (isset($_GET['submit'])) {
	if (!empty($_GET['key'])) {
		$key = $addslashes($_GET['key']);
		$sql = "REPLACE INTO ".TABLE_PREFIX."config VALUES('gsearch','$key')";
		$result = mysql_query($sql, $db);

		$msg->addFeedback('GOOGLE_KEY_SAVED');

	} else {
		$msg->addError('GOOGLE_KEY_EMPTY');
	}
}

require(AT_INCLUDE_PATH.'header.inc.php');

?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
	<div class="input-form" style="max-width: 525px">
		<div class="row">
			<?php echo _AT('google_key_txt'); ?>
		</div>
		<div class="row buttons">
			<input type="text" name="key" class="input" size="50" value="<?php echo $key; ?>" /> <input type="submit" class="submit" value="<?php echo _AT('submit'); ?>" name="submit" />
		</div>
	</div>

</form>


<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>