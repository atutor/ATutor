<?php 
global $savant;
global $_base_path;

/* start output buffering: */
ob_start(); ?>

<script type="text/javascript">
var ATutor = ATutor || {};

(function ($, ATutor) {
	ATutor.cf_switch_doPost = function () {
		//synchronous request - remeber that next line will be executed 
		//immediately after post whether or not post is successful.
        jQuery.post("<?php echo AT_BASE_HREF; ?>mods/cpref_switch/ajax_save.php", 
                { "alt_to_text": jQuery("#cs_preferred_alt_to_text").val(),
                  "alt_to_audio": jQuery("#cs_preferred_alt_to_audio").val(),
                  "alt_to_visual": jQuery("#cs_preferred_alt_to_visual").val()
                 }
       );

        //location.reload(true) will reload the page?? We could do this in the success callback if
        //cid exists (ie. if there is content on the page) and only if a change was actually made
        
    };
})(jQuery, ATutor);
</script>

<?php
define('AT_PREF_NONE', 'none');
define('AT_PREF_TEXT', 'text');
define('AT_PREF_AUDIO', 'audio');
define('AT_PREF_VISUAL', 'visual');
define('AT_PREF_SIGN', 'sign_lang');

$alt_to_text_values = array(AT_PREF_NONE, AT_PREF_AUDIO, AT_PREF_VISUAL, AT_PREF_SIGN);
$alt_to_text_labels = array("", _AT(AT_PREF_AUDIO), _AT(AT_PREF_VISUAL), _AT(AT_PREF_SIGN));
$alt_to_text = AT_PREF_NONE;
if ($_SESSION['prefs']['PREF_USE_ALTERNATIVE_TO_TEXT'] == 1) {
    $alt_to_text = $_SESSION['prefs']['PREF_ALT_TO_TEXT'];
}


$alt_to_audio_values = array(AT_PREF_NONE, AT_PREF_TEXT, AT_PREF_VISUAL, AT_PREF_SIGN);
$alt_to_audio_labels = array("", _AT(AT_PREF_TEXT), _AT(AT_PREF_VISUAL), _AT(AT_PREF_SIGN));
$alt_to_audio = AT_PREF_NONE;
if ($_SESSION['prefs']['PREF_USE_ALTERNATIVE_TO_AUDIO'] == 1) {
    $alt_to_audio = $_SESSION['prefs']['PREF_ALT_TO_AUDIO'];
}

$alt_to_visual_values = array(AT_PREF_NONE, AT_PREF_TEXT, AT_PREF_AUDIO, AT_PREF_SIGN);
$alt_to_visual_labels = array("", _AT(AT_PREF_TEXT), _AT(AT_PREF_AUDIO), _AT(AT_PREF_SIGN));
$alt_to_visual = AT_PREF_NONE;
if ($_SESSION['prefs']['PREF_USE_ALTERNATIVE_TO_VISUAL'] == 1) {
    $alt_to_visual = $_SESSION['prefs']['PREF_ALT_TO_VISUAL'];
}

?>
<form class="cpref_switch" method="post" name="cpref_switch_form">
<div>
<label for="cs_preferred_alt_to_text"><?php echo _AT("alt_to_text") ?></label>
<select name="cs_preferred_alt_to_text" id="cs_preferred_alt_to_text">
    <?php 
        foreach ($alt_to_text_values as $key => $value) {
            echo '<option value="'.$value.'"';
            if ($alt_to_text == $value) echo ' selected="selected"';
            echo '>'.$alt_to_text_labels[$key]."</option>";
        } 
    ?>
</select>

<label for="cs_preferred_alt_to_audio"><?php echo _AT("alt_to_audio") ?></label>
<select name="cs_preferred_alt_to_audio" id="cs_preferred_alt_to_audio">
    <?php 
        foreach ($alt_to_audio_values as $key => $value) {
            echo '<option value="'.$value.'" ';
            if ($alt_to_audio == $value) echo 'selected="selected"';
            echo '>'.$alt_to_audio_labels[$key]."</option>";
        } 
    ?>
</select>

<label for="cs_preferred_alt_to_visual"><?php echo _AT("alt_to_visual") ?></label>
<select name="cs_preferred_alt_to_visual" id="cs_preferred_alt_to_visual">
    <?php 
        foreach ($alt_to_visual_values as $key => $value) {
            echo '<option value="'.$value.'" ';
            if ($alt_to_visual == $value) echo 'selected="selected"';
            echo '>'.$alt_to_visual_labels[$key]."</option>";
        } 
    ?>
</select>

<input class="button" type="button" value="<?php echo _AT('apply') ?>" 
       onclick="ATutor.cf_switch_doPost();" />
</div>
</form>

<?php
$savant->assign('dropdown_contents', ob_get_contents());
ob_end_clean();

$savant->assign('title', _AT('cpref_switch')); // the box title
$savant->display('include/box.tmpl.php');
?>