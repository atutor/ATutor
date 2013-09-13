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
     * This file is used to associate/dissociate user's Google account
     * with ATutor Calendar module.
     * If request is for association then first this page will 
     * display a login screen, after login is successful or 
     * user is already logged in then a consent screen is displaed. 
     * After user gives consent, the pop-up window closes.
     * If the request is for dissociation then user's entry in the 
     * database is removed and session token is invalidated. 
     */
    $_user_location	= 'public';
    require_once 'includes/classes/googlecalendar.class.php';
    define('AT_INCLUDE_PATH', '../../../include/');
    require(AT_INCLUDE_PATH.'vitals.inc.php');

    $gcalobj = new GoogleCalendar();
    
    if (isset($_GET['logout'])) {
        //request is for dissociation
        //removed entry from database

        $query = "DELETE FROM %scalendar_google_sync WHERE userid=%d";
        queryDB($query, array(TABLE_PREFIX, $_SESSION['member_id']));
        
        //invalidate session token
        $gcalobj->logout();
    } else {
        //request is for association
        if (!isset($_GET['token'])) {
            //redirect to login page/consent page

            $query = "DELETE FROM %scalendar_google_sync WHERE userid=%d";
            queryDB($query, array(TABLE_PREFIX, $_SESSION['member_id']));
            
            unset($_SESSION['sessionToken']);
            $authSubUrl = $gcalobj->getAuthSubUrl();
            header('Location:' . $authSubUrl);
        } else {
            //insert session token in the database for future use and close pop-up window
            $client = $gcalobj->getAuthSubHttpClient();

            $query    = "INSERT INTO %scalendar_google_sync (token,userid,calids) VALUES ('%s', %d,'')";
            queryDB($query, array(TABLE_PREFIX, $_SESSION['sessionToken'],  $_SESSION['member_id']));
            
            echo '<script>window.opener.location.reload(true);window.close();</script>';
        }
    }
?>