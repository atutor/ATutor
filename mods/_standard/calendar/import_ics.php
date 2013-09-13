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
     * This file is used to populate database using the uploaded ics
     * file. Once database is populated, the uploaded ics file is deleted.
     */
    define('AT_INCLUDE_PATH', '../../../include/');
    require(AT_INCLUDE_PATH.'vitals.inc.php');

    $filename = $_SESSION['member_id'] . '.ics';

    if ($_FILES['file']['error'] > 0) {
        //Error in file processing
        $msg->addError('CAL_FILE_ERROR');
        header('Location: file_import.php');
    } else {
        if (file_exists('../../../content/calendar/' . $filename)) {
            //File already exists so replace it with new version
            if (unlink('../../../content/calendar/' . $filename)) {
                //File successfully deleted
            }
            else {
                //Error in deleting files
                $msg->addError('CAL_FILE_DELETE');
                header('Location: file_import.php');
            }
        }
        //Move uploaded file to proper location in content directory
        move_uploaded_file($_FILES['file']['tmp_name'],
        '../../../content/calendar/' . $filename);
    }

    require_once('lib/SG_iCal/SG_iCal.php');

    function dump_t($x) {
        echo '<pre>' . print_r($x,true) . '</pre>';
    }
    //Open iCal File
    $ICS = '../../../content/calendar/' . $filename;
    $ical = new SG_iCalReader($ICS);
    $query = new SG_iCal_Query();
    //Get all the events from ics file
    $evts = $ical->getEvents();
    $data = array();
    //Iterate through events and construct array that can be used by full calendar
    foreach ($evts as $id => $ev) {
        //Parse data using SG_iCal parser
        $jsEvt = array(
            "id"     => ($id+1),
            "title"  => $ev->getProperty('summary'),
            "start"  => $ev->getStart(),
            "end"    => $ev->getEnd()-1,
            "allDay" => $ev->isWholeDay()
        );

        if (isset($ev->recurrence)) {
            $count = 0;
            $start = $ev->getStart();
            $freq  = $ev->getFrequency();
            if ($freq->firstOccurrence() == $start) {
                $data[] = $jsEvt;
            }
            while (($next = $freq->nextOccurrence($start)) > 0) {
                if (!$next or $count >= 1000) 
                    break;
                $count++;
                $start          = $next;
                $jsEvt['start'] = $start;
                $jsEvt['end']   = $start + $ev->getDuration()-1;
                $data[]         = $jsEvt;
            }
        } else {
            $data[] = $jsEvt;
        }
    }
    global $db;
    //If there are events then insert them in the database
    if(count($data) > 0) {
        foreach($data as $event) {
            if($event['allDay'] == 1) {
                $alld = "true";
            } else  {
                $alld = "false";
            }
            //Insert each event in the database

            $query = "INSERT INTO `%scalendar_events` (title,start,end,allDay,userid) values ('%s','%s','%s','%s',%d)" ; 
            queryDB($query, array(TABLE_PREFIX, $event['title'], Date('Y-m-d H:m:s',$event['start']), Date('Y-m-d H:m:s',$event['end']), $alld, $_SESSION['member_id']));
        }
    }
    $msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
    unlink('../../../content/calendar/'.$filename);
    header('Location: index.php');
?>