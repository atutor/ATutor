<?php require('../common/body_header.inc.php'); $lm = '$LastChangedDate$'; ?>

<h2>Creating Courses</h2>

<p>After logging in, use the <em>Create Course</em> link from My Start Page. Properties set here can be modified through Manage > <a href="properties.php">Properties</a></p>

<p>Some course properties include:</p>

	<dl>
		<dt>Description</dt>
		<dd>Enter a meaningful but brief paragraph describing the course, to be displayed under the course name in <em>Browse Courses</em>.</dd>

		<dt>Export Content</dt>
		<dd>Choose the availability of the "Export Content" link on course content pages.</dd>

		<dt>Syndicate Announcements</dt>
		<dd>Enable this setting if you wish to make an RSS feed of the course announcements available for display on another website.</dd>

		<dt>Access</dt>
		<dd>Determines who can have access to the course content - any user, only logged in users, or logged in and enrolled users.</dd>

		<dt>Release Date</dt>
		<dd>An optional date from when the course can be accessed by non-privileged students.</dd>

		<dt>End Date</dt>
		<dd>An optional date from when the course can no longer be accessed by non-privileged students.</dd>

		<dt>Banner</dt>
		<dd>HTML that forms a custom banner or splash screen for the course home page. Appears above the course announcements, if there are any.</dd>

		<dt>Initial Content</dt>
		<dd>Initialise the course content to be either empty, basic place-holder content, or a restored backup from other courses you own.</dd>
	</dl>

<p>Enter the necessary information and use the <code>Save</code> button to proceed into the newly created course. </p>


<?php require('../common/body_footer.inc.php'); ?>