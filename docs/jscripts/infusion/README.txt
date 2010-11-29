Fluid Infusion 1.3
====================
Main Project Site:  http://fluidproject.org
Documentation:      http://wiki.fluidproject.org/display/fluid/Infusion+Documentation


What's New in 1.3
===================

This release:
    * Mobile FSS themes: stable, full-featured support for iPhone and Android
    * Support for jQuery 1.4.2 and jQuery UI 1.8
    * Rich Text InlineEdit now supports the new CKEditor 3 rich text editor
    * Smaller, faster, and more mobile-friendly package
        * The uncompressed distributions are now over 5 MB smaller
    * Bug fixes for Renderer, data binding, Inline Edit, and Reorderer
    * Improved internationalization for Undo
    * Better ARIA support for Inline Edit and Undo
    * Improved build scripts
    * Easier asynchronous unit testing
    * Ubiquitous UTF-8 support

More information on migrating from Infusion 1.1.x to Infusion 1.2 can be found on the "Upgrading to Infusion 1.2" page on the wiki.
http://wiki.fluidproject.org/display/fluid/Upgrading+to+Infusion+1.2


What's in this Release
======================

This release is available in two forms:
    Deployment Bundle - infusion-1.3.zip 
    Source Code Bundle - infusion-1.3-src.zip

In addition to source code, samples and tests, both bundles include at the top level a single JavaScript file

    InfusionAll.js

that is a combination of all other source files. Developers can include this single file in their
pages to provide all the necessary support for the Infusion component Library. In the Deployment Bundle,
this script is compressed and suitable for production use.

The Deployment Bundle also includes a WAR file suitable for deployment in Java-based containers: 
        fluid-components-1.3.war

Source Code
-----------
The organization of the full source code for the Infusion library is as follows:

        components/
             inlineEdit/
             pager/
             progress/
             reorderer/
             tableOfContents/
             uiOptions/
             undo/
             uploader/
        framework/
             core/
             fss/
             renderer/
        lib/
             fastXmlPull/
             jquery/
             json/
             swfobject/
             swfupload/

In the Deployment Bundle, the JavaScript source has been minified: comments and whitespace have
been removed. 

Developers wishing to learn about the Fluid Infusion code, or debug their applications, should use
the Source Code Bundle.

Demo Portal
-----------
The bundle now comes with a convenient one-stop-shop for seeing all components in action. It is organized as follows:

        demos/
            fss/
                layout/
                mobile/
                reset/
                text/
                themes/
            inlineEdit/
                rich/
                simple/
            keyboard-a11y/            
            pager/
            portal/                
            progress/
            renderer/
            reorderer/
                gridReorderer/
                imageReorderer/
                layoutReorderer/                
                listReorderer/
            uiOptions/
            uploader/

            
Other Examples and Sample Code
------------------------------
Sample code illustrating how Infusion components can be used:

        integration-demos/
             bspace/    (showcases: Inline Edit)
             sakai/     (showcases: Inline Edit, Pager, UI Options, FSS)
             uportal/   (showcases: Reorderer, UI Options, FSS)
        standalone-demos/
             keyboard-a11y/
             lib/
             pager/
             renderer/
             reorderer/
             table-of-contents/

Tests
-----
        tests/
            component-tests/
            escalated-tests/
            framework-tests/
            lib/
            manual-tests/
            test-core/

License
-------
Fluid Infusion code is licensed under a dual ECL 2.0 / BSD license. The specific licenses can be
found in the license file:
        licenses/Infusion-LICENSE.txt

Infusion also depends upon some third party open source modules. These are contained in their own
folders, and their licenses are also present in
        licenses/

