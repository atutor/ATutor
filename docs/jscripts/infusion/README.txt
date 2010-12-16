Fluid Infusion 1.1
==================
Main Project Site:  http://fluidproject.org
Documentation:      http://wiki.fluidproject.org/display/fluid/Infusion+Documentation

What's New in 1.1
=================

This release:

    * Updates our supported browsers to include the latest from Yahoo's A-grade browser list
      * see http://wiki.fluidproject.org/display/fluid/Browser+Support
    * Provides the ability to create custom builds:
      * see http://wiki.fluidproject.org/display/fluid/Custom+Build
    * Adds jQuery UI Themes for working with FSS themes
    * Adds new and powerful decorators for the Renderer:
      * The "fluid" decorator instantiates any Fluid Infusion component bound to the markup
      * New support for removing arbitrary attributes and CSS classes
    * Updates the Pager:
      * Improved handing of column sorting
      * Fixed persistence of focus
    * Updates the Uploader:
      * User can manually switch to the standard non-Flash http file uploader
      * Uploader Browse button now respects DOM z-index in Flash 10
    * Updates the User Interface Options:
      * Better cross browser support
      * Better keyboard and screen reader accessibility
    * Changes some class names in the FSS and components:
	  * see http://wiki.fluidproject.org/display/fluid/Upgrading+to+Infusion+1.1
	* Changes some Framework API:
	  * see http://wiki.fluidproject.org/display/fluid/Upgrading+to+Infusion+1.1
    * Fixes many bugs

What's in this Release
======================

This release is available in two forms:
    Deployment Bundle - infusion-1.1.zip 
    Source Code Bundle - infusion-1.1-src.zip

In addition to source code, samples and tests, both bundles include at the top level a single JavaScript file

    InfusionAll.js

that is a combination of all other source files. Developers can include this single file in their
pages to provide all the necessary support for the Infusion component Library. In the Deployment Bundle,
this script is compressed and suitable for production use.

The Deployment Bundle also includes a WAR file suitable for deployment in Java-based containers: 
        fluid-components-1.1.war

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

Examples and Sample Code
------------------------
Sample code illustrating how Infusion components can be used:

        integration-demos/
             bspace/    (showcases: Inline Edit)
             sakai/     (showcases: Inline Edit, Pager, UI Options, FSS)
             uportal/   (showcases: Reorderer, UI Options, FSS)
        standalone-demos/
             keyboard-a11y/
             lib/
             pager/
             progress/
             quick-start-examples/
                  fss/
                  inlineEdit/
                  reorderer/
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

    * jQuery javascript library v1.3.2: http://jquery.com/ (MIT and GPL licensed http://docs.jquery.com/Licensing)
    * jQuery UI javascript widget library v1.7: http://ui.jquery.com/ (MIT and GPL licensed http://docs.jquery.com/Licensing)
    * jQuery QUnit testrunner r6173: http://docs.jquery.com/QUnit (MIT and GPL licensed http://docs.jquery.com/Licensing)
    * Douglas Crockford's JSON parsing and stringifying methods (from 2007-11-06): http://www.json.org/ (Public Domain)
    * SWFUpload v2.2.0.1: http://swfupload.org/ (MIT licensed http://www.opensource.org/licenses/mit-license.php)
    * SWFObject v2.1: http://code.google.com/p/swfobject/ (MIT licensed http://www.opensource.org/licenses/mit-license.php)
    * Sample markup and stylesheets from Sakai v2.5 (http://sakaiproject.org) and uPortal v2.6 (http://www.uportal.org/)
    * FCKeditor v2.6, HTML text editor (LGPL licensed http://www.fckeditor.net/license)
    
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

    http://wiki.fluidproject.org/display/fluid/Infusion+Documentation

The Fluid Project uses a wiki for documentation and project collaboration: http://wiki.fluidproject.org.

The documentation for Infusion consists of a number of information pages stored in the Fluid Wiki.
The pages include tutorials, API descriptions, testing procedures, and data-gathering approaches. To make the 
manual pages easy to navigate we have added the following guides:

    * A landing page is provided for the reader, with links to all of our documenation.
    * A link to the documentation appears at the top of the left-side wiki navigation
      bar with the name "Infusion Documentation".


Supported Browsers
==================
Firefox 2.x, 3.x: full support
Internet Explorer 6.x, 7.x: full support
Safari 3.1, Opera 9.6: full support (except keyboard interaction, which is not supported by these browsers)

Internet Explorer 8: preliminary support

For more information on Fluid Infusion browser support, please see:
    http://wiki.fluidproject.org/display/fluid/Browser+Support


Status of Components and Framework Features
===========================================

Production: supports A-Grade browsers, stable for production usage across a wide range of
applications and use cases
    * Fluid Skinning System 
    * Infusion Framework Core
    * Inline Edit: Simple Text
    * Reorderer: List, Grid, Layout, Image
    * Undo

Preview: still growing, but with broad browser support. Expect new features in upcoming releases
    * Pager
    * Progress
    * UI Options
    * Uploader
    * Renderer

Sneak Peek: in development; APIs will change. Share your feedback, ideas, and code
    * Inline Edit: Dropdown
    * Inline Edit: Rich Text
    * Table of Contents


Known Issues
============

The Fluid Project uses a JIRA website to track bugs: http://issues.fluidproject.org.
Some of the known issues in this release are described here:

FSS:
    FLUID-2504: Flexible columns don't maintain proper alignment under certain conditions
    FLUID-2434: In IE, major font size changes break text positioning within form controls
    FLUID-2397: Opera doesn't seem to repaint certain css changes on the fly, requiring a refresh to see them

Framework:
    FLUID-2577 Renderer performance can be slow on IE 6 and 7 in some contexts.

Inline Edit: 
    FLUID-1600 Pressing the "Tab" key to exit edit mode places focus on the wrong item
    FLUID-2536 Inline Edit test fails using IE 8
  
Uploader: 
    FLUID-2582 Uploader is dependent on ProgressiveEnhancement.js, which is not included in InfusionAll.js
    FLUID-2895 The browse files button displays as a white box in IE 7 when running off the local system in Flash 10
    FLUID-2052 Cannot tab away from the "Browse Files" button with Flash 10; using FF3*
    FLUID-2032 Cannot Tab to the 'Browse More" button with Flash 10, using FF2*
    * For information related to known issues with Flash 10 compatibility, 
      see http://wiki.fluidproject.org/x/kwZo

Layout Reorderer: 
    FLUID-1540 Can't use keyboard reordering to move a nested reorderer to the right column, using IE6
    FLUID-2171 In IE, can't reorderer portlets containing Google components
    FLUID-858  Portlet Columns load with no padding between them in IE7

Pager:
    FLUID-2880 The Pager will be refactored. Note that as a result of this, there will be significant changes to the Pager API
    FLUID-2329 The self-rendering mode of the Pager is not the default mode

Reorderer: 
    FLUID-539 Can't use the "Tab" key to navigate out of reorderable tabs
    FLUID-148 Edge case: visual position of drop target when droppable is at beginning or end of a row
    FLUID-118 Dragging an image offscreen or out of the frame has some unexpected results.

UI Options: 
    FLUID-2398 Minimum font size control changes the text size even when the base size is larger then the minimum.
    FLUID-2481 "Links" selection does not work correctly in UIOptions
    FLUID-2506 Keyboard navigation inside the dialog breaks in simple layout mode: using FF
    FLUID-2524 scrolling the screen while the UI Options dialog is open, will cause it's contents to appear distorted: using IE
    
