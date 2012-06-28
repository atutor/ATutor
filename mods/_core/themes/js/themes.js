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
    var disableDelBtn = function (currentRow) {
        // Disable the delete button when the corresponding radio button is selected
        if (jQuery(currentRow).hasClass("AT_disable_del")) {
            jQuery('#AT_del_btn').attr("disabled", "disabled");
        } else {
            jQuery('#AT_del_btn').removeAttr("disabled");
        }
    }

    jQuery(document).ready(function () {
        jQuery(".AT_theme_row").click({callback: disableDelBtn}, ATutor.highlightTableRow);
    });
})();