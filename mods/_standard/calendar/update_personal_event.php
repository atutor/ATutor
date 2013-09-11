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
    define('AT_INCLUDE_PATH', '../../../include/');
    require (AT_INCLUDE_PATH.'vitals.inc.php');
    $iddd      = $_GET["id"];
    $newstartd = $_GET["start"];
    $newend    = $_GET["end"];
    $newtitle  = $_GET["title"];
    $alld      = $_GET["allday"];
    $command   = $_GET["cmd"];
    
    //Connect to database
    global $db;
    //Use SQL Query according to the situation
    if( strcmp($command,"drag") == 0 ) {
        //Event is dragged, so update dates
        $query = "UPDATE `".TABLE_PREFIX."calendar_events` SET start = '".$newstartd.
        "', end = '".$newend."' WHERE id=".$iddd;
        mysql_query( $query, $db );
    }
    else if( strcmp($command,"create") == 0 ) {
        //New event is created, first insert the record in the table and then return id to javascript
        $query = "INSERT INTO `".TABLE_PREFIX."calendar_events` (title,start,end,allDay,userid) values".
        " ('".$newtitle."','".$newstartd."','".$newend."','".$alld."','".$_SESSION['member_id']."')" ;
        mysql_query( $query, $db );
        $query = "SELECT MAX(id) from `".TABLE_PREFIX."calendar_events`";
        $resultno = mysql_query( $query, $db );
        $idno=mysql_fetch_row($resultno);  
        echo $idno[0];    
    }
    else if( strcmp($command,"delete") == 0 ) {
        //Delete the event
        $query = "DELETE FROM `".TABLE_PREFIX."calendar_events` WHERE id=".$iddd;
        mysql_query( $query, $db );
    }
    else if( strcmp($command,"update") == 0 ) {
        //User clicked on the event. The dates and title may be changed. So update the database record.
        $query = "UPDATE `".TABLE_PREFIX."calendar_events` SET title = '".$newtitle."' , start = '".
        $newstartd."', end = '".$newend."' WHERE id=".$iddd;
        mysql_query( $query, $db );
    }
?>