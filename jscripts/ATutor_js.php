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
    ATutor.course.showside = "<?php echo _AT('show_side_menu'); ?>";
    ATutor.course.hideside = "<?php echo _AT('hide_side_menu'); ?>";
    ATutor.course.theme = "<?php echo $_SESSION['prefs']['PREF_THEME']; ?>";
    ATutor.course.collapse_icon = "<?php echo AT_print($tree_collapse_icon, 'url.tree'); ?>";
    ATutor.course.expand_icon = "<?php echo AT_print($tree_expand_icon,  'url.tree'); ?>";

    //everything in the document.ready block executes after the page is fully loaded
    jQuery(document).ready( function () {  

                // Floating topnavlist bar
                $('#lrg_topnav').scrollToFixed({
                    marginTop: 20,
                    dontSetWidth: true,
                    preFixed: function() { 
                        $(this).find('div').css('background-color', '#EAEAEA'); 
                        $(this).find('div').css('padding', '');
                        $(this).find('div').css('width', '100%');
                        $("#topnavlistcontainer").css('padding-left', '3.3em');
                        $("#topnavlistcontainer").css('padding-bottom', '.3em');
                        $("#topnavlistcontainer").css('margin-left', '-2.3em');
                        $("#topnavlistcontainer").css('background-image', 'none');                    
                        $(this).find('ul').css('padding-left', '200');
                        $("#manage_off").css("display","none");
                        $("#manage_on").css("display","none");
                        },
                    postFixed: function() { 
                        $(this).find('div').css('background-image', ''); 
                        $(this).find('div').css('background-color', ''); 
                        $(this).find('div').css('padding', '');
                        $("#topnavlistcontainer").css('padding-left', '');
                        $("#topnavlistcontainer").css('margin-left', '');
                        $("#manage_off").css("display","none");
                        $("#manage_on").css("display","none");
                        }
                });
                
                // Float Navigation tab in 640 mode
         /*       $('#sm_topnav').scrollToFixed({
                    marginTop: 32,
                    dontSetWidth: true,
                    preFixed: function() { 
                        $(this).find('li').css('background-image', 'linear-gradient(#FAFAFA, #EAEAEA)'); 
                        $(this).find('li').css('margin-top', '.2em');
                        $(this).find('li').css('padding-right', '2em');
                        $(this).find('li').css('border', '0');
                        $(this).find('div').css('padding', '8');
                        $(this).find('div').css('width', '100%');
                        $(this).find('div').css('float', 'left');                        
                        },
                    postFixed: function() { 
                        $(this).find('div').css('background-image', ''); 
                        $(this).find('div').css('padding', '');
                        }
                });    
            */    
                // Float Content tab in 640 mode
                $('#sm_content').scrollToFixed({
                    marginTop: 0,
                    dontSetWidth: true,
                    preFixed: function() {  
                        $("a.content_link").css('margin-top', '0em');
                        $("a.content_link").css('margin-left', '1.8em');
                        },
                    postFixed: function() { 
                        $("a.content_link").css('margin-left', '0em');
                        }
                });      
                      
                // Floating subnavlist bar

                $('#lrg_subnav').scrollToFixed({
                    marginTop: 22,
                    dontSetWidth: false,
                    preFixed: function() { 
                        $(this).find('div').css('padding', '0');
                        $(this).find('div').css('margin-left', '0');
                        $(this).find('div').css('width', '100%');
                        $(this).find('div').css('position','');
                        $(this).find('div').css('height', '2.6em')
                        $("ul#subnavlist_i li").css('background-color', '#FFFFFF'); 
                        $("ul#subnavlist_i li").css('padding', '.3em'); 
                        $("ul#subnavlist li").css('padding', '.4em'); 
                        $("ul#subnavlist").css('margin-top', '-2.6em');  
                        $("#subnavlist").css('float', 'left'); 
                        $("#subnavlist").css('clear', 'left');
                        if($.cookie('side-menu') === 'none'){
                            $("#subnavlistcontainer").css('margin-left', '-20.5em');
                        }
                        if($.cookie('showSubNav_i') === 'on'){
                            $("#subnavlistcontainer").css('background-color', '#eeeeee');
                        }   
                        <?php
                        $current_page = substr($_SERVER['PHP_SELF'], strlen($_base_path));
                       /* 
                        if(count(get_sub_navigation($current_page))> 1){ ?>
                            $("#subnavlistcontainer").css('background-color', '#eeeeee');
                       <?php } else {?>
                         //   $("#subnavlist").css('display', 'none');
                         //   $("#subnavlist").css('border-bottom', 'none');
                       <?php }
                       */
                       ?>
                        },
                    postFixed: function() { 
                        $(this).find('div').css('background-color', ''); 
                        $(this).find('div').css('padding', '');
                        $(this).find('div').css('right','');
                        $(this).find('div').css('width', '100%');
                        $(this).find('div').css('position','');
                        $(this).find('div').css('height','');
                        $(this).find('div').css('padding', '0');
                        $("ul#subnavlist li").css('padding', '.4em'); 
                        $("ul#subnavlist").css('float', 'none');                         
                        $("ul#subnavlist").css('padding-left', '2em');
                        $("ul#subnavlist").css('clear', 'none');
                        $("ul#subnavlist li").css('margin-bottom', '0');
                        $("ul#subnavlist").css('margin-top', '0'); 
                        $("div#subnavbacktopage").css('width', '1.5em'); 
                        
                        $("#subnavlistcontainer").css('border-bottom', '0'); 
                        if($.cookie('side-menu') === 'none'){
                            $("#subnavlistcontainer").css('margin-left', '0');
                        }  
                    }
                });
                // Fixed Footer

                $('#footer').scrollToFixed( {
                    bottom:0,
                    limit: $('#footer').offset().top - ($('#footer').outerHeight()),
                    preFixed: function() {    
                       //$('#footer').show("slow");
                       //console.log("pre");
                        },
                    postFixed: function() {
                       // $('#footer').hide("slow");
                       // console.log("post");
                        }
                });
                 
                /********
                **  Hide/Show instructor course admin tools 
                *******/
                var initialStatus = ($.cookie('showSubNav_i') === "on") ? "1" : "0";
                if(initialStatus === "0"){
                    $("ul#subnavlist").css("border-bottom", "none");
                    $("#subnavlist_i").toggleClass("hidden").hide('slow');
                    $(".menuedit").toggleClass("hidden").hide('slow');
                    $("#shortcuts").toggleClass("hidden").hide('slow');
                    $(".del-content-icon").toggleClass("hidden").hide('slow');  
                    $(".buttonbox").toggleClass("hidden").hide('slow'); 
                }

                ATutor.switchView = function (viewFlag) {
                    if(viewFlag === "0"){
                        $("ul#subnavlist").css("border-bottom", "1px solid #DED29E");
                        $("#subnavlist_i").toggleClass("show").show('slow');
                        $("#manage_off").css("display","none");
                        $("#manage_on").css("display","inline");
                        $(".menuedit").toggleClass("show").show('slow');
                        $("#shortcuts").toggleClass("show").show('slow');
                        $(".del-content-icon").toggleClass("show").show('slow');  
                       // $(".detail_switch").toggleClass("show").show('slow');  
                        $(".buttonbox").toggleClass("show").show('slow');    
                        $.cookie('showSubNav_i', "on", { expires: 30, path: '/' });

                    } else if(viewFlag === "1") {
                        $("ul#subnavlist").css("border-bottom", "none");
                        $("#subnavlist_i").toggleClass("hidden").hide('slow');
                        $("#manage_off").css("display","inline");
                        $("#manage_on").css("display","none");
                        $(".menuedit").toggleClass("hidden").hide('slow');
                        $("#shortcuts").toggleClass("hidden").hide('slow');
                        $(".del-content-icon").toggleClass("hidden").hide('slow');  
                        $(".buttonbox").toggleClass("hidden").hide('slow');
                        $.cookie('showSubNav_i', "off", { expires: 30, path: '/' });
                    }
                    return false;     
                };
                <?php if(isset($_SESSION['valid_user'])){ ?>
                // Initialize the switch based on previously saved cookie value    
                $('#admin_switch option[value="' + initialStatus + '"]').attr("selected", true);
                $('#admin_switch').switchify();
                $('#admin_switch').val(($.cookie('showSubNav_i') === "on") ? "1" : "0");
                $("#subnavlistcontainer").css("background", "transparent");
                $(".ui-switch").click(function(e){
                    ATutor.switchView($('#admin_switch').val());
                });
                $(".ui-switch").keypress(function(e){
                    var code = e.keyCode || e.which;
                    if(code == 13 || code == 32) { 
                    ATutor.switchView($('#admin_switch').val());
                    }
                });
            <?php } ?>

 <?php
    if(isset($_SESSION['prefs']['PREF_HIDE_FEEDBACK']) && $_SESSION['prefs']['PREF_HIDE_FEEDBACK'] != 0) {
    ?>
         $('#message').css('display', 'block').slideDown("slow");
            setTimeout(function() {
            $("#message").hide('blind', {}, 500);
            }
        );
        
        <?php } ?>
    /* To hide feedback div when clicked */
        $(".message_link").click(function() {
            // the following line doesn't work from the login screen
            // replaced with the line below it for now
            //$("#message").hide("blind",  {},  500), 8000;
            $("#message").hide("blind");
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
                $('#icon_to_detailed').css("display","none");
                $('#detailed_to_icon').css("display","inline");
                $.cookie('showDetails', "off", { expires: 30, path: '/' });
                $('#detail_switch').val('0');
             } else if(viewFlag === "0") {
                $("#details_view").show('blind', {}, 500), 8000;
                $("#icon_view").hide('blind', {}, 500), 8000;
                $('#icon_to_detailed').css("display","inline");
                $('#detailed_to_icon').css("display","none");
                $('#detail_switch').removeClass('detail_switch');
                $('#detail_switch').addClass('detail_switch_back');
                $.cookie('showDetails', "on", { expires: 30, path: '/' });
                $('#detail_switch').val('1')
            }
            return false;
        }
        $('#detail_switch').val(($.cookie('showDetails') === "on") ? "1" : "0");

        $("#detail_switch").click( function(){
            ATutor.switchDetails($('#detail_switch').val());                    
        });
        $("#detail_switch").keypress(function(e){
            $(".ui-switch").click(function(e){
                var code = e.keyCode || e.which;
                if(code == 13 || code == 32) { 
                    ATutor.switchDetails($('#detail_switch').val());
                }
            });
        });
    /*********/ 
        
    /*****
    ** Switch between hide and show submenu
    *****/
        // Get initial subnavbar toggle state
        var initialSubNav = ($.cookie('showSubNav') === "expanded") ? "expanded" : "collapsed";
        if(initialSubNav === 'collapsed'){
            $('#showsubnav').css('display', 'none');
            $('#hidesubnav').css('display', 'inline');
        }else{
            $('#hidesubnav').css('display', 'inline');
            $('#subnavlist li:not(.active)').css('display', 'none');
        }
        // Enable subnavtoggle mouse access
        $("#showsubnav").click(function() {
            $('#showsubnav').css('display', 'none');
            $('#hidesubnav').css('display', 'inline');
            $('#subnavlist li:not(.active)').show("1200");
            $('#subnav-hide').css('display', 'none');
            $('#subnav-open').css('display', 'inline');
            var new_value = $("#showsubnav").is(":visible") ? 'expanded' : 'collapsed';
            $.cookie('showSubNav', new_value, { expires: 30, path: '/' });
        });
        $("#hidesubnav").click(function() {
            $('#showsubnav').css('display', 'inline');
            $('#hidesubnav').css('display', 'none');
            $('#subnavlist li:not(.active)').hide("1200");
            $('#subnav-hide').css('display', 'inline');
            $('#subnav-open').css('display', 'none');
            var new_value = $("#showsubnav").is(":visible") ? 'expanded' : 'collapsed';
            $.cookie('showSubNav', new_value, { expires: 30, path: '/' });
        });
        
        //Enable subnav toggle keyboard access
        $("#subnavlist li.active").keypress(function(e) {
            var code = e.keyCode || e.which;
            if(code == 13 || code == 32) { 
                if($.cookie('showSubNav') === 'collapsed'){
                    $('#subnavlist li:not(.active)').show("1200");
                    $('#showsubnav').css('display', 'none');
                    $('#hidesubnav').css('display', 'inline');
                    $('#subnav-hide').css('display', 'none');
                    $('#subnav-open').css('display', 'inline');
                    var new_value = $("#subnavlist li:not(.active)").is(":visible") ? 'expanded' : 'collapsed';
                    $.cookie('showSubNav', new_value, { expires: 30, path: '/' });
                }else{
                    $('#subnavlist li:not(.active)').hide("1200");
                    $('#showsubnav').css('display', 'inline');
                    $('#hidesubnav').css('display', 'none');
                    $('#subnav-hide').css('display', 'inline');
                    $('#subnav-open').css('display', 'none');
                    var new_value = $("#subnavlist li:not(.active)").is(":visible") ? 'expanded' : 'collapsed';
                    $.cookie('showSubNav', new_value, { expires: 30, path: '/' });
                }
            }
        });
        
        
        // Inject aria-live update when sidebar opened/closed for screen readers
        $("#menutoggle").click(function() {
            if($.cookie('side-menu') === 'none'){
                $('#side_bar_off').show('fast');
                $('#side_bar_on').css('display','none');
            }else{
                $('#side_bar_on').show('fast');  
                $('#side_bar_off').css('display', 'none');           
            }
        });



    /* Show/Hide Advanced Admin System Preferences, set cookie */
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
     <?php 
     // If there are search results, send focus to them
     if(isset($_GET['search'])){ ?>
            $( "#search_results" ).focus();
    <?php } ?>
})();

ATutor.addJavascript(ATutor.base_href+"jscripts/lib/jquery.autoHeight.js");