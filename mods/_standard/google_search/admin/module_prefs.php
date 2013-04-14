<?php
define('AT_INCLUDE_PATH', '../../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_ADMIN);
require(AT_INCLUDE_PATH.'header.inc.php');

$key = $_config['gsearch'];
$googleType = $_config['gtype'];

//For AJAX validation.  If valid key, save it.
if (isset($_GET['keyIsValidated'])){
	$_GET['key'] = trim($_GET['key']);
	$_GET['gtype'] = trim($_GET['gtype']);
	if ($_GET['keyIsValidated']=='true'){
		$key = $addslashes($_GET['key']);
		$sql = "REPLACE INTO ".TABLE_PREFIX."config VALUES('gsearch','$key')";
		$result = mysql_query($sql, $db);
		$msg->addFeedback('GOOGLE_KEY_SAVED');
	} elseif ($_GET['keyIsValidated']=='false'){
		//If invalid, remove whatever key that's in the system
		$msg->addError('GOOGLE_KEY_INVALID');
		$key = htmlspecialchars($stripslashes($_GET['key']));
		$sql = "DELETE FROM ".TABLE_PREFIX."config WHERE name='gsearch'";
		$result = mysql_query($sql, $db);
		$key = '';
	}
	//Manually print it out
	$msg->printAll();
}

if (isset($_POST['submit'])) {
	require('../SOAP_Google.php');
	$_POST['key'] = trim($_POST['key']);
	$_POST['gtype'] = trim($_POST['gtype']);

	if ($_POST['key']) {
		//Default google search type to soap
		if (!isset($_POST['gtype'])){
			$_POST['gtype'] = GOOGLE_TYPE_SOAP;
		}
		if ($_POST['gtype']==GOOGLE_TYPE_SOAP){
			//test key
			$google = new SOAP_Google($_POST['key']);
			$search_array = array();
			$search_array['filter'] = true;	
			$search_array['query'] = 'testing';
			$search_array['maxResults'] = 1;
			$search_array['lr'] = "lang_en";

			$result = $google->search($search_array);

			if (isset($result['faultstring'])) {
				//If it is invalid, remove whatever keys that are in the system.
				$msg->addError('GOOGLE_KEY_INVALID');
				$key = htmlspecialchars($stripslashes($_POST['key']));
				$sql = "DELETE FROM ".TABLE_PREFIX."config WHERE name='gsearch'";
				$result = mysql_query($sql, $db);
				$key = '';
			} else {
				$key = $addslashes($_POST['key']);
				$sql = "REPLACE INTO ".TABLE_PREFIX."config VALUES('gsearch','$key')";
				$result = mysql_query($sql, $db);
				$msg->addFeedback('GOOGLE_KEY_SAVED');
			}
		} elseif ($_POST['gtype']==GOOGLE_TYPE_AJAX){			
			$key = $addslashes($_POST['key']);
			$gtype = $addslashes($_POST['gtype']);
			//Test the key by the script site.
			?>		
			   <script src="http://www.google.com/uds/api?file=uds.js&amp;v=1.0&key=<?php echo $key?>" type="text/javascript"></script>
		       <script type="text/javascript">
					location.href="<?php echo $_SERVER['SCRIPT_NAME'].'?'.'key='.$key.'&gtype='.$gtype.'&keyIsValidated='?>" + UDS_KeyVerified;
				</script>
		<?php
		}
	} else {
		$sql = "DELETE FROM ".TABLE_PREFIX."config WHERE name='gsearch'";
		$result = mysql_query($sql, $db);
		$msg->addFeedback('GOOGLE_KEY_SAVED');
		$key = '';
	}

	//Set Google interface's type.
	$googleType = $addslashes($_POST['gtype']);
	$sql = "REPLACE INTO ".TABLE_PREFIX."config VALUES('gtype','$googleType')";
	$result = mysql_query($sql, $db);

	//Manually print it out
	$msg->printAll();
}

$savant->assign('googleType', $googleType);
$savant->display('admin/system_preferences/module_prefs.tmpl.php');
require(AT_INCLUDE_PATH.'footer.inc.php'); ?>