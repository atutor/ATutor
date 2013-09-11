<?php

/**
 * This class contains the callback functions that are called in the core scripts to manipulate content etc. 
 *
 * Note:
 * 1. CHANGE the class name and ensure its uniqueness by prefixing with the module name
 * 2. DO NOT change the script name. Leave as "ModuleCallbacks.class.php"
 * 3. DO NOT change the names of the methods.
 * 4. REGISTER the unique class name in module.php
 *
 * @access	public
 */
if (!defined('AT_INCLUDE_PATH')) exit;

class BasicLTICallbacks {
	/*
	 * To append output onto course content page 
	 * @param: None
	 * @return: a string, plain or html, to be appended to course content page
	 */ 
	public static function appendContent($cid) {
		if ( !is_int($_SESSION['course_id']) || $_SESSION['course_id'] < 1 ) return;

		$sql = "SELECT * FROM %sbasiclti_content WHERE content_id=%d AND course_id =%d" ;
		$basiclti_content_row = queryDB($sql, array(TABLE_PREFIX, $cid, $_SESSION['course_id']), TRUE);
 		if(count($basiclti_content_row) == 0) return;

		if ( $basiclti_content_row === false ) return;
		$toolid = $basiclti_content_row['toolid'];

		$sql = "SELECT * FROM %sbasiclti_tools WHERE toolid='%s'";
		$basiclti_tool_row = queryDB($sql, array(TABLE_PREFIX, $toolid), TRUE);
				
		if ( ! $basiclti_tool_row ) {
			return _AT('blti_missing_tool').$toolid;
		}
		// Figure height
		$height = 1200;
		if ( isset($basiclti_tool_row['preferheight']) && $basiclti_tool_row['preferheight'] > 0 ) {
			$height = $basiclti_tool_row['preferheight'];
		}
		if ( $basiclti_tool_row['allowpreferheight'] == 2 && isset($basiclti_content_row['preferheight']) && $basiclti_content_row['preferheight'] > 0 ) {
			$height = $basiclti_content_row['preferheight'];
		}

		$myurl = AT_BASE_HREF.'mods/_standard/basiclti/launch/launch.php?cid='.$cid;
		if ( $basiclti_tool_row['launchinpopup'] == 1 ||
		   ( $basiclti_tool_row['launchinpopup'] == 2 && $basiclti_content_row['launchinpopup'] == 1 ) ) {
			// return '<script type="text/javascript">window.open("'.$myurl.'");</script>'."\n";
				/*****************
				*	The ID in the next bit is temporary until we can add the box style to ATutor and
				*	change the ID value to content-tool. In the meantime this will fail validation if tools and
				*   tests or forums are also present for the content page
				**********************/
				return '<div class="input-form" id="content-test"><ol><strong>'._AT('proxy').'</strong><ul class="tools"><li><a href="" onclick="ATutor.poptastic(\''.$myurl.'\'); return false;"'.'>'.$basiclti_tool_row['title'].'</a> ('._AT('new_window').')</li></ul></ol></div>'."\n";
		} else {
			return '<iframe src="'.$myurl.'" height="'.$height.'" width="100%"></iframe>'."\n";
		}
	}

}

?>
