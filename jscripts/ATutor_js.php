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

$tree_collapse_icon = AT_BASE_HREF.find_image($rtl.'tree/tree_collapse.gif', '');
$tree_expand_icon = AT_BASE_HREF.find_image($rtl.'tree/tree_expand.gif', '');
		
?>
ATutor = ATutor || {};
ATutor.course = ATutor.course || {};

(function () {

    ATutor.base_href = "<?php echo AT_print(AT_BASE_HREF, 'url.base'); ?>";
    ATutor.customized_data_dir = "<?php echo AT_print(AT_CUSTOMIZED_DATA_DIR, 'url.base'); ?>";
    ATutor.course.show = "<?php echo _AT('show'); ?>";
    ATutor.course.hide = "<?php echo _AT('hide'); ?>";
    ATutor.course.theme = "<?php echo $_SESSION['prefs']['PREF_THEME']; ?>";
    ATutor.course.collapse_icon = "<?php echo AT_print($tree_collapse_icon, 'url.tree'); ?>";
    ATutor.course.expand_icon = "<?php echo AT_print($tree_expand_icon,  'url.tree'); ?>";

    //everything in the document.ready block executes after the page is fully loaded
    jQuery(document).ready( function () {
    /* To automatically hide feedback message, uncomment */
  	/* $('#message').css('display', 'block').slideDown("slow");
            setTimeout(function() {
        $("#message").hide('blind', {}, 500)
        }, 8000);
    */
        
    /* To hide feedback div when clicked */
        $("#message").click(function() {
         $("#message").hide('blind', {}, 500), 8000;
         return false;
        });  
    /* Show/Hide Advanced Admin System Preferecnes, set cookie */
        $(".adv_opts").toggle($.cookie('showTop') != 'collapsed');
            $("div.adv_toggle").click(function() {
            $(this).toggleClass("active").next().toggle();
            var new_value = $(".adv_opts").is(":visible") ? 'expanded' : 'collapsed';
            $.cookie('showTop', new_value);
        });
        ATutor.users.preferences.setStyles(
                     '<?php if(isset($_SESSION["prefs"]["PREF_BG_COLOUR"])){echo $_SESSION["prefs"]["PREF_BG_COLOUR"];} ?>',
                     '<?php if(isset($_SESSION["prefs"]["PREF_FG_COLOUR"])){ echo $_SESSION["prefs"]["PREF_FG_COLOUR"];} ?>',
                     '<?php if(isset($_SESSION["prefs"]["PREF_HL_COLOUR"])){echo $_SESSION["prefs"]["PREF_HL_COLOUR"];} ?>',
                     '<?php if(isset($_SESSION["prefs"]["PREF_FONT_FACE"])){echo $_SESSION["prefs"]["PREF_FONT_FACE"];} ?>',
                     '<?php if(isset($_SESSION["prefs"]["PREF_FONT_TIMES"])){echo $_SESSION["prefs"]["PREF_FONT_TIMES"];} ?>');

        ATutor.users.preferences.addPrefWizClickHandler();
        ATutor.users.preferences.course_id = "<?php if(isset($_SESSION['course_id'])){ echo $_SESSION['course_id'];} ?>";                
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

ATutor.addJavascript(ATutor.base_href+"jscripts/lib/jquery.autoHeight.js");