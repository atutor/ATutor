<fieldset> 
<legend><strong><?php echo _AT("text"); ?></strong> </legend>   

	<div class="row">
		<label for="fontface"><?php echo _AT('font_face'); ?></label><br />
			<select name="fontface" id="fontface" onchange="setPreviewFace()"><?php
				if (isset($_POST['fontface']))
					$selected_ff = $_POST['fontface'];
				else if (isset($_SESSION['prefs']['PREF_FONT_FACE']))
					$selected_ff = $_SESSION['prefs']['PREF_FONT_FACE'];
				else
					$selected_ff = "serif";  // default
?>
				<option value="serif" <?php if ($selected_ff == "serif") echo 'selected="selected"'; ?>><?php echo _AT('serif'); ?></option>   
				<option value="sans-serif" <?php if ($selected_ff == "sans-serif") echo 'selected="selected"'; ?>><?php echo _AT('sans_serif'); ?></option>   
				<option value="monospace" <?php if ($selected_ff == "monospace") echo 'selected="selected"'; ?>><?php echo _AT('monospaced'); ?></option>   
				<option value="cursive" <?php if ($selected_ff == "cursive") echo 'selected="selected"'; ?>><?php echo _AT('cursive'); ?></option>   
				<option value="fantasy" <?php if ($selected_ff == "fantasy") echo 'selected="selected"'; ?>><?php echo _AT('fantasy'); ?></option>   
			</select>
	</div>

	<div class="row">
		<label for="fontsize"><?php echo _AT('font_size'); ?></label><br />
			<select name="fontsize" id="fontsize" onchange="setPreviewSize()"><?php
				$selected_fs = "12";
				
				if (isset($_POST['fontsize']))
					$selected_fs = $_POST['fontsize'];
				else if (isset($_SESSION['prefs']['PREF_FONT_SIZE']))
					$selected_fs = $_SESSION['prefs']['PREF_FONT_SIZE'];
				else
					$selected_fs = "12";   // default to 12pt

				output_fontsize("10", "30", "pt", $selected_fs);
?>
			</select>
	</div>

	<div class="row">
		<label for="fg"><?php echo _AT('fg_colour'); ?></label><br />
			<select name="fg" id="fg" onchange="setPreviewColours()">
<?php
				if (isset($_POST['fg']))
					$selected_fgc = $_POST['fg'];
				else if (isset($_SESSION['prefs']['PREF_FG_COLOUR']))
					$selected_fgc = $_SESSION['prefs']['PREF_FG_COLOUR'];
				else
					$selected_fgc = "000000ff";  // default to black
?>
				<option value="ffffffff" <?php if ($selected_fgc == "ffffffff") echo 'selected="selected"'; ?>><?php echo _AT('white'); ?></option>     
				<option value="000000ff" <?php if ($selected_fgc == "000000ff") echo 'selected="selected"'; ?>><?php echo _AT('black'); ?></option>     
				<option value="ff0000ff" <?php if ($selected_fgc == "ff0000ff") echo 'selected="selected"'; ?>><?php echo _AT('red'); ?></option>     
				<option value="ffff00ff" <?php if ($selected_fgc == "ffff00ff") echo 'selected="selected"'; ?>><?php echo _AT('yellow'); ?></option>     
				<option value="0000ffff" <?php if ($selected_fgc == "0000ffff") echo 'selected="selected"'; ?>><?php echo _AT('blue'); ?></option>     
				<option value="00ff00ff" <?php if ($selected_fgc == "00ff00ff") echo 'selected="selected"'; ?>><?php echo _AT('green'); ?></option>     
				<option value="999999ff" <?php if ($selected_fgc == "999999ff") echo 'selected="selected"'; ?>><?php echo _AT('gray'); ?></option>     
				<option value="ccccccff" <?php if ($selected_fgc == "ccccccff") echo 'selected="selected"'; ?>><?php echo _AT('light_gray'); ?></option>     
				<option value="666666ff" <?php if ($selected_fgc == "666666ff") echo 'selected="selected"'; ?>><?php echo _AT('dark_gray'); ?></option>     
				<option value="ffccccff" <?php if ($selected_fgc == "ffccccff") echo 'selected="selected"'; ?>><?php echo _AT('pink'); ?></option>     
				<option value="00ffffff" <?php if ($selected_fgc == "00ffffff") echo 'selected="selected"'; ?>><?php echo _AT('cyan'); ?></option>     
				<option value="ff00ffff" <?php if ($selected_fgc == "ff00ffff") echo 'selected="selected"'; ?>><?php echo _AT('magenta'); ?></option> 
			</select>
	</div>

	<div class="row">
		<label for="bg"><?php echo _AT('bg_colour'); ?></label><br />
			<select name="bg" id="bg" onchange="setPreviewColours()">
