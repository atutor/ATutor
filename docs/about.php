<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/

$section = 'users';
$page    = 'about';
$_public	= true;
define('AT_INCLUDE_PATH', 'include/');
require(AT_INCLUDE_PATH.'/vitals.inc.php');
require(AT_INCLUDE_PATH.'basic_html/header.php');

unset($_SESSION['member_id']);
unset($_SESSION['valid_user']);
unset($_SESSION['login']);
unset($_SESSION['is_admin']);
unset($_SESSION['course_id']);
unset($_SESSION['is_guest']);

?>
<h3><?php echo _AT('about_atutor'); ?></h3><br />

	<table border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td valign="top"><img src="images/ss.gif" height="198" width="254" alt="ATutor screen shot"/></td>
		<td><img src="images/clr.gif" height="1" width="1" alt="" />			
		<p><strong><em>ATutor</em></strong> is an Open Source Web-based <strong>Learning Content Management System (LCMS)</strong> designed with accessibility and adaptability in mind. Administrators can install or update ATutor in minutes. Educators can quickly assemble, package, and redistribute Web-based instructional content, and conduct their courses online. Students learn in an adaptive learning environment.</p>
		<p>Learn more about <a href="http://atutor.ca/index.php">ATutor</a> by browsing the following links:

		<ul>
		<li><a href="http://atutor.ca/atutor/docs/howto.php">ATutor HowTo Course</a> - Learn how to use ATutor in this instructional course</li>
		<li><a href="http://atutor.ca/atutor/docs/index.php">FAQs</a> - Frequently asked questions and answers</li>
		<li><a href="http://atutor.ca/forums/index.php">Support Forums</a> - Post to the user forums</li>
		<li><a href="http://atutor.ca/services/index.php">Support Services</a> - If you need our help, support is available</li>		
		<li><a href="http://atutor.ca/atutor/translate/index.php">Translation</a> - Download language packs, become a translator</li>


		<li><a href="http://atutor.ca/services/licensing.php">Licensing</a> - ATutor software is available for free under certain terms</li>
		<li><a href="http://atutor.ca/atutor/download.php">Download ATutor</a> - All of the system software required to run ATutor can be downloaded here</li>

		</ul></p><br /><br />
		</td>
	</tr>
	</table>

<?php
	require (AT_INCLUDE_PATH.'basic_html/footer.php'); 
?>