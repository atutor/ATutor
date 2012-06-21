/************************************************************************/
/* ATutor                                                               */
/************************************************************************/
/* Copyright (c) 2012                                                   */
/* Inclusive Design Institute                                           */
/* http://atutor.ca                                                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id: $

/*global jQuery, ATutor */

ATutor = ATutor || {};
ATutor.mods = ATutor.mods || {};
ATutor.mods.themes = ATutor.mods.themes || {};

(function () {
    var themeRowProcess = function () {
        var id = this.id;
        var radioID = id.replace("AT_r_", "AT_t_");
        
        // Check the corresponding radio button
        jQuery("#" + radioID).attr("checked", "checked");
        
        // remove "selected" class from all other table rows
        jQuery(".AT_theme_row").removeClass("selected");
        
        // add "selected" class to current table row to highlight the selected row
        jQuery(this).addClass("selected");

        // Disable the delete button when the corresponding radio button is selected
        if (jQuery(this).hasClass("AT_disable_del")) {
            jQuery('#AT_del_btn').attr("disabled", "disabled");
        } else {
            jQuery('#AT_del_btn').removeAttr("disabled");
        }
    };
    
    var initialize = function () {
        jQuery(".AT_theme_row").click(themeRowProcess);
    };
    
    jQuery(document).ready(initialize);
})();