<?php
				if (isset($_POST['bg']))
					$selected_bgc = $_POST['bg'];
				else if (isset($_SESSION['prefs']['PREF_BG_COLOUR']))
					$selected_bgc = $_SESSION['prefs']['PREF_BG_COLOUR'];
				else
					$selected_bgc = "ffffffff";  // default to white
?>
				<option value="ffffffff" <?php if ($selected_bgc == "ffffffff") echo 'selected="selected"'; ?>><?php echo _AT('white'); ?></option>     
				<option value="000000ff" <?php if ($selected_bgc == "000000ff") echo 'selected="selected"'; ?>><?php echo _AT('black'); ?></option>     
				<option value="ff0000ff" <?php if ($selected_bgc == "ff0000ff") echo 'selected="selected"'; ?>><?php echo _AT('red'); ?></option>     
				<option value="ffff00ff" <?php if ($selected_bgc == "ffff00ff") echo 'selected="selected"'; ?>><?php echo _AT('yellow'); ?></option>     
				<option value="0000ffff" <?php if ($selected_bgc == "0000ffff") echo 'selected="selected"'; ?>><?php echo _AT('blue'); ?></option>     
				<option value="00ff00ff" <?php if ($selected_bgc == "00ff00ff") echo 'selected="selected"'; ?>><?php echo _AT('green'); ?></option>     
				<option value="999999ff" <?php if ($selected_bgc == "999999ff") echo 'selected="selected"'; ?>><?php echo _AT('gray'); ?></option>     
				<option value="ccccccff" <?php if ($selected_bgc == "ccccccff") echo 'selected="selected"'; ?>><?php echo _AT('light_gray'); ?></option>     
				<option value="666666ff" <?php if ($selected_bgc == "666666ff") echo 'selected="selected"'; ?>><?php echo _AT('dark_gray'); ?></option>     
				<option value="ffccccff" <?php if ($selected_bgc == "ffccccff") echo 'selected="selected"'; ?>><?php echo _AT('pink'); ?></option>     
				<option value="00ffffff" <?php if ($selected_bgc == "00ffffff") echo 'selected="selected"'; ?>><?php echo _AT('cyan'); ?></option>     
				<option value="ff00ffff" <?php if ($selected_bgc == "ff00ffff") echo 'selected="selected"'; ?>><?php echo _AT('magenta'); ?></option> 
			</select>
	</div>

	<div class="row">
		<label for="hl"><?php echo _AT('hl_colour'); ?></label><br />
			<select name="hl" id="hl" onchange="setPreviewColours()">
<?php
				if (isset($_POST['hl']))
					$selected_hlc = $_POST['hl'];
				else if (isset($_SESSION['prefs']['PREF_HL_COLOUR']))
					$selected_hlc = $_SESSION['prefs']['PREF_HL_COLOUR'];
				else
					$selected_hlc = "ff0000ff";  // default to red
