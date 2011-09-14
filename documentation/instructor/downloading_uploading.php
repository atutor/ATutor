<?php require('../common/body_header.inc.php'); $lm = '$LastChangedDate$'; ?>

<h2>Downloading &amp; Uploading Backups</h2>
	<p>Backups can be downloaded and stored locally by selecting from the list of backups created and using the <kbd>Download</kbd> button. Locally stored backups can be uploaded back into the original course, into a new course, or into another installation of ATutor.</p>

	<p>The backup file itself is a compressed archive in a format specific to ATutor. Backups cannot be used by any other system other than ATutor (see <a href="content_packages.php">Import/Export Content</a> for information about reuseable content). Extracting the backup archive to view and change its contents is strongly discouraged as it may currupt the backup, making it impossible to restore.</p>

	<p>Backups are forwards compatible, but not backwards compatible with older versions of ATutor. That is, backups can be used with all future versions of ATutor, but cannot be used with versions of ATutor older than the version originally used in the backup's creation.</p>

<?php require('../common/body_footer.inc.php'); ?>
