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
if (!defined('AT_INCLUDE_PATH')) { exit; }

print_progress($step);

?>
<p><strong>Congratulations on your installation of ATutor <?php echo $new_version; ?><i>!</i></strong> You may now <a href="../login.php"><strong>login</strong></a> using your personal account you created in Step 4.</p>

<p>You may also try out the administration section by logging-in using the administrator account you created in Step 3.</p>

<p>See the official <a href="http://atutor.ca/atutor/docs/howto.php">ATutor <em>HowTo</em> Course</a> or the <a href="http://atutor.ca/forums/">Support Forums</a> on <a href="http://atutor.ca">ATutor.ca</a> for additional help &amp; support.</p>

<p><strong>Note 1:</strong> It is recommended that you now make the <code>config.inc.php</code> file read-only in the <code>include/</code> directory. On a Windows machine right-click on the file and select <em>Properties</em> and then select the <em>Read-only</em> attribute. On a Unix machine execute the command <code>chmod a-xw config.inc.php</code>.</p>

<p><strong>Note 2:</strong> It is recommended that after you verify the correctness of the installation that you delete the <code>install</code> directory to ensure additional security.</p>


<table border="0" cellspacing="0" cellpadding="4" align="center" style="border: 1px solid #cccccc;">
<tr>
	<td><b>» <a href="../login.php">Log-in<i>!</i></a></b></td>
</tr>
</table>