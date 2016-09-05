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
    $gdata_cal = new Google_Service_Calendar($client);

    $event_id = $_GET['id'];
    $command   = $_GET['cmd'];
    $cal_id    = $_GET['cal_id'];

    try {
        if (strcmp($command,'delete') == 0) {
            $gdata_cal->events->delete($cal_id, $event_id);
        } else if (strcmp($command,'update') == 0) {
            $event = $gdata_cal->events->get($cal_id, $event_id);

            $event->setSummary($_GET['title']);

            $startEventTimeObj = new Google_Service_Calendar_EventDateTime();
            $startDateTime = new DateTime($_GET['start'], new DateTimeZone("UTC"));
            $startEventTimeObj->setDateTime($startDateTime->format(DateTime::RFC3339));
            $event->setStart($startEventTimeObj);

            $endEventTimeObj = new Google_Service_Calendar_EventDateTime();
            $endDateTime = new DateTime($_GET['end'], new DateTimeZone("UTC"));
            $endEventTimeObj->setDateTime($endDateTime->format(DateTime::RFC3339));
            $event->setEnd($endEventTimeObj);

            $updatedEvent = $gdata_cal->events->update($cal_id, $event->getId(), $event);
        }
        exit();
    }
    catch (Exception $e) {
        //If some error occurs then stop execution and print error.
        echo _AT('calendar_error') . $e->getMessage();
        exit();
    }
?>