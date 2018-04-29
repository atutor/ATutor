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

global $msg, $_base_href, $savant;
$current_help = queryDB("SELECT help_id FROM %shelpme_user WHERE user_id ='%d'", array(TABLE_PREFIX, $member_id), true);
unset($myhelp); // = "";

if($current_help['help_id'] <= $helpme_total){
    if($_SESSION['course_id'] == 0){
        if($msg->containsHelps()){
            $msg->deleteHelp('COURSE_TOOLS');
            $msg->deleteHelp('MANAGE_ONOFF');
            $msg->deleteHelp('COURSE_PROPERTIES');
            $msg->deleteHelp('CREATE_CONTENT');
            $msg->deleteHelp('ADD_USERS');
            $msg->deleteHelp('CREATE_BACKUP');
            $msg->deleteHelp('READ_HANDBOOK');
        }
        helpme_msg('CREATE_COURSE', $_base_path."mods/_core/courses/users/create_course.php");
    } else if ($_SESSION['course_id'] > 0){
        // Hack to remove the get.php/ added to $_base_href
        $_help_href = str_replace("get.php/", "", $_base_href);
        $_help_href = str_replace("get.php/", "", $_help_href);    
        $_help_href = str_replace("gameme/badges/", "", $_help_href); 

        $msg->deleteHelp('CREATE_COURSE');
        helpme_msg('COURSE_TOOLS', $_help_href."mods/_standard/course_tools/modules.php");
        helpme_msg('MANAGE_ONOFF', '');
        helpme_msg('COURSE_PROPERTIES', $_help_href."mods/_core/properties/course_properties.php");
        helpme_msg('CREATE_CONTENT', $_help_href."mods/_core/editor/add_content.php");
        helpme_msg('ADD_USERS', $_help_href."mods/_core/enrolment/create_course_list.php");
        helpme_msg(array('CREATE_BACKUP', $_help_href.'mods/_core/backups/create.php',$_help_href.'mods/_core/imscp/index.php '), ''); 
        helpme_msg(array('READ_HANDBOOK', '<a target="_new" onclick="ATutor.poptastic(\''.$_help_href.'documentation/instructor/index.php?en\'); return false;" href="documentation/index_list.php?lang=en">'._AT('atutor_handbook').'</a>', $_help_href.'help/index.php'),'');
    }

    if($next_help ==0) $next_help = 1;
    $savant->assign('helpme_count', $next_help);
    $savant->assign('helpme_total', $helpme_total);
}
?>