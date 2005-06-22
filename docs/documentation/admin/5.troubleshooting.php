<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg 		*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id: 10.1.link_categories.php 4824 2005-06-08 19:27:33Z joel $

require('../common/body_header.inc.php'); ?>

<h2>5. Troubleshooting</h2>
	<p>A variety of strategies are available to troubleshoot an ATutor installation that may not be functioning properly.</p>
	<dl>
		<dt><kbd>AT_DEVEL</kbd></dt>
		<dd>ATutor includes a function called <kbd>debug()</kbd>, which can be enabled in the <kbd>include/vitals.inc.php</kbd> file. Near the top of the file set the value of <kbd>AT_DEVEL</kbd> to a <kbd>true</kbd>. This will display your session variables at the bottom of the screen. It will also display the variable names associated with all feedback message, so they are easier to find through the language manager if you wish to modify language.</dd>

		<dt><kbd>debug(mixed variable [, string title])</kbd></dt>
		<dd>It is possible to display the value of variables using <kbd>debug()</kbd>. <kbd>variable</kbd> is the PHP variable to output. <kbd>title</kbd> is an optional title that can be printed inside the debugging box to easily identify which variable is being outputted.
<pre>debug($_SESSION); // print current session variables
debug($_REQUEST); // print all GET, POST, and COOKIE variables
</pre>

		</dd>
		<dt>Error Logging</dt>
		<dd>View the error log recorded in ATutor through the Configuration section. There may be information in the error reports that can help you identify where or how an error occured. The output from the error log can be sent to the ATutor team to help them track donw problem you might be experiencing on your system.</dd>

		<dt><kbd>phpinfo()</kbd></dt>
		<dd>It is often possible to determine the cause of an error by reviewing the phpinfo page for your system. It will print all the configuration options for your system. Review the <a href="1.1.requirements_recommendations.php">Requirement & Recommendations</a> for different values that should be set and displayed in the phpinfo output. Below is the contents of a phpinfo file:<br /><br />
<pre>
&lt;?php
phpinfo();
?&gt;
</pre>


		</dd>
	</dl>
<?php require('../common/body_footer.inc.php');?>
