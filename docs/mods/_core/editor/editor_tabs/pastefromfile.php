<?php
define('AT_INCLUDE_PATH', '../../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

//$cid = intval($_REQUEST['cid']);

//if (isset($_POST['submit_file']))
//{
//    $paste_content = paste_from_file();
//    echo '<script type="text/javascript">';
//    echo 'window.opener.document.getElementById("body_text")="'.$paste_content.'"';
//    echo '</script>';
//}
?>
<script type="text/javascript">
function pasteFromFile() {
	form.submit();
	self.close();
}

window.opener.name = "edit_window";
</script>
<form action="<?php echo AT_BASE_HREF; ?>mods/_core/editor/edit_content.php?cid=<?php echo $cid; ?>" target="edit_window" method="post" name="form" enctype="multipart/form-data">
    <input type="hidden" name="current_tab" id="current_tab" value = "0" /> 
    <input type="file" name="uploadedfile_paste" id="uploadedfile" class="formfield" size="20" /> 
    <input type="submit" name="submit_file" value="<?php echo _AT('paste'); ?>"  class="button" onClick="pasteFromFile();" />
</form>