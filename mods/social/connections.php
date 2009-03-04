<?php
define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_SOCIAL_INCLUDE.'friends.inc.php');
$_custom_css = $_base_path . 'mods/social/module.css'; // use a custom stylesheet

//display all friends
$friends = getFriends($_SESSION['member_id']);

include(AT_INCLUDE_PATH.'header.inc.php');
$savant->assign('friends', $friends);
$savant->display('connections.tmpl.php');
include(AT_INCLUDE_PATH.'footer.inc.php');
?>
