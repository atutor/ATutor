<fieldset>
<legend><strong><?php echo _AT("navigation"); ?></strong> </legend>  

	<div class="row">
		<?php echo _AT('show_contents'); ?><br />
		<?php
			$yes = $no  = '';
			
			if (isset($_POST["show_contents"]))
				$selected_sc = $_POST["show_contents"];
			else
				$selected_sc = $_SESSION['prefs']['PREF_SHOW_CONTENTS'];
				
			if ($selected_sc == 1) {
				$yes = ' checked="checked"';
			} else {
				$no  = ' checked="checked"';
			}
?>
		<input type="radio" name="show_contents" id="sc_yes" value="1" <?php echo $yes; ?> /><label for="sc_yes"><?php echo _AT('yes'); ?></label>
		<input type="radio" name="show_contents" id="sc_no" value="0" <?php echo $no; ?> /><label for="sc_no"><?php echo _AT('no'); ?></label>
	</div>

	<div class="row">
		<?php echo _AT('next_previous_buttons'); ?><br />
		<?php
			$depth = $breadth  = '';
			
			if (isset($_POST["next_previous_buttons"]))
				$selected_npb = $_POST["next_previous_buttons"];
			else
				$selected_npb = $_SESSION['prefs']['PREF_NEXT_PREVIOUS_BUTTONS'];
				
			if ($selected_npb == "breadth") {
				$breadth = ' checked="checked"';
			} else {
				$depth  = ' checked="checked"';
			}
?>
		<input type="radio" name="next_previous_buttons" id="depth" value="depth" <?php echo $depth; ?> /><label for="depth"><?php echo _AT('depth'); ?></label>
		<input type="radio" name="next_previous_buttons" id="breadth" value="breadth" <?php echo $breadth; ?> /><label for="breadth"><?php echo _AT('breadth'); ?></label>
	</div>
</fieldset>

<fieldset> <legend><strong><?php echo _AT("structural_presentation"); ?></strong> </legend>  
	<div class="row">
		<?php echo _AT('show_notes'); ?><br />
		<?php
			$yes = $no  = '';
			
			if (isset($_POST["show_notes"]))
				$selected_n = $_POST["show_notes"];
			else
				$selected_n = $_SESSION['prefs']['PREF_SHOW_NOTES'];
				
			if ($selected_n == 1) {
				$yes = ' checked="checked"';
			} else {
				$no  = ' checked="checked"';
			}
?>
		<input type="radio" name="show_notes" id="n_yes" value="1" <?php echo $yes; ?> /><label for="n_yes"><?php echo _AT('yes'); ?></label>
		<input type="radio" name="show_notes" id="n_no" value="0" <?php echo $no; ?> /><label for="n_no"><?php echo _AT('no'); ?></label>
	</div>

	<div class="row">
		<?php echo _AT('level_of_detail'); ?><br />
		<?php
			$overview = $full = '';
			
			if (isset($_POST["level_of_detail"]))
				$selected_ld = $_POST["level_of_detail"];
			else
				$selected_ld = $_SESSION['prefs']['PREF_LEVEL_OF_DETAIL'];
				
			if ($selected_ld == "overview") {
				$overview = ' checked="checked"';
			} else {
				$full  = ' checked="checked"';
			}
?>
		<input type="radio" name="level_of_detail" id="full" value="full" <?php echo $full; ?> /><label for="full"><?php echo _AT('full'); ?></label>
		<input type="radio" name="level_of_detail" id="overview" value="overview" <?php echo $overview; ?> /><label for="overview"><?php echo _AT('overview'); ?></label>
	</div>

	<div class="row">
		<?php echo _AT('content_views'); ?><br />
		<?php
			$text = $image  = '';
			
			if (isset($_POST["content_views"]))
				$selected_cv = $_POST["content_views"];
			else
				$selected_cv = $_SESSION['prefs']['PREF_CONTENT_VIEWS'];
				
			if ($selected_cv == "image") {
				$image = ' checked="checked"';
			} else {
				$text  = ' checked="checked"';
			}
?>
		<input type="radio" name="content_views" id="text_intensive" value="text" <?php echo $text; ?> /><label for="text_intensive"><?php echo _AT('text_intensive'); ?></label>
		<input type="radio" name="content_views" id="image_intensive" value="image" <?php echo $image; ?> /><label for="image_intensive"><?php echo _AT('image_intensive'); ?></label>
	</div>

	<div class="row">
		<?php echo _AT('show_separate_links'); ?><br />
		<?php
			$yes = $no  = '';
			
			if (isset($_POST["show_separate_links"]))
				$selected_ssl = $_POST["show_separate_links"];
			else
				$selected_ssl = $_SESSION['prefs']['PREF_SHOW_SEPARATE_LINKS'];
				
			if ($selected_ssl == 1) {
				$yes = ' checked="checked"';
			} else {
				$no  = ' checked="checked"';
			}
?>
		<input type="radio" name="show_separate_links" id="ssl_yes" value="1" <?php echo $yes; ?> /><label for="ssl_yes"><?php echo _AT('yes'); ?></label>
		<input type="radio" name="show_separate_links" id="ssl_no" value="0" <?php echo $no; ?> /><label for="ssl_no"><?php echo _AT('no'); ?></label>
	</div>

	<div class="row">
		<?php echo _AT('show_transcript'); ?><br />
		<?php
			$yes = $no  = '';
			
			if (isset($_POST["show_transcript"]))
				$selected_st = $_POST["show_transcript"];
			else
				$selected_st = $_SESSION['prefs']['PREF_SHOW_TRANSCRIPT'];
				
			if ($selected_st == 1) {
				$yes = ' checked="checked"';
			} else {
				$no  = ' checked="checked"';
			}
?>
		<input type="radio" name="show_transcript" id="st_yes" value="1" <?php echo $yes; ?> /><label for="st_yes"><?php echo _AT('yes'); ?></label>
		<input type="radio" name="show_transcript" id="st_no" value="0" <?php echo $no; ?> /><label for="st_no"><?php echo _AT('no'); ?></label>
	</div>

	<div class="row">
		<?php echo _AT('window_layout'); ?><br />
		<?php
			$frost_most = $tiled = $overlap = '';
			
			if (isset($_POST["window_layout"]))
				$selected_wl = $_POST["window_layout"];
			else
				$selected_wl = $_SESSION['prefs']['PREF_WINDOW_LAYOUT'];
				
			if ($selected_wl == "tiled") {
				$tiled = ' checked="checked"';
			} else if ($selected_wl == "overlap"){
				$overlap  = ' checked="checked"';
			} else {
				$frost_most  = ' checked="checked"';
			}
?>
		<input type="radio" name="window_layout" id="frost_most" value="frost_most" <?php echo $frost_most; ?> /><label for="frost_most"><?php echo _AT('frost_most'); ?></label>
		<input type="radio" name="window_layout" id="tiled" value="tiled" <?php echo $tiled; ?> /><label for="tiled"><?php echo _AT('tiled'); ?></label>
		<input type="radio" name="window_layout" id="overlap" value="overlap" <?php echo $overlap; ?> /><label for="overlap"><?php echo _AT('overlap'); ?></label>
	</div>

</fieldset>
