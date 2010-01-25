<?php 
global $savant;
global $_base_path;

include_once('module.inc.php');
/* start output buffering: */
ob_start(); ?>

<script type="text/javascript">
var ATutor = ATutor || {};
ATutor.cpref_switch = ATutor.cpref_switch || {};

(function (ATutor) {
	/**
	* Sends the alternative content request to the server and reloads the page on successful completion.
	* Perhaps change this to a .ajax request so that we can display a fail message if it doesn't work.
	*/
	ATutor.cpref_switch.doPost = function () {
		
        jQuery.post("<?php echo AT_BASE_HREF; ?>mods/cpref_switch/ajax_save.php", 
                { "<?php echo AT_POST_ALT_TO_TEXT; ?>": jQuery("#cs_preferred_alt_to_text").val(),
                  "<?php echo AT_POST_ALT_TO_AUDIO; ?>": jQuery("#cs_preferred_alt_to_audio").val(),
                  "<?php echo AT_POST_ALT_TO_VISUAL; ?>": jQuery("#cs_preferred_alt_to_visual").val()
                 },
                 function (isPrefsChanged) {
                     if (isPrefsChanged) {
                         //provide feedback
                         jQuery("#cpref_switch_feedback").show();
                     }
                     else {
                    	 jQuery("#cpref_switch_feedback").hide();
                     }
                     if ((location.href.indexOf("content.php") > -1) && (location.href.indexOf("cid") > -1) && isPrefsChanged === '1') {
                    	//if page is reloaded, the ajax stops running - no feedback is possible
                         location.reload(true);  
                     }
                 }
        );        
    };   
})(ATutor);
</script>

<?php
$alt_to_text_values = array(AT_PREF_NONE, AT_PREF_AUDIO, AT_PREF_VISUAL, AT_PREF_SIGN);
$alt_to_text_labels = array(_AT(AT_PREF_NONE), _AT(AT_PREF_AUDIO), _AT(AT_PREF_VISUAL), _AT(AT_PREF_SIGN));
$alt_to_text = AT_PREF_NONE;
if ($_SESSION['prefs']['PREF_USE_ALTERNATIVE_TO_TEXT'] == 1) {
    $alt_to_text = $_SESSION['prefs']['PREF_ALT_TO_TEXT'];
}


$alt_to_audio_values = array(AT_PREF_NONE, AT_PREF_TEXT, AT_PREF_VISUAL, AT_PREF_SIGN);
$alt_to_audio_labels = array(_AT(AT_PREF_NONE), _AT(AT_PREF_TEXT), _AT(AT_PREF_VISUAL), _AT(AT_PREF_SIGN));
$alt_to_audio = AT_PREF_NONE;
if ($_SESSION['prefs']['PREF_USE_ALTERNATIVE_TO_AUDIO'] == 1) {
    $alt_to_audio = $_SESSION['prefs']['PREF_ALT_TO_AUDIO'];
}

$alt_to_visual_values = array(AT_PREF_NONE, AT_PREF_TEXT, AT_PREF_AUDIO, AT_PREF_SIGN);
$alt_to_visual_labels = array(_AT(AT_PREF_NONE), _AT(AT_PREF_TEXT), _AT(AT_PREF_AUDIO), _AT(AT_PREF_SIGN));
$alt_to_visual = AT_PREF_NONE;
if ($_SESSION['prefs']['PREF_USE_ALTERNATIVE_TO_VISUAL'] == 1) {
    $alt_to_visual = $_SESSION['prefs']['PREF_ALT_TO_VISUAL'];
}

?>

<form class="cpref_switch" method="post" name="cpref_switch_form">
<div style="position: relative;padding: 1em;height: 15.5em;">
<label style="display:block;margin-bottom:0.25em;" for="cs_preferred_alt_to_text"><?php echo _AT("alt_to_text") ?></label>
<select style="margin-bottom:1em;margin-left:1em;" name="cs_preferred_alt_to_text" id="cs_preferred_alt_to_text">
    <?php 
        foreach ($alt_to_text_values as $key => $value) {
            echo '<option value="'.$value.'"';
            if ($alt_to_text == $value) echo ' selected="selected"';
            echo '>'.$alt_to_text_labels[$key]."</option>";
        } 
    ?>
</select>

<label style="display:block;margin-bottom:0.25em;" for="cs_preferred_alt_to_audio"><?php echo _AT("alt_to_audio") ?></label>
<select style="margin-bottom:1em;margin-left:1em;"name="cs_preferred_alt_to_audio" id="cs_preferred_alt_to_audio">
    <?php 
        foreach ($alt_to_audio_values as $key => $value) {
            echo '<option value="'.$value.'" ';
            if ($alt_to_audio == $value) echo 'selected="selected"';
            echo '>'.$alt_to_audio_labels[$key]."</option>";
        } 
    ?>
</select>

<label style="display:block;margin-bottom:0.25em;" for="cs_preferred_alt_to_visual"><?php echo _AT("alt_to_visual") ?></label>
<select style="margin-bottom:1em;margin-left:1em;"name="cs_preferred_alt_to_visual" id="cs_preferred_alt_to_visual">
    <?php 
        foreach ($alt_to_visual_values as $key => $value) {
            echo '<option value="'.$value.'" ';
            if ($alt_to_visual == $value) echo 'selected="selected"';
            echo '>'.$alt_to_visual_labels[$key]."</option>";
        } 
    ?>
</select>

<div style="color:#17B506;position:absolute;left:1em;right:3em;display:none" id="cpref_switch_feedback" role="region" aria-live="polite" aria-relevant="all"><?php echo _AT("AT_FEEDBACK_ACTION_COMPLETED_SUCCESSFULLY")?></div>
<input style="position:absolute;right:2em;bottom:1em;" class="button" type="button" value="<?php echo _AT('apply') ?>" onclick="ATutor.cpref_switch.doPost();" />
</div>
</form>

<?php
$savant->assign('dropdown_contents', ob_get_contents());
ob_end_clean();

$savant->assign('title', _AT('content_settings')); // the box title
$savant->display('include/box.tmpl.php');
?>