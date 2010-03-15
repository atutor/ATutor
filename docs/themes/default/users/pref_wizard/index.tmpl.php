<?php 
define('DISPLAY', 0);
define('STRUCTURE', 1);
define('NAVIGATION', 2);

global $_custom_head, $onload;
    
$_custom_head = "<script language=\"JavaScript\" src=\"jscripts/TILE.js\" type=\"text/javascript\"></script>";
$onload = "setPreviewFace(); setPreviewSize(); setPreviewColours();";

require(AT_INCLUDE_PATH.'header.inc.php'); 
?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form" enctype="multipart/form-data">
<?php
    if (isset($this->pref_template)) {
        switch ($this->pref_template) {
            case DISPLAY:
                include_once('../display_settings.inc.php');
                break;
            case STRUCTURE:
                echo "structural stuff";
                break;
            case NAVIGATION:
                include_once('../control_settings.inc.php');
                break;
        }
    } else {
        $savant->display('users/pref_wizard/initialize.tmpl.php');
    }
?>
    <input type="submit" value="Next" name="next" id="next" />
</form>

<?php 
    require(AT_INCLUDE_PATH.'footer.inc.php'); 
?>