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
// $Id$
define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

require(AT_INCLUDE_PATH.'../mods/_standard/chat/lib/chat.inc.php');

$myPrefs = getPrefs($_SESSION['login']);
writePrefs($myPrefs, $_SESSION['login']);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN"
   "http://www.w3.org/TR/html4/frameset.dtd">
<html lang="<?php echo $myLang->getCode(); ?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $myLang->getCharacterSet(); ?>">
	<meta http-equiv="Pragma" content="no-cache">
	<title>ATutor AChat</title>
</head>

<?php
	if ($myPrefs['bingFlag'] > 0 && $myPrefs['refresh'] == 'manual') {
		//makeBingFile($chatID);
?>
	<frameset cols="*,300" frameborder="0">
		<frame src="display.php?firstLoginFlag=<?php echo $_GET['firstLoginFlag']; ?>" name="display" title="Message Display" frameborder="0">
		<frameset rows="*,1">
			<frame src="options.php" name="options" title="Chat Options" frameborder="0">
			<frame src="bings/taras.php" name="bing" title="Hidden Bing Frame" frameborder="0">
		</frameset>
  <noframes>
      <p><?php echo _AT('frame_contains'); ?><br />
	  * <a href="display.php?firstLoginFlag=<?php echo $_GET['firstLoginFlag']; ?>"><?php echo _AT('chat_messages') ?></a>
	  * <a href="options.php"><?php echo _AT('chat_options'); ?></a>
	  * <a href="poster.php"><?php echo _AT('chat_compose_message'); ?></a>
	  </p>
  </noframes>
	</frameset>
<?php
	} else if ($myPrefs['refresh'] == 'manual') {
?>
	<frameset cols="*,300" frameborder="0">
		<frame src="display.php?firstLoginFlag=<?php echo $_GET['firstLoginFlag']; ?>" name="display" title="Message Display and Poster" frameborder="0">
		<frame src="options.php" name="options" title="<?php echo _AT('chat_options'); ?>" frameborder="0" />
	<noframes>
      <p><?php echo _AT('frame_contains'); ?><br />
	  * <a href="display.php?firstLoginFlag=<?php echo $_GET['firstLoginFlag']; ?>"><?php echo _AT('chat_messages') ?></a>
	  * <a href="options.php"><?php echo _AT('chat_options'); ?></a>
	  * <a href="poster.php"><?php echo _AT('chat_compose_message'); ?></a>
	  </p>
  </noframes>
	</frameset>
<?php
	} else {
?>
	<frameset cols="*,300" frameborder="0">
		<frameset rows="*,120">
			<frame src="display.php?firstLoginFlag=<?php echo $_GET['firstLoginFlag']; ?>" name="display" title="Message Display" frameborder="0" marginwidth="0" marginheight="0">
			<frame src="poster.php" name="compose" title="Message Poster" frameborder="0" marginwidth="0" marginheight="0">
		</frameset>
		<frame src="options.php" name="options" title="<?php echo _AT('chat_options'); ?>" frameborder="0" marginwidth="0" marginheight="0">
			<noframes>
      <p><?php echo _AT('frame_contains'); ?><br />
	  * <a href="display.php?firstLoginFlag=<?php echo $_GET['firstLoginFlag']; ?>"><?php echo _AT('chat_messages') ?></a>
	  * <a href="options.php"><?php echo _AT('chat_options'); ?></a>
	  * <a href="poster.php"><?php echo _AT('chat_compose_message'); ?></a>
	  </p>
  </noframes>
	</frameset>
<?php
	}
?>
</html>
