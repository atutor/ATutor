<?php
define('DISPLAY', 0);
define('NAVIGATION', 1);
define('ALT_TO_TEXT', 2);
define('ALT_TO_AUDIO', 3);
define('ALT_TO_VISUAL', 4);
define('SUPPORT', 5);
define('ATUTOR', 6);

define('AT_INCLUDE_PATH', '../../include/');
$_user_location = 'users';
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'lib/themes.inc.php');
require(AT_INCLUDE_PATH.'../mods/_core/users/lib/pref_tab_functions.inc.php');
require(AT_INCLUDE_PATH.'../mods/_core/users/lib/pref_functions.inc.php');

//first time loading - show pref checkboxes
if (!isset($_POST['submit'])) {
    $savant->assign('start_template', "users/pref_wizard/initialize.tmpl.php");
    $savant->display('users/pref_wizard/index.tmpl.php');
} 
// last preference page reached - close the wizard
else if ($_POST['submit'] == 'Done') {
    echo '<script type="text/javascript">';
    echo "window.close();";
    echo '</script>';
} 
// show appropriate preference page
else if ($_POST['submit'] == 'Next') {
    //if there are no checkboxes checked then put up error message.
    if(!isset($_POST['pref_wiz'])) {
        $msg->addError("checkboxes must be checked");
        $savant->assign('start_template', "users/pref_wizard/initialize.tmpl.php");
        $savant->display('users/pref_wizard/index.tmpl.php');
    } else {
        $pref_next = intVal($_POST['pref_next']);
        switch ($_POST['pref_wiz'][$pref_next]) {
            case DISPLAY:
                $savant->assign('pref_template', '../display_settings.inc.php');
                $savant->assign('onload', 'setPreviewFace(); setPreviewSize(); setPreviewColours();');
                break;
            case NAVIGATION:
               $savant->assign('pref_template', '../control_settings.inc.php');
                break;
            case ALT_TO_TEXT:
               $savant->assign('pref_template', '../alt_to_text.inc.php');
                break;
            case ALT_TO_AUDIO:
               $savant->assign('pref_template', '../alt_to_audio.inc.php');
              break;
            case ALT_TO_VISUAL:
                $savant->assign('pref_template', '../alt_to_visual.inc.php');
                 break;
            case SUPPORT:
                 $savant->assign('pref_template', '../tool_settings.inc.php');
                 break;
            case ATUTOR:
                $savant->assign('pref_template', '../atutor_settings.inc.php');
                break;
        }
        $savant->assign('pref_wiz', $_POST['pref_wiz']);
        $savant->assign('pref_next', $pref_next);
        $savant->display('users/pref_wizard/index.tmpl.php');     
    }
} 
?>
