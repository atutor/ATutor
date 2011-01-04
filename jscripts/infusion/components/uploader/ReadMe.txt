Infusion Uploader Read Me

1) Upgrading
2) Known Issues
3) Troubleshooting
4) Running the Uploader with out a Server

--------------------------------------

UPGRADING from previous versions:

Before upgrading from Infusion 0.5 or earlier, please refer to the Uploader API documentation and the 
latest example code. The Fluid Uploader was extensively refactored in the 0.6 release, and a fresh new 
API has been introduced.

Please refer to the Uploader API documentation on the Fluid Wiki and the 
Infusion 1.0 example code before using the Uploader with an existing integration. 
http://wiki.fluidproject.org/display/fluid/Uploader+API

--------------------------------------

KNOWN ISSUES: 

* To support Flash 10 (released on 9/26/2008), the Uploader required a new version of the SWFUpload 
  Flash component (2.2.0 beta 3). This new version, still in beta, still has numerous bugs. We have 
  worked around many of the bugs and inconsistencies in the SWFUpload code, but there are still 
  significant compromises and issues in this release. For this reason we do not consider this version 
  of the Uploader to be production-ready. 

  In the previous version of the Uploader, the Flash component worked completely "behind the scenes". 
  To support Flash 10, the Uploader displays a Flash-based "Browse files..." button in place of a 
  HTML button. The Flash-based button presents the following quirks:
  
      In Firefox and IE, the Flash-based Browse button does not size correctly when the text/page 
      is resized or zoomed.

      In Firefox (FF):
      - The AIRA role for the Browse button is read correctly as "button" but the text of the button, 
        "Browse Files", is ignored.
      - The Flash-based Browse button traps keyboard navigation, refusing to give up focus without a 
        mouse click. 
      
      In Internet Explorer (IE):
      - AIRA is not supported by Internet Explorer.

    We are exploring work-arounds for most of these issues, and will have a patch out as soon 
    as possible to fix them.

* In previous versions of the Uploader the upload process would stop immediately at the moment that 
  the Stop Upload button was clicked.
   
  With Infusion 0.8, we wait for the current file to complete or to error before we stop the upload 
  process. This avoids a serious bug in the SWFUploader where the Upload process could get stuck when 
  the Upload process as resumed.


--------------------------------------

TROUBLE SHOOTING:

* When running the Uploader sample code on a local system without a server, check to make 
  sure that you have followed the instructions below under "RUNNING THE UPLOADER ON A 
  LOCAL SYSTEM WITHOUT A SERVER". 

* If you see this error in your console: 
  [Exception... "'Invalid function name' when calling method: [nsIDOMEventListener::handleEvent]" 
  nsresult: "0x8057001e (NS_ERROR_XPC_JS_THREW_STRING)" location: "<unknown>" data: no]

  The flashUrl option is probably wrong. Check that first. 


--------------------------------------

RUNNING THE UPLOADER ON A LOCAL SYSTEM WITHOUT A SERVER

Running the Uploader locally without a server is intended for basic testing purposes only. The 
DemoUploadManager provides a simulated conversation with the server, but it doesn't represent a
fully accurate picture of the component's behaviour when used in a real deployment environment.

So see the Uploader in action with a real server, have a look at Fluid's Image Gallery demo:

http://build.fluidproject.org:8080/sakai-imagegallery2-web/site/AddImages/


Additionally, you may need to modify some of your Flash settings to allow the local SWFUpload 
object to access your file system. To do so, follow these directions:

1. Open your browser
2. Browse to:
   http://www.macromedia.com/support/documentation/en/flashplayer/help/settings_manager04.html
3. In the Flash Settings panel, click "Edit locations..."
4. Select "Add location..."
5. Click "Browse for folder..."
6. Select the local /src/webapp/lib/swfupload/flash/ directory that contains the swfupload.swf file
7. Restart your browser

You should be good to go! 

However, if you move your installation, you'll need to do this all over again. There are settings 
that will allow the file to be run from any location on your local machine but these instructions 
are the minimum settings and therefor pose the least security risk.

These settings are global and do not need to repeated for every browser on a given system. 