?>
				<option value="ffffffff" <?php if ($selected_hlc == "ffffffff") echo 'selected="selected"'; ?>><?php echo _AT('white'); ?></option>     
				<option value="000000ff" <?php if ($selected_hlc == "000000ff") echo 'selected="selected"'; ?>><?php echo _AT('black'); ?></option>     
				<option value="ff0000ff" <?php if ($selected_hlc == "ff0000ff") echo 'selected="selected"'; ?>><?php echo _AT('red'); ?></option>     
				<option value="ffff00ff" <?php if ($selected_hlc == "ffff00ff") echo 'selected="selected"'; ?>><?php echo _AT('yellow'); ?></option>     
				<option value="0000ffff" <?php if ($selected_hlc == "0000ffff") echo 'selected="selected"'; ?>><?php echo _AT('blue'); ?></option>     
				<option value="00ff00ff" <?php if ($selected_hlc == "00ff00ff") echo 'selected="selected"'; ?>><?php echo _AT('green'); ?></option>     
				<option value="999999ff" <?php if ($selected_hlc == "999999ff") echo 'selected="selected"'; ?>><?php echo _AT('gray'); ?></option>     
				<option value="ccccccff" <?php if ($selected_hlc == "ccccccff") echo 'selected="selected"'; ?>><?php echo _AT('light_gray'); ?></option>     
				<option value="666666ff" <?php if ($selected_hlc == "666666ff") echo 'selected="selected"'; ?>><?php echo _AT('dark_gray'); ?></option>     
				<option value="ffccccff" <?php if ($selected_hlc == "ffccccff") echo 'selected="selected"'; ?>><?php echo _AT('pink'); ?></option>     
				<option value="00ffffff" <?php if ($selected_hlc == "00ffffff") echo 'selected="selected"'; ?>><?php echo _AT('cyan'); ?></option>     
				<option value="ff00ffff" <?php if ($selected_hlc == "ff00ffff") echo 'selected="selected"'; ?>><?php echo _AT('magenta'); ?></option> 
			</select>
	</div>

	<div class="row">
		<?php echo _AT('invert_colour_selection'); ?><br />
		<?php
			$yes = $no  = '';
			
			if (isset($_POST["invert_colour_selection"]))
				$selected_ics = $_POST["invert_colour_selection"];
			else
				$selected_ics = $_SESSION['prefs']['PREF_INVERT_COLOUR_SELECTION'];
				
			if ($selected_ics == 1) {
				$yes = ' checked="checked"';
			} else {
				$no  = ' checked="checked"';
			}
?>
		<input type="radio" name="invert_colour_selection" id="ics_yes" value="1" <?php echo $yes; ?> /><label for="ics_yes"><?php echo _AT('yes'); ?></label> 
		<input type="radio" name="invert_colour_selection" id="ics_no" value="0" <?php echo $no; ?> /><label for="ics_no"><?php echo _AT('no'); ?></label>		
	</div>

	<div id="previewArea" style="padding: 0em; border-bottom-width: 0px; margin-left: auto; margin-right: auto; font-weight: normal; width: 80%;"> 
		<div id="previewText" style="border: 2px solid rgb(0, 0, 0); padding: 2em; width: 80%; color: rgb(255, 255, 255); background-color: rgb(0, 0, 0); font-family: monospace;">     Sample 
			<span id="highlightedPreview" style="background-color: rgb(0, 255, 0); font-family: monospace;">Highlighted</span> Text  
		</div> 
	</div>
</fieldset>

<fieldset> <legend><strong><?php echo _AT("avoid_red"); ?></strong></legend>  

	<div class="row">
		<?php echo _AT('avoid_red'); ?><br />
		<?php
			$yes = $no  = '';
			
			if (isset($_POST["avoid_red"]))
				$selected_ar = $_POST["avoid_red"];
			else
				$selected_ar = $_SESSION['prefs']['PREF_AVOID_RED'];
				
			if ($selected_ar == 1) {
				$yes = ' checked="checked"';
			} else {
				$no  = ' checked="checked"';
			}
