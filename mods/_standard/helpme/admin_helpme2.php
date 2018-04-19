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
global $msg, $_base_href, $savant, $_base_path;
$member_id = "-1";
//$current_help = queryDB("SELECT help_id FROM %shelpme_user WHERE user_id ='%d'", array(TABLE_PREFIX, $member_id), true);
//$next_help = $current_help['help_id'];
$myhelp = "";
if($current_help['help_id'] <= $helpme_total || !isset($current_help['help_id'] )){

        $myhelp .= helpme_msg('CREATE_A_COURSE', "mods/_core/courses/admin/create_course.php");

        $myhelp .= helpme_msg('SYS_PREFS', $_base_href."admin/config_edit.php");

        $myhelp .= helpme_msg('DEFAULT_TOOLS', $_base_href."mods/_core/courses/admin/default_mods.php");

        $myhelp .= helpme_msg('ADMIN_CREATE_USER', $_base_href."mods/_core/users/create_user.php");

        $myhelp .= helpme_msg('USERS_PREFS', $_base_href."mods/_core/users/default_preferences.php");

        $myhelp .= helpme_msg('CHANGE_THEME', $_base_href."mods/_core/themes/index.php");

        $myhelp .= helpme_msg('MANAGE_MODULE', $_base_href."mods/_core/modules/index.php");

        $myhelp .= helpme_msg('APPLY_PATCHES', $_base_href."mods/_standard/patcher/index_admin.php");

        $myhelp .= helpme_msg('CREATE_ADMIN', $_base_href."mods/_core/users/admins/create.php");

        $myhelp .= helpme_msg(array('READ_HANDBOOK', '<a target="_new" onclick="ATutor.poptastic(\''.$_base_href.'documentation/admin/index.php?en\'); return false;" href="documentation/index_list.php?lang=en">'._AT('atutor_handbook').'</a>', $_base_href.'help/index.php'),'');

        //echo $myhelp;
        /////////
        //
        // UPDATE THE NUMBER FOLLOWING WITH THE NUMBER OF CASES ABOVE
        //
        /////////
        // $helpme_total = '10'; 
        if($next_help ==0) $next_help = 1;
        $savant->assign('helpme_total', $helpme_total);
        $savant->assign('helpme_count', $next_help);
}
?>
