<?php
/************************************************************************/
/* ATutor                                                               */
/************************************************************************/
/* Copyright (c) 2012 to 2017                                                            */
/* ATutorSpaces                                                                                */
/*                                                                                                     */
/* This program is free software. You can redistribute it and/or              */
/* modify it under the terms of the GNU General Public License              */
/* as published by the Free Software Foundation.                                  */
/************************************************************************/
// $Id$
define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

$max_badge_size = 15000;  // maximum file size for  badge in bytes
$max_height = 95;
$max_width = 95;
$ds = DIRECTORY_SEPARATOR; 
 if($_SESSION['course_id'] == -1){
    $course_id = "0";
 } else{
    $course_id = $_SESSION['course_id'];
 }
$base_gameme_dir = AT_CONTENT_DIR.$course_id;
$gameme_course_dir = $base_gameme_dir.'/gameme';
$storeFolder = $gameme_course_dir.'/badges';  
$base_store_folder = "content/".$course_id."/gameme/badges"; 

if(!is_dir($base_gameme_dir )){
    mkdir($base_gameme_dir);
}
if(!is_dir($gameme_course_dir)){
    mkdir($gameme_course_dir);
}
if(!is_dir($storeFolder)){
    mkdir($storeFolder);
}

if (!empty($_FILES)) {
    $tempFile = $_FILES['file']['tmp_name'];                            
    $targetPath = $storeFolder . $ds;     
    $targetFile =  $targetPath. $_FILES['file']['name'];   
    move_uploaded_file($tempFile,$targetFile);      
}

list($width, $height) = getimagesize($targetFile);

if($height > $max_height || $width > $max_width){
    unset($targetFile);
    $msg->addError('GM_MAX_DIMENSION', $max_height, $max_width);
    return false;
} else{
    if($_FILES['file']['size'] < $max_badge_size){
        $sql ="UPDATE %sgm_badges set image_url ='%s' WHERE id = %d AND course_id=%d";
        queryDB($sql, array(TABLE_PREFIX,$base_store_folder.'/'.$_FILES['file']['name'], $_POST["badge_id"], $course_id));
    } else {
        $msg->addError('GM_MAX_FILESESIZE', ($max_badge_size/1000));
        return false;
    }
} 
return true;
?>