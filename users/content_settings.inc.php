<fieldset>
<legend><strong><?php echo _AT("alt_to_text"); ?></strong> </legend>  

	<div class="row">
		<?php echo _AT('use_alt_to_text'); ?><br />
		<?php
			$yes = $no  = '';
			
			if (isset($_POST["use_alternative_to_text"]))
				$selected_uat = $_POST["use_alternative_to_text"];
			else
				$selected_uat = $_SESSION['prefs']['PREF_USE_ALTERNATIVE_TO_TEXT'];
				
			if ($selected_uat == 1) {
				$yes = ' checked="checked"';
			} else {
				$no  = ' checked="checked"';
			}
?>
		<input type="radio" name="use_alternative_to_text" id="uat_yes" value="1" <?php echo $yes; ?> /><label for="uat_yes"><?php echo _AT('yes'); ?></label> 
		<input type="radio" name="use_alternative_to_text" id="uat_no" value="0" <?php echo $no; ?> /><label for="uat_no"><?php echo _AT('no'); ?></label>		
	</div>

	<div class="row">
		<label for="preferred_alt_to_text"><?php echo _AT('prefer_alt'); ?></label><br />
			<select name="preferred_alt_to_text" id="preferred_alt_to_text"><?php
				if (isset($_POST['preferred_alt_to_text']))
					$selected_lang = $_POST['preferred_alt_to_text'];
				else
					$selected_lang = $_SESSION['prefs']['PREF_ALT_TO_TEXT'];
?>
				<option value="audio" <?php if ($selected_lang == "audio") echo 'selected="selected"'; ?>><?php echo _AT('audio'); ?></option>
				<option value="visual" <?php if ($selected_lang == "visual") echo 'selected="selected"'; ?>><?php echo _AT('visual'); ?></option>
				<option value="sign_lang" <?php if ($selected_lang == "sign_lang") echo 'selected="selected"'; ?>><?php echo _AT('sign_lang'); ?></option>
			</select>
	</div>

	<div class="row">
		<?php echo _AT('append_or_replace'); ?><br />
		<?php
			$append = $replace = '';
			
			if (isset($_POST["alt_to_text_append_or_replace"]))
				$selected_ar = $_POST["alt_to_text_append_or_replace"];
			else
				$selected_ar = $_SESSION['prefs']['PREF_ALT_TO_TEXT_APPEND_OR_REPLACE'];
				
			if ($selected_ar == 'replace') {
				$replace = ' checked="checked"';
			} else {
				$append  = ' checked="checked"';
			} 
?>
		<input type="radio" name="alt_to_text_append_or_replace" id="ar_append" value="append" <?php echo $append; ?> /><label for="ar_append"><?php echo _AT('append'); ?></label> 
		<input type="radio" name="alt_to_text_append_or_replace" id="ar_replace" value="replace" <?php echo $replace; ?> /><label for="ar_replace"><?php echo _AT('replace'); ?></label>		
	</div>

	<div class="row">
		<label for="alt_text_prefer_lang"><?php echo _AT('prefer_lang'); ?></label><br />
			<select name="alt_text_prefer_lang" id="alt_text_prefer_lang"><?php
				if (isset($_POST['alt_text_prefer_lang']))
					$selected_lang = $_POST['alt_text_prefer_lang'];
				else
					$selected_lang = $_SESSION['prefs']['PREF_ALT_TEXT_PREFER_LANG'];

				output_language_options($this->languages, $selected_lang);
?>
			</select>
	</div>

</fieldset>

<fieldset>
<legend><strong><?php echo _AT("alt_to_audio"); ?></strong> </legend>  

	<div class="row">
		<?php echo _AT('use_alt_to_audio'); ?><br />
		<?php
			$yes = $no  = '';
			
			if (isset($_POST["use_alternative_to_audio"]))
				$selected_uaa = $_POST["use_alternative_to_audio"];
			else
				$selected_uaa = $_SESSION['prefs']['PREF_USE_ALTERNATIVE_TO_AUDIO'];
				
			if ($selected_uaa == 1) {
				$yes = ' checked="checked"';
			} else {
				$no  = ' checked="checked"';
			}
