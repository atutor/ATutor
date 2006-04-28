<?php require('../common/body_header.inc.php'); ?>

<h2>Instructor Requests</h2>
	<p>If the <a href="system_preferences.php">System Preferences</a> <em>Allow Instructor Requests</em> option is enabled and the <em>Auto Approve Instructor Requests</em> option is disabled, then pending instructor account requests will be listed on this page.</p>

	<p>Using the <code>Deny</code> or <code>Approve</code> buttons after selecting an entry will remove it from the list and take the appropriate action. An email message will be sent to the account holder notifying them of the change.</p>

	<p>Note that the number of pending Instructor Requests is always listed on the main <a href="configuration.php">Configuration</a> page.</p>

<?php require('../common/body_footer.inc.php'); ?>