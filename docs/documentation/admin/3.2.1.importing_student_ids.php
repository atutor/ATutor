<?php require('../common/body_header.inc.php'); ?>

<h2>3.2.1 Importing Student IDs</h2>
	<p>Importing Student IDs into the Master Student List requires a specifically formatted file:</p>

	<p>The master list must be a plain text file, where each row in the file contains two fields seperated by a single comma. The fields may optionally be enclosed by double quotes. Such a file is known as a <acronym title="Comma Separated Values">CSV</acronym> file and can be generated manually using a text editor, or by any spreadsheet application (such as MS Excel). The first field will be used as the Student ID. The second field will be the PIN or Password, which will be encoded in such a way that cannot be viewed and read by anyone. Those two fields together will be used to authenticate students when creating new accounts.</p>

<?php require('../common/body_footer.inc.php'); ?>