<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* http://atutor.ca                                                     */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id$

if (!defined('AT_INCLUDE_PATH')) { exit; }

print_progress($step);

?>
?>
<script type="text/javascript">
function SetCookie(cookieName,cookieValue,nDays) {
	 var today = new Date();
	 var expire = new Date();
	 if (nDays==null || nDays==0) nDays=1;
	 expire.setTime(today.getTime() + 3600000*24*nDays);
	 document.cookie = cookieName+"="+escape(cookieValue)
					 + ";expires="+expire.toGMTString();
	}
</script>
<script type="text/javascript">
	SetCookie('jstest','0','-5')
</script>
<?php

<p><strong>Congratulations on your installation of ATutor <?php echo $new_version; ?><i>!</i></strong></p>

<p>You may now login using your personal and administrator accounts you created in Step 3.</p>
<?php
if(!isset($AT_SUBSITE)){ ?>
<p>For security reasons once you have confirmed that ATutor has installed correctly, you should delete the <kbd>install/</kbd> directory,
and reset the permissions on the config.inc.php file to read only.</p>

<?php 
}
?>
<p>See the <a href="http://atutor.ca/forums/">Support Forums</a> on <a href="http://atutor.ca">atutor.ca</a> for additional help &amp; support.</p>

<br />

<form method="get" action="../login.php">
	<div align="center">
		<input type="submit" name="submit" value="&raquo; Log-in!" class="button" />
	</div>
</form>