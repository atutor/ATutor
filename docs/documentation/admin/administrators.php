<?php require('../common/body_header.inc.php'); ?>

<h2>Administrators</h2>
	<p>An ATutor installation can be maintained by multiple administrators, each with their own privilege access level. The three kinds of administrator accounts are described below.</p>

	<dl>
		<dt>Super Administrator</dt>
		<dd>This administrator has no restrictions and has access to all of the administrator options. This is the only administrator type that can create and delete other administrator accounts. There must always be at least one Super Administrator account.</dd>

		<dt>Active Administrator</dt>
		<dd>An administrator account whose access is limited. This administrator only has privileged access to sections that they were assigned to when their account was created by the Super Administrator.</dd>

		<dt>Inactive Administrator</dt>
		<dd>An administrator account that has not been assigned any access privileges. As a result, this administrator cannot login.</dd>
	</dl>

	<h3>Create Administrator Account</h3>
	<p>To make a new administrator, follow the <em>Create Administrator Account</em> link, enter the login name, password, real name and email and select the appropriate administrative privileges to be assigned to this account.</p>

	<h3>Administrator Activity Log</h3>
	<p>The <em>Administrator Activity Log</em> lists all actions made to the ATutor database tables. Viewing a log entry will give detailed information about the selected activity. The log can be reset by using the <em>Reset Log</em> feature.</p>

<?php require('../common/body_footer.inc.php'); ?>