?>
		<input type="radio" name="use_alternative_to_audio" id="uaa_yes" value="1" <?php echo $yes; ?> /><label for="uaa_yes"><?php echo _AT('yes'); ?></label> 
		<input type="radio" name="use_alternative_to_audio" id="uaa_no" value="0" <?php echo $no; ?> /><label for="uaa_no"><?php echo _AT('no'); ?></label>		
	</div>

	<div class="row">
		<label for="preferred_alt_to_audio"><?php echo _AT('prefer_alt'); ?></label><br />
			<select name="preferred_alt_to_audio" id="preferred_alt_to_audio"><?php
				if (isset($_POST['preferred_alt_to_audio']))
					$selected_lang = $_POST['preferred_alt_to_audio'];
				else
					$selected_lang = $_SESSION['prefs']['PREF_ALT_TO_AUDIO'];
?>
				<option value="text" <?php if ($selected_lang == "text") echo 'selected="selected"'; ?>><?php echo _AT('text'); ?></option>
				<option value="visual" <?php if ($selected_lang == "visual") echo 'selected="selected"'; ?>><?php echo _AT('visual'); ?></option>
				<option value="sign_lang" <?php if ($selected_lang == "sign_lang") echo 'selected="selected"'; ?>><?php echo _AT('sign_lang'); ?></option>
			</select>
	</div>

	<div class="row">
		<?php echo _AT('append_or_replace'); ?><br />
		<?php
			$append = $replace = '';
			
			if (isset($_POST["alt_to_audio_append_or_replace"]))
				$selected_aa = $_POST["alt_to_audio_append_or_replace"];
			else
				$selected_aa = $_SESSION['prefs']['PREF_ALT_TO_AUDIO_APPEND_OR_REPLACE'];
				
			if ($selected_aa == "replace") {
				$replace = ' checked="checked"';
			} else {
				$append  = ' checked="checked"';
			}
?>
		<input type="radio" name="alt_to_audio_append_or_replace" id="aa_append" value="append" <?php echo $append; ?> /><label for="aa_append"><?php echo _AT('append'); ?></label> 
		<input type="radio" name="alt_to_audio_append_or_replace" id="aa_replace" value="replace" <?php echo $replace; ?> /><label for="aa_replace"><?php echo _AT('replace'); ?></label>
	</div>

	<div class="row">
		<label for="alt_audio_prefer_lang"><?php echo _AT('prefer_lang'); ?></label><br />
			<select name="alt_audio_prefer_lang" id="alt_audio_prefer_lang"><?php
				if (isset($_POST['alt_audio_prefer_lang']))
					$selected_lang = $_POST['alt_audio_prefer_lang'];
				else
					$selected_lang = $_SESSION['prefs']['PREF_ALT_AUDIO_PREFER_LANG'];

				output_language_options($this->languages, $selected_lang);
?>
			</select>
	</div>

</fieldset>

<fieldset>
<legend><strong><?php echo _AT("alt_to_visual"); ?></strong> </legend>  

	<div class="row">
		<?php echo _AT('use_alt_to_visual'); ?><br />
		<?php
			$yes = $no  = '';
			
			if (isset($_POST["use_alternative_to_visual"]))
				$selected_uav = $_POST["use_alternative_to_visual"];
			else
				$selected_uav = $_SESSION['prefs']['PREF_USE_ALTERNATIVE_TO_VISUAL'];
				
			if ($selected_uav == 1) {
				$yes = ' checked="checked"';
			} else {
				$no  = ' checked="checked"';
			}
?>
		<input type="radio" name="use_alternative_to_visual" id="uav_yes" value="1" <?php echo $yes; ?> /><label for="uav_yes"><?php echo _AT('yes'); ?></label> 
		<input type="radio" name="use_alternative_to_visual" id="uav_no" value="0" <?php echo $no; ?> /><label for="uav_no"><?php echo _AT('no'); ?></label>		
	</div>

	<div class="row">
		<label for="preferred_alt_to_visual"><?php echo _AT('prefer_alt'); ?></label><br />
			<select name="preferred_alt_to_visual" id="preferred_alt_to_visual"><?php
				if (isset($_POST['preferred_alt_to_visual']))
					$selected_lang = $_POST['preferred_alt_to_visual'];
				else
					$selected_lang = $_SESSION['prefs']['PREF_ALT_TO_VISUAL'];
