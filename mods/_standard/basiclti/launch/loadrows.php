<?php

// Needs $content_id and $member_id for the BasicLTI placement 
$sql = "SELECT * FROM ".TABLE_PREFIX."basiclti_content
                WHERE content_id=".$content_id;
$instanceresult = mysql_query($sql, $db);
$basiclti_content_row = mysql_fetch_assoc($instanceresult);
if ( ! $basiclti_content_row ) {
    loadError("Not Configured\n");
    exit;
}
// echo("basiclti_content_row<br/>\n");print_r($basiclti_content_row); echo("<hr>\n");

$toolid = $basiclti_content_row['toolid'];
$sql = "SELECT * FROM ".TABLE_PREFIX."basiclti_tools
                WHERE toolid='".$toolid."'";
$contentresult = mysql_query($sql, $db);
$basiclti_tool_row = mysql_fetch_assoc($contentresult);
if ( ! $basiclti_tool_row ) {
    loadError("Tool definition missing\n");
    exit;
}
// echo("basiclti_tool_row<br/>\n");print_r($basiclti_tool_row); echo("<hr>\n");

$sql = "SELECT * FROM ".TABLE_PREFIX."content
                WHERE content_id=".$content_id;
$contentresult = mysql_query($sql, $db);
$atutor_content_row = mysql_fetch_assoc($contentresult);
if ( ! $atutor_content_row ) {
    loadError("Not Configured\n");
    exit;
}
// echo("atutor_content_row<br/>\n");print_r($atutor_content_row); echo("<hr>\n");

$sql = "SELECT * FROM ".TABLE_PREFIX."courses
                WHERE course_id='".$atutor_content_row['course_id']."'";
$courseresult = mysql_query($sql, $db);
$atutor_course_row = mysql_fetch_assoc($courseresult);
if ( ! $atutor_course_row ) {
    loadError("Course definition missing\n");
    exit;
}
// echo("atutor_course_row<br/>\n");print_r($atutor_course_row); echo("<hr>\n");

$sql = "SELECT * FROM ".TABLE_PREFIX."course_enrollment
                WHERE member_id='".$member_id."'";
$enrollresult = mysql_query($sql, $db);
$atutor_course_enrollment_row = mysql_fetch_assoc($enrollresult);
if ( ! $atutor_course_enrollment_row ) {
    loadError("Course enrollment missing\n");
    exit;
}
// echo("atutor_course_enrollment_row<br/>\n");print_r($atutor_course_enrollment_row); echo("<hr>\n");

$sql = "SELECT * FROM ".TABLE_PREFIX."members
                WHERE member_id='".$member_id."'";
$memberresult = mysql_query($sql, $db);
$atutor_member_row = mysql_fetch_assoc($memberresult);
if ( ! $atutor_member_row ) {
    loadError("Course definition missing\n");
    exit;
}
// echo("atutor_member_row<br/>\n");print_r($atutor_member_row); echo("<hr>\n");

?>
