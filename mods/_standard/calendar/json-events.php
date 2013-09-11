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
    
    //Retrieve all the personal events.
    define('AT_INCLUDE_PATH', '../../../include/');
    require(AT_INCLUDE_PATH.'vitals.inc.php');
    require('includes/classes/events.class.php');
    
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
            $event['editable'] = false;
        }
        array_push($rows, $event);
    }
    
    if (isset($_GET['all']) || isset($_GET['mini']) || isset($_GET['mid'])) {
        if (isset($_GET['all']) || isset($_GET['mini'])) {
            foreach ($eventObj->get_atutor_events($_SESSION['member_id'],$_SESSION['course_id']) as $event) {
                array_push($rows, $event);
            }
        }
        if (isset($_GET['mid'])) {
            foreach ($eventObj->get_atutor_events($_GET['mid'],$_GET['cid']) as $event) {
                array_push($rows, $event);
            }
        }
    }    
    echo $eventObj->caledar_encode($rows);
?>