?>
				<option value="text" <?php if ($selected_lang == "text") echo 'selected="selected"'; ?>><?php echo _AT('text'); ?></option>
				<option value="audio" <?php if ($selected_lang == "audio") echo 'selected="selected"'; ?>><?php echo _AT('audio'); ?></option>
				<option value="sign_lang" <?php if ($selected_lang == "sign_lang") echo 'selected="selected"'; ?>><?php echo _AT('sign_lang'); ?></option>
			</select>
	</div>

	<div class="row">
		<?php echo _AT('append_or_replace'); ?><br />
		<?php
			$append = $replace = '';
			
			if (isset($_POST["alt_to_visual_append_or_replace"]))
			{
				$selected_av = $_POST["alt_to_visual_append_or_replace"];
			}
			else
				$selected_av = $_SESSION['prefs']['PREF_ALT_TO_VISUAL_APPEND_OR_REPLACE'];
				
			if ($selected_av == "replace") {
				$replace = ' checked="checked"';
			} else {
				$append  = ' checked="checked"';
			}
?>
		<input type="radio" name="alt_to_visual_append_or_replace" id="av_append" value="append" <?php echo $append; ?> /><label for="av_append"><?php echo _AT('append'); ?></label> 
		<input type="radio" name="alt_to_visual_append_or_replace" id="av_replace" value="replace" <?php echo $replace; ?> /><label for="av_replace"><?php echo _AT('replace'); ?></label>
	</div>

	<div class="row">
		<label for="alt_visual_prefer_lang"><?php echo _AT('prefer_lang'); ?></label><br />
			<select name="alt_visual_prefer_lang" id="alt_visual_prefer_lang"><?php
				if (isset($_POST['alt_visual_prefer_lang']))
					$selected_lang = $_POST['alt_visual_prefer_lang'];
				else
					$selected_lang = $_SESSION['prefs']['PREF_ALT_VISUAL_PREFER_LANG'];

				output_language_options($this->languages, $selected_lang);
?>
			</select>
	</div>

</fieldset>

<!--
<fieldset>
<legend><strong><?php echo _AT("text_alternatives"); ?></strong> </legend>  

	<div class="row">
		<?php echo _AT('use_alternate_text'); ?><br />
		<?php
			$yes = $no  = '';
			
			if (isset($_POST["use_alternate_text"]))
				$selected_uat = $_POST["use_alternate_text"];
			else
				$selected_uat = $_SESSION['prefs']['PREF_USE_ALTERNATE_TEXT'];
				
			if ($selected_uat == 1) {
				$yes = ' checked="checked"';
			} else {
				$no  = ' checked="checked"';
			}
?>
		<input type="radio" name="use_alternate_text" id="uat_yes" value="1" <?php echo $yes; ?> /><label for="uat_yes"><?php echo _AT('yes'); ?></label> 
		<input type="radio" name="use_alternate_text" id="uat_no" value="0" <?php echo $no; ?> /><label for="uat_no"><?php echo _AT('no'); ?></label>		
	</div>

	<div class="row">
		<label for="alt_text_lang"><?php echo _AT('alt_text_lang'); ?></label><br />
			<select name="alt_text_lang" id="alt_text_lang"><?php
				if (isset($_POST['alt_text_lang']))
					$selected_lang = $_POST['alt_text_lang'];
				else
					$selected_lang = $_SESSION['prefs']['PREF_ALT_TEXT_LANG'];

				output_language_options($this->languages, $selected_lang);
?>
			</select>
	</div>

	<div class="row">
		<label for="long_desc_lang"><?php echo _AT('long_desc_lang'); ?></label><br />
			<select name="long_desc_lang" id="long_desc_lang"><?php
				if (isset($_POST['long_desc_lang']))
					$selected_lang = $_POST['long_desc_lang'];
				else
					$selected_lang = $_SESSION['prefs']['PREF_LONG_DESC_LANG'];

				output_language_options($this->languages, $selected_lang);
