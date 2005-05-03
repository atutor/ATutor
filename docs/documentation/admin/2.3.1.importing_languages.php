<?php require('./body_header.inc.php'); ?>

<h2>2.3.1 Importing Languages</h2>
	<p>Language packs can be imported either manually by retreiving the package and then importing it into ATutor, or automatically by having ATutor connect to the atutor.ca language repository directly.</p>

	<p>To <em>manually</em> import a new language pack:</p>
	<ol>
		<li>Visit <a href="http://atutor.ca/atutor/translate/index.php">atutor.ca/atutor/translate/</a> to download one of the available language packs for your version.</li>
		<li>Use the <code>Browse...</code> button to find the downloaded language pack.</li>
		<li>Use the <code>Import</code> button to import the language.</li>
	</ol>

	<p>If your ATutor installation is connected to the Internet and can contact the atutor.ca website then it will try to retrieve the list remotely. If it cannot retrieve the list a message indicating so will be presented rather than a drop down list, in that case you will have to use the manual method described above. To automatically import a new language pack from within ATutor:</p>

	<ol>
		<li>Select the language you want to import from the drop down.</li>
		<li>Use the <code>Import</code> button to import the selected language.</li>
	</ol>

<?php require('./body_footer.inc.php'); ?>