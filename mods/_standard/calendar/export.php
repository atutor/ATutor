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
     * This file is used to generate ics file.
     */
    $_user_location	= 'public';
    define('AT_INCLUDE_PATH', '../../../include/');
    require(AT_INCLUDE_PATH.'vitals.inc.php');
    require('includes/classes/events.class.php');
    
    $rows     = array();
    $eventObj = new Events();
    
    //Create ics file in $ical string variable
    $ical = "BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//ATutor//ATutor Calendar Module//EN";
    
    //Get all the events of the user
    foreach ($eventObj->get_personal_events($_SESSION['member_id'],TRUE) as $event) {
        array_push($rows, $event);
    }

    //Get ATutor system events
    foreach ($eventObj->get_atutor_events($_SESSION['member_id'],$_SESSION['course_id'],TRUE) as $event) {
        array_push($rows, $event);
    }

    foreach ($rows as $row) {
        /*Timezone manipulation
        $sstamp   = strtotime($row['start']) - ($_GET['hrs'] * 60 * 60);
        $estamp   = strtotime($row['end'])   - ($_GET['hrs'] * 60 * 60);
        
        $startdt  = gmdate('Y-m-d H:i:s', $sstamp);
        $enddt    = gmdate('Y-m-d H:i:s', $estamp);
        
        $part_s   = explode(' ', $startdt);
        $part_e   = explode(' ', $enddt);*/
        
        $part_s = explode(" ", $row['start']);
        $part_e = explode(" ", $row['end']);
        
        $s_date_p = explode('-', $part_s[0]);
        $e_date_p = explode('-', $part_e[0]);

        $s_time_p = explode(':', $part_s[1]);
        $e_time_p = explode(':', $part_e[1]);
        $ical    .= "
BEGIN:VEVENT
UID:" . md5(uniqid(mt_rand(), true)). "@atutor.ca
DTSTAMP:" . gmdate('Ymd'). 'T' . gmdate('His') . "Z
DTSTART:" . $s_date_p[0] . $s_date_p[1] . $s_date_p[2] ."T". $s_time_p[0] . $s_time_p[1] . "00Z
DTEND:" . $e_date_p[0] . $e_date_p[1] . $e_date_p[2] ."T" . $e_time_p[0] . $e_time_p[1] . "00Z
SUMMARY:" . $row['title'] . "
END:VEVENT";
    }

    $ical .= "
END:VCALENDAR";

    //set correct content-type-header
    header('Content-type: text/calendar; charset=utf-8');
    header('Content-Disposition: inline; filename=calendar.ics');
    echo $ical;
    exit;
?>