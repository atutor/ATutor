<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2008                                      */
/* Written by Greg Gay, Joel Kronenberg & Chris Ridpath         */
/* Inclusive Design Institute                                   */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

require(AT_INCLUDE_PATH.'header.inc.php');

$sql = "SELECT * FROM %sreading_list WHERE course_id=%d ORDER BY date_start";
$rows_readings = queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id']));

if(count($rows_readings) > 0){
    foreach($rows_readings as $row_reading){
    $id = $row_reading['resource_id']; 

    $sql = "SELECT * FROM %sexternal_resources WHERE course_id=%d AND resource_id=%d";
    $row = queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id'], $id), TRUE);
    if(count($row) > 0){
        $row['type']		= intval($row['type']);
        $row['title']		= AT_print($row['title'], 'input.text');
        $row['author']		= AT_print($row['author'], 'input.text');
        $row['publisher']	= AT_print($row['publisher'], 'input.text');
        $row['date']		= AT_print($row['date'], 'input.text');
        $row['comments']	= AT_print($row['comments'], 'input.text');

        if ($row['type'] == RL_TYPE_BOOK): ?>
        <div class="input-form">
            <p><?php  echo _AT('title'). ": <strong>". $row['title']. "</strong>"; ?><br/>
                <?php  echo _AT('rl_type_of_resource'). ": ". _AT($_rl_types[$row['type']]); ?><br/>
                <?php  echo _AT('author'). ": ". $row['author']; ?><br/>
                <?php  echo _AT('rl_publisher'). ": ". $row['publisher']; ?><br/>
                <?php  echo _AT('date'). ": ". $row['date']; ?><br/>
                <?php  echo _AT('rl_isbn_number'). ": ". $row['id']; ?><br/>
                <?php  echo _AT('comment'). ": ". $row['comments']; ?>
            </p>
        </div>
    <?php elseif ($row['type'] == RL_TYPE_URL): ?>
        <div class="input-form">	
            <p><?php  echo _AT('title'). ": <strong>". $row['title']. "</strong>"; ?><br/>
                <?php  echo _AT('rl_type_of_resource'). ": ". _AT($_rl_types[$row['type']]); ?><br/>
                <?php echo _AT('location'). ": " ?><a href="<?php echo $row['url']?>"><?php echo $row['url']; ?></a><br/>
                <?php  echo _AT('author'). ": ". $row['author']; ?><br/>
                <?php  echo _AT('comment'). ": ". $row['comments']; ?>
                </p>
        </div>
    <?php elseif ($row['type'] == RL_TYPE_HANDOUT): ?>
        <div class="input-form">	
            <p><?php  echo _AT('title'). ": <strong>". $row['title']. "</strong>"; ?><br/>
                <?php  echo _AT('rl_type_of_resource'). ": ". _AT($_rl_types[$row['type']]); ?><br/>
                <?php  echo _AT('author'). ": ". $row['author']; ?><br/>
                <?php  echo _AT('date'). ": ". $row['date']; ?><br/>
                <?php  echo _AT('comment'). ": ". $row['comments']; ?>
            </p>
        </div>
    <?php elseif ($row['type'] == RL_TYPE_AV): ?>
        <div class="input-form">	
            <p><?php  echo _AT('title'). ": <strong>". $row['title']. "</strong>" ; ?><br/>
                <?php  echo _AT('rl_type_of_resource'). ": ". _AT($_rl_types[$row['type']]); ?><br/>
                <?php  echo _AT('author'). ": ". $row['author']; ?><br />
                <?php  echo _AT('date'). ": ". $row['date']; ?><br/>
                <?php  echo _AT('comment'). ": ". $row['comments']; ?>
            </p>
        </div>
    <?php elseif ($row['type'] == RL_TYPE_FILE): ?>
        <div class="input-form">	
            <p><?php  echo _AT('title'). ": <strong>". $row['title']. "</strong>"; ?><br/>
                <?php  echo _AT('rl_type_of_resource'). ": ". _AT($_rl_types[$row['type']]); ?><br/>
                <?php  echo _AT('author'). ": ". $row['author']; ?><br/>
                <?php  echo _AT('rl_publisher'). ": ". $row['publisher']; ?><br/>
                <?php  echo _AT('date'). ": ". $row['date']; ?><br/>
                <?php  echo _AT('id'). ": ". $row['id']; ?><br/>
                <?php  echo _AT('comment'). ": ". $row['comments']; ?>
            </p>
        </div>
    <?php endif;
    }
?>
<?php } // END FOREACH ?>
<?php } else { ?>
	<table  class="data" style="width: 95%;"><tr>
		<td colspan="3"><strong><?php echo _AT('none_found'); ?></strong></td>
	</tr></table>
<?php }  ?>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>