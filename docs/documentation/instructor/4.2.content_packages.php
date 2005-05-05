<?php require('../common/body_header.inc.php'); ?>

<h2>2.2 Content Packages</h2>
	<p>ATutor provides importing and exporting course content using IMS 1.1.3/SCORM 1.2 content package specification. This allows ATutor content to be viewed offline, and transferred to other systems.</p>

	<a name="2.2.1"></a><h3>2.2.1 Exporting Content</h3>
	<p>An entire course or a single chapter can be exported as an IMS 1.1.3/SCORM 1.2 content package. Exported packages are archived into a single file using ZIP compression. All content is exported including the terms and glossary, colours, and code.</p>
	
	<p>To export content, select the scope by choosing an option from the <em>What to export</em> menu. Then, clicking <kbd>Export</kbd> will generate a download through your browser. Optionally, you can choose to export the content directly to TILE if you have a TILE authoring account.</p>
	
	<a name="2.2.2"></a><h3>2.2.2 Viewing Exported Content</h3>
	<p>To view a content package exported from ATutor, you will either need a IMS 1.1.3/SCORM 1.2 viewer, or a web browser. To view the content in a web browser, first extract the contents of the ZIP file and then open the file <kbd>index.html</kbd> in the browser.</p>

	<a name="2.2.3"></a><h3>2.2.3 Importing Content</h3>
	<p>To import a content package into ATutor, it must conform to IMS 1.1.3/SCORM 1.2 content package specifications.</p>

	<p>Before importing, you must specify where in the course structure the new content is to be placed by using the <em>Import into</em> menu.</p>
	
	<p>Select the content package to upload by supplying the file from your local filesystem by typing in the path into the textfield or by using the <kbd>Browse</kbd> button. You can also import a content package over the Web by providing an URL.</p>

	<p>Clicking <kbd>Import</kbd> will upload the content into the course and at the hierarchy location specified.</p>
	
<?php require('../common/body_footer.inc.php'); ?>