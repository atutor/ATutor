<?php

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'config.inc.php');
require_once(AT_INCLUDE_PATH.'lib/mysql_connect.inc.php');

    require_once("ims-blti/OAuth.php");
    require_once("TrivialStore.php");

error_reporting(E_ALL & ~E_NOTICE);
ini_set("display_errors", 1);

    function message_response($major, $severity, $minor=false, $message=false, $xml=false) {
        $lti_message_type = $_REQUEST['lti_message_type'];
        $retval = '<?xml version="1.0" encoding="UTF-8"?>'."\n" .
        "<message_response>\n" .
        "  <lti_message_type>$lti_message_type</lti_message_type>\n" .
        "  <statusinfo>\n" .
        "     <codemajor>$major</codemajor>\n" .
        "     <severity>$severity</severity>\n";
        if ( ! $codeminor === false ) $retval = $retval .  "     <codeminor>$minor</codeminor>\n";
	$retval = $retval . 
        "     <description>$message</description>\n" .
        "  </statusinfo>\n";
        if ( ! $xml === false ) $retval = $retval . $xml;
        $retval = $retval . "</message_response>\n";
	return $retval;
    }

    function doError($message) {
        print message_response('Fail', 'Error', false, $message);
        exit();
    }

    $lti_version = $_REQUEST['lti_version'];
    if ( $lti_version != "LTI-1p0" ) doError("Improperly formed message");

    $lti_message_type = $_REQUEST['lti_message_type'];
    if ( ! isset($lti_message_type) ) doError("Improperly formed message");

    $message_type = false;
    if( $lti_message_type == "basic-lis-replaceresult" ||
        $lti_message_type == "basic-lis-createresult" ||
        $lti_message_type == "basic-lis-updateresult" ||
        $lti_message_type == "basic-lis-deleteresult" ||
        $lti_message_type == "basic-lis-readresult" ) {
          $sourcedid = $_REQUEST['sourcedid'];
          $message_type = "basicoutcome";
    } else if ( $lti_message_type == "basic-lti-loadsetting" ||
        $lti_message_type == "basic-lti-savesetting" ||
        $lti_message_type == "basic-lti-deletesetting" ) {
          $sourcedid = $_REQUEST['id'];
          $message_type = "toolsetting";
    } else if ( $lti_message_type == "basic-lis-readmembershipsforcontext") {
          $sourcedid = $_REQUEST['id'];
          $message_type = "roster";
    }

    if ( $message_type == false ) {
        doError("Illegal lti_message_type");
    }

    if ( !isset($sourcedid) ) {
        doError("sourcedid missing");
    }
    // Truncate to maximum length
    $sourcedid = substr($sourcedid, 0, 2048);

    try {
        $info = explode(':::',$sourcedid);
        if ( ! is_array($info) ) doError("Bad sourcedid");
        $signature = $info[0];
        $userid = intval($info[1]);
        $placement = $info[2];
    }
    catch(Exception $e) {
        doError("Bad sourcedid");
    }

    if ( isset($signature) && isset($userid) && isset($placement) ) {
        // OK
    } else {
        doError("Bad sourcedid");
    }

function loadError($msg) {
   doError($msg);
}

