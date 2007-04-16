<?php require('../common/body_header.inc.php'); $lm = '$LastChangedDate$'; ?>

<h2>Backups</h2>
	<p>A course backup includes all available course material as an archive in a format specific to ATutor. Backups are forwards compatible with future versions of ATutor but may not be backwards compatible with previous versions of ATutor. Once a backup is created, it can be downloaded for safe-keeping, imported into another ATutor installation, used as the basis for a newly created course, and available in the originating course's Backup Manager. Instructor can also create their own course backups from within a course.</p>

<h3>Creating Backups</h3>
	<p>To create a backup, use the <em>Create Backup</em> link in the sub-navigation. The number of backups a single course can keep on the server is defined by the <a href="system_preferences.php">System Preferences</a> <em>Course Backups</em> option.</p>

	<p>Administrators can create backups for any course, while instructors can only create backups of courses they own.</p>

<h3>Restoring Backups</h3>

	<p>Restoring a backup as an administrator is similar to restoring a backup as an instructor, with the added option of being able to select which course the backup should be restored into.</p>

	<p>For details on restoring a backup into a course, see the Backup Manager's <a href="../instructor/creating_restoring.php">Restoring Backups</a> section in the Instructor Documentation.</p>

<h3>Managing Backups</h3>
	<p>Backups can be downloaded to the administrator's hard-drive for safe-keeping by using the <code>Download</code> button. Backups can also be edited or deleted.</p>

<?php require('../common/body_footer.inc.php'); ?>
