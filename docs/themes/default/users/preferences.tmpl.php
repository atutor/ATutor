<?php require(AT_INCLUDE_PATH.'header.inc.php'); ?>
<?php global $_stacks; ?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" name="prefs">

<div class="input-form">

	<div class="row">
		<?php if (defined('AT_ENABLE_CATEGORY_THEMES') && AT_ENABLE_CATEGORY_THEMES): ?>
			<?php echo _AT('themes_disabled'); ?>
		<?php else: ?>
			<label for="seq_icons"><?php echo _AT('theme'); ?></label><br />
				<select name="theme"><?php
							$_themes = get_enabled_themes();
							
							foreach ($_themes as $theme) {
								if (!$theme) {
									continue;
								}

								$theme_fldr = get_folder($theme);

								if ($theme_fldr == $_SESSION['prefs']['PREF_THEME']) {
									echo '<option value="'.$theme_fldr.'" selected="selected">'.$theme.'</option>';
								} else {
									echo '<option value="'.$theme_fldr.'">'.$theme.'</option>';
								}
							}
						?>
					</select>
		<?php endif; ?>
	</div>

	<div class="row">
		<label for="seq"><?php echo _AT('seq_links'); ?></label><br />
		<?php
		/* sequence links preference */
		if ($_SESSION['prefs'][PREF_SEQ] == TOP) {
			$top = ' checked="checked"';
		} else if ($_SESSION['prefs'][PREF_SEQ] == BOTTOM) {
			$bottom = ' checked="checked"';
		} else {
			$both = ' checked="checked"';
		}
		?><input type="radio" name="seq" id="seq_top" value="<?php echo TOP; ?>" <?php echo $top; ?> /><label for="seq_top"><?php echo _AT('top');  ?></label> 
		<input type="radio" name="seq" id="seq_bottom" value="<?php echo BOTTOM; ?>" <?php echo $bottom; ?> /><label for="seq_bottom"><?php echo _AT('bottom');  ?></label> 
		<input type="radio" name="seq" id="seq_both" value="<?php echo BOTH; ?>" <?php echo $neither; ?> /><label for="seq_both"><?php echo _AT('top_bottom');  ?></label>
	</div>

	<div class="row">
		<label for="mnot"><?php echo _AT('inbox_notification'); ?></label><br />
		<?php
			$yes = '';
			$no  = '';
			if ($this->notify == 1) {
				$yes = ' checked="checked"';
			} else {
				$no  = ' checked="checked"';
			}
		?>
		<input type="radio" name="mnot" id="mnot_yes" value="1" <?php echo $yes; ?> /><label for="mnot_yes"><?php echo _AT('enable'); ?></label> 
		<input type="radio" name="mnot" id="mnot_no" value="0" <?php echo $no; ?> /><label for="mnot_no"><?php echo _AT('disable'); ?></label>		
	</div>

	<div class="row">
		<label for="numbering"><?php echo _AT('show_numbers');  ?></label><br />
		<?php
			$num = '';  $num2 = '';
			if ($_SESSION['prefs'][PREF_NUMBERING] == 1) {
				$num = ' checked="checked"';
			} else {
				$num2 = ' checked="checked"';
			}
			?><input type="radio" name ="numbering" id="num_en" value="1" <?php echo $num; ?> /><label for="num_en"><?php echo _AT('enable');  ?></label> 
			<input type="radio" name ="numbering" id="num_dis" value="0" <?php echo $num2; ?> /><label for="num_dis"><?php echo _AT('disable');  ?></label>
	</div>

	<div class="row">
		<label for="use_help"><?php echo _AT('help'); ?></label><br />
		<?php
			$num = '';  $num2 = '';
			if ($_SESSION['prefs'][PREF_HELP] == 1) {
				$num = ' checked="checked"';
			} else {
				$num2 = ' checked="checked"';
			}
			?><input type="radio" name ="use_help" id="help_en" value="1" <?php echo $num; ?> /><label for="help_en"><?php echo _AT('enable');  ?></label> 
			<input type="radio" name ="use_help" id="help_dis" value="0" <?php echo $num2; ?> /><label for="help_dis"><?php echo _AT('disable');  ?></label>
	</div>

	<div class="row">
		<label for="use_mini_help"><?php echo _AT('show_mini_help'); ?></label><br />
		<?php
			$num = '';  $num2 = '';
			if ($_SESSION['prefs'][PREF_MINI_HELP] == 1) {
				$num = ' checked="checked"';
			} else {
				$num2 = ' checked="checked"';
			}
			?><input type="radio" name ="use_mini_help" id="mhelp_en" value="1" <?php echo $num; ?> /><label for="mhelp_en"><?php echo _AT('enable');  ?></label> 
			<input type="radio" name ="use_mini_help" id="mhelp_dis" value="0" <?php echo $num2; ?> /><label for="mhelp_dis"><?php echo _AT('disable');  ?></label>
	</div>

	<div class="row">
		<label for="use_jump_redirect"><?php echo _AT('jump_redirect');  ?></label><br />
		<?php
			$num = '';  $num2 = '';
			if ($_SESSION['prefs'][PREF_JUMP_REDIRECT] == 1) {
				$num = ' checked="checked"';
			} else {
				$num2 = ' checked="checked"';
			}
			?><input type="radio" name ="use_jump_redirect" id="jump_en" value="1" <?php echo $num; ?> /><label for="jump_en"><?php echo _AT('enable');  ?></label> 
			<input type="radio" name ="use_jump_redirect" id="jump_dis" value="0" <?php echo $num2; ?> /><label for="jump_dis"><?php echo _AT('disable');  ?></label>
	</div>

	<!--div class="row">
		<label for="seq_icons"><?php echo _AT('menus'); ?></label><br />
		<?php
			$num_stack = count($_stacks);

			for ($i = 0; $i< 8; $i++) {
				echo '<select name="stack'.$i.'">'."\n";
				echo '<option value="">'._AT('empty').'</option>'."\n";
				for ($j = 0; $j<$num_stack; $j++) {
					echo '<option value="'.$j.'"';
					if (isset($_SESSION['prefs'][PREF_STACK][$i]) && ($j == $_SESSION[prefs][PREF_STACK][$i])) {
						echo ' selected="selected"';
					}
					echo '>'._AT($_stacks[$j]['file']).'</option>'."\n";
				}
				echo '</select>'."\n";
				echo '<br />'; 
			} ?>
	</div>-->

	<div class="row">
		<label for="auto"><?php echo _AT('auto_login1');  ?></label><br /><?php
			if ( ($_COOKIE['ATLogin'] != '') && ($_COOKIE['ATPass'] != '') ) {
				$auto_en = 'checked="checked"';
			} else {
				$auto_dis = 'checked="checked"';
			}
		?><input type="radio" name ="auto" id="auto_en" value="enable" <?php echo $auto_en; ?> /><label for="auto_en"><?php echo _AT('enable');  ?></label> 
		<input type="radio" name ="auto" id="auto_dis" value="disable" <?php echo $auto_dis; ?> /><label for="auto_dis"><?php echo _AT('disable');  ?></label>
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('apply'); ?>" accesskey="s" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>