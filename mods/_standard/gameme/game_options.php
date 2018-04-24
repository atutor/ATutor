<?php
namespace gameme\PHPGamification;

define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

global $_base_href;
array_pop($_POST);
$sql = "DELETE from %sgm_options WHERE course_id=%d";
queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id']));
foreach($_POST as $option=>$value){
    if($value == 'on'){
        $value = 1;
    } else if($value == 'off'){
        $value = 0;
    }
    $sql = "INSERT into %sgm_options(`id`,`course_id`, `gm_option`, `value`) VALUES ('',%d, '%s',%d)";
    if(queryDB($sql, array(TABLE_PREFIX,$_SESSION['course_id'], $option, $value))){
        // do nothing
    } else {
        // en error occured
        $has_error = 1;
    }
}

if(!isset($has_error)){
    $msg->addFeedback('GM_UPDATED_OPTIONS');
}else{
    $msg->addError('GM_UPDATED_OPTIONS_FAILED');
}
header('Location: '.$_base_href.'mods/_standard/gameme/index_instructor.php');
exit;

?>