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
     * This file is used to edit and delete events in the database.
     * This file creates a bridge between Javascript(front end) and database(back end).
     */
    
    //Retrieve all the parameters from request
    $_user_location = 'public';

    define('AT_INCLUDE_PATH', '../../../include/');
    require (AT_INCLUDE_PATH.'vitals.inc.php');
        
    if (!$_SESSION['valid_user']) {
        require(AT_INCLUDE_PATH.'header.inc.php');
        $info = array('INVALID_USER', $_SESSION['course_id']);
        $msg->printInfos($info);
        require(AT_INCLUDE_PATH.'footer.inc.php');
        exit;
    }
    
    $iddd      = $_GET["id"];
    $newstartd = $_GET["start"];
    $newend    = $_GET["end"];
    $newtitle  = strip_tags($_GET["title"]);
    $alld      = $_GET["allday"];
    $command   = $_GET["cmd"];
    
    //Use SQL Query according to the situation
    if( strcmp($command,"drag") == 0 ) {
        //Event is dragged, so update dates
        $query = "UPDATE `%scalendar_events` SET start = '%s', end = '%s' WHERE id=%d";
        queryDB( $query, array(TABLE_PREFIX, $newstartd, $newend, $iddd));
    }
    else if( strcmp($command,"create") == 0 ) {
        //New event is created, first insert the record in the table and then return id to javascript
        $query = "INSERT INTO `%scalendar_events` (title,start,end,allDay,userid) values ('%s','%s','%s',%d,%d)" ;
        queryDB( $query, array(TABLE_PREFIX, $newtitle, $newstartd, $newend, $alld, $_SESSION['member_id']));
        
        $query = "SELECT MAX(id) from `%scalendar_events`";
        $idno = queryDB($query, array(TABLE_PREFIX), TRUE); 
        echo $idno['id'];    
    }
    else if( strcmp($command,"delete") == 0 ) {
        //Delete the event
        $query = "DELETE FROM `%scalendar_events` WHERE id=%d";
        queryDB($query, array(TABLE_PREFIX, $iddd));
    }
    else if( strcmp($command,"update") == 0 ) {
        //User clicked on the event. The dates and title may be changed. So update the database record.
        $query = "UPDATE `%scalendar_events` SET title = '%s' , start = '%s', end = '%s' WHERE id=%d";
        queryDB( $query, array(TABLE_PREFIX, $newtitle, $newstartd, $newend, $iddd) );
    }
?>