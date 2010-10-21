<?php 
/************************************************************************/
/* ATutor                                                               */
/************************************************************************/
/* Copyright (c) 2002 - 2010                                            */
/* Inclusive Design Institute                                           */
/* http://atutor.ca                                                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id: $
//
// This file is essentially a javascript file, but needs to be terminated with
// a .php extension so that php calls can be used within it. Please put pure javascript
// in ATutor.js

// Look for tree icons for displaying content navigation from theme image folder,
// if the icon is not there, look up in atutor root image folder
global $rtl;

$theme_image_folder = 'themes/'.$_SESSION['prefs']['PREF_THEME'].'/images/';
$atutor_image_folder = 'images/';

if (file_exists(AT_INCLUDE_PATH.'../'.$theme_image_folder.$rtl.'tree/tree_collapse.gif')) {
	$tree_collapse_icon = AT_BASE_HREF.$theme_image_folder.$rtl.'tree/tree_collapse.gif';
} else {
	$tree_collapse_icon = AT_BASE_HREF.$atutor_image_folder.$rtl.'tree/tree_collapse.gif';
}

if (file_exists(AT_INCLUDE_PATH.'../'.$theme_image_folder.'tree/tree_expand.gif')) {
	$tree_expand_icon = AT_BASE_HREF.$theme_image_folder.$rtl.'tree/tree_expand.gif';
} else {
	$tree_expand_icon = AT_BASE_HREF.$atutor_image_folder.$rtl.'tree/tree_expand.gif';
}
		
?>
ATutor = ATutor || {};
ATutor.course = ATutor.course || {};

(function () {

    ATutor.base_href = "<?php echo AT_BASE_HREF; ?>";
    ATutor.course.show = "<?php echo _AT('show'); ?>";
    ATutor.course.hide = "<?php echo _AT('hide'); ?>";
    ATutor.course.theme = "<?php echo $_SESSION['prefs']['PREF_THEME']; ?>";
    ATutor.course.collapse_icon = "<?php echo $tree_collapse_icon; ?>";
    ATutor.course.expand_icon = "<?php echo $tree_expand_icon; ?>";

    //everything in the document.ready block executes after the page is fully loaded
    jQuery(document).ready( function () {
        ATutor.users.preferences.setStyles(
                     '<?php echo $_SESSION["prefs"]["PREF_BG_COLOUR"]; ?>',
                     '<?php echo $_SESSION["prefs"]["PREF_FG_COLOUR"]; ?>',
                     '<?php echo $_SESSION["prefs"]["PREF_HL_COLOUR"]; ?>',
                     '<?php echo $_SESSION["prefs"]["PREF_FONT_FACE"]; ?>',
                     '<?php echo $_SESSION["prefs"]["PREF_FONT_TIMES"]; ?>');

        ATutor.users.preferences.addPrefWizClickHandler();
        ATutor.users.preferences.course_id = "<?php echo $_SESSION['course_id']; ?>";                
<?php 
        if (isset($_SESSION['course_id']) && ($_SESSION['course_id'] > 0)) {
?>
            var myName = self.name;
            if (myName != "prefWizWindow" && myName != "progWin") {
                ATutor.course.doSideMenus();
                ATutor.course.doMenuToggle();
            }
<?php   }
?>        
     });
})();


