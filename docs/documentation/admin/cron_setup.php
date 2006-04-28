<?php require('../common/body_header.inc.php'); ?>

<h2>Cron Set-up</h2>

	<p> ATutor operates best with the help of an automated event scheduler, commonly known as a cron job. The cron internval should be between 5 minutes and 30 minutes.
Since the cron simply loads an ATutor web page, the cron can run on any machine which has Internet access or access to your ATutor installation.</p>

<h3>Unix Setup</h3>
	<ol>
		<li>Enter your hosts cron utility, either using an existing web interface or from the shell with crontab -e.</li>
		<li>Decide whether you want to use wget or lynx to execute the file remotely</li>
		<li>To run the cron every 5 minutes enter<br />
		  */5 * * * * wget -q -O http://142.150.154.185/atutor/docs/admin/cron.php?k=31B3AE<br />
		  Or<br />
		  */5 * * * * lynx -dump http://142.150.154.185/atutor/docs/admin/cron.php?k=31B3AE > /dev/null</li>
	</ol>

<?php require('../common/body_footer.inc.php'); ?>