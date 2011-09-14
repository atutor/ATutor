<?php require('../common/body_header.inc.php'); $lm = '$LastChangedDate$'; ?>

<h2>Users</h2>

	<p>The Users section allows managment of students, instructors, and administrators. Note that administrators are not 
considered regular users of the system; an administrator account can not normally be used to login to a course. They can however login temporarily as the course instructor, using the View button in the administrator's Courses listing. For the 
purposes of documentation the term "users" will be reserved for any account type that is <em>not</em> an administrator.</p>	

<p>There are four types of user accounts that can exist in an ATutor installation, as defined by their Status:	
	<dl>
		<dt>Disabled</dt>
		<dd>Only administrators may disable an account. Disabled accounts cannot login to the ATutor installation, and will not appear in a course's Enrollment Manager.</dd>
		<dt>Unconfirmed</dt>
		<dd>Unconfirmed accounts are created only when the <a href="system_preferences.php">System Preferences</a> <em>Require Email Confirmation Upon Registration</em> option is enabled.</dd>
		<dt>Student</dt>
		<dd>A regular account which can enroll, but not create courses.</dd>

		<dt>Instructor</dt>
		<dd>A regular account which can enroll as well as create courses.</dd>
	</dl>
</p>

<h3>Creating User Accounts</h3>

<p>Administrators can manually add users to the system by using <em>Create User Account</em>. Manually created accounts are automatically confirmed and the account status is set to Student, Instructor, or disabled as choosen in the Account Status field of the user account creation form. </p>
<p>User accounts can also be created by individuals using the Registration form available through the public pages of ATutor. Instructors can also generate user accounts by importing a course list in the Enrollment Manager.</p>

<?php require('../common/body_footer.inc.php'); ?>
