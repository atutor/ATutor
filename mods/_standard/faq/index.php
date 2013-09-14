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
// $Id$
define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
require (AT_INCLUDE_PATH.'header.inc.php');

$counter = 1;

$sql	 = "SELECT name, topic_id FROM %sfaq_topics WHERE course_id=%d ORDER BY name";
$rows_topics  = queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id']));
?>

<?php 
if(count($rows_topics) > 0):  ?>
	<ul style="list-style: none;">
		<?php 
		foreach($rows_topics as $row){ ?>
			<li style="font-weight: bold; margin-bottom: 10px;">
				<?php echo AT_print($row['name'], 'faqs.topic'); ?>
				<?php 
					$entry_sql = "SELECT * FROM %sfaq_entries WHERE topic_id=%d ORDER BY question";
					$rows_entries = queryDB($entry_sql, array(TABLE_PREFIX, $row['topic_id']));
                if($rows_entries > 0){ ?>
				<ol start="<?php echo $counter; ?>">
					<?php  foreach($rows_entries as $entry_row){ ?>
						<li style="font-weight: normal">
							<h3><?php echo AT_print($entry_row['question'], 'faqs.question'); ?></h3>
							<p><?php echo AT_print($entry_row['answer'], 'faqs.answer');?></p>
						</li>
						<?php $counter++; ?>
					<?php }  ?>
				</ol>
				<?php } else { ?>
					<p style="padding-left: 20px; padding-top:3px; font-weight:normal;"><?php echo _AT('no_questions');  ?></p>
				<?php } ?>
			</li>
		<?php }  ?>
	</ul>
<?php else: ?>
	<?php echo _AT('none_found'); ?>
<?php endif; ?>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>