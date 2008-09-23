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
		<?php echo _AT('show_next_previous_buttons'); ?><br />
		<?php
			$yes = $no = '';
			
			if (isset($_POST["show_next_previous_buttons"]))
				$selected_snpb = $_POST["show_next_previous_buttons"];
			else
				$selected_snpb = $_SESSION['prefs']['PREF_SHOW_NEXT_PREVIOUS_BUTTONS'];
				
			if ($selected_snpb == 1) {
				$yes = ' checked="checked"';
			} else {
				$no = ' checked="checked"';
			}
?>
		<input type="radio" name="show_next_previous_buttons" id="snpb_yes" value="1" <?php echo $yes; ?> /><label for="snpb_yes"><?php echo _AT('yes'); ?></label>
		<input type="radio" name="show_next_previous_buttons" id="snpb_no" value="0" <?php echo $no; ?> /><label for="snpb_no"><?php echo _AT('no'); ?></label>
	</div>

	<div class="row">
		<?php echo _AT('show_bread_crumbs'); ?><br />
		<?php
			$yes = $no  = '';
			
			if (isset($_POST["show_bread_crumbs"]))
				$selected_sbc = $_POST["show_bread_crumbs"];
			else
				$selected_sbc = $_SESSION['prefs']['PREF_SHOW_BREAD_CRUMBS'];
				
			if ($selected_sbc == 1) {
				$yes = ' checked="checked"';
			} else {
				$no  = ' checked="checked"';
			}
?>
		<input type="radio" name="show_bread_crumbs" id="sbc_yes" value="1" <?php echo $yes; ?> /><label for="sbc_yes"><?php echo _AT('yes'); ?></label>
		<input type="radio" name="show_bread_crumbs" id="sbc_no" value="0" <?php echo $no; ?> /><label for="sbc_no"><?php echo _AT('no'); ?></label>
	</div>
</fieldset>

