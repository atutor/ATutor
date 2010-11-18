<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2010                                      */
/* Inclusive Design Institute                                   */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: tests.inc.php 10197 2010-09-16 16:18:25Z greg $
if (!defined('AT_INCLUDE_PATH')) { exit; }

$cid = intval($_REQUEST['cid']);

/* get a list of all the tools, we have */
$sql    = "SELECT * FROM ".TABLE_PREFIX."basiclti_tools ORDER BY title;";
$toolresult = mysql_query($sql, $db);
$num_tools = mysql_num_rows($toolresult);

//If there are no Tools, don't display anything except a message
if ($num_tools == 0){
        $msg->addInfo('NO_PROXY_TOOLS');
        $msg->printInfos();
        return;
}

// Get the current content item
$sql = "SELECT * FROM ".TABLE_PREFIX."basiclti_content 
		WHERE content_id=$cid";
$contentresult = mysql_query($sql, $db);
$row = mysql_fetch_assoc($contentresult);
// if ( $row ) echo("FOUND"); else echo("NOT");
?>
<div class="row">
        <span style="font-weight:bold"><?php echo _AT('about_content_tools'); ?></span>
</div>

<div class="row">
   <?php echo _AT('bl_choose_tool'); ?><br/>
   <select id="toolid" name="toolid"> 
      <option value="--none--">&nbsp;</option><?php
      while ( $tool = mysql_fetch_assoc($toolresult) ) {
         $selected = "";
         if ( $tool['toolid'] == $row['toolid'] ) {
           $selected = ' selected="yes"';
         }
         echo '<option value="'.$tool['toolid'].'"'.$selected.'>'.$tool['title']."</option>\n";
      } ?>
   </select>
</div>
