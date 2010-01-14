<?php
function check_post_var($post_var, $expected) {
    $var = "none";    
    if (isset($post_var) && in_array($post_var, $expected)) {
        $var = $post_var;    
    }
    return $var;
}

$alt_to_text = check_post_var($_POST['alt_to_text'], array('none', 'audio', 'visual', 'sign_lang'));
$alt_to_audio = check_post_var($_POST['alt_to_audio'], array('none', 'text', 'visual', 'sign_lang'));
$alt_to_visual = check_post_var($_POST['alt_to_visual'], array('none', 'text', 'audio', 'sign_lang'));

echo $alt_to_text." ".$alt_to_audio." ".$alt_to_visual;

?>