<?php
define('AT_INCLUDE_PATH', '../../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');


function loadError($message) {
    print $message;
    exit();
}

$cid = intval($_GET['cid']);

$content_id = $cid;
$member_id = $_SESSION['member_id'];
require("loadrows.php");
$course_id = $atutor_content_row['course_id'];
// echo("basiclti_content_row<br/>\n");print_r($basiclti_content_row); echo("<hr>\n");
// echo("basiclti_tool_row<br/>\n");print_r($basiclti_tool_row); echo("<hr>\n");
// echo("atutor_content_row<br/>\n");print_r($atutor_content_row); echo("<hr>\n");
// echo("atutor_course_row<br/>\n");print_r($atutor_course_row); echo("<hr>\n");
// echo("atutor_member_row<br/>\n");print_r($atutor_member_row); echo("<hr>\n");
// echo("atutor_course_membership_row<br/>\n");print_r($atutor_course_membership_row); echo("<hr>\n");

    $lmsdata = array(
      "resource_link_id" => $cid,
      "resource_link_title" => $atutor_content_row['title'],
      "resource_link_description" => $atutor_content_row['text'],
      "user_id" => $atutor_member_row['member_id'],
      "roles" => "Learner",
      "launch_presentation_locale" => $_SESSION['lang'],
      "context_id" => $atutor_course_row['course_id'],
      "context_title" => $atutor_course_row['title'],
      "context_label" => $atutor_course_row['title'],
      );

    $lmsdata['ext_lms'] = 'ATutor';

    if ( $atutor_course_membership_row['role'] == 'Instructor' ) {
        $lmsdata["roles"] = 'Instructor';
    }

    if ( $_SESSION['is_admin'] == 1 ) {
        $lmsdata["roles"] = 'Instructor';
    }

    if ( $basiclti_tool_row['sendemailaddr'] == 1 ||
         ( $basiclti_tool_row['sendemailaddr'] == 2 && $basiclti_content_row['sendemailaddr'] == 1 ) ) {
        $lmsdata["lis_person_contact_email_primary"] = $atutor_member_row['email'];
    }

    if ( $basiclti_tool_row['sendname'] == 1 ||
         ( $basiclti_tool_row['sendname'] == 2 && $basiclti_content_row['sendname'] == 1 ) ) {
        $lmsdata["lis_person_name_family"] = $atutor_member_row['last_name'];
        $lmsdata["lis_person_name_given"] = $atutor_member_row['first_name'];
    }

    $placementsecret = $basiclti_content_row['placementsecret'];
    $sourcedid = false;
    if ( isset($placementsecret) && strlen($placementsecret) > 0 ) {
        $suffix = ':::' . $atutor_member_row['member_id'] . ':::' . $cid;
        $plaintext = $placementsecret . $suffix;
        $hashsig = hash('sha256', $plaintext, false);
        $sourcedid = $hashsig . $suffix;
    }

    if ( $sourcedid !== false  &&
         ( $basiclti_tool_row['acceptgrades'] == 1 && $basiclti_content_row['gradebook_test_id'] != 0 ) ) {
        $lmsdata["lis_result_sourcedid"] = $sourcedid;
        $lmsdata["ext_ims_lis_basic_outcome_url"] = AT_BASE_HREF.'mods/_standard/basiclti/launch/service.php';
    }

    if ( $sourcedid !== false  &&
         ( $basiclti_tool_row['allowroster'] == 1 ||
         ( $basiclti_tool_row['allowroster'] == 2 && $basiclti_content_row['allowroster'] == 1 ) ) ) {
        $lmsdata["ext_ims_lis_memberships_id"] = $sourcedid;
        $lmsdata["ext_ims_lis_memberships_url"] = AT_BASE_HREF.'mods/_standard/basiclti/launch/service.php';
    }

    if ( $sourcedid !== false  &&
         ( $basiclti_tool_row['allowsetting'] == 1 ||
         ( $basiclti_tool_row['allowsetting'] == 2 && $basiclti_content_row['allowsetting'] == 1 ) ) ) {
        $lmsdata["ext_ims_lti_tool_setting_id"] = $sourcedid;
        $lmsdata["ext_ims_lti_tool_setting_url"] = AT_BASE_HREF.'mods/_standard/basiclti/launch/service.php';
        $setting = $basiclti_content_row['setting'];
        if ( isset($setting) ) {
             $lmsdata["ext_ims_lti_tool_setting"] = $setting;
        }
    }

require_once("ims-blti/blti_util.php");

    if ( strlen($basiclti_tool_row['customparameters']) > 0 ) {
        $lmsdata = merge_custom_parameters($lmsdata,$basiclti_tool_row['customparameters']);
    }
    if ( $basiclti_tool_row['customparameters'] == 1 && strlen($basiclti_content_row['customparameters']) > 0 ) {
        $lmsdata = merge_custom_parameters($lmsdata,$basiclti_content_row['customparameters']);
    }

// print_r($lmsdata);echo("<hr>\n");

$parms = $lmsdata;

$endpoint = $basiclti_tool_row['toolurl'];
$key = $basiclti_tool_row['resourcekey'];
$secret = $basiclti_tool_row['password'];

  $parms = signParameters($parms, $endpoint, "POST", $key, $secret, "Press to Launch", $tool_consumer_instance_guid, $tool_consumer_instance_description);

  $debuglaunch = false;
  if ( ( $basiclti_tool_row['debuglaunch'] == 1 ||
       ( $basiclti_tool_row['debuglaunch'] == 2 && $basiclti_content_row['debuglaunch'] == 1 ) ) ) {
    $debuglaunch = true;
  }

  $content = postLaunchHTML($parms, $endpoint, $debuglaunch);

  print($content);


?>
