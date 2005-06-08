<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id: menu_pages.php 4799 2005-06-06 13:19:09Z heidi $

require('../common/body_header.inc.php'); ?>

<h2>4.2 Import/Export Content</h2>
	<p>ATutor provides importing and exporting of course content using IMS 1.1.3 or SCORM 1.2 content package specifications. Exported content can be viewed offline, and transferred to other systems. If enabled, students can also export content for offline viewing. See course <em>Properties</em> to learn how to enable content exporting for students.</p>

<h3>Exporting Content</h3>
	<p>An entire course, a chapter, or a single page of content can be exported as an IMS 1.1.3 or SCORM 1.2 content package. Exported packages are archived into a single file using ZIP compression. All content is exported including the terms and glossary, colours, and code.</p>
	
	<p>To export content, select the scope by choosing an option from the <em>What to export</em> menu. Then, clicking <kbd>Export</kbd> will generate a download through your browser. Optionally, you can choose to export the content directly to the TILE content repository if you have a TILE authoring account.</p>
	
<h3>Viewing Exported Content</h3>
	<p>To view a content package offline that has been exported from ATutor, you will need a IMS 1.1.3 or SCORM 1.2 viewer, or a web browser, and an application to unzip the package. To view the content in a web browser, first extract the contents of the ZIP file into an empty folder on your computer, and then open the file <kbd>index.html</kbd> in your browser.</p>

<h3>Importing Content</h3>
	<p>To import a content package into ATutor, it must conform to IMS 1.1.3 or SCORM 1.2 content package specifications. </p>

	<p>Before importing, specify where in the course structure the new content is to be placed by using the <em>Import into</em> menu.</p>
	
	<p>Select the content package to upload by choosing the file from your local file system, either typing in the path into the <em>Upload a Content Package</em> text field, or by using the <kbd>Browse</kbd> button. You can also import a content package over the Web by providing entering the URL of the package.</p>

	<p>Clicking <kbd>Import</kbd> will upload the content into the course and at the  location specified.</p>
	
<?php require('../common/body_footer.inc.php'); ?>
