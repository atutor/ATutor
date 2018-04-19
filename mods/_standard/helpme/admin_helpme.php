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
define('AT_INCLUDE_PATH', '../../include/');
require_once(AT_INCLUDE_PATH.'vitals.inc.php');
global $msg, $_base_href, $savant;
//if(isset($_GET['next_helpme'])){
//    $next_help = $_GET['next_helpme'];
//}

switch($next_help){
    case '1':
    /////////
    //Admin Create Courses
    // Check if Services module is installed
        $services_mod = queryDB("SELECT * FROM %smodules WHERE dir_name='%s'", array(TABLE_PREFIX, "_core/services"), true);
        if(!empty($services_mod)){
            $help_url = $_base_href."mods/_core/services/admin/create_course.php";
        }else{
            $help_url = $_base_href."mods/_core/courses/admin/create_course.php";
        }
        
        $has_courses = queryDB("SELECT * FROM %scourses", array(TABLE_PREFIX, TRUE));
       
        if(empty($has_courses)){
            helpme_msg('CREATE_A_COURSE', $help_url);
        } else{
            queryDB("REPLACE INTO %shelpme_user (`user_id`, `help_id`) VALUES ('%d','%d')",array(TABLE_PREFIX, $member_id, $next_help));
            unset($_SESSION['message']);
        }
        
        break;
    //////// END ADMIN CREATE COURSE
    case '2':
        helpme_msg('SYS_PREFS', $_base_href."admin/config_edit.php");
        break;
    case '3':
        helpme_msg('DEFAULT_TOOLS', $_base_href."mods/_core/courses/admin/default_mods.php");
        break;
    case '4':
        helpme_msg('ADMIN_CREATE_USER', $_base_href."mods/_core/users/create_user.php");
        break;
    case '5':
        helpme_msg('USERS_PREFS', $_base_href."mods/_core/users/default_preferences.php");
        break;
    case '6':
        helpme_msg('CHANGE_THEME', $_base_href."mods/_core/themes/index.php");
        break;
    case '7':
        helpme_msg('MANAGE_MODULE', $_base_href."mods/_core/modules/index.php");
        break;
    case '8':
        $services_mod = queryDB("SELECT * FROM %smodules WHERE dir_name='%s'", array(TABLE_PREFIX, "_core/services"), true);
        if(!empty($services_mod)){
            queryDB("REPLACE INTO %shelpme_user (`user_id`, `help_id`) VALUES ('%d','%d')",array(TABLE_PREFIX, $member_id, $next_help));
            unset($_SESSION['message']);
        }else{
            helpme_msg('APPLY_PATCHES', $_base_href."mods/_standard/patcher/index_admin.php");
        }
        break;
    case '9':
        helpme_msg('CREATE_ADMIN', $_base_href."mods/_core/users/admins/create.php");
        break;
    case '10':
        helpme_msg(array('READ_HANDBOOK', '<a target="_new" onclick="ATutor.poptastic(\''.$_base_href.'documentation/admin/index.php?en\'); return false;" href="documentation/index_list.php?lang=en">'._AT('atutor_handbook').'</a>', $_base_href.'help/index.php'),'');
        break;

} // END SWITCH

/////////
//
// UPDATE THE NUMBER FOLLOWING WITH THE NUMBER OF CASES ABOVE
//
/////////
//$helpme_total = '10'; 
$savant->assign('helpme_total', $helpme_total);
$savant->assign('helpme_count', $next_help);
?>
