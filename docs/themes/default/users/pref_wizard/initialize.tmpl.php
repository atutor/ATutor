<?php $prefs_set = isset($this->pref_wiz);?>

    <div>
        <input type="checkbox" name="pref_wiz[]" value="<?php echo DISPLAY ?>" id="display" <?php if ($prefs_set && in_array(DISPLAY, $this->pref_wiz)) echo checked ?> />
        <label for="display">I would like to make the text on the screen easier to see.</label>
    </div>

    <div>
        <input type="checkbox" name="pref_wiz[]" value="<?php echo NAVIGATION ?>" id="navigation" <?php if ($prefs_set && in_array(NAVIGATION, $this->pref_wiz)) echo checked ?> />
        <label for="navigation">I would like to enhance the navigation of the content.</label>
    </div>

    <div>
        <input type="checkbox" name="pref_wiz[]" value="<?php echo ALT_TO_TEXT ?>" id="altToText" <?php if ($prefs_set && in_array(ALT_TO_TEXT, $this->pref_wiz)) echo checked ?> />
        <label for="altToText">I would like alternatives to textual content.</label>
    </div>

    <div>
        <input type="checkbox" name="pref_wiz[]" value="<?php echo ALT_TO_AUDIO ?>" id="altToAudio" <?php if ($prefs_set && in_array(ALT_TO_AUDIO, $this->pref_wiz)) echo checked ?> />
        <label for="altToAudio">I would like alternatives to audio content.</label>
    </div>
    
    <div>
        <input type="checkbox" name="pref_wiz[]" value="<?php echo ALT_TO_VISUAL ?>" id="altToVisual" <?php if ($prefs_set && in_array(ALT_TO_VISUAL, $this->pref_wiz)) echo checked ?> />
        <label for="altToVisual">I would like alternatives to visual content.</label>
    </div>
    
    <div>
        <input type="checkbox" name="pref_wiz[]" value="<?php echo SUPPORT ?>" id="support" <?php if ($prefs_set && in_array(SUPPORT, $this->pref_wiz)) echo checked ?> />
        <label for="support">I would like access to learner support tools.</label>
    </div>
    
    <div>
        <input type="checkbox" name="pref_wiz[]" value="<?php echo ATUTOR ?>" id="atutor_pref" <?php if ($prefs_set && in_array(ATUTOR, $_POST['pref_wiz'])) echo checked ?> />
        <label for="atutor_pref">I would like to change or review my ATutor preferences.</label>
    </div>
    
    <input type="hidden" value="-1" name="pref_index" id="pref_index" />
    <input class="fl-force-right" type="submit" value="Next" name="next" id="next" />