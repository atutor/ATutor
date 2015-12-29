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
     * This file returns text for a term. 
     */
     
    if (isset($_GET['pub'])) {
        $_user_location = 'public';
    }
    define('AT_INCLUDE_PATH', '../../../include/');
    require(AT_INCLUDE_PATH.'vitals.inc.php');
    
    $token = strip_tags($addslashes($_GET['token']));
    echo _AT($token);
?>