<?php require('../common/body_header.inc.php'); ?>

<h2>Master Student List</h2>
	<p>If the <a href="system_preferences.php">System Preferences</a> <em>Authenticate Against A Master Student List</em> option is enabled, this page will an administrator to manage that list. If enabled, only new registrations that validate against the master list will be successful. The master list is flexible and can be used to validate any two fields, one of which is publicly viewable to Administrators, while the other is hidden. A common use of this feature would be to authenticate students using a previously assigned Student ID &amp; PIN combination.</p>

	<p>Subsequently, when a student registers for an ATutor account on the system, he/she must provide this authenticating information (like their student ID and a PIN). Once an account is authenticated and created, the user will then be associated with the appropriate entry in the Master Student List. If <em>Require Email Confirmation Upon Registration</em> is enabled in <a href="system_preferences.php">System Preferences</a>, the user must confirm his/her account using that email before the account is activated.</p>

	<p>Viewing the Master Student List shows Student ID-Username pairs. Student IDs in the Master Student List that are not associated with any student account are considered to not have been created.</p>

<h3>Importing Student IDs</h3>
	<p>Importing Student IDs into the Master Student List requires a specifically formatted file. This file can be uploaded under the "Upload List" heading.</p>

	<p>The master list must be a plain text file, where each row in the file contains two fields seperated by a single comma. The first field will be used as the Student ID. The second field will be the PIN or Password which will be encoded by the ATutor system, once the list is uploaded, so that it cannot be viewed and read by anyone. Those two fields together will be used to authenticate students when creating new accounts. The fields may optionally be enclosed by double quotes. Such a file is known as a <acronym title="Comma Separated Values">CSV</acronym> file and can be generated manually using a text editor, or by any spreadsheet application (such as MS Excel).</p>

	<p>In the example below, a student number and a birth date are used to construct a master list:</p>
	<pre>"12345", "10/07/54"
"12346", "23/04/76"
"12347", "30/05/68"</pre>

<?php require('../common/body_footer.inc.php'); ?>