<?php
define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_BASICLTI);

require_once('forms.php');

;
// Filter all GET data //
$_POST['framed'] = intval($_POST['framed']);
$_POST['popup'] = intval($_POST['popup']);
$_POST['cid'] = intval($_POST['cid']);

// Filter all POST data //
$_POST['toolid'] = $addslashes($_POST['toolid']);
$_POST['cid'] = intval($_POST['cid']);
$_POST['preferheight'] = intval($_POST['preferheight']);
$_POST['launchinpopup'] = intval($_POST['launchinpopup']);
$_POST['debuglaunch'] = intval($_POST['debuglaunch']);
$_POST['sendname'] = intval($_POST['sendname']);
$_POST['sendemailaddr'] = intval($_POST['sendemailaddr']);
$_POST['allowroster'] = intval($_POST['allowroster']);
$_POST['allowsetting'] = intval($_POST['allowsetting']);
$_POST['customparameters'] = $addslashes($_POST['customparameters']);

if ( !is_int($_SESSION['course_id']) || $_SESSION['course_id'] < 1 ) {
    $msg->addFeedback('NEED_COURSE_ID');
    exit;
}

// Add/Update The Tool
if ( isset($_POST['toolid']) && at_form_validate($blti_content_edit_form, $msg)) {
    $toolid = $_POST['toolid']; // Escaping is done in the at_form_util code
    $sql = "SELECT * FROM ".TABLE_PREFIX."basiclti_content
            WHERE content_id=".$_POST[cid]." AND course_id=".$_SESSION[course_id];


    $result = mysql_query($sql, $db);
    if ( $toolid == '--none--' ) {
        $sql = "DELETE FROM ". TABLE_PREFIX . "basiclti_content 
                       WHERE content_id=".$_POST[cid]." AND 
                             course_id=".$_SESSION[course_id];
            $result = mysql_query($sql, $db);
            if ($result===false) {
                $msg->addError('MYSQL_FAILED');
            } else {
                $msg->addFeedback('BASICLTI_DELETED');
            }
    } else if ( mysql_num_rows($result) == 0 ) {
            $sql = "INSERT INTO ". TABLE_PREFIX . "basiclti_content 
                       SET toolid='".$toolid."', content_id=".$_POST[cid].",
                             course_id=".$_SESSION[course_id];

            $result = mysql_query($sql, $db);
            if ($result===false) {
                $msg->addError('MYSQL_FAILED');
            } else {
                $msg->addFeedback('BASICLTI_SAVED');
            }

    } else if ( $result !== false ) {

            $gradebook_test_id = 0;
            $basiclti_content_row = mysql_fetch_assoc($result);
            $placementsecret = $basiclti_content_row['placementsecret'];
            $gradebook_check = intval($_POST['gradebook_test_id']);
            if ( isset($_POST['gradebook_test_id']) && $gradebook_check > 0 ) {
		$gradebook_test_id = $gradebook_check;
                $sql = "SELECT g.gradebook_test_id AS id, g.title AS title
                        FROM  ".TABLE_PREFIX."gradebook_tests AS g
                        WHERE g.course_id = ".$_SESSION[course_id]."
                        AND g.type = 'External' and g.grade_scale_id = 0
                        AND gradebook_test_id = ".$gradebook_test_id;
                $result = mysql_query($sql, $db);
                if ( $result === false ) {
                    $gradebook_test_id = 0;
                } else {
                    if ( strlen($placementsecret) < 1 ) {
                        $placementsecret = uniqid("bl",true);
                    }
                }
            }
	    // Override these fields (don't take from form)
            $fields = array('toolid' => $toolid, 'gradebook_test_id' => $gradebook_test_id,
                            'placementsecret' => $placementsecret);
            $sql = at_form_update($_POST, $blti_content_edit_form, $fields);
            $sql = "UPDATE ". TABLE_PREFIX . "basiclti_content 
                       SET ".$sql." WHERE content_id=".$_POST[cid]." AND 
                           course_id=".$_SESSION[course_id];
            $result = mysql_query($sql, $db);
            if ($result===false) {
                $msg->addError('MYSQL_FAILED');
            } else {
                $msg->addFeedback('BASICLTI_SAVED');
            }
    }
}

// echo("<hr>$sql<hr>\n");

$cid = intval($_REQUEST['cid']);

global $framed, $popup;

if ((isset($_REQUEST['popup']) && $_REQUEST['popup']) &&
    (!isset($_REQUEST['framed']) || !$_REQUEST['framed'])) {
    $popup = TRUE;
    $framed = FALSE;
} elseif (isset($_REQUEST['framed']) && $_REQUEST['framed'] && isset($_REQUEST['popup']) && $_REQUEST['popup']) {
    $popup = TRUE;
    $framed = TRUE;
    $tool_flag = TRUE;
} else {
    $popup = FALSE;
    $framed = FALSE;
}

