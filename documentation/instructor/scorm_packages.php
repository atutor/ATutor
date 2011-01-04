<?php require('../common/body_header.inc.php'); $lm = '$LastChangedDate: 2008-11-05 12:48:49 -0500 (Wed, 05 Nov 2008) $'; ?>

<h2>SCORM Packages</h2>
	<p>The Packages tool, when enabled, allows instructors to include SCORM 1.2 Sharable Content Objects (SCOs) as part of their courses. SCOs remain separated from the course content as complete learning units. SCOs should not be confused with content packages which are loaded into ATutor using the Import/Export tool in the Content Manager.</p>
	<p><strong>Note:</strong> The ATutor SCORM Run-Time Environment (RTE) that plays SCOs requires users to have Java 1.5 (i.e. JRE 1.5) installed on their computer.</p>

	<p>Use the <em>Packages</em> link from the Manage area to access the following:</p>

	<dl>
		<dt>Import Package</dt>
		<dd><p>Upload a SCO from your computer, or enter the URL to a SCO located on the Web to import it into your course.</p></dd>

		<dt>Delete Package</dt>
		<dd><p>Removes a SCO from a course, and deletes all associated files.</p></dd>

		<dt>Package Setting (DISABLED IN THIS VERSION OF ATUTOR)</dt>
		<dd>
   			<p><code>Credit Mode</code> sets the package to credit or no credit.</p>

			<p><code>Lesson Mode</code> is set to <code>browse</code> if the package is to be available for evaluation, or set to <code>normal</code> as a lesson..</p>
		</dd>
	</dl>

<?php require('../common/body_footer.inc.php'); ?>