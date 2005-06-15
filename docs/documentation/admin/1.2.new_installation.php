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

<h2>1.2 New Installation</h2>
<p>For the most recent version of the installation instructions, visit <a href="http://www.atutor.ca/atutor/docs/installation.php" target="_new">http://www.atutor.ca/atutor/docs/installation.php</a>
	<p>Please review the <a href="#1">requirements</a> section <em>before</em> attempting to install ATutor. The latest version of ATutor can always be found on the <a href="http://atutor.ca/atutor/download.php" target="_new">atutor.ca downloads</a> page.</p>

	<h3>Windows Considerations</h3>
		<p>To extract the ATutor archive you will need an application like <a href="http://winzip.com" target="_new">WinZip</a> or <a href="http://rarlabs.com" target="_new">WinRar</a>.</p>

		<p>Windows users may be required to create the Content/ folder specified in Step 4 of the installation within the ATutor installation, rather than outside the installation as recommended.</p>

	<h3>Unix Considerations</h3>
		<p>To extract the ATutor archive you will have to use the command <kbd>tar -zxvf ATutor-version_number.tar.gz</kbd>, which will create a directory called <kbd>ATutor</kbd> in your current working directory.</p>

		<p>Installing on a Unix machine requires some knowledge of file and directory permissions. You will be required to create a content directory and set permissions for that directory and for the <kbd>include/config.inc.php</kbd> file, so that the web server can manipulate them. The installation will not be successful if the permissions are not correctly set on that file and directory.</p>

		<p>Changing Unix file permissions from the shell prompt: <kbd>chmod a+rw filename</kbd> or <kbd>chmod a+rwx directoryname</kbd>.</p>
		
		<p>Changing Unix file permissions from an FTP client: Many FTP clients allow you to change a file's permissions. The option may be labled as "Unix Permissions", "CHMOD", or simply as "Properties" or "Attributes" and will display a window with Read, Write, and Execute checkboxes for Owner, Group, and World; checking the appropriate boxes will change that file's permissions. In our case we need the <kbd>include/config.inc.php</kbd> to be Readable and Writeable by World, and the content directory to be Readable, Writeable, and Executable by World.</p>

	<h3>Installation Procedure</h3>
		<p>Extract the downloaded archive using the method specific to your system (either Windows or Unix). Open a web browser and enter the address to your new ATutor installation, http://your_server.com/path_to_atutor/ATutor/, then follow the step-by-step instructions:</p>

		<ol>
			<li><strong>Terms of Use</strong><br />
				The usage of ATutor is restricted by the <acronym title="Recursive acronym for GNU's Not Unix">GNU</acronym> General Public License (GPL). Your agreement with the GPL is required if you wish to use ATutor. See the <a href="http://atutor.ca/services/licensing.php">Licensing section</a> for more details.</li>

			<li><strong>Database</strong><br />
				Enter the required details needed to connect to your MySQL database. The optional <em>Table Prefix</em> (e.g. "AT_") option allows ATutor to share an existing database with other applications and tables. The ATutor installation script will attempt to create the database specified, if it does not already exist. This requires that your MySQL user account have permission to create databases, and permission to create tables. If this step fails, contact your system administrator to have your MySQL account upgraded to allow creation of new databases, or ask your administrator to create the database for you.</li>

			<li><strong>Accounts &amp; Preferences</strong><br />
				The Super Administrator account is used for managing your ATutor installation. The Super Administrator can also create additional Administrators each with their own privileges and roles once ATutor is installed. The personal account can be used to enroll into or create courses. If the personal account is created as an instructor then the <em>Welcome Course</em> may be also be created.</li>

			<li><strong>Content Directory</strong><br />
				Create a content directory, preferably outside your web server's document directory for added security, and set permissions as described above. On a Unix machine you will need to manually change the permissions on the listed files and directories in this step. No action is usually required on a Windows server, though in some circumstances Windows users may need to adjust the properties of the specified files and directories to make them writable. Copy the path of the directory into the text box provided. <strong>Ensure there are no shortcuts (Windows), or symbolic links (Unix) contained in the path.</strong> </li>

			<li><strong>Save configuration</strong><br />
				Before reaching the final step the <kbd>include/config.inc.php</kbd> file needs to be writable, otherwise an error will appear. Follow the instructions on the screen if the file permissions need to be changed. If the file does not exist in the <kbd>include/</kbd> directory, then you will need to create an empty text file with the filename <kbd>config.inc.php</kbd>.</li>

			<li><strong>Anonymous Usage Collection</strong><br />
				To assist the development team in serving the ATutor community, you can submit some basic information about the system you are running. All information is private. Though you are encouraged to list the location of your ATutor installation, you may remain anonymous by choosing not to submit the URL to your ATutor server during this step.</li>

			<li><strong>Done!</strong><br />
				ATutor installation has been successful and you may now log-in with your personal account or the administrator account created in Step 3.</li>
		</ol>

<?php require('../common/body_footer.inc.php'); ?>
