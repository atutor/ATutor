<?php

define('AT_INCLUDE_PATH', '../../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_BASICLTI);

require_once(AT_INCLUDE_PATH.'classes/AContent_lcl/Utils.php');
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

    $sql = "SELECT * FROM %sbasiclti_content WHERE content_id=%d AND course_id=%d";
    $row_content = queryDB($sql, array(TABLE_PREFIX, $_POST['cid'], $_SESSION['course_id']), TRUE);
    
    if ( $toolid == '--none--' ) {

            $sql = "DELETE FROM %sbasiclti_content WHERE content_id=%d AND course_id=%d";
            $result = queryDB($sql, array(TABLE_PREFIX, $_POST['cid'], $_SESSION['course_id']));
            if ($result === false) {
                $msg->addError('MYSQL_FAILED');
            } else {
                $msg->addFeedback('BASICLTI_DELETED');
            }
    } else if ( count($row_content) == 0 ) {

            $sql = "INSERT INTO %sbasiclti_content SET toolid='%s', content_id=%d, course_id=%d";
            $result = queryDB($sql, array(TABLE_PREFIX, $toolid, $_POST['cid'], $_SESSION['course_id']));
            
            if ($result == 0) {
                $msg->addError('MYSQL_FAILED');
            } else {
                $msg->addFeedback('BASICLTI_SAVED');
            }

    } else if ( count($row_content) > 0 ) {
            $gradebook_test_id = 0;
            $basiclti_content_row = $row_content;
            $placementsecret = $basiclti_content_row['placementsecret'];
            $gradebook_check = intval($_POST['gradebook_test_id']);
            if ( isset($_POST['gradebook_test_id']) && $gradebook_check > 0 ) {
		        $gradebook_test_id = $gradebook_check;

                $sql = "SELECT g.gradebook_test_id AS id, g.title AS title
                        FROM  %sgradebook_tests AS g
                        WHERE g.course_id = %d
                        AND g.type = 'External' and g.grade_scale_id = 0
                        AND gradebook_test_id = %d";
                $rows_grades = queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id'], $gradebook_test_id));
                                
                if ( $rows_grades == 0 ) {
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

            $sql = "UPDATE %sbasiclti_content SET ".$sql." WHERE content_id=%d AND course_id=%d";
            $result = queryDB($sql, array(TABLE_PREFIX, $_POST['cid'], $_SESSION['course_id']));

            if ($result === false) {
                $msg->addError('MYSQL_FAILED');
            } else {
             //   $msg->addFeedback('BASICLTI_SAVED');
            }
    }
}

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

// Mauro Donadio
// donadiomauro@gmail.com

if(isset($_POST['save'])){

	// get all checked boxes
	// about the selected lesson(s) to import
	if(isset($_POST['lesson']) AND is_array($_POST['lesson'])){

		$res		= array();
		$offspring	= array();
		$current_log= null;

		for($i=0; $i<count($_POST['lesson']); $i++){

			$value	= explode('|', $_POST['lesson'][$i]);
			list($log, $depth, $key) = $value;

			
			if($log != $current_log){
				$current_log = $log;
				// empty the stack
				$offspring	= null;
			}

			if(!empty($offspring) AND $depth > end($offspring)){

				continue;
			}else{
				$res[]	= $key;
			}

			$offspring[]	= $depth;
		}
		
		$res = array_unique($res);

		for($i = 0; $i < count($res); $i++){

			// create LTI connection to get the course structure
			// format the course structure as tree

			// AContent course
			require_once(AT_INCLUDE_PATH . 'classes/AContent_lcl/AContent_LiveContentLink.class.php');

			$content_id	= $res[$i];
			$xml	= null;
			$xml	= new AContent_LiveContentLink($content_id, 0);

			// store the XML data into the database
			// (implementation of AContent Live Content Link)
			// Hack to change parent page to a folder
			if(isset($_SESSION['s_cid'])){
				$this_cid = $_SESSION['s_cid'];
			}else{
				$this_cid = $_REQUEST['cid'];
			}

			$sql_folder = "UPDATE %scontent SET content_type ='1', content_parent_id = '0', formatting = '0' WHERE content_id = %d";
			$result = queryDB($sql_folder, array(TABLE_PREFIX, $this_cid));

			require_once(AT_INCLUDE_PATH . 'classes/AContent_lcl/AContent_lcl_importxml.class.php');
			$ac_xml	= new AContent_lcl_importxml();
			// ATutor course
			$course_id			= htmlentities($_SESSION['course_id']);

			$import	= $ac_xml->importXML($xml->xmlStructure, $course_id);
			if($import){
				$msg->addFeedback('BASICLTI_SAVED');
			}
        }
	}
	
	// show "Close window" button
	echo '<div style="text-align:center; margin:0 auto; padding: 20px">';
		echo '<input type="submit" onclick="window.opener.location.reload(); javascript:window.close()" value="Close Window" class="button" />';
	echo '</div>';

	require(AT_INCLUDE_PATH.'footer.inc.php');

	die();
}

/* get a list of all the tools, we have */

$sql    = "SELECT * FROM %sbasiclti_tools WHERE course_id = 0".
          " OR course_id=%d ORDER BY course_id,title";
$rows_tools = queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id']));
$num_tools = count($rows_tools);

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
$sql = "SELECT * FROM %sbasiclti_content WHERE content_id=%d";
$basiclti_content_row = queryDB($sql, array(TABLE_PREFIX, $cid), TRUE);

?>
<div class="row">
   <?php echo _AT('bl_choose_tool'); ?><br/>
   <select id="toolid" name="toolid" onchange="datagrid.submit();"> 
      <option value="--none--">&nbsp;</option><?php
      $basiclti_tool_row = false;
      $found = false;  // Only the first one
      foreach($rows_tools as $tool){
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
            FROM  %sgradebook_tests AS g
            WHERE g.course_id = %d
            AND g.type = 'External' and g.grade_scale_id = 0";
    $rows_grades = queryDB($sql, array(TABLE_PREFIX, $_SESSION[course_id]));
    if(count($rows_grades) > 0) { ?>
<div class="row">
   <?php echo _AT('bl_choose_gradbook_entry'); ?><br/>
        <select id="gradebook_test_id" name="gradebook_test_id"> 
           <option value="--none--">&nbsp;</option><?php
           foreach($rows_grades as $gradeitem){
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

</div>
</legend>
</div>

<?php

	if($basiclti_tool_row){

		##
		## Mauro Donadio (donadiomauro@gmail.com)
		##

		echo '<div style="padding: 10px">';

			if (AContent_has_lcl_support()){

				echo '<div style="border-bottom: 1px solid #CCC; border-left: 10px solid #CCC; padding-left: 10px; font-weight: bold; margin-top:10px; margin-bottom:20px">';
				echo _AT('course_list');
				echo '</div>';
	
				// SEARCH
	
				echo '<table style="width:100%; margin:auto; margin-bottom: 10px; border-bottom: 1px solid #CCC">';
	
						echo '<tr>';
							echo '<td>';
								echo '<div>';
									echo '<label for="words2"><b>' . _AT('tile_howto') . '</b></label>';
								echo '</div>';
		
								if(isset($_POST['keywords']) AND htmlentities($_POST['keywords']) != '')
									echo '<input type="text" name="keywords" style="width: 100%" id="words2" value="'.htmlentities($_POST['keywords']).'" /> ';
								else
									echo '<input type="text" name="keywords" style="width: 100%" id="words2" /> ';
								echo '<input type="submit" class="button" value="' . _AT('search') . '" />';
							echo '<td>';
					echo '</tr>';
					echo '<tr>';
		
					// COURSES
		
					echo '<td style="padding-top:10px">';
					
					echo '<div>';
						echo '<label for="courses"><b>' . _AT('browse_courses') . '</b></label>';
						//echo '<br />';
						//echo '<label for="courses"><b>' . _AT('course_list') . '</b></label>';
					echo '</div>';
		
					echo '<select name="course_list" id="courses" size="10" style="width: 100%; border: 1px solid #CCC">';
						if(isset($_POST['keywords']) AND htmlentities($_POST['keywords']) != ''){
		
							$url	= explode('home', $basiclti_tool_row['toolurl']);
							$xmlfp	= file_get_contents($url[0] . 'search.php?id='.$GLOBALS['_config']['transformable_web_service_id'].'&keywords='.htmlentities($_POST['keywords']));
		
							$xml	= simplexml_load_string(trim($xmlfp));
							$i		= 1;
		
							foreach($xml->results->result as $res) {
		
								if(isset($_POST['course_list']) AND $res->courseID == $_POST['course_list']){
									$choosen_course = $res->title;
									echo '<option value="'.trim($res->courseID).'" selected="selected" style="padding-top:5px; padding-left:5px">'.$i.'. '.$res->title.'</option>';
								}elseif($i==1)
									echo '<option value="'.trim($res->courseID).'" selected="selected" style="padding-top:5px; padding-left:5px">'.$i.'. '.$res->title.'</option>';
								else
									echo '<option value="'.trim($res->courseID).'" style="padding-top:5px; padding-left:5px">'.$i.'. '.$res->title.'</option>';
								$i++;
							}
						}
		
					echo '</select>';
		
					echo '</td>';
				echo '</tr>';
				echo '<tr>';
					echo '<td>';
		
						echo '<div>';
							echo '<input type="submit" value="' . _AT('show_pages') . '" class="button" />';
						echo '</div>';
		
					echo '</td>';
		
				// LESSONS
		
		
				if(isset($_POST['course_list']) AND $_POST['course_list'] != null){
					
					echo '</tr>';
					echo '<tr>';
					echo '<td style="padding-top:10px; padding-bottom:10px" id="tree_box">';
		
					echo '<div style="padding-top: 10px; padding-bottom: 10px">';
						echo '<b>* Select pages to import</b>';
					echo '</div>';
		
		
					echo '<div style="padding-bottom: 5px"><u>';
						echo $choosen_course;
					echo '</u></div>';
		
					$course_id		= htmlentities($_POST['course_list']);	
		
					// create LTI connection to get the course structure
					// format the course structure as tree

					require_once(AT_INCLUDE_PATH . 'classes/AContent_lcl/AContent_LiveContentLink.class.php');

					$xml	= null;
					$xml	= new AContent_LiveContentLink($course_id, 1);

					// transform the XML to a particular array
					// required by the TreeGenerator class
		
					require_once(AT_INCLUDE_PATH . 'classes/AContent_lcl/AContent_lcl_processxml.class.php');
					$ac_xml	= new AContent_lcl_processxml();
					$struct	= $ac_xml->XMLtoArray($xml->xmlStructure);
		
					// create the tree view of the choosen course

					require_once(AT_INCLUDE_PATH . 'classes/AContent_lcl/TreeGenerator.class.php');
					$ac_tree= new TreeGenerator($struct);
					//$ac_tree->plainTree();
					//$ac_tree->plainFolderTree();
					$ac_tree->checkBoxTree('lesson');
					//$ac_tree->radioButtonTree('lesson');
		
					echo '</td>';
				}
		
				echo '</tr>';

			echo '</table>';

		} // LTI supported

		if ( $basiclti_tool_row !== false ) {
		    $blti_content_edit_form = filterForm($basiclti_tool_row, $blti_content_edit_form);
		    at_form_generate($basiclti_content_row, $blti_content_edit_form);
			echo('<input type="submit" name="save" value="' . _AT('save') . '" class="button" />'."\n");
		}

		echo '</div>';
		
			//--
		
			// // // // //
	
		echo '</form>';
	
	
		echo '<h3>'.$basiclti_tool_row['title'].' '._AT('bl_settings').'</h3>';
		echo '<ul style="list-style-type:none;">';
		foreach($basiclti_tool_row as $title=>$setting){
			if($title == "password" || $title == "resourcekey" && $basiclti_tool_row['course_id'] == 0){
				// Hide the tool password if its not an instructor created tool //
				// Don't print out the key or password id its an admin added tool
				continue;
			} else {
	
				echo '<li>'.$title.' = '.$setting.'</li>';
			}
		}
		echo '</ul>';
	}

	if ( $basiclti_tool_row == false ) {
	    $blti_content_edit_form = filterForm($basiclti_tool_row, $blti_content_edit_form);
	    at_form_generate($basiclti_content_row, $blti_content_edit_form);
		echo('<input type="submit" name="save" value="' . _AT('save') . '" class="button" />'."\n");
	}
//echo("<hr><pre>\n");print_r($basiclti_tool_row); echo("\n</pre>\n"); 
?>
<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>
