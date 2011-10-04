<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca                                                     */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

if (isset($_GET['back'])) {
	header('Location: '.url_rewrite('mods/_standard/chat/index.php', AT_PRETTY_URL_IS_HEADER));
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

@readfile(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/tran/'.$_GET['t'].'.html');
?>

</table>
<br />

<form method="get" action="mods/_standard/chat/view_transcript.php">
	<input type="submit" value="<?php echo _AT('back'); ?>" name="back" class="button" />
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>