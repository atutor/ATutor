<?php
/*
This is the main ATutor ccnet module page. It allows users to access
the UofT CCNet installation through courses that have CCNet enabled
*/
global $ccnet_url_db;
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

//////////
//Check to see if the url to ccnet exists in the db 
$sql = 'SELECT * from '.TABLE_PREFIX.'config WHERE name="ccnet"';
$result = mysql_query($sql, $db);

while($row = mysql_fetch_array($result)){
	$ccnet_url_db = $row[1];
}

require (AT_INCLUDE_PATH.'header.inc.php');

if($ccnet_url_db == ''){
	$msg->addInfo('CCNET_URL_ADD_REQUIRED');
}else{
?>
		<div class="input-form">
		<div class="row">
			<p><?php echo _AT('ccnet_text');  ?>
		</p>
			<div class="row buttons">
			<form>
			<input type="submit" value="<?php echo _AT('ccnet_open'); ?>" onclick="window.open('<?php echo $ccnet_url_db; ?>','mywindow','width=800,height=600,scrollbars=yes, resizable=yes'); return false" style="botton">
			</form>
			</div>
		</div>
		</div>
<?php
}

 require (AT_INCLUDE_PATH.'footer.inc.php'); ?>