?>
			</select>
	</div>

	<div class="row">
		<?php echo _AT('use_graphic_alternative'); ?><br />
		<?php
			$yes = $no  = '';
			
			if (isset($_POST["use_graphic_alternative"]))
				$selected_uga = $_POST["use_graphic_alternative"];
			else
				$selected_uga = $_SESSION['prefs']['PREF_USE_GRAPHIC_ALTERNATIVE'];
				
			if ($selected_uga == 1) {
				$yes = ' checked="checked"';
			} else {
				$no  = ' checked="checked"';
			}
?>
		<input type="radio" name="use_graphic_alternative" id="uga_yes" value="1" <?php echo $yes; ?> /><label for="uga_yes"><?php echo _AT('yes'); ?></label> 
		<input type="radio" name="use_graphic_alternative" id="uga_no" value="0" <?php echo $no; ?> /><label for="uga_no"><?php echo _AT('no'); ?></label>		
	</div>

	<div class="row">
		<?php echo _AT('use_graphic_alternative'); ?><br />
		<?php
			$yes = $no  = '';
			
			if (isset($_POST["use_sign_lang"]))
				$selected_usl = $_POST["use_sign_lang"];
			else
				$selected_usl = $_SESSION['prefs']['PREF_USE_SIGN_LANG'];
				
			if ($selected_usl == 1) {
				$yes = ' checked="checked"';
			} else {
				$no  = ' checked="checked"';
			}
?>
		<input type="radio" name="use_sign_lang" id="usl_yes" value="1" <?php echo $yes; ?> /><label for="usl_yes"><?php echo _AT('yes'); ?></label> 
		<input type="radio" name="use_sign_lang" id="usl_no" value="0" <?php echo $no; ?> /><label for="usl_no"><?php echo _AT('no'); ?></label>		
	</div>

	<div class="row">
		<label for="sign_lang"><?php echo _AT('sign_lang'); ?></label><br />
			<select name="sign_lang" id="sign_lang"><?php
				if (isset($_POST['sign_lang']))
					$selected_lang = $_POST['sign_lang'];
				else
					$selected_lang = $_SESSION['prefs']['PREF_SIGN_LANG'];
?>
				<option value="American-ASL" <?php if ($selected_lang == "American-ASL") echo 'selected="selected"'; ?>><?php echo _AT('american-asl'); ?></option>     
				<option value="Australian-Auslan" <?php if ($selected_lang == "Australian-Auslan") echo 'selected="selected"'; ?>><?php echo _AT('australian-auslan'); ?></option>     
				<option value="Austrian" <?php if ($selected_lang == "Austrian") echo 'selected="selected"'; ?>><?php echo _AT('austrian'); ?></option>     
				<option value="British-BSL" <?php if ($selected_lang == "British-BSL") echo 'selected="selected"'; ?>><?php echo _AT('british-bsl'); ?></option>     
				<option value="Danish-DSL" <?php if ($selected_lang == "Danish-DSL") echo 'selected="selected"'; ?>><?php echo _AT('danish-dsl'); ?></option>     
				<option value="French-LSF" <?php if ($selected_lang == "French-LSF") echo 'selected="selected"'; ?>><?php echo _AT('french-lsf'); ?></option>     
				<option value="German-DGS" <?php if ($selected_lang == "German-DGS") echo 'selected="selected"'; ?>><?php echo _AT('german-dgs'); ?></option>     
				<option value="Irish-ISL" <?php if ($selected_lang == "Irish-ISL") echo 'selected="selected"'; ?>><?php echo _AT('irish-isl'); ?></option>     
				<option value="Italian-LIS" <?php if ($selected_lang == "Italian-LIS") echo 'selected="selected"'; ?>><?php echo _AT('italian-lis'); ?></option>     
				<option value="Japanese-JSL" <?php if ($selected_lang == "Japanese-JSL") echo 'selected="selected"'; ?>><?php echo _AT('japanese-jsl'); ?></option>     
				<option value="Malaysian-MSL" <?php if ($selected_lang == "Malaysian-MSL") echo 'selected="selected"'; ?>><?php echo _AT('malaysian-msl'); ?></option>     
				<option value="Mexican-LSM" <?php if ($selected_lang == "Mexican-LSM") echo 'selected="selected"'; ?>><?php echo _AT('mexican-lsm'); ?></option>     
				<option value="Native-American" <?php if ($selected_lang == "Native-American") echo 'selected="selected"'; ?>><?php echo _AT('native-american'); ?></option>     
				<option value="Norwegian-NSL" <?php if ($selected_lang == "Norwegian-NSL") echo 'selected="selected"'; ?>><?php echo _AT('norwegian-nsl'); ?></option>     
				<option value="Russian-RSL" <?php if ($selected_lang == "Russian-RSL") echo 'selected="selected"'; ?>><?php echo _AT('russian-rsl'); ?></option>     
				<option value="Quebec-LSQ" <?php if ($selected_lang == "Quebec-LSQ") echo 'selected="selected"'; ?>><?php echo _AT('quebec-lsq'); ?></option>     
				<option value="Singapore-SLS" <?php if ($selected_lang == "Singapore-SLS") echo 'selected="selected"'; ?>><?php echo _AT('singapore-sls'); ?></option>     
				<option value="Netherlands-NGT" <?php if ($selected_lang == "Netherlands-NGT") echo 'selected="selected"'; ?>><?php echo _AT('netherlands-ngt'); ?>Netherlands-NGT</option>     
				<option value="Spanish-LSE" <?php if ($selected_lang == "Spanish-LSE") echo 'selected="selected"'; ?>><?php echo _AT('spanish-lse'); ?></option>     
				<option value="Swedish" <?php if ($selected_lang == "Swedish") echo 'selected="selected"'; ?>><?php echo _AT('swedish'); ?></option>     
				<option value="other" <?php if ($selected_lang == "other") echo 'selected="selected"'; ?>><?php echo _AT('other'); ?></option>   
			</select>
	</div>
