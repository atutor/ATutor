<?php $prefs_set = isset($this->pref_wiz);?>

<fieldset class="wizscreen"><legend><?php echo _AT('prefs_set_init_legend'); ?></legend>
<div id="feedback" style="width:90%;">
<?php echo _AT('prefs_set_init'); ?>
</div>
    <div>
        <input type="checkbox" name="pref_wiz[]" value="<?php echo DISPLAY ?>" id="display" <?php if ($prefs_set && in_array(DISPLAY, $this->pref_wiz)) echo checked ?> />
        <label for="display"><?php echo _AT('prefs_set_init_see'); ?></label>
    </div>

    <div>
        <input type="checkbox" name="pref_wiz[]" value="<?php echo NAVIGATION ?>" id="navigation" <?php if ($prefs_set && in_array(NAVIGATION, $this->pref_wiz)) echo checked ?> />
        <label for="navigation"><?php echo _AT('prefs_set_init_nav'); ?></label>
    </div>

    <div>
        <input type="checkbox" name="pref_wiz[]" value="<?php echo ALT_TO_TEXT ?>" id="altToText" <?php if ($prefs_set && in_array(ALT_TO_TEXT, $this->pref_wiz)) echo checked ?> />
        <label for="altToText"><?php echo _AT('prefs_set_init_text'); ?></label>
    </div>

    <div>
        <input type="checkbox" name="pref_wiz[]" value="<?php echo ALT_TO_AUDIO ?>" id="altToAudio" <?php if ($prefs_set && in_array(ALT_TO_AUDIO, $this->pref_wiz)) echo checked ?> />
        <label for="altToAudio"><?php echo _AT('prefs_set_init_audio'); ?></label>
    </div>
    
    <div>
        <input type="checkbox" name="pref_wiz[]" value="<?php echo ALT_TO_VISUAL ?>" id="altToVisual" <?php if ($prefs_set && in_array(ALT_TO_VISUAL, $this->pref_wiz)) echo checked ?> />
        <label for="altToVisual"><?php echo _AT('prefs_set_init_visual'); ?></label>
    </div>
    
    <div>
        <input type="checkbox" name="pref_wiz[]" value="<?php echo SUPPORT ?>" id="support" <?php if ($prefs_set && in_array(SUPPORT, $this->pref_wiz)) echo checked ?> />
        <label for="support"><?php echo _AT('prefs_set_init_tool'); ?></label>
    </div>
    
    <div>
        <input type="checkbox" name="pref_wiz[]" value="<?php echo ATUTOR ?>" id="atutor_pref" <?php if ($prefs_set && in_array(ATUTOR, $_POST['pref_wiz'])) echo checked ?> />
        <label for="atutor_pref"><?php echo _AT('prefs_set_init_atutor'); ?></label>
    </div>
    
    <input type="hidden" value="-1" name="pref_index" id="pref_index" /><br />
    <input type="hidden" value="<?php echo $_SESSION['course_id']; ?>" name="course_id" id="course_id" /><br />
    <input class="button" type="submit" value="<?php echo _AT('next'); ?>" name="next" id="next" style="float:right;"/>

</fieldset>