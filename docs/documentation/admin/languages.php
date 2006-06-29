<?php require('../common/body_header.inc.php'); $lm = '$LastChangedDate$'; ?>

<h2>Languages</h2>
	<p>ATutor can be displayed in many different languages! Only completely translated languages are made available as importable packages from the atutor.ca website.</p>

<h3>Managing Existing Languages</h3>
	<p>Installed languages can be edited, deleted or exported as an ATutor language pack for redistribution. When exporting a language, a download prompt will appear asking to download a zip file of the language pack.</p>
	
	<p>Editing the language allows you to change the following:
	<ul>
		<li>Language Code</li>
		<li>Locale</li>
		<li>Character Set</li>
		<li>Direction</li>
		<li>Left to Right, Right to Left</li>
		<li>Regular Expression</li>
		<li>Language name translated</li>
		<li>Language name in English</li>
	</ul>
	</p>

	<p>Note that the default language (as specified in the <a href="system_preferences.php">System Preferences</a> <em>Default Language</em>) cannot be deleted.</p>


<h3>Importing Languages</h3>
	<p>Language packs can be imported either manually by retreiving the package and then importing it into ATutor, or automatically by having ATutor connect to the atutor.ca language repository directly.</p>

	<p>To <em>manually</em> import a new language pack:</p>
	<ol>
		<li>Visit <a href="http://atutor.ca/atutor/translate/index.php" target="_new">atutor.ca/atutor/translate/</a> to download one of the available language packs for your version.</li>
		<li>Use the <code>Browse...</code> button to find the downloaded language pack.</li>
		<li>Use the <code>Import</code> button to import the language.</li>
	</ol>

	<p>If your ATutor installation is connected to the Internet and can contact the atutor.ca website, then it will try to retrieve the list remotely. To <em>automatically</em> import a new language pack from within ATutor:</p>

	<ol>
		<li>Select the language you want to import from the drop down.</li>
		<li>Use the <code>Import</code> button to import the selected language.</li>
	</ol>

	<p>If your installation cannot retrieve the language list from atutor.ca, a message indicating so will be presented rather than a drop down list. In this case you will have to use the manual method described above.</p>


<h3>Translating ATutor</h3>

	<p>Administrators have the ability to customize an installation's language. In order to translate a language, 
	<ol>
		<li>Set the AT_DEVEL_TRANSLATE constant in /include/vitals.inc.php to '1'</li> 
		<li>Set the session language to the language you wish to translate by using the language selector at the bottom of the screen.</li>
		<li>Use the <code>Translate</code> button to pop up a translation box</li>
		<li>Translating from English to the chosen language can now be done. </li>
	</ol>
	</p>

	<p>One can assist the ATutor project by translating to a new language from within ATutor and exporting it to create a new language pack. For official documentation on translating see the <a href="http://atutor.ca/atutor/docs/translate.php" target="_new">atutor.ca/atutor/docs/translate.php</a>.</p>


<?php require('../common/body_footer.inc.php'); ?>
