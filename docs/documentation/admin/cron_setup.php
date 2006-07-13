<?php require('../common/body_header.inc.php'); $lm = '$LastChangedDate$'; ?>

<h2>Cron Set-Up</h2>

	<p>ATutor operates best with the help of an automated event scheduler, commonly known as a <em>cron job</em>. The cron interval should be set at between 5-30 minutes, depending on server resources. Setting the cron to 10-15 minutes is recommended.</p>

	<p>The cron is run by requesting a specific ATutor page, and can be initiated by any machine that has an Internet connection and access to the ATutor installation.</p>

	<p>Notice that the URL being used will be unique for each installation and that for security reasons the requested URL includes a secret six-character alpha-numerica authentication key. The cron will not run if the key is incorrect or missing.</p>

	<p>The <em><a href="system_preferences.php">Mail Queue</a></em> feature requires the cron to be set-up and running correctly before it can be enabled.</p>

	<h3>Unix Setup</h3>
		<ol>
			<li>Enter your hosts cron utility, either using an existing web interface or from the shell with the command <code>crontab -e</code>.</li>
			<li>To run the cron every 10 minutes enter one of the following lines into the crontab editor:<br />
			  <code>*/10 * * * * wget -q -O /dev/null http://<em>your-server.com/atutor/</em>admin/cron.php?k=<em>SECRET-KEY</em></code><br />
			  Or<br />
			  <code>*/10 * * * * lynx -dump http://<em>your-server.com/atutor/</em>admin/cron.php?k=<em>SECRET-KEY</em> > /dev/null</code>
			  <p>Replace <em><code>your-server.com/atutor/</code></em> with the full server and path to your ATutor installation.</p>
			  <p>Replace <em><code>SECRET-KEY</code></em> with the key provided on the Cron Configuration page in your ATutor Administration section.</p>
			  <p>Replace <code>10</em> with the desired interval.</p>
			  </li>
		</ol>


<?php require('../common/body_footer.inc.php'); ?>