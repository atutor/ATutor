<?php 
global $savant;
global $_base_path;
/* start output buffering: */
ob_start(); ?>

<script type="text/javascript">
var ATutor = ATutor || {};

(function ($, ATutor) {
	ATutor.cf_switch_doPost = function () {
        jQuery.post("<?php echo AT_BASE_HREF; ?>mods/cpref_switch/ajax_save.php", 
                { "alt_to_text": jQuery("#cs_preferred_alt_to_text").val(),
                  "alt_to_audio": jQuery("#cs_preferred_alt_to_audio").val(),
                  "alt_to_visual": jQuery("#cs_preferred_alt_to_visual").val()
                 }
       );
    };
})(jQuery, ATutor);
</script>

<form class="cpref_switch" method="post" name="cpref_switch_form">
<div>
<label for="cs_preferred_alt_to_text">Alternative to Text:</label>
<select name="cs_preferred_alt_to_text" id="cs_preferred_alt_to_text">
    <option value="none" selected="selected">None</option>
    <option value="audio">Audio</option>
    <option value="visual" >Visual</option>
    <option value="sign_lang" >Sign Language</option>
</select>

<label for="cs_preferred_alt_to_audio">Alternative to Audio:</label>
<select name="cs_preferred_alt_to_audio" id="cs_preferred_alt_to_audio">
    <option value="none" selected="selected">None</option>
    <option value="text">Text</option>
    <option value="visual" >Visual</option>
    <option value="sign_lang" >Sign Language</option>
</select>

<label for="cs_preferred_alt_to_visual">Alternative to Visual:</label>
<select name="cs_preferred_alt_to_visual" id="cs_preferred_alt_to_visual">
    <option value="none" selected="selected">None</option>
    <option value="text">Text</option>
    <option value="audio" >Audio</option>
    <option value="sign_lang" >Sign Language</option>
</select>

<input class="button" type="button" value="Submit" 
       onclick="ATutor.cf_switch_doPost();" />
</div>
</form>

<?php
$savant->assign('dropdown_contents', ob_get_contents());
ob_end_clean();

$savant->assign('title', _AT('cpref_switch')); // the box title
$savant->display('include/box.tmpl.php');
?>