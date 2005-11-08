<?php

define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_FAQ);


if (isset($_POST['edit'], $_POST['poll'])) {
	header('Location: edit.php?poll_id=' . $_POST['poll']);
	exit;
} else if (isset($_POST['delete'], $_POST['poll'])) { 
	header('Location: delete.php?pid=' . $_POST['poll'] );
	exit;
} else if (!empty($_POST)) {
	$msg->addError('NO_ITEM_SELECTED');
}

require(AT_INCLUDE_PATH.'header.inc.php'); 

$orders = array('asc' => 'desc', 'desc' => 'asc');

if (isset($_GET['asc'])) {
	$order = 'asc';
	$col   = $addslashes($_GET['asc']);
} else if (isset($_GET['desc'])) {
	$order = 'desc';
	$col   = $addslashes($_GET['desc']);
} else {
	// no order set
	$order = 'desc';
	$col   = 'created_date';
}

$counter = 1;
$sql	 = "SELECT name, topic_id FROM ".TABLE_PREFIX."faq_topics WHERE course_id=$_SESSION[course_id] ORDER BY name";
$result  = mysql_query($sql, $db);
?>

<?php if ($row = mysql_fetch_assoc($result)) : ?>
	<ul style="list-style: none;">
		<?php do { ?>
			<li style="font-weight: bold; margin-bottom: 10px;">
				<a href="mods/_standard/faq/edit_topic.php?id=<?php echo $row['topic_id']; ?>"><?php echo _AT('edit'); ?></a> <a href="mods/_standard/faq/delete_topic.php?id=<?php echo $row['topic_id']; ?>"><?php echo _AT('delete'); ?></a> <?php echo $row['name']; ?>
				<ol start="<?php echo $counter; ?>">
					<?php 
						$entry_sql = "SELECT * FROM ".TABLE_PREFIX."faq_entries WHERE topic_id=$row[topic_id] ORDER BY question";
						$entry_result = mysql_query($entry_sql, $db);
					?>
					<?php while ($entry_row = mysql_fetch_assoc($entry_result)): ?>
						<li style="font-weight: normal"><a href="mods/_standard/faq/edit_question.php?id=<?php echo $entry_row['entry_id']; ?>"><?php echo _AT('edit'); ?></a> <a href="mods/_standard/faq/delete_question.php?id=<?php echo $entry_row['entry_id']; ?>"><?php echo _AT('delete'); ?></a> <?php echo $entry_row['question']; ?></li>
						<?php $counter++; ?>
					<?php endwhile; ?>
				</ol>
			</li>
		<?php } while($row = mysql_fetch_assoc($result)); ?>
	</ul>
<?php else: ?>
	<?php echo _AT('none_found'); ?>
<?php endif; ?>

<?php require(AT_INCLUDE_PATH.'footer.inc.php');  ?>