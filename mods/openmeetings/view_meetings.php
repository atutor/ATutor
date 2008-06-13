<?php
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
//require ('openmeetings.class.php');
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

$_GET['room_id']	= abs($_GET['room_id']);
$_GET['sid']		= addslashes($_GET['sid']);
?>

<div>
<a href="<?php echo $_SERVER['PHP_SELF']; ?>" onclick="window.open('<?php echo $_config['openmeetings_location']; ?>/main.lzx.lzr=swf8.swf?roomid=<?php echo $_GET['room_id']; ?>&sid=<?php echo $_GET['sid'];?>','marratechwin','width=800,height=720,scrollbars=yes, resizable=yes'); return false"><?php echo  _AT('openmeetings_own_window'); ?></a> </li>

<iframe name="openmeetings" id="openmeetings" title="Openmeetings" frameborder="1" scrolling="auto" src="<?php echo $_config['openmeetings_location']; ?>/main.lzx.lzr=swf8.swf?roomid=<?php echo $_GET['room_id']; ?>&sid=<?php echo $_GET['sid'];?>" height="700" width="90%" align="center" style="border:thin white solid; align:center;" allowautotransparency="true"></iframe>

</div>
<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>