<?php 
global $savant;
/* start output buffering: */
ob_start(); ?>

<form action="" method="get" name="">
<label for="cs_preferred_alt_to_text">Alternative to Text</label>
<select name="cs_preferred_alt_to_text" id="cs_preferred_alt_to_text">
    <option value="audio" selected="selected">Audio</option>
    <option value="visual" >Visual</option>
    <option value="sign_lang" >Sign Language</option>
</select>

<label for="cs_preferred_alt_to_audio">Alternative to Audio</label><br />
<select name="cs_preferred_alt_to_audio" id="cs_preferred_alt_to_audio">
    <option value="text" selected="selected">Text</option>
    <option value="visual" >Visual</option>
    <option value="sign_lang" >Sign Language</option>
</select>

<label for="cs_preferred_alt_to_visual">Preferred Alternative</label><br />
<select name="cs_preferred_alt_to_visual" id="cs_preferred_alt_to_visual">
    <option value="text" selected="selected">Text</option>
    <option value="audio" >Audio</option>
    <option value="sign_lang" >Sign Language</option>
</select>

<input type="submit" class="button" />
</form>

<?php
$savant->assign('dropdown_contents', ob_get_contents());
ob_end_clean();

$savant->assign('title', _AT('cpref_switch')); // the box title
$savant->display('include/box.tmpl.php');
?>