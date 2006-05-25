<?php require('../common/body_header.inc.php'); ?>

<h2>Cron Set-up</h2>

	<p> ATutor operates best with the help of an automated event scheduler, commonly known as a cron job. The cron interval should be between 5 minutes and 30 minutes, depending on the purpose of the script cron is running. Some other script might run daily, such as running a backup script, or perhaps once a week, such as running a script that generates system statistics.</p>

<p>Since the cron simply loads an ATutor web page, it can run on any machine which has Internet access or access to your ATutor installation.</p>

<h3>Unix Setup</h3>
	<ol>
		<li>Enter your hosts cron utility, either using an existing web interface or from the shell with crontab -e.</li>
		<li>Decide whether you want to use wget or lynx to execute the file remotely</li>
		<li>Create a cron job by typing at the system's command prompt<br />
		>crontab -e
		<li>To run the cron every 5 minutes enter one of the following lines into the crontab editor opened above<br />
		  */5 * * * * wget -q -O http://192.168.134.115/atutor/docs/admin/cron.php?k=31B3AE<br />
		  Or<br />
		  */5 * * * * lynx -dump http://192.168.134.115/atutor/docs/admin/cron.php?k=31B3AE > /dev/null</li>
	</ol>
<p>Do a Web search for "using cron" to find a large amount of documentation on the subject.</p>

<p>Also see  "Enabled Mail Queue" in the <a href="system_preferences.php">System Preferences</a> section of the handbook. If this setting is enabled, the cron utility can be set to send email at some delay. This can speed up the message posting features if you have a slow mail server.</p>
<?php require('../common/body_footer.inc.php'); ?>