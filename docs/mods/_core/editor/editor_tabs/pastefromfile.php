<?php
define('AT_INCLUDE_PATH', '../../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

$cid = intval($_REQUEST['cid']);
?>
<form action="<?php echo AT_BASE_HREF; ?>mods/_core/editor/edit_content.php?cid=<?php echo $cid; ?>" method="post" name="form" enctype="multipart/form-data"> 
    <input type="file" name="uploadedfile_paste" id="uploadedfile" class="formfield" size="20" /> 
    <input type="submit" name="submit_file" value="<?php echo _AT('paste'); ?>"  class="button" />
</form>