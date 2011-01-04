<legend><strong><?php echo _AT("alt_to_audio"); ?></strong> </legend>  
<div id="feedback" style="width:90%;">
<?php echo _AT('prefs_set_audio'); ?>
</div>
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
