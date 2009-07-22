<?php require('../common/body_header.inc.php'); $lm = '$LastChangedDate$'; ?>

<h2>Extracting Zip Archives</h2>
	<p>After uploading a ZIP file to the File Manager, select the <em>Extract Archive</em> icon next to the file name. This will display the contents of the zip file and suggest a directory name in which to unzip the archive. Use the <code>Extract</code> button in the ZIP file viewer to unzip the file into the specified directory.</p>

	<p><strong>Illegal file types</strong> will not be extracted, and file names containing illegal characters will be renamed. The viewer will show illegal file types <span style="text-decoration: line-through;">crossed out</span>, and files with illegal characters pointing ( => ) to the renamed file that will be extracted.</p>


<?php require('../common/body_footer.inc.php'); ?>
