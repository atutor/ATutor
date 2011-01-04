<?php if (!defined('AT_HANDBOOK')) { exit; } ?>
<?php

	// if ATutor config.inc is available, then:
	// read it for DB info and comments_enabling option.
	// else: use local configuration option for DB/comments option

	$enable_user_notes = false;

	$config_location = dirname(__FILE__) . '/../../include/config.inc.php';
	if (is_file($config_location) && is_readable($config_location)) {
		require($config_location);
		if (defined('DB_HOST')) {
		
			$db = @mysql_connect(DB_HOST . ':' . DB_PORT, DB_USER, DB_PASSWORD);
			@mysql_select_db(DB_NAME, $db);

			// check atutor config table to see if handbook notes is enabled.
			$sql    = "SELECT value FROM ".TABLE_PREFIX."config WHERE name='user_notes'";
			$result = @mysql_query($sql, $db);
			if (($row = mysql_fetch_assoc($result)) && $row['value']) {
				define('AT_HANDBOOK_ENABLE', true);
				$enable_user_notes = true;
			} else {
				define('AT_HANDBOOK_ENABLE', false);
			}
			define('AT_HANDBOOK_DB_TABLE_PREFIX', TABLE_PREFIX);
		}
	} 
	if (!defined('AT_HANDBOOK_ENABLE')) {
		// use local config file
		require(dirname(__FILE__) . '/../config.inc.php');
	}

	if (defined('AT_HANDBOOK_ENABLE') && AT_HANDBOOK_ENABLE) {
		if (!$db) {
			$db = @mysql_connect(AT_HANDBOOK_DB_HOST . ':' . AT_HANDBOOK_DB_PORT, AT_HANDBOOK_DB_USER, AT_HANDBOOK_DB_PASSWORD);
			@mysql_select_db(AT_HANDBOOK_DB_DATABASE, $db);
		}
		if ($db) {
			$enable_user_notes = true;
			$sql = "SELECT note_id, date, email, note FROM ".AT_HANDBOOK_DB_TABLE_PREFIX."handbook_notes WHERE section='$section' AND page='$this_page' AND approved=1 ORDER BY date DESC";
			$result = mysql_query($sql, $db);
		}
	}
?>

<?php if ($enable_user_notes): ?>
	<div class="add-note">
		<a href="../add_note.php?<?php echo $section . SEP . 'p='.$this_page; ?>" style="float: right;">+ <?php get_text('add_note'); ?> +</a>
		<h3><?php get_text('user_contributed_notes'); ?></h3>
	</div>

	<?php if ($result && mysql_num_rows($result) > 0): ?>
		<?php while ($row = mysql_fetch_assoc($result)): ?>
			<div class="note">
				<h5><?php echo $row['date']; ?>
					<?php if (isset($_SESSION['handbook_admin']) && $_SESSION['handbook_admin']): ?>
						<a href="../delete_note.php?<?php echo $section.SEP.'p='.$this_page.SEP.'id='.$row['note_id']; ?>" onclick="return confirm('<?php get_text('are_you_sure_delete_note'); ?>');"><?php get_text('delete'); ?></a>
					<?php endif; ?>
				</h5>
				<h4><?php echo $row['email'];?></h4>
				<p><?php echo nl2br($row['note']); ?></p>
			</div>
		<?php endwhile; ?>
	<?php else: ?>
		<div class="note"><?php get_text('no_notes_on_page'); ?></div>
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
	<?php echo htmlspecialchars($lm); ?><br />
	All text is available under the terms of the GNU Free Documentation License. 
</div>
</body>
</html>