$content_id = $placement;
$member_id = $userid;
require("loadrows.php");
$course_id = $atutor_content_row['course_id'];
// echo("basiclti_content_row<br/>\n");print_r($basiclti_content_row); echo("<hr>\n");
// echo("basiclti_tool_row<br/>\n");print_r($basiclti_tool_row); echo("<hr>\n");
// echo("atutor_content_row<br/>\n");print_r($atutor_content_row); echo("<hr>\n");
// echo("atutor_course_row<br/>\n");print_r($atutor_course_row); echo("<hr>\n");
// These two might not be important here
// echo("atutor_member_row<br/>\n");print_r($atutor_member_row); echo("<hr>\n");
// echo("atutor_course_membership_row<br/>\n");print_r($atutor_course_membership_row); echo("<hr>\n");

    if ( $message_type == "basicoutcome" ) {
        if ( $basiclti_tool_row['acceptgrades'] == 1 && $basiclti_content_row['gradebook_test_id'] > 0 ) {
            // The placement is configured to accept grades
        } else { 
            doError("Not permitted");
        }
    } else if ( $message_type == "roster" ) {
        if ( $basiclti_tool_row['allowroster'] == 1 ||
           ( $basiclti_tool_row['allowroster'] == 2 && $basiclti_content_row['allowroster'] == 1 ) ) {
            // OK
        } else { 
            doError("Not permitted");
        }
    } else if ( $message_type == "toolsetting" ) {
        if  ( $basiclti_tool_row['allowsetting'] == 1 ||
            ( $basiclti_tool_row['allowsetting'] == 2 && $basiclti_content_row['allowsetting'] == 1 ) ) {
            // OK
        } else { 
            doError("Not permitted");
        }
    }

    // Retrieve the secret we use to sign lis_result_sourcedid
    $placementsecret = $basiclti_content_row['placementsecret'];
    $oldplacementsecret = $basiclti_content_row['oldplacementsecret'];
    if ( ! isset($placementsecret) ) doError("Not permitted");

    $suffix = ':::' . $userid . ':::' . $placement;
    $plaintext = $placementsecret . $suffix;
    $hashsig = hash('sha256', $plaintext, false);
    if ( $hashsig != $signature && isset($oldplacementsecret) && strlen($oldplacementsecret) > 1 ) {
        $plaintext = $oldplacementsecret . $suffix;
        $hashsig = hash('sha256', $plaintext, false);
    }
        
    if ( $hashsig != $signature ) {
        doError("Invalid sourcedid");
    }

    // Check the OAuth Signature 
    $oauth_consumer_key = $basiclti_tool_row['resourcekey'];
    $oauth_secret = $basiclti_tool_row['password'];

    if ( ! isset($oauth_secret) ) doError("Not permitted");
    if ( ! isset($oauth_consumer_key) ) doError("Not permitted");

    // Verify the message signature
    $store = new TrivialOAuthDataStore();
    $store->add_consumer($oauth_consumer_key, $oauth_secret);

    $server = new OAuthServer($store);

    $method = new OAuthSignatureMethod_HMAC_SHA1();
    $server->add_signature_method($method);
    $request = OAuthRequest::from_request();

    $basestring = $request->get_signature_base_string();

    try {
        $server->verify_request($request);
    } catch (Exception $e) {
        doError($e->getMessage());
    }

    // Beginning of actual grade processing
    if ( $message_type == "basicoutcome" ) {
        if ( ! isset( $basiclti_content_row['gradebook_test_id'] ) ) {
            doError("Not permitted");
        }

        // TODO: Greg - Is this appropriate?  It would be nice to allow this.
        if ( $atutor_course_membership_row['role'] == 'Instructor' ) {
            doError('Grades not supported for instructors');
        }

        $gradebook_test_id = $basiclti_content_row['gradebook_test_id'];

        // Check to see if this grade is in this course and member is in this course
	// And that this grade item is of the right type
        $sql = 'SELECT role,m.member_id AS member_id,first_name,last_name,email 
            FROM  '.TABLE_PREFIX.'gradebook_tests AS g
            JOIN  '.TABLE_PREFIX.'course_enrollment AS e
            JOIN  '.TABLE_PREFIX.'members AS m 
            ON g.course_id = e.course_id AND e.member_id = m.member_id 
            WHERE e.course_id = '.$course_id.' AND m.member_id ='.$member_id.'
            AND g.gradebook_test_id = '.$gradebook_test_id."
            AND g.type = 'External' and g.grade_scale_id = 0";
        $gradebook_result = mysql_query($sql, $db);
        $count = mysql_num_rows($gradebook_result);
        if ( $count < 1 ) {
            doError("Not gradable");
        }

        $read_sql = 'SELECT d.grade AS grade
            FROM  '.TABLE_PREFIX.'gradebook_detail AS d
            JOIN  '.TABLE_PREFIX.'gradebook_tests AS g
            JOIN  '.TABLE_PREFIX.'course_enrollment AS e
            JOIN  '.TABLE_PREFIX.'members AS m 
            ON d.gradebook_test_id = g.gradebook_test_id 
            AND g.course_id = e.course_id AND e.member_id = m.member_id 
            WHERE e.course_id = '.$course_id.' AND d.member_id ='.$member_id.'
            AND g.gradebook_test_id = '.$gradebook_test_id."
            AND g.type = 'External' and g.grade_scale_id = 0";

        if ( $lti_message_type == "basic-lis-readresult" ) {
            $grade_result = mysql_query($read_sql, $db);
            $count = mysql_num_rows($gradebook_result);
            if ( $count < 1 ) {
                doError("Not gradable");
            }
            unset($grade);
            $grade_row = mysql_fetch_assoc($grade_result);
            if ( $grade_row === false ) {
                // Skip
            } else if ( isset($grade_row['grade']) ) { 
                $grade = $grade_row['grade'];
            }

            if ( ! isset($grade) ) {
                doError("Unable to read grade");
            }
               
            $result = "  <result>\n" .
                "     <resultscore>\n" .
                "        <textstring>" .
                htmlspecialchars($grade*1.0) .
                "</textstring>\n" .
                "     </resultscore>\n" .
                "  </result>\n";
            print message_response('Success', 'Status', false, "Grade read", $result);
            exit();
       }
    
        if ( $lti_message_type == "basic-lis-deleteresult" ) {
            $delete_sql = 'DELETE FROM '.TABLE_PREFIX.'gradebook_detail 
                WHERE member_id ='.$member_id.'
                AND gradebook_test_id = '.$gradebook_test_id;

            $gradebook_result = mysql_query($delete_sql, $db);
            if ( $gradebook_result === false ) {
                doError("Could not delete grade");
            }
            print message_response('Success', 'Status', 'fullsuccess', 'Grade deleted');

        } else { // Replace
            $gradeval = -1.0;
            if ( isset($_REQUEST['result_resultscore_textstring']) && strlen($_REQUEST['result_resultscore_textstring']) > 0) {
               $gradeval = floatval($_REQUEST['result_resultscore_textstring']);
            } 
            if ( $gradeval < 0.0 || $gradeval > 1.0 ) {
                doError('Invalid Grade');
            }

            // TODO: Greg - do we do Insert or Update?
            $replace_sql = 'INSERT INTO '.TABLE_PREFIX.'gradebook_detail 
                (gradebook_test_id, member_id, grade) VALUES
                ('.$gradebook_test_id.','.$member_id.','.$gradeval.')
                ON DUPLICATE KEY UPDATE grade='.$gradeval;

            $gradebook_result = mysql_query($replace_sql, $db);
            if ( $gradebook_result === false ) {
                // TODO: Log message would be good here
                doError("Could not store grade");
            }
            print message_response('Success', 'Status', 'fullsuccess', 'Grade updated');
        }
    

    } else if ( $lti_message_type == "basic-lti-loadsetting" ) {
        $xml = "  <setting>\n" .
               "     <value>".htmlspecialchars($basiclti_content_row['setting'])."</value>\n" .
               "  </setting>\n";
        print message_response('Success', 'Status', 'fullsuccess', 'Setting retrieved', $xml);
    } else if ( $lti_message_type == "basic-lti-savesetting" ) {
        $setting = $_REQUEST['setting'];
        if ( ! isset($setting) ) doError('Missing setting value');
        // $sql = "UPDATE {$CFG->prefix}basiclti SET 
               // setting='". mysql_escape_string($setting) . "' WHERE id=" . $basiclti->id;
        $sql = "UPDATE ".TABLE_PREFIX."basiclti_content
               SET setting='". mysql_escape_string($setting) . "' WHERE content_id=" . $placement;
        $success = mysql_query($sql);
        if ( $success ) {
            print message_response('Success', 'Status', 'fullsuccess', 'Setting updated');
        } else {
            doError("Error updating setting");
        }
    } else if ( $lti_message_type == "basic-lti-deletesetting" ) {
        $sql = "UPDATE ".TABLE_PREFIX."basiclti_content
               SET setting='' WHERE content_id=" . $placement;
        $success = mysql_query($sql);
        if ( $success ) {
            print message_response('Success', 'Status', 'fullsuccess', 'Setting deleted');
        } else {
            doError("Error updating setting");
        }
    } else if ( $message_type == "roster" ) {
        $sql = 'SELECT role,m.member_id AS member_id,first_name,last_name,email 
            FROM  '.TABLE_PREFIX.'course_enrollment AS e
            JOIN  '.TABLE_PREFIX.'members AS m ON e.member_id = m.member_id 
            WHERE course_id = '.$course_id;
        $roster_result = mysql_query($sql, $db);
        $xml = "  <memberships>\n";
        while ($row = mysql_fetch_assoc($roster_result)) {
            $role = "Learner";
            if ( $row['role'] == 'Instructor' ) $role = 'Instructor';
            $userxml = "    <member>\n".
                       "      <user_id>".htmlspecialchars($row['member_id'])."</user_id>\n".
                       "      <roles>$role</roles>\n";
            if ( $basiclti_tool_row['sendname'] == 1 ||
                 ( $basiclti_tool_row['sendname'] == 2 && $basiclti_content_row['sendname'] == 1 ) ) {
                if ( isset($row['first_name']) ) $userxml .=  "      <person_name_given>".htmlspecialchars($row['first_name'])."</person_name_given>\n";
                if ( isset($row['last_name']) ) $userxml .=  "      <person_name_family>".htmlspecialchars($row['last_name'])."</person_name_family>\n";
            }
            if ( $basiclti_tool_row['sendemailaddr'] == 1 ||
                 ( $basiclti_tool_row['sendemailaddr'] == 2 && $basiclti_content_row['sendemailaddr'] == 1 ) ) {
                if ( isset($row['email']) ) $userxml .=  "      <person_contact_email_primary>".htmlspecialchars($row['email'])."</person_contact_email_primary>\n";
            }
            if ( isset($placementsecret) ) {
                $suffix = ':::' . $row['member_id'] . ':::' . $placement;
                $plaintext = $placementsecret . $suffix;
                $hashsig = hash('sha256', $plaintext, false);
                $sourcedid = $hashsig . $suffix;
            }
            if ( $basiclti_tool_row['acceptgrades'] == 1 && $basiclti_content_row['gradebook_test_id'] > 0 ) {
                if ( isset($sourcedid) ) $userxml .=  "      <lis_result_sourcedid>".htmlspecialchars($sourcedid)."</lis_result_sourcedid>\n";
            }
            $userxml .= "    </member>\n";
            $xml .= $userxml;
        }
        $xml .= "  </memberships>\n";
        print message_response('Success', 'Status', 'fullsuccess', 'Roster retreived', $xml);

    }
    
?>
