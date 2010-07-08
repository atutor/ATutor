<legend><strong><?php echo _AT("text"); ?></strong> </legend>   
<div id="feedback">
<?php echo _AT('prefs_set_display'); ?>
</div>
<div id="defaultfontsize-wrapper">
	<div class="row">
		<input type="hidden" id="defaultfontface" value="Verdana" />
		<label for="fontface"><?php echo _AT('font_face'); ?></label><br />
			<select name="fontface" id="fontface" onchange="setPreviewFace()"><?php
				if (isset($_POST['fontface']))
					$selected_ff = $_POST['fontface'];
				else if (isset($_SESSION['prefs']['PREF_FONT_FACE']))
					$selected_ff = $_SESSION['prefs']['PREF_FONT_FACE'];
				else
					$selected_ff = "";  // default
?>
				<option value="" <?php if ($selected_ff == "") echo 'selected="selected"'; ?>><?php echo _AT('default'); ?></option>   
				<option value="serif" <?php if ($selected_ff == "serif") echo 'selected="selected"'; ?>><?php echo _AT('serif'); ?></option>   
				<option value="sans-serif" <?php if ($selected_ff == "sans-serif") echo 'selected="selected"'; ?>><?php echo _AT('sans_serif'); ?></option>   
				<option value="monospace" <?php if ($selected_ff == "monospace") echo 'selected="selected"'; ?>><?php echo _AT('monospaced'); ?></option>   
				<option value="cursive" <?php if ($selected_ff == "cursive") echo 'selected="selected"'; ?>><?php echo _AT('cursive'); ?></option>   
				<option value="fantasy" <?php if ($selected_ff == "fantasy") echo 'selected="selected"'; ?>><?php echo _AT('fantasy'); ?></option>   
			</select>
	</div>

	<div class="row">
		<input type="hidden" id="defaultfontsize" value="12" />
		<label for="font_times"><?php echo _AT('font_size'); ?></label><br />
			<select name="font_times" id="font_times" onchange="setPreviewSize()">
<?php
				if (isset($_POST['font_times']))
					$selected_fs = $_POST['font_times'];
				else if (isset($_SESSION['prefs']['PREF_FONT_TIMES']))
					$selected_fs = $_SESSION['prefs']['PREF_FONT_TIMES'];
				else
					$selected_fs = "1";   // default

?>
				<option value=".8" <?php if ($selected_fs == ".8") echo 'selected="selected"'; ?>><?php echo _AT('default'); ?></option>     
				<option value="1.5" <?php if ($selected_fs == "1.5") echo 'selected="selected"'; ?>>1.5X</option>     
				<option value="2" <?php if ($selected_fs == "2") echo 'selected="selected"'; ?>>2X</option>     
				<option value="2.5" <?php if ($selected_fs == "2.5") echo 'selected="selected"'; ?>>2.5X</option>     
				<option value="3" <?php if ($selected_fs == "3") echo 'selected="selected"'; ?>>3X</option>     
			</select>
	</div>

	<div class="row">
		<input type="hidden" id="defaultfg" value="000000" />
		<label for="fg"><?php echo _AT('fg_colour'); ?></label><br />
			<select name="fg" id="fg" onchange="setPreviewColours()">
<?php
				if (isset($_POST['fg']))
					$selected_fgc = $_POST['fg'];
				else if (isset($_SESSION['prefs']['PREF_FG_COLOUR']))
					$selected_fgc = $_SESSION['prefs']['PREF_FG_COLOUR'];
				else
					$selected_fgc = "";  // default
?>
				<option value="" <?php if ($selected_fgc == "") echo 'selected="selected"'; ?>><?php echo _AT('default'); ?></option>     
				<option value="FFFFFF" <?php if ($selected_fgc == "FFFFFF") echo 'selected="selected"'; ?>><?php echo _AT('white'); ?></option>     
				<option value="000000" <?php if ($selected_fgc == "000000") echo 'selected="selected"'; ?>><?php echo _AT('black'); ?></option>     
				<option value="FF0000" <?php if ($selected_fgc == "FF0000") echo 'selected="selected"'; ?>><?php echo _AT('red'); ?></option>     
				<option value="FFFF00" <?php if ($selected_fgc == "FFFF00") echo 'selected="selected"'; ?>><?php echo _AT('yellow'); ?></option>     
				<option value="0000FF" <?php if ($selected_fgc == "0000FF") echo 'selected="selected"'; ?>><?php echo _AT('blue'); ?></option>     
				<option value="00FF00" <?php if ($selected_fgc == "00FF00") echo 'selected="selected"'; ?>><?php echo _AT('green'); ?></option>     
				<option value="999999" <?php if ($selected_fgc == "999999") echo 'selected="selected"'; ?>><?php echo _AT('gray'); ?></option>     
				<option value="CCCCCC" <?php if ($selected_fgc == "CCCCCC") echo 'selected="selected"'; ?>><?php echo _AT('light_gray'); ?></option>     
				<option value="666666" <?php if ($selected_fgc == "666666") echo 'selected="selected"'; ?>><?php echo _AT('dark_gray'); ?></option>     
				<option value="FFCCCC" <?php if ($selected_fgc == "FFCCCC") echo 'selected="selected"'; ?>><?php echo _AT('pink'); ?></option>     
				<option value="00FFFF" <?php if ($selected_fgc == "00FFFF") echo 'selected="selected"'; ?>><?php echo _AT('cyan'); ?></option>     
				<option value="FF00FF" <?php if ($selected_fgc == "FF00FF") echo 'selected="selected"'; ?>><?php echo _AT('magenta'); ?></option> 
			</select>
	</div>

	<div class="row">
		<input type="hidden" id="defaultbg" value="FFFFFF" />
		<label for="bg"><?php echo _AT('bg_colour'); ?></label><br />
			<select name="bg" id="bg" onchange="setPreviewColours()">
