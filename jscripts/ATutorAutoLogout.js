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
    
    /**
    * Function which is called once at the page start to set the times and start the session check process
    * @options
    *    timeLogout                 - Time in seconds when user will be logged out
    *    timeWarningBeforeLogout    - Time in seconds when warning dialog will be shown to the user before being logged out
    *    logoutUrl                  - URL where use will be bounced upon logout
    *    title                      - Title of the dialog
    *    textButtonLogout           - Text on Log Out button inside of the dialog
    *    textButtonStayConnected    - Text on the button which will make user stay connected inside of the dialog
    *    message                    - Message text inside of the dialog
    * @author    Alexey Novak
    */
    ATutor.autoLogout.pageNavigate = function (options) {
        options = options || {};
        options.cookieTimeoutName = "userActivity"; // Cookie name which will be used for tracking activity time
        var timeLogout = options.timeLogout,
            timeWarningBeforeLogout = options.timeWarningBeforeLogout,
            textButtonLogout = options.textButtonLogout,
            textButtonStayConnected = options.textButtonStayConnected,
            buttonOptions = {};
        
        // If times are invalid then just stop right there
        if (timeWarningBeforeLogout >= timeLogout) {
            return;
        }
        // Calculate time for the warning timer since user passes how many seconds before logout user should see the message popup
        options.timeWarningBeforeLogout = timeLogout - timeWarningBeforeLogout;
        
        // Set starting options
        ATutor.autoLogout = $.extend(ATutor.autoLogout, options);
        
        // Buttons for the sessionTimeout dialog
        buttonOptions[textButtonLogout] = function() {
            window.location = autoLogout.logoutUrl;
        };
        buttonOptions[textButtonStayConnected] = function() {
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
    
    /**
    * Function which will write the activity time into cookie and will update our JS activity variable
    * @author    Alexey Novak
    */
    ATutor.autoLogout.writeCookieTime = function () {
        var autoLogout = ATutor.autoLogout,
            now = new Date();
        // Update our JS activity variable
        ATutor.autoLogout.activityTime = now;
        // Write new timestamp into a cookie
        $.cookie(autoLogout.cookieTimeoutName, now.toString());
    };
    
    /**
    * Function which will start timeouts
    * @author    Alexey Novak
    */
    ATutor.autoLogout.startLogoutProcess = function () {
        var autoLogout = ATutor.autoLogout,
            warningCallback = function () {
                // open a warning dialog
                autoLogout.sessionTimeoutDialog.dialog("open");
            },
            logoutCallback = function () {
                // Logout user
                window.location = autoLogout.logoutUrl;
            };
        
        // Clear all timers first if they are set
        clearTimeout(autoLogout.warningTimeout);
        clearTimeout(autoLogout.logoutTimeout);
        
        // Set the timeout for warning
        ATutor.autoLogout.warningTimeout = setTimeout(function () {
            autoLogout.activityCheck(warningCallback);
        }, autoLogout.timeWarningBeforeLogout);
        
        // Set the timeout for logout
        ATutor.autoLogout.logoutTimeout = setTimeout(function () {
            autoLogout.activityCheck(logoutCallback);
        }, autoLogout.timeLogout);
    };
    
    /**
    * Function which will check if user is active and either execute a callback or start the session logout process all over again
    * @param    a function which is called if user is not active after the check
    * @author    Alexey Novak
    */
    ATutor.autoLogout.activityCheck = function (callback) {
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
    
    /**
    * Function which returns true or false depending if user is active. The check is accomplished by comparing JS activity variable with the cookie one.
    * @param    timeStamp which will be stored into a cookie
    * @return   true if user is active and cookie time is newer. false if user is still inactive and cookie time is old or the same
    * @author    Alexey Novak
    */
    ATutor.autoLogout.checkIfActive = function (timeStamp) {
        var autoLogout = ATutor.autoLogout,
            cookieActiveTimeStamp = new Date($.cookie(autoLogout.cookieTimeoutName));
        if (timeStamp < cookieActiveTimeStamp) {
            // Overwrite our JS activity variable with the one which is in a cookie
            ATutor.autoLogout.activityTime = cookieActiveTimeStamp;
            return true;
        }
        return false;
    };
    
})();