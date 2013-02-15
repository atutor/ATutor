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
            logoutUrl = options.logoutUrl,
            message = options.message,
            title = options.title,
            cookieTimeoutName = options.cookieTimeoutName,
            button_1 = options.button_1,
            button_2 = options.button_2,
            autoLogout = ATutor.autoLogout,
            buttonOptions = {};
        
        if (warningBeforeLogoutTime >= logoutTime) {
            return;
        }
        
        warningBeforeLogoutTime = logoutTime - warningBeforeLogoutTime;
        
        ATutor.autoLogout.warningBeforeLogoutTime = warningBeforeLogoutTime;
        ATutor.autoLogout.logoutTime = logoutTime;
        ATutor.autoLogout.logoutUrl = logoutUrl;
        ATutor.autoLogout.cookieTimeoutName = cookieTimeoutName;
        
        buttonOptions[button_1] = function() {
            window.location = autoLogout.logoutUrl;
        };
        buttonOptions[button_2] = function() {
            $(this).dialog("close");
            autoLogout.startLogoutProcess();
        };
        
        $("body").append("<div title='"+ title +"' id='sessionTimeout-dialog'>"+ message +"</div>");
        ATutor.autoLogout.sessionTimeoutDialog = $("#sessionTimeout-dialog").dialog({
            autoOpen: false,
            width: 400,
            modal: true,
            closeOnEscape: false,
            open: function() { $(".ui-dialog-titlebar-close").hide(); },
            buttons: buttonOptions
        });
        
        ATutor.autoLogout.startLogoutProcess();
    };
    
    ATutor.autoLogout.startLogoutProcess = function () {
        var autoLogout = ATutor.autoLogout;
        
        // Clear all timers first if they are set
        clearTimeout(autoLogout.warningTimeout);
        clearTimeout(autoLogout.logoutTimeout);
        
        // Store the time stamp and also write into the cookie
       autoLogout.updateActiveTime();
        
        // Set the timeout for warning
        ATutor.autoLogout.warningTimeout = setTimeout(function () {
            autoLogout.logoutUpdate(function () {
                autoLogout.sessionTimeoutDialog.dialog("open");
            });
        }, autoLogout.warningBeforeLogoutTime);
        
        // Set the timeout for logout
        ATutor.autoLogout.logoutTimeout = setTimeout(function () {
            autoLogout.logoutUpdate(function () {
                window.location = autoLogout.logoutUrl;
            });
        }, autoLogout.logoutTime);
    };
    
    ATutor.autoLogout.logoutUpdate = function (callback) {
        var autoLogout = ATutor.autoLogout;
        if (autoLogout.checkIfActive(autoLogout.activityTime)) {
            autoLogout.startLogoutProcess();
        } else {
            callback();
        }
    };
    
    ATutor.autoLogout.updateActiveTime = function () {
        var autoLogout = ATutor.autoLogout,
            now = new Date();
        autoLogout.activityTime = now;
        $.cookie(autoLogout.cookieTimeoutName, now);
    };
    
    ATutor.autoLogout.checkIfActive = function (timeStamp) {
        var cookieActiveTimeStamp = new Date($.cookie(ATutor.autoLogout.cookieTimeoutName));
        return (timeStamp < cookieActiveTimeStamp);
    };
    
})();