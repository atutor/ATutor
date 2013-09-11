<?php
    /****************************************************************/
    /* ATutor Calendar Module                                       */
    /* https://atutorcalendar.wordpresults.com/                     */
    /*                                                              */
    /* This module provides standard calendar featuresult in ATutor.*/
    /*                                                              */
    /* Author: Anurup Raveendran, Herat Gandhi                      */
    /* This program is free software. You can redistribute it and/or*/
    /* modify it under the terms of the GNU General Public License  */
    /* as published by the Free Software Foundation.                */
    /****************************************************************/
    
    /**
     * This file is used to display all the available
     * calendars in Google Account of the user.
     */     
    require_once 'includes/classes/googlecalendar.class.php';

    $gcalobj = new GoogleCalendar();
    global $db;
    
    $query  = "SELECT * FROM " . TABLE_PREFIX . "calendar_google_sync WHERE userid='" . 
              $_SESSION['member_id'] . "'";
    $result = mysql_query($query, $db);
    //Check if user has associated his/her Google account or not
    if (mysql_num_rows($result) > 0) {
        /**
         * User has already associated his/her Google account. 
         * So get the session token from database.
         */
        $row                      = mysql_fetch_assoc($result);
        $_SESSION['sessionToken'] = $row['token'];
        //Verify token
        if ($gcalobj->isvalidtoken($_SESSION['sessionToken'])) {
            $client = $gcalobj->getAuthSubHttpClient();
            //Output calendar list in the right side panel
            $gcalobj->outputCalendarList($client);
        }
    }
?>