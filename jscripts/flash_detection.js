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

var ATutor = ATutor || {};

/**
 * flash detection
 */
(function() {
    //VB-Script for InternetExplorer
    var iExploreCheck = function ()
    {
        document.writeln("<scr" + "ipt language=\'VBscript\'>");
        //document.writeln("\'Test to see if VBScripting works");
        document.writeln("detectableWithVB = False");
        document.writeln("If ScriptEngineMajorVersion >= 2 then");
        document.writeln("   detectableWithVB = True");
        document.writeln("End If");
        //document.writeln("\'This will check for the plugin");
        document.writeln("Function detectActiveXControl(activeXControlName)");
        document.writeln("   on error resume next");
        document.writeln("   detectActiveXControl = False");
        document.writeln("   If detectableWithVB Then");
        document.writeln("      detectActiveXControl = IsObject(CreateObject(activeXControlName))");
        document.writeln("   End If");
        document.writeln("End Function");
        document.writeln("</scr" + "ipt>");
        return detectActiveXControl("ShockwaveFlash.ShockwaveFlash.1");
    }
    
    
    var plugin = (navigator.mimeTypes && navigator.mimeTypes["application/x-shockwave-flash"]) ? navigator.mimeTypes["application/x-shockwave-flash"].enabledPlugin : false;
    if(!(plugin) && (navigator.userAgent && navigator.userAgent.indexOf("MSIE")>=0 && (navigator.appVersion.indexOf("Win") != -1))) {
        if (iExploreCheck()) {
            ATutor.setcookie("flash", "yes", 100);
        } else {
            ATutor.setcookie("flash", "no", 100);
        }
    
    } else if(plugin) {
        ATutor.setcookie("flash", "yes", 100);
    } else {
        ATutor.setcookie("flash", "no", 100);
    }
})();
