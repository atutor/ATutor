<?php require('./body_header.inc.php'); ?>

<h2>3.2 Master Student List</h2>
	<p>If the <a href="2.2.system_preferences.php">System Preferences</a> <em>Authenticate Against A Master Student List</em> option is enabled then this page will allow you to manage that list. If enabled, only new account submissions that validate against the master list will be created. The master list is flexible and can be used to validate any two fields, one of which is publicly viewable to Administrators, while the other is hidden. A common use of this feature would be to authenticate students using a previously assigned Student ID &amp; PIN combination.</p>

	<p>Once an account is authenticated and created--and confirmed if <em>Require Email Confirmation Upon Registration</em> is enabled--then that user will be associated with the appropriate entry in the Master Student List. Viewing the Master Student List shows Student ID-Username pairs.</p>

	<p>Student IDs in the Master Student List that are not associated with any student account are considered to not have been created.</p>

<?php require('./body_footer.inc.php'); ?>