<?php require('../common/body_header.inc.php'); ?>

<h2>3. Users</h2>
	<p>The Users section allows the managing of students, instructors, and administrators. Please note that administrators are not considered real users of the system as they cannot create or view courses. For the purposes of documentation the term "users" will be reserved for any account type that is <em>not</em> an administrator.</p>

	<p>There are four types of user accounts that can exist in an ATutor installation, as defined by their Status:</p>
	<dl>
		<dt>Disabled</dt>
		<dd>Only administrators may disable an account. Disabled accounts cannot sign-in to your ATutor installation, but may still appear as enrolled in courses.</dd>

		<dt>Unconfirmed</dt>
		<dd>Unconfirmed accounts are created only when the <a href="2.2.system_preferences.php">System Preferences</a> <em>Require Email Confirmation Upon Registration</em> option is enabled.</dd>

		<dt>Student</dt>
		<dd>A regular account which can enroll, but not create courses.</dd>

		<dt>Instructor</dt>
		<dd>A regular account which can enroll as well as create courses.</dd>
	</dl>

<?php require('../common/body_footer.inc.php'); ?>