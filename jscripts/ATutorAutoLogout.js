/**
 * @author Alexey Novak
 * @copyright Copyright © 2013, ATutor, All rights reserved.
 */

var ATutor = ATutor || {};
ATutor.autoLogout = ATutor.autoLogout || {};

/*global $*/
/*global ATutor */

(function() {
    "use strict";
    
    // function which is called once at the page start to set the times and start the session check process
    ATutor.autoLogout.pageNavigate = function (options) {
        options = options || {};
        var logoutTime = options.logoutTime,
            warningBeforeLogoutTime = options.warningBeforeLogoutTime,
            button_1 = options.button_1,
            button_2 = options.button_2,
            buttonOptions = {};
        
        // If times are invalid then just stop right there
        if (warningBeforeLogoutTime >= logoutTime) {
            return;
        }
        // Calculate time for the warning timer since user passes how many seconds before logout user should see the message popup
        options.warningBeforeLogoutTime = logoutTime - warningBeforeLogoutTime;
        
        // Set starting options
        ATutor.autoLogout = $.extend(ATutor.autoLogout, options);
        
        // Buttons for the sessionTimeout dialog
        buttonOptions[button_1] = function() {
            window.location = autoLogout.logoutUrl;
        };
        buttonOptions[button_2] = function() {
            $(this).dialog("close");
            autoLogout.writeCookieTime();
            autoLogout.startLogoutProcess();
        };
        
        // Create dialog for the page
        $("body").append("<div title='"+ options.title +"' id='sessionTimeout-dialog'>"+ options.message +"</div>");
        ATutor.autoLogout.sessionTimeoutDialog = $("#sessionTimeout-dialog").dialog({
            autoOpen: false,
            width: 400,
            modal: true,
            closeOnEscape: false,
            open: function() { $(".ui-dialog-titlebar-close").hide(); },
            buttons: buttonOptions
        });
        
        var autoLogout = ATutor.autoLogout;
        
        // Since moving to a page means that user is active then update user activity
        autoLogout.writeCookieTime();
        // And start the logging out process
        autoLogout.startLogoutProcess();
    };
    
    // Function which will write the activity time into cookie and will update our JS activity variable
    ATutor.autoLogout.writeCookieTime = function () {
        var autoLogout = ATutor.autoLogout,
            now = new Date();
        // Update our JS activity variable
        ATutor.autoLogout.activityTime = now;
        // Write new timestamp into a cookie
        $.cookie(autoLogout.cookieTimeoutName, now.toString());
    };
    
    // Function which will start timeouts
    ATutor.autoLogout.startLogoutProcess = function () {
        var autoLogout = ATutor.autoLogout;
        
        // Clear all timers first if they are set
        clearTimeout(autoLogout.warningTimeout);
        clearTimeout(autoLogout.logoutTimeout);
        
        // Set the timeout for warning
        ATutor.autoLogout.warningTimeout = setTimeout(function () {
            autoLogout.logoutUpdate(function () {
                // open a warning dialog
                autoLogout.sessionTimeoutDialog.dialog("open");
            });
        }, autoLogout.warningBeforeLogoutTime);
        
        // Set the timeout for logout
        ATutor.autoLogout.logoutTimeout = setTimeout(function () {
            autoLogout.logoutUpdate(function () {
                // Logout user
                window.location = autoLogout.logoutUrl;
            });
        }, autoLogout.logoutTime);
    };
    
    // Function which will check if user is active and either execute a callback or start the session logout process all over again
    ATutor.autoLogout.logoutUpdate = function (callback) {
        var autoLogout = ATutor.autoLogout;
        if (autoLogout.checkIfActive(autoLogout.activityTime)) {
            // Close the warning dialog and start the session logout process again
            // NOTE: We do not want to update cookie or JS activity time here.
            // Checking and seeing that user is active does NOT imply that he/she created an action by doing so.
            autoLogout.startLogoutProcess();
            autoLogout.sessionTimeoutDialog.dialog("close");
        } else {
            callback();
        }
    };
    
    // Function which returns true or false depending if user is active by comparing JS activity variable with the cookie one.
    // If user is active we update our JS activity variable.
    ATutor.autoLogout.checkIfActive = function (timeStamp) {
        var autoLogout = ATutor.autoLogout,
            cookieActiveTimeStamp = new Date($.cookie(autoLogout.cookieTimeoutName));
        if (timeStamp < cookieActiveTimeStamp) {
            ATutor.autoLogout.activityTime = cookieActiveTimeStamp;
            return true;
        }
        return false;
    };
    
})();