<?php require('../common/body_header.inc.php'); ?>

<h2>Creating Courses</h2>

<p>After logging in, use the <em>Create Course</em> link from My Start Page.</p>

<p>Some course properties include:</p>

	<dl>
		<dt>Description</dt>
		<dd>Enter a meaningful but brief paragraph describing the course.  This will be displayed under the course name in <em>Browse Courses</em> as well as on the My Start Page for those enrolled.</dd>

		<dt>Export Content</dt>
		<dd>Choose the availability of the "Export Content" link on course content pages.</dd>

		<dt>Syndicate Announcements</dt>
		<dd>Enable this setting if you wish to make an RSS feed of the course announcements available for display on another website.</dd>

		<dt>Access</dt>
		<dd>Determines who can have access to the course content - any user, only logged in users, or logged in and enrolled users.</dd>

		<dt>Release Date</dt>
		<dd>The date when the course can be accessed by students. Or, available immediately</dd>

		<dt>Banner</dt>
		<dd>HTML that form a custom banner or splash page for the coruse home page. Apears above course announcements if there are any.</dd>

		<dt>Initial Content</dt>
		<dd>Initialise the course content to be either empty, basic place-holder content, or a restored backup from other courses you teach.</dd>
	</dl>

<p>Enter the necessary information and use the <code>Save</code> button to proceed into the newly created course. Properties set here can be modified through Manage > <a href="properties.php">Properties</a></p>


<?php require('../common/body_footer.inc.php'); ?>