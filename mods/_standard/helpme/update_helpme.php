<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2013                                                   */
/* ATutorSpaces                                                         */
/* https://atutorspaces.com                                             */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
define('AT_INCLUDE_PATH', '../../../include/');
require_once(AT_INCLUDE_PATH.'vitals.inc.php');

if(isset($_GET['next_helpme'])){
     $help_id = intval($_GET['next_helpme']);
}else{
     $help_id = intval($_GET['help_id']);
}

if(isset($_GET['user_id'])){
    $member_id = intval($_GET['user_id']);
} else {
    $member_id = $_SESSION['member_id'];
}

if($_GET['help_id'] > 0){
    $sql = "REPLACE INTO %shelpme_user (`user_id`, `help_id`) VALUES (%d,%d)";
    queryDB( $sql, array(TABLE_PREFIX, $member_id, $help_id));
} else{
    $sql = "DELETE from %shelpme_user WHERE user_id = %d";
    queryDB( $sql, array(TABLE_PREFIX, $member_id));
}
unset($_SESSION['message']);
?>