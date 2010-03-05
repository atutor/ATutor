<fieldset>
<legend><strong><?php echo _AT("atutor_settings"); ?></strong> </legend>  
	<div class="row">
		<?php if (defined('AT_ENABLE_CATEGORY_THEMES') && AT_ENABLE_CATEGORY_THEMES): ?>
			<?php echo _AT('themes_disabled'); ?>
		<?php else: ?>
			<label for="theme"><?php echo _AT('theme'); ?></label><br />
				<select name="theme" id="theme"><?php
					if (isset($_POST['theme']))
						$selected_theme = $_POST['theme'];
					else
						$selected_theme = $_SESSION['prefs']['PREF_THEME'];
						
					$_themes = get_enabled_themes();
					
					foreach ($_themes as $theme) {
						if (!$theme) {
							continue;
						}

						$theme_fldr = get_folder($theme);

						if ($theme_fldr == $selected_theme) {
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
		<?php echo _AT('time_zone');  ?><br />
		
		
		<?php
		// Replace this hack to use the PHP timezone functions when the PHP requirement is raised to 5.2
		global $utc_timezones; // set in include/lib/constants.inc.php
		$local_offset = (((date(Z)/3600)+$_config['time_zone']));
		echo '<select name="time_zone">';	
			echo '<option value="0">'._AT('none').'</option>';
		foreach ($utc_timezones as $zone => $offset){
			if(($offset[1]-$local_offset) == $_SESSION['prefs']['PREF_TIMEZONE']){
			echo '<option value="'.($offset[1]-$local_offset).'" selected="selected">'.$offset[0].'</option>';
			}else{
			echo '<option value="'.($offset[1]-$local_offset).'">'.$offset[0].'</option>';

			}
		}
		echo "</select>";
		// end of hack
		echo "&nbsp;".AT_date(_AT('server_date_format'), '', AT_DATE_MYSQL_DATETIME);
		?>
	</div>
	
	<div class="row">
		<?php echo _AT('inbox_notification'); ?><br />
		<?php
			$yes = $no  = '';
			
			if (isset($_POST["mnot"]))
				$selected_mnot = $_POST["mnot"];
			else
				$selected_mnot = $this->notify;
				
			if ($selected_mnot == 1) {
				$yes = ' checked="checked"';
			} else {
				$no  = ' checked="checked"';
			}
		?>
		<input type="radio" name="mnot" id="mnot_yes" value="1" <?php echo $yes; ?> /><label for="mnot_yes"><?php echo _AT('enable'); ?></label> 
		<input type="radio" name="mnot" id="mnot_no" value="0" <?php echo $no; ?> /><label for="mnot_no"><?php echo _AT('disable'); ?></label>		
	</div>

	<div class="row">
		<?php echo _AT('show_numbers');  ?><br />
		<?php
			if (isset($_POST['numbering']))
				$selected_numbering = $_POST['numbering'];
			else
				$selected_numbering = $_SESSION['prefs']['PREF_NUMBERING'];
				
			$num = $num2 = '';
			if ($selected_numbering == 1) {
				$num = ' checked="checked"';
			} else {
				$num2 = ' checked="checked"';
			}
			?><input type="radio" name ="numbering" id="num_en" value="1" <?php echo $num; ?> /><label for="num_en"><?php echo _AT('enable');  ?></label> 
			<input type="radio" name ="numbering" id="num_dis" value="0" <?php echo $num2; ?> /><label for="num_dis"><?php echo _AT('disable');  ?></label>
	</div>

	<div class="row">
		<?php echo _AT('jump_redirect'); ?><br />
		<?php
			if (isset($_POST['use_jump_redirect']))
				$selected_numbering = $_POST['use_jump_redirect'];
			else
				$selected_numbering = $_SESSION['prefs']['PREF_JUMP_REDIRECT'];
				
			$num = $num2 = '';
			if ($selected_numbering == 1) {
				$num = ' checked="checked"';
			} else {
				$num2 = ' checked="checked"';
			}
			?><input type="radio" name ="use_jump_redirect" id="jump_en" value="1" <?php echo $num; ?> /><label for="jump_en"><?php echo _AT('enable');  ?></label> 
			<input type="radio" name ="use_jump_redirect" id="jump_dis" value="0" <?php echo $num2; ?> /><label for="jump_dis"><?php echo _AT('disable');  ?></label>
	</div>

	<div class="row">
		<?php echo _AT('auto_login1');  ?><br /><?php
			$auto_en = $auto_dis = '';

			// Check flag $is_auto_login instead of session vars !empty($_SESSION['ATLogin']) is because 
			// the set cookies are only accessible at the next page reload
			if ( $this->is_auto_login == "enable") {
				$auto_en = 'checked="checked"';
			} else {
				$auto_dis = 'checked="checked"';
			}
		?><input type="radio" name ="auto" id="auto_en" value="enable" <?php echo $auto_en; ?> /><label for="auto_en"><?php echo _AT('enable');  ?></label> 
		<input type="radio" name ="auto" id="auto_dis" value="disable" <?php echo $auto_dis; ?> /><label for="auto_dis"><?php echo _AT('disable');  ?></label>
	</div>

	<div class="row">
		<?php echo _AT('form_focus');  ?><br />
		<?php
			if (isset($_POST['form_focus']))
				$selected_form_focus = $_POST['form_focus'];
			else
				$selected_form_focus = $_SESSION['prefs']['PREF_FORM_FOCUS'];
				
			$num = $num2 = '';
			if ($selected_form_focus == 1) {
				$num = ' checked="checked"';
			} else {
				$num2 = ' checked="checked"';
			}
			?><input type="radio" name ="form_focus" id="focus_on" value="1" <?php echo $num; ?> /><label for="focus_on"><?php echo _AT('enable');  ?></label> 
			<input type="radio" name ="form_focus" id="focus_off" value="0" <?php echo $num2; ?> /><label for="focus_off"><?php echo _AT('disable');  ?></label>
	</div>

	<div class="row">
		<?php echo _AT('show_guide');  ?><br />
		<?php
			if (isset($_POST['show_guide']))
				$selected_show_guide = $_POST['show_guide'];
			else if (isset($_SESSION['prefs']['PREF_SHOW_GUIDE']))
				$selected_show_guide = $_SESSION['prefs']['PREF_SHOW_GUIDE'];
			else
				$selected_show_guide = 1;
				
			$num = $num2 = '';
			if ($selected_show_guide == 1) {
				$num = ' checked="checked"';
			} else {
				$num2 = ' checked="checked"';
			}
			?><input type="radio" name ="show_guide" id="show_guide_on" value="1" <?php echo $num; ?> /><label for="show_guide_on"><?php echo _AT('enable');  ?></label> 
			<input type="radio" name ="show_guide" id="show_guide_off" value="0" <?php echo $num2; ?> /><label for="show_guide_off"><?php echo _AT('disable');  ?></label>
	</div>

	<div class="row">
		<?php
			if (isset($_POST['content_editor']))
				$selected_content_editor = $_POST['content_editor'];
			else
				$selected_content_editor = $_SESSION['prefs']['PREF_CONTENT_EDITOR'];
				
			$num0 = $num1 = $num2 = '';
			if ($selected_content_editor == 1) {
				$num1 = ' checked="checked"';
			} else if ($_SESSION['prefs']['PREF_CONTENT_EDITOR'] == 2) {
				$num2 = ' checked="checked"';
			} else {
				$num0 = ' checked="checked"';
			}
		?>
		<?php echo _AT('content_editor'); ?><br />
		<input type="radio" name="content_editor" id="ce_0" value="0" <?php echo $num0; ?>/><label for="ce_0"><?php echo _AT('plain_text');?></label>
		<input type="radio" name="content_editor" id="ce_1" value="1" <?php echo $num1; ?>/><label for="ce_1"><?php echo _AT('html'); ?></label>
		<input type="radio" name="content_editor" id="ce_2" value="2" <?php echo $num2; ?>/><label for="ce_2"><?php echo _AT('html') . ' - '. _AT('visual_editor'); ?></label>
	</div>
</fieldset>

