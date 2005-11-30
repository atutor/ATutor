<?php

define('AT_INCLUDE_PATH', '../../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_ADMIN);

require('../SOAP_Google.php');

$key = $_config['gsearch'];

if (isset($_GET['submit'])) {

	//test key
	$google = new SOAP_Google($_GET['key']);
	$search_array = array();
	$search_array['filter'] = true;	
	$search_array['query'] = stripslashes('testing');
	$search_array['maxResults'] = 1;
	$search_array['lr'] = "lang_en";

	$result = $google->search($search_array);

	if (isset($result['faultstring'])) {
		$msg->addError('GOOGLE_KEY_INVALID');
	} else {
		$key = $addslashes($_GET['key']);
		$sql = "REPLACE INTO ".TABLE_PREFIX."config VALUES('gsearch','$key')";
		$result = mysql_query($sql, $db);

		$msg->addFeedback('GOOGLE_KEY_SAVED');
	}
}

require(AT_INCLUDE_PATH.'header.inc.php');

?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
	<div class="input-form" style="max-width: 525px">
		<div class="row">
			<?php echo _AT('google_key_txt'); ?>
		</div>
		<div class="row">
			<input type="text" name="key" size="80" value="<?php echo $key; ?>" style="min-width: 90%;" />
		</div>

		<div class="row buttons">
			<input type="submit" name="submit" value="<?php echo _AT('submit'); ?>" />
		</div>
	</div>

</form>


<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>