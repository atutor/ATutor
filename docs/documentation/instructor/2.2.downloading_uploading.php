<?php require('../common/body_header.inc.php'); ?>

<h2>2.2 Downloading &amp; Uploading Backups</h2>
	<p>Backups can be downloaded localy by selecting the backup and using the <kbd>Download</kbd> button. Locally stored backups can optionally be uploaded back into originating or other installations of ATutor.</p>

	<p>The backup file itself is a compressed archive in an ATutor-specific format. Backups created with ATutor cannot be used by any other system other than ATutor. Extracting the backup archive to view and change its contents is strongly discouraged as it may currupt the backup, making it impossible to import back into ATutor.</p>

	<p>Backups are forwards compatible, but are not backwards compatible with versions of ATutor. That is, backups can be used with all future versions of ATutor, but cannot be used with versions of ATutor older than the version originally used in its creation.</p>

<?php require('../common/body_footer.inc.php'); ?>