</fieldset>

<fieldset>
<legend><strong><?php echo _AT("described_video"); ?></strong> </legend>  

	<div class="row">
		<?php echo _AT('use_video'); ?><br />
		<?php
			$yes = $no  = '';
			
			if (isset($_POST["use_video"]))
				$selected_uv = $_POST["use_video"];
			else
				$selected_uv = $_SESSION['prefs']['PREF_USE_VIDEO'];
				
			if ($selected_uv == 1) {
				$yes = ' checked="checked"';
			} else {
				$no  = ' checked="checked"';
			}
?>
		<input type="radio" name="use_video" id="uv_yes" value="1" <?php echo $yes; ?> /><label for="uv_yes"><?php echo _AT('yes'); ?></label> 
		<input type="radio" name="use_video" id="uv_no" value="0" <?php echo $no; ?> /><label for="uv_no"><?php echo _AT('no'); ?></label>		
	</div>

	<div class="row">
		<label for="prefer_lang"><?php echo _AT('prefer_lang'); ?></label><br />
			<select name="prefer_lang" id="prefer_lang"><?php
				if (isset($_POST['prefer_lang']))
					$selected_lang = $_POST['prefer_lang'];
				else
					$selected_lang = $_SESSION['prefs']['PREF_PREFER_LANG'];

				output_language_options($this->languages, $selected_lang);
?>
			</select>
	</div>

	<div class="row">
		<?php echo _AT('description_type'); ?><br />
		<?php
			$yes = $no  = '';
			
			if (isset($_POST["description_type"]))
				$selected_dt = $_POST["description_type"];
			else
				$selected_dt = $_SESSION['prefs']['PREF_DESC_TYPE'];
?>
		<input type="radio" name="description_type" id="dt_s" value="standard" <?php if ($selected_dt == "standard" || $selected_dt == "") echo "checked=checked"; ?> /><label for="dt_s"><?php echo _AT('standard'); ?></label> 
		<input type="radio" name="description_type" id="dt_e" value="expanded" <?php if ($selected_dt == "expanded") echo "checked=checked"; ?> /><label for="dt_e"><?php echo _AT('expanded'); ?></label>		
	</div>
</fieldset>

<fieldset>
<legend><strong><?php echo _AT("captioning"); ?></strong> </legend>  

	<div class="row">
		<?php echo _AT('enable_captions'); ?><br />
		<?php
			$yes = $no  = '';
			
			if (isset($_POST["enable_captions"]))
				$selected_ec = $_POST["enable_captions"];
			else
				$selected_ec = $_SESSION['prefs']['PREF_ENABLE_CAPTIONS'];
				
			if ($selected_ec == 1) {
				$yes = ' checked="checked"';
			} else {
				$no  = ' checked="checked"';
			}
