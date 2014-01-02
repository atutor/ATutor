/************************************************************************/
/* ATutor                                                                */
/************************************************************************/
/* Copyright (c) 2002 - 2010                                            */
/* Inclusive Design Institute                                            */
/* http://atutor.ca                                                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id: $

// Declare dependencies
/*global window, jQuery*/

var ATutor = ATutor || {};
ATutor.mobile = ATutor.mobile || {};

(function ($) {
    jQuery(document).ready(function () {
        $('#navigation-column').hide();
        // any click on the page closes the content menu but the link "content_link" itself
        $(document).click(function (e) {
            // hide content menu
/*            if ($('#lrg_content').has("#" + e.target.id).length === 0) {
                $('#lrg_content').slideUp(600);
                $('#content_link').removeClass('content_link_tablet_highlight triangle-isosceles top right');
                $('#content_link_phone').removeClass('topnavlist-link-highlight content-closed');
            }
*/
            // hide navigation menu
            $('#navigation-column').slideUp(200);
            $('#topnavlist-link').removeClass('topnavlist-link-highlight triangle-isosceles top topnavlist-link-highlight-background');
        });
        
        // open/close the content menu - tablets
  /*      $('#content_link').click(function (e) {
            $('#content').slideToggle(0);
            $('#content_link').toggleClass('content_link_tablet_highlight').toggleClass('triangle-isosceles').toggleClass('top').toggleClass('right');
            
            return false;
        });*/
        $('#content_link').click(function (e) {
            $('#lrg_content').slideToggle(500);
            $('#content_link').toggleClass('content_link_tablet_highlight').toggleClass('triangle-isosceles').toggleClass('top').toggleClass('right');
            
            return false;
        }); 
 /*       $('#content_link').click(function (e) {
            $('#side-menu').slideToggle(500);
            $('#content_link').toggleClass('content_link_tablet_highlight').toggleClass('triangle-isosceles').toggleClass('top').toggleClass('right');
            
            return false;
        });*/
        // open/close content menu - smartphones
        $('#content_link_phone').click(function (e) {
            $('#content').slideToggle();
            $('#content_link_phone').toggleClass('topnavlist-link-highlight').toggleClass('content-closed');
            $('.subnavcontain').toggleClass('subnavcontain3');
            
            return false;
        });
        
        $('#subnavlist-link').click(function (e) {
            $('#subnavlist').slideToggle();
            $('#subnavlist-link').toggleClass('content-closed').toggleClass('subnavcontain-active');
            $('.subnavcontain').toggleClass('subnavcontain3');
            
            return false;
        });
    
        // open/close header navigational menu - tablets & smartphones
        $('.topnavlist-link').click(function (e) {
            $('#navigation-column').slideToggle("slow");
            $('#topnavlist_sm').css('display', 'inline');
            $('#topnavlist-link').toggleClass('topnavlist-link-highlight').toggleClass('triangle-isosceles').toggleClass('top').toggleClass('topnavlist-link-highlight-background');
            return false;
        });
    
        // makes the subnavlist expand for more options
        $('.more-button').toggle(function (e) {
            $('.subnavlist-more').show();
            $('#switch').attr('src', 'images/hidemenu.gif').attr('title', 'less menu items').attr('alt', 'less menu items');
        }, function () {
            $('.subnavlist-more').hide(); 
            $('#switch').attr('src', 'images/showmenu.gif').attr('title', 'more menu items').attr('alt', 'more menu items');
        });
    
        // hide and show results on Browse Courses page
        $('.results-hide-show-link').click(function (e) {
            $(this).parent().next('.results-display').slideToggle(); 
            $(this).toggleClass('content-closed');
            
            return false;
        });
    
        // Hide the addressbar
        setTimeout(function () { window.scrollTo(0, 1); }, 100);

    }); // end of document.ready

})(jQuery);
