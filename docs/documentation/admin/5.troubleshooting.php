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
		<dt>AT_DEVEL function</dt>
		<dd>ATutor includes a function called Debug, which can be enabled in the <kbd>include/vitals.inc.php</kbd> file. Near the top of the file set the value of AT_DEVEL to 1 (i.e. <kbd>define('AT_DEVEL', 1);</kbd>). This will display at the bottom of the screen all the variable and their values currently running in the ATutor session. It will also display the variable names associated with all feedback message, so they are easier to find through the language manager if you wish to modify language.</dd>

		<dt>Debug function</dt>
		<dd>It is possible to display the value of an ATutor variable by entering the vaiable into the debug() function. Several Examples below demonstrate how the function can be used to display values being past through an ATutor session, GET, and POST values. Generally the debug function can be written into the ATutor header after the call to the vital.inc.php file, or in the footer. Any variable, constant, array, or function call can be entered into the debug function to display its value(s);<br /><br />
<kbd>debug($_SESSION); </kbd>[displays all value held in the current session]<br/>
<kbd>debug($_REQUEST); </kbd>[displays the values of a form or URL just just submitted]<br />
<kbd>debug($date); </kbd>[displays the value associated with the $date variable]<br />


		</dd>
		<dt>Error Logging</dt>
		<dd>View the error log recorded in ATutor through the Configuration section. There may be information in the error reports that can help you identify where or how an error occured. The output from the error log can be sent to the ATutor team to help them track donw problem you might be experiencing on your system.</dd>

		<dt>phpinfo()</dt>
		<dd>It is often possible to determine the cause of an error by reviewing the phpinfo page for your system. It will list all the configuration options for your system. Review the <a href="1.1.requirements_recommendations.php">Requirement & Recommendations</a> for different values that should be set and displayed in the phpinfo output. Below is the contents of a phpinfo file:<br /><br />
&lt;?php
phpinfo();
?&gt;


		</dd>
	</dl>
<?php require('../common/body_footer.inc.php'); ?>
