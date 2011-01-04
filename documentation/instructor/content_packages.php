<?php require('../common/body_header.inc.php'); $lm = '$LastChangedDate: 2009-11-16 11:31:05 -0500 (Mon, 16 Nov 2009) $'; ?>

<h2>Import/Export Content</h2>

	<p>ATutor provides importing and exporting of course content as IMS Content Packages, or as IMS Common Cartridges.</p>
	<p> Exported content packages can be viewed offline, and transferred to other systems that will import IMS conformant content. If enabled, students can also export content for offline viewing. See course <a href="properties.php">Properties</a> to learn how to enable content exporting for students.</p>

<h3>Exporting Content</h3>
	<p>An entire course, a chapter, or a single page of content can be exported as an <strong>IMS Content Package</strong>. Exported packages are archived into a single ZIP file. </p>

	<p>Similarly, an entire course, a chapter, or a page can be exported as an <strong>IMS Common Cartridge</strong>. Cartridges can include content, tests, and activity tools (forum discussions currently) as a single unit of content.</p>
	
	<p>To export content, select the scope by choosing an option from the <em>What to export</em> menu. Select the checkbox to <strong>export AccessForAll adapted content</strong> as an IMS Access4All integrated content package or common cartridge,  if adaptations exist for the content being exported. Then, using <kbd>Export</kbd> will generate a downloadable ZIP file through your browser.</p>
	
<h3>Viewing Exported Content Packages</h3>
	<p>To view a content package offline that has been exported from ATutor, you will need an IMS or SCORM 1.2 viewer, or a web browser, and an application to unzip the package. To view the content in a web browser, first extract the contents of the ZIP file into an empty folder on your computer, and then open the file <kbd>index.html</kbd> in your browser. Note that tests and adapted content are not currently viewable with the content package viewer, nor is content in a common cartridge.</p>

<h3>Importing Content</h3>
	<p>To import content into ATutor, it must conform to IMS or SCORM 1.2 content package specifications, or to IMS Common Cartridge 1.0 specifications.  </p>

	<p>Before importing, specify where in the course structure the new content is to be placed by using the <em>Import into</em> menu.</p>
	
	<p>Select the content to upload by choosing the ZIP file from your local file system, either by typing the path into the <em>Upload a Content Package or Common Cartridge</em> text field, or by using the <kbd>Browse</kbd> button. You can also import a cartridge or  package over the Web by entering a URL.</p>

	<p>Select the checkboxes to <strong>Import available Tests</strong>, or to <strong>Import available AccessForAll content</strong>, if they are included with the package being imported. QTI test packages should be imported through <a href="tests_surveys.php">Tests &amp; Surveys</a> if they are not part of a content package.</p>

	<p>Using <kbd>Import</kbd> will upload the zipped content into the course, and unpack it into the specified location in the course.</p>
	
<?php require('../common/body_footer.inc.php'); ?>