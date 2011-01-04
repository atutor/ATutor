<?php require('../common/body_header.inc.php'); $lm = '$LastChangedDate: 2006-06-29 11:25:07 -0400 (Thu, 29 Jun 2006) $'; ?>

<h2>Importing/Exporting Themes</h2>
	<p>Themes can be imported into, or exported from, ATutor using the Themes manager in the ATutor administrators' configuration tools. An existing theme can be exported, then imported back into an ATutor installation to create a copy, after which the copy can be modified to create a new theme. Themes can be exported and shared with others. See the <a href="http://www.atutor.ca/atutor/themes/index.php" target="_new">Themes page on atutor.ca</a> for a list of available themes, and for a place to share your themes.</p>

	<p>To import a theme the <kbd>./themes/</kbd> directory must be writable. On Windows machines using multiple user accounts, that directory will have to be shared to provide write access to it. On Unix machines the command <code>chmod a+rw themes</code> should be used to make the directory writable.</p>

<?php require('../common/body_footer.inc.php'); ?>