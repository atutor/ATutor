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
    // Instructor Create Courses
    // Check if Services module is installed
        if($_SESSION['course_id'] == 0){
            $services_mod = queryDB("SELECT * FROM %smodules WHERE dir_name='%s'", array(TABLE_PREFIX, "_core/services"), true);
            if(!empty($services_mod)){
                $help_url = $_base_href."mods/_core/services/users/create_course.php";
            }else{
                $help_url = $_base_href."mods/_core/courses/users/create_course.php";
            }
        }
        ///
        /// WHAT ABOUT DISABLE_CREATE///
        ///
        //if(!isset($_SESSION['course_id'])){
        // Check if instructor already has courses
            $has_courses = queryDB("SELECT * FROM %scourses WHERE member_id='%s'", array(TABLE_PREFIX, $member_id), true);
       // }
        if(empty($has_courses)){
            // if instructor has no course yet, display the creat course help message
                helpme_msg('CREATE_COURSE', $help_url);

        } else {
            // if instructor has courses already, update helpme_user to skip this help message
            queryDB("REPLACE INTO %shelpme_user (`user_id`, `help_id`) VALUES ('%d','%d')", array(TABLE_PREFIX, $member_id, $next_help));
            unset($_SESSION['message']);
        }

        break;
    case '2':
        global $_config_defaults, $_config;
        $has_courses = queryDB("SELECT * FROM %scourses WHERE member_id='%s'", array(TABLE_PREFIX, $member_id), true);
       if(!empty($has_courses)){
            unset($_SESSION['message']);
       }
        require_once(AT_INCLUDE_PATH.'lib/constants.inc.php');
        // Get the main and sidemenu course tools for the currrent course
        $main_tools = queryDB("SELECT main_links FROM %scourses WHERE course_id = '%d'", array(TABLE_PREFIX, $_SESSION['course_id']), TRUE); 
        $side_tools = queryDB("SELECT side_menu FROM %scourses WHERE course_id = '%d'", array(TABLE_PREFIX, $_SESSION['course_id']), TRUE);

        // check if course tools are the same as admin created course tools
        if($main_tools['main_links'] != $_config['main_defaults'] && $side_tools['side_menu'] != $_config['side_menu']){
            header('Location:'. $_SERVER['PHP_SELF'].'?next_help='.($next_help+1));
        } else {
            $is_defaults = '1';
        } 
        
        // check if course tools are the same as the system default course tools
        if($main_tools['main_links'] == $_config_defaults['main_defaults'] && $side_tools['side_menu'] == $_config_defaults['side_defaults']){
            $is_defaults = '1';
        }
        
        // Display help if course tools are the default settings
        if($_SESSION['course_id'] > 0 && isset($is_defaults)){
            helpme_msg('COURSE_TOOLS', $_base_href."mods/_standard/course_tools/modules.php");
        } else{
            // if not the default settings, update helpme_user to skip this help message
            queryDB("REPLACE INTO %shelpme_user (`user_id`, `help_id`) VALUES ('%d','%d')", array(TABLE_PREFIX, $member_id, $next_help));
            unset($_SESSION['message']);
        }
        break; 
    case '3':
        if($_SESSION['course_id'] > 0){
        helpme_msg('COURSE_PROPERTIES', $_base_href."mods/_core/properties/course_properties.php");
        }
        break;
    case '4':
        $has_content = queryDB("SELECT content_id FROM %scontent WHERE course_id = '%d'", array(TABLE_PREFIX, $_SESSION['course_id']));
        if($_SESSION['course_id'] > 0 && count($has_content) < '2'){
            helpme_msg('CREATE_CONTENT', $_base_href."mods/_core/editor/add_content.php");
        } else {
            queryDB("REPLACE INTO %shelpme_user (`user_id`, `help_id`) VALUES ('%d','%d')", array(TABLE_PREFIX, $member_id, $next_help));
       
        }
        break;
    case '5':
        $has_enrollment = queryDB("SELECT member_id FROM %scourse_enrollment WHERE course_id = '%d'", array(TABLE_PREFIX, $_SESSION['course_id']));
        if($_SESSION['course_id'] > 0 && count($has_enrollment) < 2){
            helpme_msg('ADD_USERS', $_base_href."mods/_core/enrolment/create_course_list.php");
        } else {
            queryDB("REPLACE INTO %shelpme_user (`user_id`, `help_id`) VALUES ('%d','%d')", array(TABLE_PREFIX, $member_id, $next_help));

        }
        break;
    case '6':
        $has_backup = queryDB("SELECT course_id FROM %sbackups WHERE course_id = '%d'", array(TABLE_PREFIX, $_SESSION['course_id']));
        if($_SESSION['course_id'] > 0 && empty($has_backup)){
            helpme_msg(array('CREATE_BACKUP', $_base_href.'mods/_core/backups/create.php',$_base_href.'mods/_core/imscp/index.php '), '');  
            //helpme_msg('CREATE_BACKUP', $_base_href.'mods/_core/backups/create.php,'.$_base_href.'mods/_core/imscp/index.php,'.$_base_href.'mods/_core/imscp/index.php,'.$_base_href.'mods/_core/imscp/index.php,'.$_base_href.'mods/_core/imscp/index.php');
        } else {
            queryDB("REPLACE INTO %shelpme_user (`user_id`, `help_id`) VALUES ('%d','%d')", array(TABLE_PREFIX, $member_id, $next_help));
        }
        break;
    case '7':
   
       // helpme_msg('READ_HANDBOOK', '<a target="_new" onclick="ATutor.poptastic(\''.$_base_href.'documentation/instructor/index.php?en\'); return false;" href="documentation/index_list.php?lang=en">Official ATutor Handbook</a>');
        helpme_msg(array('READ_HANDBOOK', '<a target="_new" onclick="ATutor.poptastic(\''.$_base_href.'documentation/instructor/index.php?en\'); return false;" href="documentation/index_list.php?lang=en">'._AT('atutor_handbook').'</a>', $_base_href.'help/index.php'),'');
    
        break;

} // END SWITCH
//echo "something";
/////////
//
// UPDATE THE NUMBER FOLLOWING WITH THE NUMBER OF CASES ABOVE
//
////////

//$helpme_total = '7'; 
$savant->assign('helpme_count', $next_help);
$savant->assign('helpme_total', $helpme_total);
unset($next_help);
?>