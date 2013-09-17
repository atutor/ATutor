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
    
    if(count( $row) > 0){
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
        $gdataCal = new Zend_Gdata_Calendar($client);
        $rows     = array();

        $idsofcal = explode(',',$idsofcal);
        $calFeed  = $gdataCal->getCalendarListFeed();

        foreach ($idsofcal as $idofcal) {
            if ($idofcal != '') {
                $query = $gdataCal->newEventQuery();
                
                $query->setUser(substr($idofcal,strrpos($idofcal,"/")+1));
                $query->setVisibility('private');
                $query->setProjection('full');
                $query->setOrderby('starttime');
                $query->setStartMin($startDate);
                $query->setStartMax($endDate);
                
                $eventFeed  = $gdataCal->getCalendarEventFeed($query);
                $color      = CALENDAR_DEF_COLOR;
                $accesslevl = true;
                foreach ($calFeed as $calendar) {
                    if (strpos($idofcal,$calendar->id->text) !== false) {
                        $color = $calendar->color->value;
                        if ($calendar->accesslevel->value == 'read') {
                            $accesslevl = false;
                        }
                    }
                }

                foreach ($eventFeed as $event) {
                    foreach ($event->when as $when) {
                        $startD = substr($when->startTime, 0, 19);
                        $startD = str_replace('T', ' ', $startD);
                        $endD   = substr($when->endTime, 0, 19);
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
                        $row["title"]     = $event->title->text;
                        $row["id"]        = $event->id->text;
                        $row["editable"]  = $accesslevl;
                        $row["start"]     = $startD;
                        $row["end"]       = $endD;
                        $row["allDay"]    = $allDay;
                        $row["color"]     = $color;
                        $row["textColor"] = "white";
                        $row["calendar"]  = "Google Calendar event";

                        array_push($rows, $row);
                    }
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