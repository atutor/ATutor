<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$
define('AT_INCLUDE_PATH', '../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
require (AT_INCLUDE_PATH.'header.inc.php');

$counter = 1;
$sql	 = "SELECT name, topic_id FROM ".TABLE_PREFIX."faq_topics WHERE course_id=$_SESSION[course_id] ORDER BY name";
$result  = mysql_query($sql, $db);
?>

<?php if ($row = mysql_fetch_assoc($result)) : ?>
	<ul style="list-style: none;">
		<?php do { ?>
			<li style="font-weight: bold; margin-bottom: 10px;">
				<?php echo $row['name']; ?>
				<?php 
					$entry_sql = "SELECT * FROM ".TABLE_PREFIX."faq_entries WHERE topic_id=$row[topic_id] ORDER BY question";
					$entry_result = mysql_query($entry_sql, $db);
				if ($entry_row = mysql_fetch_assoc($entry_result)) {?>
				<ol start="<?php echo $counter; ?>">

					<?php do { ?>
						<li style="font-weight: normal">
							<h3><?php echo $entry_row['question']; ?></h3>
							<p><?php echo $entry_row['answer'];?></p>
						</li>
						<?php $counter++; ?>
					<?php } while ($entry_row = mysql_fetch_assoc($entry_result)) ?>
				</ol>
				<?php } else { ?>
					<p style="padding-left: 20px; padding-top:3px; font-weight:normal;"><?php echo _AT('no_questions');  ?></p>
				<?php } ?>
			</li>
		<?php } while($row = mysql_fetch_assoc($result)); ?>
	</ul>
<?php else: ?>
	<?php echo _AT('none_found'); ?>
<?php endif; ?>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>