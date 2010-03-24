<?php 
/************************************************************************/
/* ATutor                                                               */
/************************************************************************/
/* Copyright (c) 2002-2010 by Laurel Williams                           */
/* Adaptive Technology Resource Centre / University of Toronto          */
/* http://atutor.ca                                                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id: $
//
// This file is essentially a javascript file, but needs to be terminated with
// a .php extension so that php calls can be used within it.
?>
ATutor = ATutor || {};

(function () {

    jQuery(document).ready( function () {
<?php 
        //if user has set any display preferences set styles block
        if (($_SESSION["prefs"]["PREF_FONT_FACE"] != "")
            || ($_SESSION["prefs"]["PREF_FONT_TIMES"] != 0 && $_SESSION["prefs"]["PREF_FONT_TIMES"] != 1)
            || ($_SESSION["prefs"]["PREF_FG_COLOUR"] != "")
            || ($_SESSION["prefs"]["PREF_BG_COLOUR"] != "")
            || ($_SESSION["prefs"]["PREF_HL_COLOUR"] != ""))
        {
?>            
             ATutor.users.preferences.setStyles(
                     '<?php echo $_SESSION["prefs"]["PREF_BG_COLOUR"]; ?>',
                     '<?php echo $_SESSION["prefs"]["PREF_FG_COLOUR"]; ?>',
                     '<?php echo $_SESSION["prefs"]["PREF_HL_COLOUR"]; ?>',
                     '<?php echo $_SESSION["prefs"]["PREF_FONT_FACE"]; ?>',
                     '<?php echo $_SESSION["prefs"]["PREF_FONT_TIMES"]; ?>');
<?php   }?>        
     });
})();


