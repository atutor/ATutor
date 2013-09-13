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
     * This file manages users' requests to sync or not-sync
     * Google calendars. The sidemenu in the right side shows
     * available Google calendars for sync. If user changes preference
     * for a calendar then it is reflected in the database using this file.
     */
         $_user_location = 'public';
    define('AT_INCLUDE_PATH', '../../../include/');
    require(AT_INCLUDE_PATH.'vitals.inc.php');

    //Get calendar id and operation mode
    $new_id = $_GET['calid'];
    $mode  = $_GET['mode'];

    if ($mode == 'add') {
        //Get calendar ids from database, append the new id and update database

        $query    = "SELECT * FROM %scalendar_google_sync WHERE userid=%d";
        $row_val   = queryDB($query, array(TABLE_PREFIX, $_SESSION['member_id']), TRUE);
        $prev_val = $row_val['calids'];
        $prev_val .= htmlspecialchars($new_id).',';

        $query    = "UPDATE %scalendar_google_sync SET calids='%s' WHERE userid=%d";
        queryDB($query, array(TABLE_PREFIX, $prev_val, $_SESSION['member_id']));

    } else {
        //Get calendar ids from database, remove entry for selected id and update database

        $query    = "SELECT * FROM %scalendar_google_sync WHERE userid=%d";
        $row_val   = queryDB($query, array(TABLE_PREFIX, $_SESSION['member_id'] ), TRUE);
        $prev_val = $row_val['calids'];

        $prev_val = str_replace(htmlspecialchars($new_id) . ",", "", $prev_val);

        $query    = "UPDATE %scalendar_google_sync SET calids='%s' WHERE userid=%d";
        queryDB($query, array(TABLE_PREFIX, $prev_val, $_SESSION['member_id']));
    }
?>
