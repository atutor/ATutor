<?php require('../common/body_header.inc.php'); ?>

<h2>Import/Export Content</h2>

	<p>ATutor provides importing and exporting of course content using IMS 1.1.3 or SCORM 1.2 content package specifications. Exported content can be viewed offline, and transferred to other systems. If enabled, students can also export content for offline viewing. See course <a href="properties.php">Properties</a> to learn how to enable content exporting for students.</p>

<h3>Exporting Content</h3>
	<p>An entire course, a chapter, or a single page of content can be exported as an IMS 1.1.3 or SCORM 1.2 content package. Exported packages are archived into a single file using ZIP compression. All content is exported including the terms and glossary, colours, and code.</p>
	
	<p>To export content, select the scope by choosing an option from the <em>What to export</em> menu. Then, using <kbd>Export</kbd> will generate a download through your browser. Optionally, you can choose to export the content directly to the <a href="tile_repository.php">TILE content repository</a> if you have a TILE authoring account.</p>
	
<h3>Viewing Exported Content</h3>
	<p>To view a content package offline that has been exported from ATutor, you will need a IMS 1.1.3 or SCORM 1.2 viewer, or a web browser, and an application to unzip the package. To view the content in a web browser, first extract the contents of the ZIP file into an empty folder on your computer, and then open the file <kbd>index.html</kbd> in your browser.</p>

<h3>Importing Content</h3>
	<p>To import a content package into ATutor, it must conform to IMS 1.1.3 or SCORM 1.2 content package specifications. </p>

	<p>Before importing, specify where in the course structure the new content is to be placed by using the <em>Import into</em> menu.</p>
	
	<p>Select the content package to upload by choosing the file from your local file system, either by typing the path into the <em>Upload a Content Package</em> text field, or by using the <kbd>Browse</kbd> button. You can also import a content package over the Web by entering the URL of the package.</p>

	<p>Using <kbd>Import</kbd> will upload the content into the course at the specified location on the Internet.</p>
	
<?php require('../common/body_footer.inc.php'); ?>