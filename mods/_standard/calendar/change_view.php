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
     * This file is used to save calendar's state
     * that is current view and starting date.
     * So that when user refreshes the page, he/she
     * will get the same state again.
     */
    $_user_location	= 'public';
    define('AT_INCLUDE_PATH', '../../../include/');
    require(AT_INCLUDE_PATH.'vitals.inc.php');

    $_SESSION['fc-viewname'] = "'" . $_GET['viewn'] . "'";
    $_SESSION['fc-year']     = $_GET['year'];
    $_SESSION['fc-month']    = $_GET['month'];
    $_SESSION['fc-date']     = $_GET['date'];

    exit();
?>