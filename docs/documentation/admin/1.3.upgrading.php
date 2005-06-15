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

<h2>1.3 Upgrading an Installation</h2>
	<p>For the most recent version of the upgrade instructions, visit <a href="http://www.atutor.ca/atutor/docs/installation.php#upgrade" target="_new">http://www.atutor.ca/atutor/docs/installation.php</a>

	<h3>Considerations Before Upgrading</h3>

<p>Note that Release Candidates (RC) and ATutor 1.0 upgrades are
not supported using this method and that depending on the size of the
old courses, some steps of the upgrade may require considerable time to
complete (in particular steps 2 and 6).</p>
<p>Also be sure that <strong>Language Packs</strong> you have installed
on your old version of ATutor are available for the new version. The
old language will be removed during upgrade. If they are not available,
you might volunteer to help finish any remaining language that needs to
be translated for these languages. See the <a href="http://www.atutor.ca/atutor/translate">ATutor Translation</a> site for more details.</p>
<p>It is highly recommended that you <strong>backup your old ATutor database</strong>. Although it is unlikely anything will go wrong, there is always a chance. </p>

<p>Before starting the upgrade, rename or <strong>move your old ATutor directory</strong>.
This creates a backup of the current ATutor installation, which you can
revert to if something goes wrong during the upgrade. Download the
latest version of ATutor and <strong>extract the new version into the same directory that the old one was in</strong>.
Example: If the old ATutor installation was in /htdocs/ATutor and moved
to /htdocs/ATutor_old, then the new ATutor installation should be in
/htdocs/ATutor, such that both the old and new installations are at the
same directory level. On Windows you may use WinZip or WinRar, while on
Unix use the command <kbd>tar -zxvf ATutor-version_number.tar.gz</kbd>. Once extracted, an <kbd>ATutor</kbd> directory will be created alongside your old ATutor directory. Open a web browser and enter the address to your installation, <kbd>http://your_server.com/path_to_atutor/ATutor/</kbd>, then follow the step-by-step instructions.</p>

	<h3>Step for Upgrading ATutor</h3>

<p>The following eight steps describe the upgrade process as they are presented by the ATutor installer:</p>
<ol>
	<li><strong>Locate Old Version</strong><br /> Specify the directory
name of the old ATutor installation you wish to upgrade (e.g.
ATutor_old). The new and old ATutor directories must be at the same
directory level. </li>

	<li><strong>Database</strong><br /> The upgrade will use the
old version's settings to connect to the database and then update the
old database tables with any changes to bring them up to date with the
new version.</li>

	<li><strong>Preferences</strong><br />

In some cases, the newer version will introduce new configuration
options and preferences that have to be set or confirmed. Review the
Preferences and modify them if necessary.</li>

	<li><strong>Directories</strong><br />
Create a content directory , preferably outside your web server's
document directory for added security, and set permissions as described
above. On a Unix machine you will need to manually change the
permissions on the listed files and directories in this step, if you
are using a directory other than the one used in the version of ATutor
being upgraded. No action is usually required on a Windows server,
though in some circumstances Windows users may need to adjust the
properties of the specified files and directories to make them
writable. Copy the path of the directory into the text box provided.
Ensure there are no shortcuts (Windows), or symbolic links (Unix) are
contained in the path. The path can be the same as that to the content
directory use in the version being upgraded from if the directory is
outside the old ATutor installation.</li>
	<li><strong>Save configuration</strong><br />
Before reaching the final step the include/config.inc.php file needs to
be writable, otherwise an error will appear. Follow the instructions on
the screen if the file permissions need to be changed</li>
	<li><strong>Content Files</strong><br />
All the old course content files and chat messages will be copied over
to the new installation. Depending on the size of your old
installation, this process may take a few seconds to several minutes or
more to complete.</li>

<li><strong>Submit Usage Information</strong><br />
To assist the development team in serving the ATutor community, submit
some basic information about the system you are running. All
information is private. Though you are encouraged to list the location
of your ATutor installation, you may remain anonymous by choosing not
to submit the URL to your ATutor server during this step.</li>
	<li><strong>Done!</strong><br />
		ATutor upgrade has been successful and you may now log-in with your personal account or your administrator account.</li>
</ol>
<br />

<?php require('../common/body_footer.inc.php'); ?>
