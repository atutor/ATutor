<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2003 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/


?><table border="0" width="100%" cellspacing="2" cellpadding="2" class="cat2" summary="">
<tr>
	<td class="cat2" nowrap="nowrap"><?php

		if ($page == 'about') {
			echo _AT('home');
		} else {
			echo '<a href="about.php">'._AT('home').'</a>';
		}

		echo '</td><td class="cat2" nowrap="nowrap">';

		if ($page == 'register') {
			echo _AT('register');
		} else {
			echo '<a href="registration.php">'._AT('register').'</a>';
		}

		echo '</td><td class="cat2" nowrap="nowrap">';

		if ($page == 'browse') {
			echo _AT('browse_courses');
		} else {
			echo '<a href="browse.php">'._AT('browse_courses').'</a>';
		}

		echo '</td><td class="cat2" nowrap="nowrap">';

		if ($page == 'login') { 
			echo _AT('login');
		} else {
			echo '<a href="login.php">'._AT('login').'</a>';
		}

		echo '</td><td class="cat2" nowrap="nowrap">';

		if ($page == 'password') { 
			echo _AT('password_reminder');
		} else {
			echo '<a href="password_reminder.php">'._AT('password_reminder').'</a>';
		}

?></td>
</tr>
</table>
