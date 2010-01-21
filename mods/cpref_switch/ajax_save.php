<?php
include_once('module.inc.php');
include_once(AT_INCLUDE_PATH."vitals.inc.php");

/**
 * Performs input validation by confirming that the posted variable is one of the 
 * expected input variables. Returns a default variable AT_PREF_NONE if the input is invalid.
 * 
 * @param $post_var The posted variable
 * @param $expected An array of expected variables
 * @return Either the correct posted variable or AT_PREF_NONE if the posted variable is invalid
 */
function check_post_var($post_var, $expected) {
    $var = AT_PREF_NONE;
    if (isset($post_var) && in_array($post_var, $expected)) {
        $var = $post_var;
    }
    return $var;
}

/**
 * Checks if the content preference type changed from the user's current settings.
 * 
 * @param $post_var The content preference type posted.
 * @param $use_pref_name The name of the 'use content' preference (ie. PREF_USE_ALTERNATIVE_TO_TEXT).
 * @param $alternative_pref The name of the 'alternative content' preference (ie. PREF_ALT_TO_TEXT).
 * @return A boolean value false if the preference has not changed, true otherwise.
 */
function isPrefChanged($post_var, $use_pref_name, $alternative_pref) {
    if ($_SESSION['prefs'][$use_pref_name] == 0 && $post_var == AT_PREF_NONE) {
        return false;
    }
    if ($_SESSION['prefs'][$use_pref_name] == 1 && $post_var == $_SESSION['prefs'][$alternative_pref]) {
        return false;
    }
    return true;
}

/**
 * Changes a temporary array of preferences to reflect the new settings. Returns false if the preferences
 * have not changed and true otherwise.
 * 
 * @param $temp_prefs An array of preferences passed by reference.
 * @param $post_var The content preference posted.
 * @param $pref_type The type of content preference alternative to set, either "TEXT", "AUDIO" or "VISUAL".
 * @return A boolean value false if the preference has not changed, true otherwise.
 */
function changePreference(&$temp_prefs, $post_var, $pref_type) {
    $pref_changed = false;
    if (isPrefChanged($post_var, 'PREF_USE_ALTERNATIVE_TO_'.$pref_type, 'PREF_ALT_TO_'.$pref_type)) {
        $pref_changed = true;
        if ($post_var == AT_PREF_NONE) {
            //change the first setting and leave the rest as chosen by the user
            $temp_prefs['PREF_USE_ALTERNATIVE_TO_'.$pref_type] = 0;
            $temp_prefs['PREF_ALT_TO_'.$pref_type] = $_SESSION['prefs']['PREF_ALT_TO_'.$pref_type];
            $temp_prefs['PREF_ALT_TO_'.$pref_type.'_APPEND_OR_REPLACE'] = $_SESSION['prefs']['PREF_ALT_TO_'.$pref_type.'_APPEND_OR_REPLACE'];
            $temp_prefs['PREF_ALT_'.$pref_type.'_PREFER_LANG'] = $_SESSION['prefs']['PREF_ALT_'.$pref_type.'_PREFER_LANG'];
        } else {
            //change first two settings and leave the rest as chosen by the user
            $temp_prefs['PREF_USE_ALTERNATIVE_TO_'.$pref_type] = 1;
            $temp_prefs['PREF_ALT_TO_'.$pref_type] = $post_var;
            $temp_prefs['PREF_ALT_TO_'.$pref_type.'_APPEND_OR_REPLACE'] = $_SESSION['prefs']['PREF_ALT_TO_'.$pref_type.'_APPEND_OR_REPLACE'];
            $temp_prefs['PREF_ALT_'.$pref_type.'_PREFER_LANG'] = $_SESSION['prefs']['PREF_ALT_'.$pref_type.'_PREFER_LANG'];
        }
    }
    return $pref_changed;
}

//do post variable input validation
$alt_to_text = check_post_var($addslashes($_POST[AT_POST_ALT_TO_TEXT]), array(AT_PREF_NONE, AT_PREF_AUDIO, AT_PREF_VISUAL, AT_PREF_SIGN));
$alt_to_audio = check_post_var($addslashes($_POST[AT_POST_ALT_TO_AUDIO]), array(AT_PREF_NONE, AT_PREF_TEXT, AT_PREF_VISUAL, AT_PREF_SIGN));
$alt_to_visual = check_post_var($addslashes($_POST[AT_POST_ALT_TO_VISUAL]), array(AT_PREF_NONE, AT_PREF_TEXT, AT_PREF_AUDIO, AT_PREF_SIGN));

//if preferences have changed then change $_SESSION variable
//save settings if user is student
$temp_prefs = $_SESSION['prefs'];
$text_prefs_changed = changePreference($temp_prefs, $alt_to_text, "TEXT");
$audio_prefs_changed = changePreference($temp_prefs, $alt_to_audio, "AUDIO");
$visual_prefs_changed = changePreference($temp_prefs, $alt_to_visual, "VISUAL");

$is_preferences_changed = $text_prefs_changed || $audio_prefs_changed || $visual_prefs_changed;
if ($is_preferences_changed) {
    assign_session_prefs($temp_prefs);
    save_prefs(); 
}

echo $is_preferences_changed;
?>