<?php 
define('DISPLAY', 0);
define('NAVIGATION', 1);
define('ALT_TO_TEXT', 2);
define('ALT_TO_AUDIO', 3);
define('ALT_TO_VISUAL', 4);
define('SUPPORT', 5);
define('ATUTOR', 6);

global $savant;
global $_base_path;
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <title>Preferences wizard</title>
        <script src="<?php echo $_base_path; ?>jscripts/infusion/InfusionAll.js" type="text/javascript"></script>
		<script src="<?php echo $_base_path; ?>jscripts/TILE.js" type="text/javascript"></script>
    </head>
    <body>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form" enctype="multipart/form-data">
<?php
    $pref_next = $this->pref_next;
    $pref = $this->pref_wiz[$pref_next];
    $submitVal = "Next";
    if ($pref == null) {
        $savant->display('users/pref_wizard/initialize.tmpl.php');
    } else {
        switch ($pref) {
            case DISPLAY:
                include_once('../display_settings.inc.php');
                break;
            case NAVIGATION:
                include_once('../control_settings.inc.php');
                break;
            case ALT_TO_TEXT:
                include_once('../alt_to_text.inc.php');
                break;
            case ALT_TO_AUDIO:
                include_once('../alt_to_audio.inc.php');
                break;
            case ALT_TO_VISUAL:
                include_once('../alt_to_visual.inc.php');
                break;
            case SUPPORT:
                include_once('../tool_settings.inc.php');
                break;
            case ATUTOR:
                include_once('../atutor_settings.inc.php');
                break;
        }

        $pref_next++;
        $max_array_key = count($this->pref_wiz) - 1;       
        if ($pref_next <= $max_array_key) {
            foreach ($this->pref_wiz as $pref => $template) { 
                echo '<input type="hidden" name="pref_wiz[]" value="'.$template.'" />';
            }
            echo '<input type="hidden" value="'.$pref_next.'" name="pref_next" id="pref_next" />';
        } else {
            $submitVal = "Done";
        }
    }
    echo '<input type="submit" value="'.$submitVal.'" name="submit" id="submit" />';
?>
</form>
</body>
<div id="footer">
    <?php require(AT_INCLUDE_PATH.'html/languages.inc.php'); ?>
    <?php require(AT_INCLUDE_PATH.'html/copyright.inc.php'); ?>
</div>
</body>
</html>