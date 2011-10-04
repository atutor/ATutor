<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2010                                      */
/* Inclusive Design Institute                                   */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/

/*function authenticate() {
	$num_args = func_num_args();
	$args 	  = func_get_args();

	for ($i=0; $i < $num_args; $i++) {
		if ($_SESSION['status'] == $args[$i]) {
			return true;
		}
	}

	//Header('Location: /sign_in.php');
	//return false;
}*/
function loadDefaultPrefs() {
	$prefs = array();

    $prefs['colours']		= 'beigeBlack';
    $prefs['back']			= 'beige';
    $prefs['lightBack']		= '#ddeecc';
    $prefs['darkBack']		= '#bbccaa';
    $prefs['front']			= 'black';
    $prefs['fontSize']		= 12;
    $prefs['fontFace']		= 'arial';
    $prefs['idColour']		= 'black';
    $prefs['bingFlag']		= 0;
    $prefs['onlyNewFlag']	= 0;
    $prefs['newestFirstFlag']	= 1;
    $prefs['navigationAidFlag'] = 0;
    $prefs['refresh']		= 20;
    $prefs['lastRead']		= 0;
    $prefs['lastChecked']	= 0;
    $prefs['lastAccessed']	= time();
	// password
	// uniqueID

	if ($myPrefs['colours'] == 'beigeBlack') {
        $myPrefs['back'] = 'beige';
        $myPrefs['front'] = 'black';
        $myPrefs['lightBack'] = '#ddeecc';
        $myPrefs['darkBack'] = '#bbccaa';
    } else if ($myPrefs['colours'] == 'whiteBlack') {
        $myPrefs['back'] = 'white';
        $myPrefs['front'] = 'black';
        $myPrefs['lightBack'] = '#ddeecc';
        $myPrefs['darkBack'] = '#bbccaa';
    } else if ($myPrefs['colours'] == 'whiteBlue') {
        $myPrefs['back'] = 'white';
        $myPrefs['front'] = '000066';
        $myPrefs['lightBack'] = '#ffddcc';
        $myPrefs['darkBack'] = '#ddbbaa';
    } else if ($myPrefs['colours'] == 'blackYellow') {
        $myPrefs['back'] = 'black';
        $myPrefs['front'] = 'yellow';
        $myPrefs['lightBack'] = '#333333';
        $myPrefs['darkBack'] = '#666666';
    } else if ($myPrefs['colours'] == 'blackWhite') {
        $myPrefs['back'] = 'black';
        $myPrefs['front'] = 'white';
        $myPrefs['lightBack'] = '#333333';
        $myPrefs['darkBack'] = '#666666';
    } else { /* blueWhite */
        $myPrefs['back'] = '#000033';
        $myPrefs['front'] = 'white';
        $myPrefs['lightBack'] = '#000066';
        $myPrefs['darkBack'] = '#333366';    
    }

	return $prefs;
}

function &getPrefs($chatID, $update = true) {

	if (!file_exists(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/users/'.$chatID.'.prefs')) {
		return loadDefaultPrefs();
	}
	$myPrefs = array();
	$file_prefs = file(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/users/'.$chatID.'.prefs');
	foreach ($file_prefs as $pref) {
		$pref = explode('=', $pref, 2);
		$myPrefs[$pref[0]] = trim($pref[1]);
	}

	if ($update) {
		$myPrefs['lastAccessed'] = time();
	}

	if ($myPrefs['colours'] == 'beigeBlack') {
        $myPrefs['back'] = 'beige';
        $myPrefs['front'] = 'black';
        $myPrefs['lightBack'] = '#ddeecc';
        $myPrefs['darkBack'] = '#bbccaa';
    } else if ($myPrefs['colours'] == 'whiteBlack') {
        $myPrefs['back'] = 'white';
        $myPrefs['front'] = 'black';
        $myPrefs['lightBack'] = '#ddeecc';
        $myPrefs['darkBack'] = '#bbccaa';
    } else if ($myPrefs['colours'] == 'whiteBlue') {
        $myPrefs['back'] = 'white';
        $myPrefs['front'] = '000066';
        $myPrefs['lightBack'] = '#ffddcc';
        $myPrefs['darkBack'] = '#ddbbaa';
    } else if ($myPrefs['colours'] == 'blackYellow') {
        $myPrefs['back'] = 'black';
        $myPrefs['front'] = 'yellow';
        $myPrefs['lightBack'] = '#333333';
        $myPrefs['darkBack'] = '#666666';
    } else if ($myPrefs['colours'] == 'blackWhite') {
        $myPrefs['back'] = 'black';
        $myPrefs['front'] = 'white';
        $myPrefs['lightBack'] = '#333333';
        $myPrefs['darkBack'] = '#666666';
    } else { /* blueWhite */
        $myPrefs['back'] = '#000033';
        $myPrefs['front'] = 'white';
        $myPrefs['lightBack'] = '#000066';
        $myPrefs['darkBack'] = '#333366';    
    }

	return ($myPrefs);
}

function writePrefs($myPrefs, $chatID) {
	if (empty($myPrefs)) {
		return 0;
	}

	if (!is_dir(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/users')) {
		mkdir(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/users');
	}

	if (file_exists(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/users/'.$chatID.'.prefs')) {
		chmod(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/users/'.$chatID.'.prefs', 0755);
	}
	$fp = @fopen(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/users/'.$chatID.'.prefs', 'w+');
	if (!$fp) {
		// error
		exit;
		return 0;
	}

	$prefs = '';
	foreach ($myPrefs as $prefKey => $prefValue) {
		$prefs .= $prefKey.'='.$prefValue."\n";
	}

	flock($fp, LOCK_EX);
	if (!@fwrite($fp, $prefs)) {
		return 0;
	}
	flock($fp, LOCK_UN);
	chmod(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/users/'.$chatID.'.prefs', 0600);

	return 1;
}

?>
