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

define('AT_INCLUDE_PATH', '../../include/');

$CACHE_DEBUG=0;
require(AT_INCLUDE_PATH.'vitals.inc.php');

require('include/functions.inc.php');
$admin = getAdminSettings();

require(AT_INCLUDE_PATH.'header.inc.php');

?>
<p align="center"><a href="discussions/achat/chat.php?firstLoginFlag=1<?php echo SEP; ?>g=31"><b> <?php echo _AC('enter_chat');  ?></b></a></p><br />
<?php

$instructor = FALSE;

require(AT_INCLUDE_PATH.'html/chat_transcripts.inc.php');

require(AT_INCLUDE_PATH.'footer.inc.php');
?>
