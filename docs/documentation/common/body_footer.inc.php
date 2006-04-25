<?php if (!defined('AT_HANDBOOK')) { exit; } ?>
<?php

	// if ATutor config.inc is available, then:
	// read it for DB info and comments_enabling option.
	// else: use local configuration option for DB/comments option

	$enable_user_notes = false;

	$config_location = dirname(__FILE__) . '/../../include/config.inc.php';
	if (is_file($config_location) && is_readable($config_location)) {
		require($config_location);
		if (defined('AT_ENABLE_HANDBOOK_NOTES') && AT_ENABLE_HANDBOOK_NOTES) {
			define('AT_HANDBOOK_DB_USER', DB_USER);

			define('AT_HANDBOOK_DB_PASSWORD', DB_PASSWORD);

			define('AT_HANDBOOK_DB_DATABASE', DB_NAME);

			define('AT_HANDBOOK_DB_PORT', DB_PORT);

			define('AT_HANDBOOK_DB_HOST', DB_HOST);

			define('AT_HANDBOOK_DB_TABLE_PREFIX', TABLE_PREFIX);

			define('AT_HANDBOOK_ENABLE', true);
		}
	} 
	if (!defined('AT_HANDBOOK_ENABLE')) {
		// use local config file
		require(dirname(__FILE__) . '/../config.inc.php');
	}

	if (defined('AT_HANDBOOK_ENABLE') && AT_HANDBOOK_ENABLE) {
		$db = @mysql_connect(AT_HANDBOOK_DB_HOST . ':' . AT_HANDBOOK_DB_PORT, AT_HANDBOOK_DB_USER, AT_HANDBOOK_DB_PASSWORD);
		if (@mysql_select_db(AT_HANDBOOK_DB_DATABASE, $db)) {
			$enable_user_notes = true;
			$sql = "SELECT note_id, date, email, note FROM ".AT_HANDBOOK_DB_TABLE_PREFIX."handbook_notes WHERE section='$section' AND page='$this_page' AND approved=1 ORDER BY date DESC";
			$result = mysql_query($sql, $db);
		}
	}
?>

<?php if ($enable_user_notes): ?>
	<div class="add-note">
		<a href="../add_note.php?<?php echo $section . SEP . 'p='.$this_page; ?>" style="float: right;">+ Add a Note +</a>
		<h3>User Contributed Notes</h3>
	</div>

	<?php if ($result && mysql_num_rows($result) > 0): ?>
		<?php while ($row = mysql_fetch_assoc($result)): ?>
			<div class="note">
				<h5><?php echo $row['date']; ?>
					<?php if (isset($_SESSION['handbook_admin']) && $_SESSION['handbook_admin']): ?>
						<a href="../delete_note.php?<?php echo $section.SEP.'p='.$this_page.SEP.'id='.$row['note_id']; ?>" onclick="return confirm('Are you sure you want to delete this note?');">Delete</a>
					<?php endif; ?>
				</h5>
				<h4><?php echo $row['email'];?></h4>
				<p><?php echo nl2br($row['note']); ?></p>
			</div>
		<?php endwhile; ?>
	<?php else: ?>
		<div class="note">There are no user contributed notes for this page.</div>
	<?php endif; ?>
<?php endif; ?>

<div class="seq">
	<?php if (isset($previous_page)): ?>
		<?php get_text('previous_chapter'); ?>: <a href="<?php echo $rel_path; ?><?php echo $section; ?>/<?php echo $previous_page; ?>?<?php echo $req_lang; ?>" title="<?php echo $_pages[$previous_page]; ?> Alt+,"><?php echo $_pages[$previous_page]; ?></a><br /> 
	<?php endif; ?>

	<?php if (isset($next_page)): ?>
		<?php get_text('next_chapter'); ?>: <a href="<?php echo $rel_path; ?><?php echo $section; ?>/<?php echo $next_page; ?>?<?php echo $req_lang; ?>" title="<?php echo $_pages[$next_page]; ?> Alt+."><?php echo $_pages[$next_page]; ?></a>
	<?php endif; ?>
</div>

<div class="tag">
	This page was last modified <?php echo date("r.", getlastmod()); ?><br />
	All text is available under the terms of the GNU Free Documentation License. 
</div>
</body>
</html>