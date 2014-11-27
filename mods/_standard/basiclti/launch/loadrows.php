<?php
// THIS FILE APPEARS TO BE UNUSED, TEST WITH IMS VALIDATOR
// exit;
// Needs $content_id and $member_id for the BasicLTI placement
/*$sql = "SELECT * FROM ".TABLE_PREFIX."basiclti_content
                WHERE content_id=".$content_id;
$instanceresult = mysql_query($sql, $db);
$basiclti_content_row = mysql_fetch_assoc($instanceresult);
*/
$sql = "SELECT * FROM %sbasiclti_content WHERE content_id=%d";
$basiclti_content_row = queryDB($sql, array(TABLE_PREFIX, $content_id), TRUE);

if ( ! $basiclti_content_row ) {
    loadError("Not Configured\n");
    exit;
}


$toolid = $basiclti_content_row['toolid'];
/*
$sql = "SELECT * FROM ".TABLE_PREFIX."basiclti_tools
                WHERE toolid='".$toolid."'";
$contentresult = mysql_query($sql, $db);
$basiclti_tool_row = mysql_fetch_assoc($contentresult);
*/
$sql = "SELECT * FROM %sbasiclti_tools WHERE toolid='%s'";
$basiclti_tool_row = queryDB($sql, array(TABLE_PREFIX, $toolid), TRUE);

if ( ! $basiclti_tool_row ) {
    loadError("Tool definition missing\n");
    exit;
}
/*
$sql = "SELECT * FROM ".TABLE_PREFIX."content
                WHERE content_id=".$content_id;
$contentresult = mysql_query($sql, $db);
$atutor_content_row = mysql_fetch_assoc($contentresult);
*/
$sql = "SELECT * FROM %scontent WHERE content_id=%d";
$atutor_content_row = queryDB($sql, array(TABLE_PREFIX, $content_id), TRUE);

if ( ! $atutor_content_row ) {
    loadError("Not Configured\n");
    exit;
}
// echo("atutor_content_row<br/>\n");print_r($atutor_content_row); echo("<hr>\n");
/*
$sql = "SELECT * FROM ".TABLE_PREFIX."courses
                WHERE course_id='".$atutor_content_row['course_id']."'";
$courseresult = mysql_query($sql, $db);
$atutor_course_row = mysql_fetch_assoc($courseresult);
*/
$sql = "SELECT * FROM %scourses WHERE course_id=%d";
$atutor_course_row = queryDB($sql, array(TABLE_PREFIX, $atutor_content_row['course_id']));

if ( ! $atutor_course_row ) {
    loadError("Course definition missing\n");
    exit;
}
// echo("atutor_course_row<br/>\n");print_r($atutor_course_row); echo("<hr>\n");
/*
$sql = "SELECT * FROM ".TABLE_PREFIX."course_enrollment
                WHERE member_id='".$member_id."'";
$enrollresult = mysql_query($sql, $db);
$atutor_course_enrollment_row = mysql_fetch_assoc($enrollresult);
*/
$sql = "SELECT * FROM %scourse_enrollment WHERE member_id=%d";
$atutor_course_enrollment_row = queryDB($sql, array(TABLE_PREFIX, $member_id));

if ( ! $atutor_course_enrollment_row ) {
    loadError("Course enrollment missing\n");
    exit;
}
// echo("atutor_course_enrollment_row<br/>\n");print_r($atutor_course_enrollment_row); echo("<hr>\n");
/*
$sql = "SELECT * FROM ".TABLE_PREFIX."members
                WHERE member_id='".$member_id."'";
$memberresult = mysql_query($sql, $db);
$atutor_member_row = mysql_fetch_assoc($memberresult);
*/
$sql = "SELECT * FROM %smembers WHERE member_id= %d";
$atutor_member_row = queryDB($sql, array(TABLE_PREFIX, $member_id), TRUE);

if (  !$atutor_member_row ) {
    loadError("Course definition missing\n");
    exit;
}
// echo("atutor_member_row<br/>\n");print_r($atutor_member_row); echo("<hr>\n");

?>
