<?php
    /****************************************************************/
    /* ATutor Calendar Module                                       */
    /* https://atutorcalendar.wordpress.com/                        */
    /*                                                              */
    /* This module provides standard calendar features in ATutor.   */
    /*                                                              */
    /* Author: Anurup Raveendran, Herat Gandhi                      */
    /* This program is free software. You can redistribute it and/or*/
    /* modify it under the terms of the GNU General Public License  */
    /* as published by the Free Software Foundation.                */
    /****************************************************************/
    
    /**
     * This file returns events from database as well as 
     * ATutor events in JSON format.
     */
    
    if (isset($_GET['pub']) && $_GET['pub'] == 1) {
         $_user_location = 'public';
    }
        // Get a list of this user's enrolled courses

    //Retrieve all the personal events.
    define('AT_INCLUDE_PATH', '../../../include/');
    require(AT_INCLUDE_PATH.'vitals.inc.php');
    require('includes/classes/events.class.php');
    
    if(!isset($_GET['cid']) && !isset($_GET['all'])) {
        $sql = "SELECT course_id FROM ".TABLE_PREFIX."course_enrollment WHERE member_id=".$_SESSION['member_id'];
        $rows_enrolled = queryDB($sql, array());
    
        foreach($rows_enrolled as $row){
            $courses[] = $row['course_id'];
        }
    }
    $eventObj = new Events();
    
    //Create an empty array and push all the events in it.
    $rows = array();
    
    if (isset($_GET['all'])) {
        $member = $_SESSION['member_id'];
    } else if (isset($_GET['mini'])) {
        $member = $_SESSION['member_id'];
    } else {
        $member = $_GET['mid'];
    }
        
    foreach ($eventObj->get_personal_events($member) as $event) {
        if (!isset($_GET['all'])) {
            $event['editable'] = true ;
        }
        array_push($rows, $event);
    }
    
    if (isset($_GET['all']) || isset($_GET['mini']) || isset($_GET['mid'])) {
        if (isset($_GET['all']) || isset($_GET['mini'])) {
            if(is_array($courses)){
                $i = 0;
                    foreach ($eventObj->get_atutor_events($_SESSION['member_id'],$courses[$i]) as $event) {
                        array_push($rows, $event);
                        $i++;
                    }
            }else{
                foreach ($eventObj->get_atutor_events($_SESSION['member_id'],$_SESSION['course_id']) as $event) {
                    array_push($rows, $event);
                }           
            }
        }
        if (isset($_GET['mid'])) {
            if(!isset($_GET['cid'])){
                $_GET['cid'] = $courses;
            }

              if(is_array($_GET['cid'])){
                $t = 0;
                   foreach($courses as $course_id){
                        foreach ($eventObj->get_atutor_events($_GET['mid'], $course_id) as $event) {
                            array_push($rows, $event);
                            $t++;
                        }
                    }
              } else{
                foreach ($eventObj->get_atutor_events($_GET['mid'],$_GET['cid']) as $event) {
                     array_push($rows, $event);
                }
              }

        }
    }    
    echo $eventObj->caledar_encode($rows);
    
    
?>