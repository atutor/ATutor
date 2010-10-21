<?php require('../common/body_header.inc.php'); $lm = '$LastChangedDate$'; ?>

<h2>Troubleshooting</h2>
	<p>A variety of strategies are available for troubleshooting an ATutor installation that may not be functioning properly.</p>
	<dl>
		<dt><kbd>AT_DEVEL</kbd></dt>
		<dd>Near the top of the <kbd>include/vitals.inc.php</kbd> file, set the value of <kbd>AT_DEVEL</kbd> to <kbd>true</kbd>. This will display your session variables at the bottom of the screen. It will also display the variable names associated with all feedback messages, so they are easier to find through the language manager if you wish to modify their language.  The <kbd>debug()</kbd> function will also become available, allowing testers to print out any type of variable in an easily readable format.</dd>

		<dt><kbd>debug(mixed variable [, string title])</kbd></dt>
		<dd>It is possible to display the value of variables using <kbd>debug()</kbd>. <kbd>variable</kbd> is the PHP variable to output. <kbd>title</kbd> is an optional title that can be printed inside the debugging box to easily identify which variable is being outputted.
<pre>debug($_SESSION); // print current session variables
debug($_REQUEST); // print all GET, POST, and COOKIE variables
</pre>

		</dd>
		<dt>Error Logging</dt>
		<dd>View the error log through the <a href="system_preferences.php">System Preferences</a> section. There may be information in the error reports that can help you identify where or how an error occured. The output from the error log can be sent to the ATutor team to aid them in finding a solution to your problem.</dd>

		<dt><kbd>phpinfo()</kbd></dt>
		<dd>Often, system problems can be fixed by reviewing the phpinfo page. This will show all of the configuration options for your system. Review the <a href="requirements_recommendations.php">Requirement &amp; Recommendations</a> for different values that should be set and displayed in the phpinfo output. Below is the contents of a phpinfo file. Viewing this page in a browser will show the system variables.<br /><br />
<pre>
&lt;?php
phpinfo();
?&gt;
</pre>
</dd>
<p>Also see the <a href="../developer/guidelines.html">Developer Documentation</a> for details about modifying the source code.</p>
		</dd>
	</dl>
<?php require('../common/body_footer.inc.php');?>