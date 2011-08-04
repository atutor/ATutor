Infusion Uploader Read Me

1) Progressive Enhancement
2) Upgrading
3) Known Issues
4) Troubleshooting
5) Running the Uploader with out a Server

--------------------------------------

PROGRESSIVE ENHANCEMENT:

As of Infusion 1.3, the Uploader automatically delivers the best version of the component 
possible, based on the features supported by a user's browser. There are three flavours of the 
Uploader:

    1. Single file: delivered to browsers that don't support JavaScript, Flash or HTML 5
    2. Flash with SWFUpload: delivered to older browsers with JavaScript enabled (especially IE)
    3. HTML 5: the best and most widely-supported version of Uploader, suitable for modern browsers
    
If you don't want to offer a particular version of Uploader to your users, you can simply omit the 
appropriate Support.js file from your page. So, for example, if you don't want to deliver Flash to 
your users, simply don't include FlashUploaderSupport.js and Flash9UploaderSupport.js in your page. 
To do this, you will have to include all the required files individually, instead of using a single 
concatenated file.

--------------------------------------

UPGRADING from previous versions:

Before upgrading from Infusion 1.2 or earlier, please refer to the Uploader API documentation and the 
latest example code. The Fluid Uploader was extensively refactored in the 1.3 release with the 
introduction of HTML 5 support. The API is expected to fully stabilize to a production level after 
Infusion 1.4.

Uploader includes automatic backwards compatibility for Infusion 1.2-era options, which can be enabled
simply by including the following files in your page:
    ModelTransformations.js
    UploaderCompatibility-Infusion1.2.js

--------------------------------------

KNOWN ISSUES: 

Uploader and HTML 5:

* Uploading more than one file at a time without Flash requires a reasonably up-to-date browser with
  support for the following open web technologies, referred to under the umbrella of "HTML 5":
    - Multiple file form elements
    - XmlHTTPRequest Level 2
    - FormData
    - File API

* The following browsers will support the HTML 5 version of the Uploader:
    - Firefox 3.6 or higher
    - Safari 4 or higher
    - Google Chrome

  All others will automatically receive the Flash or single file versions of the Uploader depending 
  on browser capabilities.
    
* Firefox 3.6 has only partial support for HTML 5 uploads, and will load the entire file into browser
  memory. This causes a risk of crashing with very large files on computers with less memory. As a 
  result, we've included an additional option called "legacyBrowserFileLimit," allowing file sizes to 
  be specially capped in Firefox 3.6. The default value for this option is 100 MB.

* The Uploader's HTML 5 implementation doesn't currently support filtering based on file types.
  
* The HTML 5 implementation is inconsistent with the Flash version when handling the 
  queueSettings.fileSize limit option, causing it to be interpreted as MB instead of KB.

Uploader and Flash:

* As of Infusion 1.3, the Flash version of the Uploader is only delivered to users with older 
  browsers that lack HTML 5 support, such as Firefox 3.5 and below and Internet Explorer. Due to 
  ongoing accessibility and stability issues with Flash and SWFUpload, we encourage you and your 
  users to upgrade to an HTML 5-compatible browser such as Firefox 3.6+, Safari 4+, or Chrome.

* The Flash 10-based implementation of the Uploader has a number of systemic bugs an accessibility 
issues, and is deprecated for all browsers except Internet Explorer on Windows. Some issues include:
  
      - In Firefox and IE, the Flash-based "Browse" button does not size correctly when the text/page 
      is resized or zoomed.

      - In most browsers, the Flash-based "Browse" button is not  keyboard navigable and may trap
      keyboard navigation, refusing to give up focus without a mouse click. 
      
      - When the user presses the Stop button, the Uploader waits until the current file is complete
      before stopping the upload process.

--------------------------------------

TROUBLESHOOTING UPLOADER AND FLASH:

* When running the Flash version of the Uploader on a local system without a server, you may need to 
  modify some of your Flash settings to allow the local SWFUpload object to access your file system. 
  To do so, follow these directions:

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
  are the minimum settings and therefore pose the least security risk.

  These settings are global and do not need to repeated for every browser on a given system.

* If you see this error in your console: 
     [Exception... "'Invalid function name' when calling method: [nsIDOMEventListener::handleEvent]" 
     nsresult: "0x8057001e (NS_ERROR_XPC_JS_THREW_STRING)" location: "<unknown>" data: no]

  the flashUrl option is probably wrong. Check that first. 


--------------------------------------

RUNNING THE UPLOADER ON A LOCAL SYSTEM WITHOUT A SERVER

Running the Uploader locally without a server is intended for basic testing purposes only. The 
DemoRemote object provides a simulated conversation with the server, but it doesn't represent an
accurate picture of the component's behaviour when used in a real deployment environment.

To see the Uploader in action with a real server, have a look at Fluid's Image Gallery demo:

http://build.fluidproject.org:8080/sakai-imagegallery2-web/site/AddImages/



