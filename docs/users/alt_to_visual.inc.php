<fieldset style="width:30%;">
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
