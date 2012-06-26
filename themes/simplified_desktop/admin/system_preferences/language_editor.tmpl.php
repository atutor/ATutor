
<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<div class="input-form">
		<div class="row">
			<h3><?php echo _AT('results_found', $this->num_results); ?></h3>
		</div>

		<div class="row">
			<?php echo _AT('type'); ?><br />
			<input type="radio" name="type" value="template" id="tyte" <?php if ($_GET['type'] == 'template') { echo 'checked="checked"'; } ?> /><label for="tyte"><?php echo _AT('template'); ?></label>
			<input type="radio" name="type" value="feedback" id="tyfe" <?php if ($_GET['type'] == 'feedback') { echo 'checked="checked"'; } ?> /><label for="tyfe"><?php echo _AT('feedback'); ?></label>
		</div>

		<div class="row">
			<input type="checkbox" name="custom" value="1" id="cus" <?php if (isset($_GET['custom'])) { echo 'checked="checked"'; } ?> /><label for="cus"><?php echo _AT('only_show_edited_terms'); ?></label>
		</div>

		<div class="row">
			<label for="search"><?php echo _AT('search'); ?></label><br />
			<input type="text" name="search" id="search" size="40" value="<?php echo htmlspecialchars($_GET['search']); ?>" />
		</div>

		<div class="row buttons">
			<input type="submit" name="filter" value="<?php echo _AT('filter'); ?>" />
			<input type="submit" name="reset_filter" value="<?php echo _AT('reset_filter'); ?>" />
		</div>
	</div>
</form>
<form name="form" method="post">
<div class="input-form">
	<table cellspacing="0" cellpadding="0">
	<tr>
	<td valign="top">
		<?php if ($this->num_results): ?>
			<select size="<?php echo min(max($this->num_results,2), 25); ?>" name="terms" id="terms" onchange="javascript:showtext(this);">
				<?php
					while ($row = mysql_fetch_assoc($result)): 
						if ($strlen($row['text']) > 30) {
							$row['text'] = $substr($row['text'], 0, 28) . '...';
						}
					?>
						<option value="<?php echo $row['term']; ?>"><?php echo htmlspecialchars($row['text']); ?></option>
					<?php endwhile; ?>
			</select>
		<?php else: ?>
			<p><?php echo _AT('none_found'); ?></p>
		<?php endif; ?>
	</td>

	<td valign="top">
		<div class="row">
			<iframe src="mods/_core/languages/language_term.php" frameborder="0" height="430" width="450" marginheight="0" marginwidth="0" name="tran" id="tran"></iframe>
		</div>
	</td>
	</tr>
	</table>
</div>
</form>

			