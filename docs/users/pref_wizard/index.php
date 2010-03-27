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

//debug($_POST);

/**
 * Tests if this is the first loading the pref wizard index page
 *
 * @return boolean true if it is the first time, false otherwise
 */
function isFirstLoad() {
    if (isset($_POST['next']) || isset($_POST['previous']) ||
    isset($_POST['done'])) return false;
    return true;
}

/**
 * Tests if this is a return to the initialization page of the pref wizard
 *
 * @return boolean true if it is a return to init page, false otherwise
 */
function isReturnToInit() {
    if (isset($_POST['previous']) && (intVal($_POST['pref_index']) == 0)) return true;
    return false;
}

/**
 * Tests if checkboxes were checked on submission of the initial pref wizard page
 *
 * @return boolean true if no checkboxes were checked, false otherwise
 */
function initNoChecks() {
    if (isset($_POST['next']) && !is_array($_POST['pref_wiz'])) return true;
    return false;
}

//START OF PROCESSING
if (isset($_POST['pref_index'])) {
    $last_pref_index = intVal($_POST['pref_index']);
    if ($last_pref_index >= 0) {
        $temp_prefs = assignPostVars();
        assign_session_prefs($temp_prefs);
        save_prefs();
    }
}

// display initialization page IF
// first time loading pref wiz OR going from first pref page
// to initialize page via previous button OR submit checkboxes with none checked
if (isFirstLoad() || isReturnToInit() || initNoChecks()) {
    if (initNoChecks()) {
        $msg->addError("checkboxes must be checked");
    }
    $savant->assign('start_template', "users/pref_wizard/initialize.tmpl.php");
    $savant->display('users/pref_wizard/index.tmpl.php');
}

// show appropriate preference page (next or previous)
else {
    if (isset($_POST['next'])) $pref_index = $last_pref_index + 1;
    if (isset($_POST['previous'])) $pref_index = $last_pref_index - 1;
    $savant->assign('pref_wiz', $_POST['pref_wiz']);
    $savant->assign('pref_index', $pref_index);
    $languages = $languageManager->getAvailableLanguages();
	$savant->assign('languages', $languages);
    switch ($_POST['pref_wiz'][$pref_index]) {
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
    $savant->display('users/pref_wizard/index.tmpl.php');
}
?>