Third Party Software in Infusion
--------------------------------
This is a list of publicly available software that is included in the Fluid Infusion bundle, along
with their licensing terms.

    * jQuery javascript library v1.4.2: http://jquery.com/ (MIT and GPL licensed http://docs.jquery.com/Licensing)
    * jQuery UI javascript widget library v1.8: http://ui.jquery.com/ (MIT and GPL licensed http://docs.jquery.com/Licensing)
    * jQuery UI tooltip plugin from the tooltip branch on github for jQuery UI v1.9: (MIT and GPL licensed http://docs.jquery.com/Licensing)
            https://github.com/jquery/jquery-ui/tree/tooltip
            commit  48a5977d3325869abd7b
            tree    43fd0cda4af2cdcd33f5
            parent  bdd815e8dcdeace8be6d 
    * jQuery QUnit revision 2dbf603: http://docs.jquery.com/QUnit (MIT and GPL licensed http://docs.jquery.com/Licensing)
    * jQuery Chili code highlighter http://code.google.com/p/jquery-chili-js/ (MIT licensed)
    * Douglas Crockford's JSON parsing and stringifying methods (from 2007-11-06): http://www.json.org/ (Public Domain)
    * SWFUpload v2.2.0.1: http://swfupload.org/ (MIT licensed http://www.opensource.org/licenses/mit-license.php)
    * SWFObject v2.2: http://code.google.com/p/swfobject/ (MIT licensed http://www.opensource.org/licenses/mit-license.php)
    * Sample markup and stylesheets from Sakai v2.5 (http://sakaiproject.org) and uPortal v2.6 (http://www.uportal.org/)
    
Other third party software

    * fastXmlPull is based on XML for Script's Fast Pull Parser v3.1
      (see: http://wiki.fluidproject.org/display/fluid/Licensing+for+fastXmlPull.js )
    * fluid.reset.css is based on YUI's CSS reset styling v2.5.2
      see: http://developer.yahoo.com/yui/reset/ (BSD licensed http://developer.yahoo.com/yui/license.html)
    
Readme
------
This file.
        README.txt


Documentation
=============

The Fluid Project uses a wiki for documentation and project collaboration: http://wiki.fluidproject.org.
The main Infusion documentation can be found at:

    http://wiki.fluidproject.org/display/fluid/Infusion+Documentation

The documentation for Infusion consists of a number of information pages stored in the Fluid Wiki.
The pages include tutorials, API descriptions, testing procedures, and data-gathering approaches. To make the 
manual pages easy to navigate we have added the following guides:

    * The above-mentioned landing page, which links to all of our documentation.
    * A link to the documentation appears at the top of the left-side wiki navigation
      bar with the name "Infusion Documentation".


Supported Browsers
==================
Chrome 4: full support in Win XP
Firefox 3.0: full support in Win XP
Firefox 3.6: full support in Mac OS 10.6, Win XP and Win 7
Internet Explorer 6.x: full support in Win XP
Internet Explorer 7.x: full support in Win XP
Internet Explorer 8.x: full support in Win XP and Win 7
Safari 4: full support in Mac OS 10.5 and 10.6

For more information on Fluid Infusion browser support, please see:
    http://wiki.fluidproject.org/display/fluid/Browser+Support


Status of Components and Framework Features
===========================================

Production: supports A-Grade browsers, stable for production usage across a wide range of
applications and use cases
    * Fluid Skinning System 
    * Infusion Framework Core
    * Inline Edit: Simple Text
    * Renderer
    * Reorderer: List, Grid, Layout, Image
    * Undo

Preview: still growing, but with broad browser support. Expect new features in upcoming releases
    * Pager
    * Progress
    * UI Options
    * Uploader

Sneak Peek: in development; APIs will change. Share your feedback, ideas, and code
    * Inline Edit: Dropdown
    * Inline Edit: Rich Text
    * Mobile Fluid Skinning System
    * Table of Contents


Known Issues
============

The Fluid Project uses a JIRA website to track bugs: http://issues.fluidproject.org.
Some of the known issues in this release are described here:

FSS:
    FLUID-2504: Flexible columns don't maintain proper alignment under certain conditions
    FLUID-2434: In IE, major font size changes break text positioning within form controls

Framework:
    FLUID-2577: Renderer performance can be slow on IE 6 and 7 in some contexts.

Inline Edit: 
    FLUID-3632: Chrome 4 in WIN XP does not allow tabbing out of tinyMCE editor's edit field
    FLUID-3611: Text persists in edit mode when view mode displays default text: using IE 6
    FLUID-2536: Inline Edit test fails using IE 8
    FLUID-1600: Pressing the "Tab" key to exit edit mode places focus on the wrong item

Layout Reorderer: 
    FLUID-3089: If columns become stacked, can't drag item into lower column
    FLUID-1540: Can't use keyboard reordering to move a nested reorderer to the right column, using IE6
    FLUID-858:  Portlet Columns load with no padding between them in IE7

Pager:
    FLUID-3584: Clicking page numbers throws an error: using IE 6
    FLUID-2880: The Pager will be refactored. Note that as a result of this, there will be significant changes to the Pager API
    FLUID-2329: The self-rendering mode of the Pager is not the default mode

Renderer: 
    FLUID-3493: Renderer appears to corrupt templates containing empty tags on Opera (maybe others)
    FLUID-3277: Attempt to add children to leaf component in tree results in "targetlump is undefined" error
    FLUID-3276: Enclosing branch nodes within markup which has "headers" attribute causes them to become invisible to the renderer

Reorderer: 
    FLUID-3288: Moving an item with the keyboard "loses" the "ctrl-key is down" status
    FLUID-118:  Dragging an image offscreen or out of the frame has some unexpected results.

UI Options: 
    FLUID-3621: The text in buttons does not change size.
    FLUID-2481: "Links" selection does not work correctly in UIOptions
    FLUID-2398: Minimum font size control changes the text size even when the base size is larger then the minimum.
    
Uploader: 
    FLUID-3241: Can only tab to the "Browse Files" button once: using IE
    FLUID-2052: Cannot tab away from the "Browse Files" button with Flash 10*
    * For information related to known issues with Flash 10 compatibility, 
      see http://wiki.fluidproject.org/x/kwZo
