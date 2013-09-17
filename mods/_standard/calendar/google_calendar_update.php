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
     * This file is used to reflect changes from ATutor to Google Calendar.
     */
          $_user_location = 'public';
    
    require_once 'includes/classes/googlecalendar.class.php';
    define('AT_INCLUDE_PATH', '../../../include/');
    require(AT_INCLUDE_PATH.'vitals.inc.php');

    $gcalobj   = new GoogleCalendar();

    $client    = $gcalobj->getAuthSubHttpClient();
    $gdata_cal = new Zend_Gdata_Calendar($client);

    $event_url = $_GET['id'];
    $command   = $_GET['cmd'];

    try {
        //Get the event object from its id.
        $event = $gdata_cal->getCalendarEventEntry($event_url);
        if (strcmp($command,'delete') == 0) {
            //If event is deleted then call delete method.
            $event->delete();
        } else if (strcmp($command,'update') == 0) {
            //Update event attributes and then save it in Google Calendar.
            $event->title    = $gdata_cal->newTitle($_GET['title']);
            $when            = $gdata_cal->newWhen();
            $when->startTime = $_GET['start'];
            $when->endTime   = $_GET['end'];
            $event->when     = array($when);
            
            $event->save();
        }
        exit();
    } 
    catch (Zend_Gdata_App_Exception $e) {
        //If some error occurs then stop execution and print error.
        echo _AT('calendar_error') . $e->getMessage();
        exit();
    }
?>