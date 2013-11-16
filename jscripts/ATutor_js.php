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
                //Handle the hide admin tool switch
                var initialStatus = ($.cookie('showSubNav') === "on") ? "1" : "0";
                if(initialStatus === "0"){
                    $("ul#subnavlist").css("border-bottom", "none");
                    $("#subnavlist_i").toggleClass("hidden").hide('slow');
                    $(".menuedit").toggleClass("hidden").hide('slow');
                    $("#shortcuts").toggleClass("hidden").hide('slow');
                    $(".del-content-icon").toggleClass("hidden").hide('slow'); 
                   // $(".detail_switch").toggleClass("hidden").hide('slow');  
                    $(".buttonbox").toggleClass("hidden").hide('slow'); 
                }
                ATutor.switchView = function (viewFlag) {
                    if(viewFlag === "0"){
                        $("ul#subnavlist").css("border-bottom", "1px solid #DED29E");
                        $("#subnavlist_i").toggleClass("show").show('slow');
                        $(".menuedit").toggleClass("show").show('slow');
                        $("#shortcuts").toggleClass("show").show('slow');
                        $(".del-content-icon").toggleClass("show").show('slow');  
                       // $(".detail_switch").toggleClass("show").show('slow');  
                        $(".buttonbox").toggleClass("show").show('slow');    
                        $.cookie('showSubNav', "on", { expires: 30, path: '/' });
                        //console.log("viewFlag 1; " + viewFlag + "; " + $.cookie("showSubNav"));
                        //console.log("switchval=" + $('#admin_switch').val() + '  initial=' + initialStatus);
                    } else if(viewFlag === "1") {
                        $("ul#subnavlist").css("border-bottom", "none");
                        $("#subnavlist_i").toggleClass("hidden").hide('slow');
                        $(".menuedit").toggleClass("hidden").hide('slow');
                        $("#shortcuts").toggleClass("hidden").hide('slow');
                        $(".del-content-icon").toggleClass("hidden").hide('slow');
                       // $(".detail_switch").toggleClass("hidden").hide('slow');  
                        $(".buttonbox").toggleClass("hidden").hide('slow');
                        $.cookie('showSubNav', "off", { expires: 30, path: '/' });
                       //console.log("viewFlag 0; " + viewFlag + "; " + $.cookie("showSubNav"));
                       //console.log("switchval=" + $('#admin_switch').val() + ' initial=' + initialStatus);
                    }
                    return false;     
                };
                // Initialize the switch based on previously saved cookie value    
                $('#admin_switch option[value="' + initialStatus + '"]').attr("selected", true);
                $('#admin_switch').switchify();
                $('#admin_switch').val(($.cookie('showSubNav') === "on") ? "1" : "0");
                
                $(".ui-switch").bind("click keypress", function(){
                    ATutor.switchView($('#admin_switch').val());
                });

    /* To automatically hide feedback message, uncomment */
  	/* $('#message').css('display', 'block').slideDown("slow");
            setTimeout(function() {
        $("#message").hide('blind', {}, 500)
        }, 8000);
    */
        
    /* To hide feedback div when clicked */
        $(".message_link").click(function() {
         $("#message").hide('blind', {}, 500), 8000;
         return false;
        });  
        
    /*****
    ** Switch between detailed and icon views on course home page
    *****/
        var initialStatusDetails = ($.cookie('showDetails') === "on") ? "1" : "0";
        if(initialStatusDetails === "0"){
                $("#icon_view").show();
                $("#details_view").hide();
                $('#detail_switch').removeClass('detail_switch_back');
                $('#detail_switch').addClass('detail_switch');
        } else{
                $("#icon_view").hide();
                $("#details_view").show();
                $('#detail_switch').removeClass('detail_switch');
                $('#detail_switch').addClass('detail_switch_back');    
        }
       ATutor.switchDetails = function (viewFlag) {
            if(viewFlag === "1"){
                    $("#details_view").hide('blind', {}, 500), 8000;
                    $("#icon_view").show('blind', {}, 500), 8000;   
                $('#detail_switch').removeClass('detail_switch_back');
                $('#detail_switch').addClass('detail_switch');
                    $.cookie('showDetails', "off", { expires: 30, path: '/' });
                    $('#detail_switch').val('0');
             } else if(viewFlag === "0") {
                    $("#details_view").show('blind', {}, 500), 8000;
                    $("#icon_view").hide('blind', {}, 500), 8000;
                $('#detail_switch').removeClass('detail_switch');
                $('#detail_switch').addClass('detail_switch_back');
                    $.cookie('showDetails', "on", { expires: 30, path: '/' });
                    $('#detail_switch').val('1')
            }
            return false;
        }
        $('#detail_switch').val(($.cookie('showDetails') === "on") ? "1" : "0");

        $("#detail_switch").bind("click keypress", function(){
            ATutor.switchDetails($('#detail_switch').val());
            
        });
    /*********/ 
        
        
    /* Show/Hide Advanced Admin System Preferecnes, set cookie */
     /*   $(".adv_opts").toggle($.cookie('showTop') != 'collapsed');
            $("div.adv_toggle").click(function() {
            $(this).toggleClass("active").next().toggle();
            var new_value = $(".adv_opts").is(":visible") ? 'expanded' : 'collapsed';
            $.cookie('showTop', new_value);
        });
        */
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