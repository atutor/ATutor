
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">

<div class="input-form">
	<div class="row">
		<h3><?php if (file_exists($this->title_file)) { readfile($this->title_file); } ?></h3>
	</div>

	<div class="row">
		<?php if (file_exists($this->cache_file) && filesize($this->cache_file) > 0) { 
			readfile($this->cache_file); 
			echo '<p><br /><small>'._AT('new_window').'</small></p>';
		} else {
			echo _AT('no_content_avail');
		}?>
	</div>

	<div class="row buttons">
		<input type="submit" name="back" value="<?php echo _AT('back'); ?>" />
	</div>
</div>
</form>
