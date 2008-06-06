<?php
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
//$_custom_css = $_base_path . 'mods/openmeetings/module.css'; // use a custom stylesheet

//local variables
$course_id = $_SESSION['course_id'];

/*
 * Check access
 * Disallowing improper accesses from a GET request
 */
$sql	= "SELECT `access` FROM ".TABLE_PREFIX."courses WHERE course_id=$course_id";
$result = mysql_query($sql, $db);
$course_info = mysql_fetch_assoc($result);

if ($course_info['access']!='public' && ($_SESSION['enroll'] == AT_ENROLL_NO || $_SESSION['enroll'] == AT_ENROLL_ALUMNUS)) {
	require(AT_INCLUDE_PATH.'header.inc.php');
	$msg->printInfos('NOT_ENROLLED');
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

if (!isset($_config['openmeetings_username']) || !isset($_config['openmeetings_userpass'])){
	require(AT_INCLUDE_PATH.'header.inc.php');
	echo 'Contact admin plz';
	//Please contact your administrator, om needs to be setup.
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}


//Header begins here
require (AT_INCLUDE_PATH.'header.inc.php');
include('SOAP_openmeetings.php');
include('openmeetings.inc.php');

/* 
 * Load obj
 * TODO: add constances for openmeetings admin username, password; and the wsdl service url
 */
$om = new SOAP_openmeetings($_config['openmeetings_location'].'/services/UserService?wsdl');

$param = array (	'username' => $_config['openmeetings_username'], 
					'userpass' => $_config['openmeetings_userpass']);

/**
 * Login to the openmeetings
 * ref: http://code.google.com/p/openmeetings/wiki/DirectLoginSoapGeneralFlow
 */
$result = $om->login($param);
if ($result < 0){
	debug($om->getError($result), 'error');
}

//Retrieve members information
$sql = 'SELECT first_name, last_name, email FROM '.TABLE_PREFIX.'members WHERE member_id='.$_SESSION['member_id'];
$result = mysql_query($sql);
$row = mysql_fetch_assoc($result);

// Save user instance
$params = array(
            "username"				=> $_SESSION['login'],
            "firstname"				=> $row['first_name'],
		    "lastname"				=> $row['last_name'],
		    "profilePictureUrl"		=> '',
		    "email"					=> $row['email']
          );
$om->saveUserInstance($params);

//Get the room id
if (isset($_SESSION['course_title']) && $_SESSION['course_title']!=''){
	$room_name = $_SESSION['course_title'];
} else {
	$room_name = 'course_'.$course_id;
}
$room_id = om_getRoom($om->getSid(), $course_id, $room_name);
?>
<div>
<a href="<?php echo $_SERVER['PHP_SELF']; ?>" onclick="window.open('<?php echo $_config['openmeetings_location']; ?>/main.lzx.lzr=swf8.swf?roomid=<?php echo $room_id; ?>&sid=<?php echo $om->_sid;?>','marratechwin','width=800,height=720,scrollbars=yes, resizable=yes'); return false"><?php echo  _AT('openmeetings_own_window'); ?></a> </li>

<iframe name="openmeetings" id="openmeetings" title="Openmeetings" frameborder="1" scrolling="auto" src="<?php echo $_config['openmeetings_location']; ?>/main.lzx.lzr=swf8.swf?roomid=<?php echo $room_id; ?>&sid=<?php echo $om->_sid;?>" height="700" width="90%" align="center" style="border:thin white solid; align:center;" allowautotransparency="true"></iframe>

</div>
<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>