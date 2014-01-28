/**
 * @author Alexey Novak
 * @copyright Copyright © 2013, ATutor, All rights reserved.
 */

var ATutor = ATutor || {};


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
    ATutor.autoLogout = ATutor.autoLogout || function (options) {
        options = options || {};
        
        var timeLogout = options.timeLogout,
            timeWarningBeforeLogout = options.timeWarningBeforeLogout,
            logoutUrl = options.logoutUrl,
            textButtonLogout = options.textButtonLogout,
            textButtonStayConnected = options.textButtonStayConnected,
            cookieTimeoutName = "userActivity", // Cookie name which will be used for tracking activity time
            buttonOptions = {},
            activityTime, sessionTimeoutDialog, warningTimeout, logoutTimeout;
        
        // If times are invalid then just stop right there
        if (timeWarningBeforeLogout >= timeLogout) {
            return;
        }
        // Calculate time for the warning timer since user passes how many seconds before logout user should see the message popup
        timeWarningBeforeLogout = timeLogout - timeWarningBeforeLogout;
        
        /**
        * Function which ends the session and logs out a user
        * @author    Alexey Novak
        */
        var sessionLogout = function () {
            // Logout user
            window.location = logoutUrl;
        };
        
        // Buttons for the sessionTimeout dialog
        buttonOptions[textButtonStayConnected] = function() {
            $(this).dialog("close");
            writeCookieTime();
            startCountdown();
        };
        buttonOptions[textButtonLogout] = sessionLogout;
        
        // Create dialog for the page
        $("body").append("<div title='"+ options.title +"' id='sessionTimeout-dialog'>"+ options.message +"</div>");
        sessionTimeoutDialog = $("#sessionTimeout-dialog").dialog({
            autoOpen: false,
            width: 400,
            modal: true,
            closeOnEscape: false,
            open: function() { $(".ui-dialog-titlebar-close").hide(); },
            buttons: buttonOptions
        });
        
        /**
        * Function which will write the activity time into cookie and will update our JS activity variable
        * @author    Alexey Novak
        */
        var writeCookieTime = function () {
            var now = new Date();
            // Update our JS activity variable
            activityTime = now;
            // Write new timestamp into a cookie
            $.cookie(cookieTimeoutName, now.toString());
        };
        
        /**
        * Function which will start timeouts
        * @author    Alexey Novak
        */
        var startCountdown = function () {
            var warningCallback = function () {
                // open a warning dialog
                sessionTimeoutDialog.dialog("open");
            };
            
            // Clear all timers first if they are set
            clearTimeout(warningTimeout);
            clearTimeout(logoutTimeout);
            
            // Set the timeout for warning
            warningTimeout = setTimeout(function () {
                activityCheck(warningCallback);
            }, timeWarningBeforeLogout);
            
            // Set the timeout for logout
            logoutTimeout = setTimeout(function () {
                activityCheck(sessionLogout);
            }, timeLogout);
        };
        
        /**
        * Function which will check if user is active and either execute a callback or start the session logout process all over again
        * @param    a function which is called if user is not active after the check
        * @author    Alexey Novak
        */
        var activityCheck = function (callback) {
            if (checkIfActive(activityTime)) {
                // Close the warning dialog and start the session logout process again
                // NOTE: We do not want to update cookie or JS activity time here.
                // Checking and seeing that user is active does NOT imply that he/she created an action by doing so.
                startCountdown();
                sessionTimeoutDialog.dialog("close");
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
        var checkIfActive = function (timeStamp) {
            var cookieActiveTimeStamp = new Date($.cookie(cookieTimeoutName));
            if (timeStamp < cookieActiveTimeStamp) {
                // Overwrite our JS activity variable with the one which is in a cookie
                activityTime = cookieActiveTimeStamp;
                return true;
            }
            return false;
        };
        
        // Since moving to a page means that user is active then update user activity
        writeCookieTime();
        // And start the logging out process
        startCountdown();
    };
    
})();