<?php
				if (isset($_POST['bg']))
					$selected_bgc = $_POST['bg'];
				else if (isset($_SESSION['prefs']['PREF_BG_COLOUR']))
					$selected_bgc = $_SESSION['prefs']['PREF_BG_COLOUR'];
				else
					$selected_bgc = "";  // default
?>
				<option value="" <?php if ($selected_bgc == "") echo 'selected="selected"'; ?>><?php echo _AT('default'); ?></option>     
				<option value="FFFFFF" <?php if ($selected_bgc == "FFFFFF") echo 'selected="selected"'; ?>><?php echo _AT('white'); ?></option>     
				<option value="000000" <?php if ($selected_bgc == "000000") echo 'selected="selected"'; ?>><?php echo _AT('black'); ?></option>     
				<option value="FF0000" <?php if ($selected_bgc == "FF0000") echo 'selected="selected"'; ?>><?php echo _AT('red'); ?></option>     
				<option value="FFFF00" <?php if ($selected_bgc == "FFFF00") echo 'selected="selected"'; ?>><?php echo _AT('yellow'); ?></option>     
				<option value="0000FF" <?php if ($selected_bgc == "0000FF") echo 'selected="selected"'; ?>><?php echo _AT('blue'); ?></option>     
				<option value="00FF00" <?php if ($selected_bgc == "00FF00") echo 'selected="selected"'; ?>><?php echo _AT('green'); ?></option>     
				<option value="999999" <?php if ($selected_bgc == "999999") echo 'selected="selected"'; ?>><?php echo _AT('gray'); ?></option>     
				<option value="CCCCCC" <?php if ($selected_bgc == "CCCCCC") echo 'selected="selected"'; ?>><?php echo _AT('light_gray'); ?></option>     
				<option value="666666" <?php if ($selected_bgc == "666666") echo 'selected="selected"'; ?>><?php echo _AT('dark_gray'); ?></option>     
				<option value="FFCCCC" <?php if ($selected_bgc == "FFCCCC") echo 'selected="selected"'; ?>><?php echo _AT('pink'); ?></option>     
				<option value="00FFFF" <?php if ($selected_bgc == "00FFFF") echo 'selected="selected"'; ?>><?php echo _AT('cyan'); ?></option>     
				<option value="FF00FF" <?php if ($selected_bgc == "FF00FF") echo 'selected="selected"'; ?>><?php echo _AT('magenta'); ?></option> 
			</select>
	</div>

	<div class="row">
		<input type="hidden" id="defaulthl" value="E6E6E6" />
		<label for="hl"><?php echo _AT('hl_colour'); ?></label><br />
			<select name="hl" id="hl" onchange="setPreviewColours()">
<?php
				if (isset($_POST['hl']))
					$selected_hlc = $_POST['hl'];
				else if (isset($_SESSION['prefs']['PREF_HL_COLOUR']))
					$selected_hlc = $_SESSION['prefs']['PREF_HL_COLOUR'];
				else
					$selected_hlc = "";  // default
?>
				<option value="" <?php if ($selected_hlc == "") echo 'selected="selected"'; ?>><?php echo _AT('default'); ?></option>     
				<option value="FFFFFF" <?php if ($selected_hlc == "FFFFFF") echo 'selected="selected"'; ?>><?php echo _AT('white'); ?></option>     
				<option value="000000" <?php if ($selected_hlc == "000000") echo 'selected="selected"'; ?>><?php echo _AT('black'); ?></option>     
				<option value="FF0000" <?php if ($selected_hlc == "FF0000") echo 'selected="selected"'; ?>><?php echo _AT('red'); ?></option>     
				<option value="FFFF00" <?php if ($selected_hlc == "FFFF00") echo 'selected="selected"'; ?>><?php echo _AT('yellow'); ?></option>     
				<option value="0000FF" <?php if ($selected_hlc == "0000FF") echo 'selected="selected"'; ?>><?php echo _AT('blue'); ?></option>     
				<option value="00FF00" <?php if ($selected_hlc == "00FF00") echo 'selected="selected"'; ?>><?php echo _AT('green'); ?></option>     
				<option value="999999" <?php if ($selected_hlc == "999999") echo 'selected="selected"'; ?>><?php echo _AT('gray'); ?></option>     
				<option value="CCCCCC" <?php if ($selected_hlc == "CCCCCC") echo 'selected="selected"'; ?>><?php echo _AT('light_gray'); ?></option>     
				<option value="666666" <?php if ($selected_hlc == "666666") echo 'selected="selected"'; ?>><?php echo _AT('dark_gray'); ?></option>     
				<option value="FFCCCC" <?php if ($selected_hlc == "FFCCCC") echo 'selected="selected"'; ?>><?php echo _AT('pink'); ?></option>     
				<option value="00FFFF" <?php if ($selected_hlc == "00FFFF") echo 'selected="selected"'; ?>><?php echo _AT('cyan'); ?></option>     
				<option value="FF00FF" <?php if ($selected_hlc == "FF00FF") echo 'selected="selected"'; ?>><?php echo _AT('magenta'); ?></option> 
			</select>
	</div>
</div>
<div id="display-settings-preview">
	<div id="previewArea">
		<div id="previewText">Sample 
			<span id="highlightedPreview" style="background-color: rgb(0, 255, 0); font-family: monospace;">Highlighted</span> Text  
		</div> 
	</div>
</div>
