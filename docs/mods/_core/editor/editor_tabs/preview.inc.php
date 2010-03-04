<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: preview.inc.php 7208 2008-01-09 16:07:24Z greg $

if (!defined('AT_INCLUDE_PATH')) { exit; }

?>
	<div class="row">
	<?php 
	
		echo '<h2>'.AT_print($stripslashes($_POST['title']), 'content.title').'</h2>';

		if ($_POST['formatting'] === '2') {
            if (isValidURL($_POST['weblink_text']) === false) {
                $msg->addError(array('INVALID_INPUT', _AT('weblink')));
                $msg->printErrors();
            } else {
                  echo format_content($stripslashes($_POST['weblink_text']), $_POST['formatting']);
            }
        } else {
            echo format_content($stripslashes($_POST['body_text']), $_POST['formatting'], $_POST['glossary_defs']);
        }
    ?>		
	</div>