?>
		<input type="radio" name="avoid_red" id="ar_yes" value="1" <?php echo $yes; ?> /><label for="ar_yes"><?php echo _AT('yes'); ?></label> 
		<input type="radio" name="avoid_red" id="ar_no" value="0" <?php echo $no; ?> /><label for="ar_no"><?php echo _AT('no'); ?></label>		
	</div>

	<div class="row">
		<?php echo _AT('avoid_red_green'); ?><br />
		<?php
			$yes = $no  = '';
			
			if (isset($_POST["avoid_red_green"]))
				$selected_arg = $_POST["avoid_red_green"];
			else
				$selected_arg = $_SESSION['prefs']['PREF_AVOID_RED_GREEN'];
				
			if ($selected_arg == 1) {
				$yes = ' checked="checked"';
			} else {
				$no  = ' checked="checked"';
			}
?>
		<input type="radio" name="avoid_red_green" id="arg_yes" value="1" <?php echo $yes; ?> /><label for="arg_yes"><?php echo _AT('yes'); ?></label> 
		<input type="radio" name="avoid_red_green" id="arg_no" value="0" <?php echo $no; ?> /><label for="arg_no"><?php echo _AT('no'); ?></label>		
	</div>

	<div class="row">
		<?php echo _AT('avoid_blue_yellow'); ?><br />
		<?php
			$yes = $no  = '';
			
			if (isset($_POST["avoid_blue_yellow"]))
				$selected_aby = $_POST["avoid_blue_yellow"];
			else
				$selected_aby = $_SESSION['prefs']['PREF_AVOID_BLUE_YELLOW'];
				
			if ($selected_aby == 1) {
				$yes = ' checked="checked"';
			} else {
				$no  = ' checked="checked"';
			}
?>
		<input type="radio" name="avoid_blue_yellow" id="aby_yes" value="1" <?php echo $yes; ?> /><label for="aby_yes"><?php echo _AT('yes'); ?></label> 
		<input type="radio" name="avoid_blue_yellow" id="aby_no" value="0" <?php echo $no; ?> /><label for="aby_no"><?php echo _AT('no'); ?></label>		
	</div>

	<div class="row">
		<?php echo _AT('avoid_green_yellow'); ?><br />
		<?php
			$yes = $no  = '';
			
			if (isset($_POST["avoid_green_yellow"]))
				$selected_agy = $_POST["avoid_green_yellow"];
			else
				$selected_agy = $_SESSION['prefs']['PREF_AVOID_GREEN_YELLOW'];
				
			if ($selected_agy == 1) {
				$yes = ' checked="checked"';
			} else {
				$no  = ' checked="checked"';
			}
?>
		<input type="radio" name="avoid_green_yellow" id="agy_yes" value="1" <?php echo $yes; ?> /><label for="agy_yes"><?php echo _AT('yes'); ?></label> 
		<input type="radio" name="avoid_green_yellow" id="agy_no" value="0" <?php echo $no; ?> /><label for="agy_no"><?php echo _AT('no'); ?></label>		
	</div>

	<div class="row">
		<?php echo _AT('use_max_contrast'); ?><br />
		<?php
			$yes = $no  = '';
			
			if (isset($_POST["use_max_contrast"]))
				$selected_umc = $_POST["use_max_contrast"];
			else
				$selected_umc = $_SESSION['prefs']['PREF_USE_MAX_CONTRAST'];
				
			if ($selected_umc == 1) {
				$yes = ' checked="checked"';
			} else {
				$no  = ' checked="checked"';
			}
?>
		<input type="radio" name="use_max_contrast" id="umc_yes" value="1" <?php echo $yes; ?> /><label for="umc_yes"><?php echo _AT('yes'); ?></label> 
		<input type="radio" name="use_max_contrast" id="umc_no" value="0" <?php echo $no; ?> /><label for="umc_no"><?php echo _AT('no'); ?></label>		
	</div>
</fieldset> 

<fieldset> <legend><strong><?php echo _AT("personal_css"); ?></strong>  </legend>  
	<div class="row">
		<?php echo _AT('upload_personal_css'); ?><br />
		<input id="ss" name="ssURL" size="40" type="file"  />
	</div>
</fieldset>
