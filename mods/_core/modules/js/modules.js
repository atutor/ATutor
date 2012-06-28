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
ATutor.mods.modules = ATutor.mods.modules || {};

(function () {
    var disableUninstallBtn = function (currentRow) {
        // Disable the delete button when the corresponding radio button is selected
        if (jQuery(currentRow).hasClass("AT_disable_uninstall")) {
            jQuery('#AT_uninstall_btn').attr("disabled", "disabled");
        } else {
            jQuery('#AT_uninstall_btn').removeAttr("disabled");
        }
    }

    jQuery(document).ready(function () {
        jQuery(".AT_module_row").click({callback: disableUninstallBtn}, ATutor.highlightTableRow);
    });
})();