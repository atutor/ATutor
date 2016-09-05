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
     * This file returns events from Google Calendar in JSON format.
     */
    $_user_location	= 'public';
    require_once 'includes/classes/googlecalendar.class.php';
    define('AT_INCLUDE_PATH', '../../../include/');
    require(AT_INCLUDE_PATH.'vitals.inc.php');

    //Default values
    define('CALENDAR_DEF_COLOR','#3399FF');
    
    $gcalobj = new GoogleCalendar();

    $qry = "SELECT * FROM %scalendar_google_sync WHERE userid=%d";
    $row = queryDB($qry, array(TABLE_PREFIX, $_SESSION['member_id']), TRUE);
    
    if(count($row) > 0){
        $_SESSION['sessionToken'] = $row['token'];
        
        if ($gcalobj->isvalidtoken($_SESSION['sessionToken'])) {
         $client  = $gcalobj->getAuthSubHttpClient();

         $query   = "SELECT * FROM %scalendar_google_sync WHERE userid= %d";
         $rowval     = queryDB($query, array(TABLE_PREFIX, $_SESSION['member_id']), TRUE);

         $prevval = $rowval['calids'];

         outputCalendarByDateRange($client, $_GET['start'], $_GET['end'], $prevval, $gcalobj);
        }
    }

    /**
     * Iterate through all the Google Calendars and create a JSON encoded array of events.
     *
     * @return array of events in JSON format
     */
    function outputCalendarByDateRange($client, $startDate, $endDate, $idsofcal, $gcalobj) {
        $gdataCal = new Google_Service_Calendar($client);

        $rows     = array();
        $idsofcal = explode(',',$idsofcal);
        $dateTime = new DateTime();

        foreach ($idsofcal as $idofcal) {
            if ($idofcal != '') {
                $optParams = array(
                        'timeMin' => $dateTime->setTimestamp($startDate)->format(DateTime::RFC3339),
                        'timeMax' => $dateTime->setTimestamp($endDate)->format(DateTime::RFC3339),
                    );
                $eventFeed  = $gdataCal->events->listEvents($idofcal, $optParams)->getItems();
                $color      = CALENDAR_DEF_COLOR;

                foreach ($eventFeed as $event) {
                    $startD = substr($event->start->dateTime, 0, 19);
                    $startD = str_replace('T', ' ', $startD);
                    $endD   = substr($event->end->dateTime, 0, 19);
                    $endD   = str_replace('T', ' ', $endD);

                    /*
                     * If both start time and end time are different and their time parts differ then allDay is false
                     */
                    if (($startD != $endD) && substr($startD,0,10) == substr($endD,0,10)) {
                        $allDay = "false";
                    } else {
                        $allDay = "true";
                    }
                    $row = array();
                    $row["title"]     = $event->summary;
                    $row["id"]        = $event->id;
                    $row["editable"]  = $event->creator->self === true ? 'true' : 'false';
                    $row["start"]     = $startD;
                    $row["end"]       = $endD;
                    $row["allDay"]    = $allDay;
                    $row["color"]     = $color;
                    $row["textColor"] = "white";
                    $row["cal_id"]    = $idofcal;
                    $row["calendar"]  = "Google Calendar event";

                    array_push($rows, $row);
                }
            }
        }

        //Encode in JSON format.
        $str =  json_encode($rows);

        //Replace "true","false" with true,false for javascript.
        $str = str_replace('"true"', 'true', $str);
        $str = str_replace('"false"', 'false', $str);

        //Return the events in the JSON format.
        echo $str;
    }
?>