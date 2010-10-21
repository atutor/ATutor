<?php require('../common/body_header.inc.php'); $lm = '$LastChangedDate$'; ?>

<h2>Creating Courses</h2>

<p>See <a href="../instructor/creating_courses.php">Creating Courses</a> documentation for Instructors.</p>

<p>In addition, administrators have access to the following properties:</p>

	<dl>
		<dt>Course Quota</dt>
		<dd>Defines the maximum size of a course.  That is, the amount of space each course's file manager can have.</dd>

		<dt>Max File Size</dt>
		<dd>Defines the maximum size allowed for a file being uploaded to a course's file manager.</dd>
	</dl>
<p>Note that Max File Size limitations can not be set higher than that allowed in the PHP settings for the system. The maximum allowable upload size can be increased by editing the values of <kbd>upload_max_filesize</kbd> and <kbd>post_max_size</kbd> in the system's <kbd>php.ini</kbd> configuration file.</p>
<?php require('../common/body_footer.inc.php'); ?>