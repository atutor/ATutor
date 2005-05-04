<?php require('../common/body_header.inc.php'); ?>

<h2>4.2 Downloading and Uploading a Backup</h2>
	<p>Backups can be downloaded to localy by selecting the backup and using the <kbd>Download</kbd> button. Locally stored backups can optionally be uploaded back into other installations of ATutor to be restored.</p>

	<p>The backup file itself is a compressed archive file in an ATutor-specific format. Backups created with ATutor cannot be used by any other system other than ATutor. Extracting the backup archive to view and change its contents is strongly discouraged as it may make the backup invalid, making it impossible to import it back into ATutor.</p>

	<p>Backups are forwards compatible, but are not backwards compatible with versions of ATutor. That is, backups will work with all future versions of ATutor, but will not work with versions of ATutor older than the version originally used to create it.</p>

<?php require('../common/body_footer.inc.php'); ?>