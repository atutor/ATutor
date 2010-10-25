<?php

define('AT_INCLUDE_PATH', '../../include/');
require_once(AT_INCLUDE_PATH.'vitals.inc.php');

require_once('lib/ACUser.php');
require_once('lib/ACRoom.php');

require_once('lib/lib.php');


$courseid = $_SESSION['course_id'];

$member = getMemberData();
if (!$member) {
    $msg->addError('adobe_connect_wrong_member_sess');
}

$assign = getMemberCourseAssign();
if (!$assign) {
    $msg->addError('adobe_connect_no_access');
}


$acuser = new ACUser();
$acroom = new ACRoom();


// admin session
$xsid = $acuser->getAdminSession();
if (!$xsid) {
    $msg->addError('adobe_connect_not_connect');
    echo '<script>
           window.opener.location.reload();
           window.close();
          </script>';

} else {

    // room id
    $scoid = $acroom->checkRoom($xsid, $courseid);
    if (!$scoid) {

        // comprovar que l'usuari té accés al mòdul

        $createresult = $acroom->createRoom($xsid, $courseid);
        if (!$createresult) {
            $msg->addError('adobe_connect_not_create_room');
        } else {
            $scoid = $acroom->checkRoom($xsid, $courseid);
            if (!$scoid) {
                $msg->addError('adobe_connect_not_roomid');
            }
        }
    }


    // user session
    $userexists = $acuser->checkUser($xsid, $member->login);
    if (!$userexists) {

        $createresult = $acuser->createUser($xsid, $member->login, $member->first_name, $member->last_name);
        if (!$createresult) {
            $msg->addError('adobe_connect_not_create_user');
        } else {
            $usid = $acuser->getUserSession($member->login);
            if (!$usid) {
                $msg->addError('adobe_connect_not_user_session');
            }
        }
    } else {
        $usid = $acuser->getUserSession($member->login);
    }


    // redirect url
    $roomurl = $acroom->getRoomUrl($usid, $scoid);
    if (!$roomurl) {
    
        $createresult = $acroom->assignUser($xsid, $member->login, $courseid, $assign->role);
        if (!$createresult) {
            $msg->addError('adobe_connect_not_assign');
        } else {
            $roomurl = $acroom->getRoomUrl($usid, $scoid);
            if (!$roomurl) {
                $msg->addError('adobe_connect_not_roomurl');
            }
        }
    }

}


$url = 'http://'.$acroom->getACHost().'/'.$roomurl.'?session='.$usid;
header('location: '.$url);

?>
