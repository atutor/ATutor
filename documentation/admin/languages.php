<?php require('../common/body_header.inc.php'); $lm = '$LastChangedDate: 2006-06-30 19:56:09 -0400 (Fri, 30 Jun 2006) $'; ?>

<h2>Languages</h2>
	<p>ATutor can be displayed in many different languages! Through the Langauge Manager completed languages packs can be selected and imported directly from the atutor.ca website. </p>

<h3>Managing Existing Languages</h3>
	<p>Installed languages can be edited, deleted or exported as an ATutor language pack for redistribution. When exporting a language, a download prompt will appear asking to download a zip file of the language pack.</p>
	
	<p>Editing the language properties allows you to change the following:
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

	<p>Note that the default language (as specified in the <a href="system_preferences.php">System Preferences</a> <em>Default Language</em>) cannot be disabled or deleted unless another language has been installed.</p>


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
		<li>Use the <code>Translate</code> button to pop up a translation window.</li>
	</ol>
	</p>

	<p>You can contribute to the ATutor community by  exporting a language pack from your ATutor installation, and attaching it to a message in the atutor.ca <a href="http://atutor.ca/forum/4/1.html">Translation Forum</a>. Also see the <a href="http://atutor.ca/atutor/docs/translate.php">Translator Documentation</a> for further details about translating ATutor.</p>


<?php require('../common/body_footer.inc.php'); ?>