?>
		<input type="radio" name="enable_captions" id="ec_yes" value="1" <?php echo $yes; ?> /><label for="ec_yes"><?php echo _AT('yes'); ?></label> 
		<input type="radio" name="enable_captions" id="ec_no" value="0" <?php echo $no; ?> /><label for="ec_no"><?php echo _AT('no'); ?></label>		
	</div>

	<div class="row">
		<?php echo _AT('caption_type'); ?><br />
		<?php
			if (isset($_POST["caption_type"]))
				$selected_ct = $_POST["caption_type"];
			else
				$selected_ct = $_SESSION['prefs']['PREF_CAPTION_TYPE'];
?>
		<input type="radio" name="caption_type" id="ct_v" value="verbatim" <?php if ($selected_ct == "verbatim" || $selected_ct == "") echo 'checked="checked"'; ?> /><label for="ct_v"><?php echo _AT('verbatim'); ?></label> 
		<input type="radio" name="caption_type" id="ct_r" value="reduced" <?php if ($selected_ct == "reduced") echo 'checked="checked"'; ?> /><label for="ct_r"><?php echo _AT('reduced_level'); ?></label>		
	</div>

	<div class="row">
		<label for="caption_lang"><?php echo _AT('caption_language'); ?></label><br />
			<select name="caption_lang" id="caption_lang"><?php
				if (isset($_POST['caption_lang']))
					$selected_lang = $_POST['caption_lang'];
				else
					$selected_lang = $_SESSION['prefs']['PREF_CAPTION_LANG'];

				output_language_options($this->languages, $selected_lang);
	?>
			</select>
	</div>

	<div class="row">
		<?php echo _AT('enhanced_captions'); ?><br />
		<?php
			$yes = $no  = '';
			
			if (isset($_POST["enhanced_captions"]))
				$selected_ec = $_POST["enhanced_captions"];
			else
				$selected_ec = $_SESSION['prefs']['PREF_ENHANCED_CAPTIONS'];
				
			if ($selected_ec == 1) {
				$yes = ' checked="checked"';
			} else {
				$no  = ' checked="checked"';
			}
?>
		<input type="radio" name="enhanced_captions" id="ec1_yes" value="1" <?php echo $yes; ?> /><label for="ec1_yes"><?php echo _AT('yes'); ?></label> 
		<input type="radio" name="enhanced_captions" id="ec1_no" value="0" <?php echo $no; ?> /><label for="ec1_no"><?php echo _AT('no'); ?></label>		
	</div>

	<div class="row">
		<?php echo _AT('request_caption_rate'); ?><br />
		<?php
			$yes = $no  = '';
			
			if (isset($_POST["request_caption_rate"]))
				$selected_rcr = $_POST["request_caption_rate"];
			else
				$selected_rcr = $_SESSION['prefs']['PREF_REQUEST_CAPTION_RATE'];
				
			if ($selected_rcr == 1) {
				$yes = ' checked="checked"';
			} else {
				$no  = ' checked="checked"';
			}
?>
		<input type="radio" name="request_caption_rate" id="rcr_yes" value="1" <?php echo $yes; ?> /><label for="rcr_yes"><?php echo _AT('yes'); ?></label> 
		<input type="radio" name="request_caption_rate" id="rcr_no" value="0" <?php echo $no; ?> /><label for="rcr_no"><?php echo _AT('no'); ?></label>		
	</div>

	<div class="row">
		<?php echo _AT('caption_rate'); ?><br />
		<?php
			if (isset($_POST["caption_rate"]))
				$selected_caption_rate = $_POST["caption_rate"];
			else if (isset($_SESSION['prefs']['PREF_CAPTION_RATE']))
				$selected_caption_rate = $_SESSION['prefs']['PREF_CAPTION_RATE'];
			else
				$selected_caption_rate = 200;    // set default
?>
		<input type="text" name="caption_rate" id="caption_rate" size="30" value="<?php echo $selected_caption_rate; ?>" />
	</div>
</fieldset>

//-->