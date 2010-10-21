<legend><strong><?php echo _AT("alt_to_text"); ?></strong> </legend>  
<div id="feedback" style="width:90%;">
<?php echo _AT('prefs_set_text'); ?></div>
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