require(AT_INCLUDE_PATH.'header.inc.php');

/* get a list of all the tools, we have */
$sql    = "SELECT * FROM ".TABLE_PREFIX."basiclti_tools WHERE course_id = 0".
          " OR course_id=".$_SESSION[course_id]." ORDER BY course_id,title";

$toolresult = mysql_query($sql, $db);
$num_tools = mysql_num_rows($toolresult);

//If there are no Tools, don't display anything except a message
if ($num_tools == 0){
        $msg->addInfo('NO_PROXY_TOOLS');
        $msg->printInfos();
        return;
}

?>
<div class="input-form">

<form name="datagrid" action="" method="POST">

<fieldset class="group_form">
   <legend class="group_form"><?php echo _AT('bl_content_title'); ?></legend>
<br/>
<?php echo _AT('basiclti_comment');?>
<br/>
<?php echo $msg->printFeedbacks();

// Get the current content item
$sql = "SELECT * FROM ".TABLE_PREFIX."basiclti_content 
                WHERE content_id=$cid";
$contentresult = mysql_query($sql, $db);
$basiclti_content_row = mysql_fetch_assoc($contentresult);
// if ( $basiclti_content_row ) echo("FOUND"); else echo("NOT");
?>
<div class="row">
   <?php echo _AT('bl_choose_tool'); ?><br/>
   <select id="toolid" name="toolid" onchange="datagrid.submit();"> 
      <option value="--none--">&nbsp;</option><?php
      $basiclti_tool_row = false;
      $found = false;  // Only the first one
      while ( $tool = mysql_fetch_assoc($toolresult) ) {
         $selected = "";
         if ( ! $found && $tool['toolid'] == $basiclti_content_row['toolid'] ) {
           $selected = ' selected="yes"';
           $basiclti_tool_row = $tool;
           $found = true;
         }
         echo '<option value="'.$tool['toolid'].'"'.$selected.'>'.$tool['title']."</option>\n";
      } ?>
   </select>
<div>
<?php
if ( $basiclti_tool_row != false && $basiclti_tool_row['acceptgrades'] == 1 ) {
    $sql = "SELECT g.gradebook_test_id AS id, g.title AS title
            FROM  ".TABLE_PREFIX."gradebook_tests AS g
            WHERE g.course_id = ".$_SESSION[course_id]."
            AND g.type = 'External' and g.grade_scale_id = 0";
    $graderesult = mysql_query($sql, $db);
    if ( $graderesult !== false && mysql_num_rows($graderesult) > 0) { ?>
<div class="row">
   <?php echo _AT('bl_choose_gradbook_entry'); ?><br/>
        <select id="gradebook_test_id" name="gradebook_test_id"> 
           <option value="--none--">&nbsp;</option><?php
        while ( $gradeitem = mysql_fetch_assoc($graderesult) ) {
            echo($gradeitem['title']);
            $selected = "";
            if ( $gradeitem['id'] == $basiclti_content_row['gradebook_test_id'] ) {
              $selected = ' selected="yes"';
            }
            echo '<option value="'.$gradeitem['id'].'"'.$selected.'>'.$gradeitem['title']."</option>\n";
        } ?>
        </select> 
</div> <?php
    }
}
?>
   <input type="hidden" name="cid" value="<?php echo($cid);?>" />
<?php
if ( $basiclti_tool_row !== false ) {
    $blti_content_edit_form = filterForm($basiclti_tool_row, $blti_content_edit_form);
    at_form_generate($basiclti_content_row, $blti_content_edit_form);
   echo('<input type="submit" name="save" value="Save" class="button" />'."\n");
}
?>
</div>
</legend>
</form>
</div>
<?php 
if($basiclti_tool_row){
	echo '<h3>'.$basiclti_tool_row['title'].' '._AT('bl_settings').'</h3>';
	echo '<ul style="list-style-type:none;">';
	foreach($basiclti_tool_row as $title=>$setting){
		if($title == "password" && $basiclti_tool_row['course_id'] == 0){
			// Hide the tool password if its not an instructor created tool //
			echo '<li>'.$title.' = #########</li>';
		} else {
			echo '<li>'.$title.' = '.$setting.'</li>';
		}
	}
	echo '</ul>';
}
//echo("<hr><pre>\n");print_r($basiclti_tool_row); echo("\n</pre>\n"); 
?>
<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>
