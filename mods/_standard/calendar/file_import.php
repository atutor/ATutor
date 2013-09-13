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
     * This file provides UI for uploading ics file.
     */
    $_user_location	= 'public';
    define('AT_INCLUDE_PATH', '../../../include/');
    require(AT_INCLUDE_PATH.'vitals.inc.php');
    require(AT_INCLUDE_PATH.'header.inc.php');
?>

<form action="mods/_standard/calendar/import_ics.php" method="post" enctype="multipart/form-data">
    <label for="file"><?php echo _AT('calendar_upload_file'); ?></label>
    <input type="file" name="file" id="file" />
    <br />
    <input type="submit" name="submit" value="<?php echo _AT('calendar_submit'); ?>" />
</form>

<?php
    require(AT_INCLUDE_PATH.'footer.inc.php');
?>