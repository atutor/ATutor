<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2003 by Greg Gay, Joel Kronenberg, Heidi Hazelton	*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/

require('../include/lib/constants.inc.php');

$new_version = VERSION;

include 'include/header.php';

//print_progress(0);

?>

<h3>Welcome to the ATutor Installation</h3>
<p>This process will guide you through your ATutor installation or upgrade.</p>
<p>During the installation or upgrade be sure not to use your browser's <em>Refresh</em> option as it may complicate the installation process.</p>

<h4>Requirements</h4>
<p>Please review the requirements below before proceeding.</p>
<ul>
	<li>HTTP Web Server (<a href="http://apache.org">Apache</a> 1.3.x is highly recommended. We do not recommend Apache 2.x)<br />
		Detected: <?php echo $_SERVER['SERVER_SOFTWARE']; ?><br /><br /></li>
	<li><a href="http://php.net">PHP</a> 4.2.0 or higher with Zlib and MySQL support enabled (Version 4.3.0 or higher is recommended)<br />
		Detected: <?php echo phpversion(); ?><br /><br /></li>
	<li><a href="http://mysql.com">MySQL</a> 3.23.x or higher (MySQL 4.x is not yet officially supported)</li>
</ul>

<br /><br />

<table border="0" width="100%">
<tr>
	<td align="center"><b>Install a fresh version</b><form action="install.php" method="post" name="form">
	<input type="hidden" name="new_version" value="<?php echo $new_version; ?>" />
	<input type="submit" class="button" value="Install » " name="next" />
	</form></td>
</tr>
<tr><td align="center"><br /><b>Or</b><br /><br /></td></tr>
<tr>
	<td align="center"><b>Upgrade an existing version</b><form action="upgrade.php" method="post" name="form">
	<input type="hidden" name="new_version" value="<?php echo $new_version; ?>" />
	<input type="submit" class="button" value="Upgrade » " name="next" />
	</form></td>
</tr>
</table>

<?php

include 'include/footer.php';

?>