<?php
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
require ('openmeetings.class.php');
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

//Initiate Openmeeting
$om_obj = new Openmeetings($course_id, $_SESSION['member_id']);

//Login
$om_obj->om_login();

//Handles form actions
if (isset($_GET['delete']) && $_GET['room_id']){
	//have to makesure the user really do have permission over the paramater room id
	$_GET['room_id'] = intval($_GET['room_id']);
	if ($om_obj->isMine($_GET['room_id'])){
		$om_obj->om_deleteRoom($_GET['room_id']);
	} else {
		echo 'Sorry, you do not have the permission to remove this group. Please stop hacking the URL';
	}
}

//Check if the room is open, if not.  Print error msg to user.
if (!$om_obj->isRoomOpen()):
	echo '<div>There are no meetings at the moment.  The next scheduled course meeting is : &lt;lalalalala&gt;</div>';
else:
	//Get the room id
	//TODO: Course title added/removed after creation.  Affects the algo here.
	if (isset($_SESSION['course_title']) && $_SESSION['course_title']!=''){
		$room_name = $_SESSION['course_title'];
	} else {
		$room_name = 'course_'.$course_id;
	}

	//Log into the room
	$room_id = $om_obj->om_getRoom($room_name);
?>
	<div>
		<a href="mods/openmeetings/view_meetings.php?room_id=<?php echo $room_id . SEP; ?>sid=<?php echo $om_obj->getSid(); ?>">Course conference</a>
	</div>
<?php 
endif;

if (empty($_SESSION['groups'])) {
	echo '<div> No groups assigned, thus no meetings </div>';
} else {
echo '<div>Your groups:</div>';
	$group_list = implode(',', $_SESSION['groups']);
	$sql = "SELECT group_id, title FROM ".TABLE_PREFIX."groups WHERE group_id IN ($group_list) ORDER BY title";
	$result = mysql_query($sql, $db);

	echo '<ul>';
	//loop through each group and print out a link beside them 
	while ($row = mysql_fetch_assoc($result)) {
		//Check in the db and see if this group has a meeting alrdy, create on if not.
		$om_obj->setGid($row['group_id']);
		if ($om_obj->isRoomOpen()){
			//Log into the room
			$room_id = $om_obj->om_getRoom($room_name);
			echo '<li>'.$row['title'].'<a href="mods/openmeetings/view_meetings.php?room_id='.$room_id.SEP.'sid='.$om_obj->getSid().'"> Room-id: '.$room_id.'</a>';
			if ($om_obj->isMine($room_id)) {
				//if 'I' created this room, then I will have the permission to remove it from the database.
				echo ' <a href="'.$_SERVER['PHP_SELF'].'?delete=delete'.SEP.'room_id='.$room_id.'">[Delete]</a>';
			}
			echo '</li>';
		} else {
			echo '<li>'.$row['title'].'<a href="mods/openmeetings/add_group_meetings.php?group_id='.$row['group_id'].'"> Start a conference </a>'.'</li>';
		}
	}
	echo '</ul>';
}
require (AT_INCLUDE_PATH.'footer.inc.php');