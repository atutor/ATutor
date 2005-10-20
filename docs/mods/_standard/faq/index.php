<?php

define('AT_INCLUDE_PATH', '../../../include/');
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
				<ol start="<?php echo $counter; ?>">
					<?php 
						$entry_sql = "SELECT * FROM ".TABLE_PREFIX."faq_entries WHERE topic_id=$row[topic_id] ORDER BY question";
						$entry_result = mysql_query($entry_sql, $db);
					?>
					<?php while ($entry_row = mysql_fetch_assoc($entry_result)): ?>
						<li style="font-weight: normal">
							<h3><?php echo $entry_row['question']; ?></h3>
							<p><?php echo $entry_row['answer'];?></p>
						</li>
						<?php $counter++; ?>
					<?php endwhile; ?>
				</ol>
				</li>
		<?php } while($row = mysql_fetch_assoc($result)); ?>
	</ul>
<?php else: ?>
	<?php echo _AT('none_found'); ?>
<?php endif; ?>

<?php include('module_backup.php?moo=1'); ?>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>