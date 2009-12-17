<?php
/*
 * tools/packages/scorm-1.2/view.inc.php
 *
 * This file is part of ATutor, see http://www.atutor.ca
 * 
 * Copyright (C) 2005  Matthai Kurian 
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

function treeEl ($s) {
	return '<img src="images/tree/tree_' . $s . '.gif" alt="">';
}

if (!$_GET['org_id']) {
	header('Location: ../index.php');
	exit;
}  


$sql = "SELECT	first_name, last_name
	FROM	".TABLE_PREFIX."members
	WHERE	member_id = ".$_SESSION['member_id'];

$result = mysql_query($sql, $db);
$q_row  = mysql_fetch_assoc($result);
$student_name = $q_row['last_name'] . ', ' . $q_row['first_name'];

//if ($student_name == ', ') $msg->addWarning('packages_no_student_name');

	$sql = "SELECT	package_id
		FROM	".TABLE_PREFIX."scorm_1_2_org
		WHERE	org_id = $_GET[org_id]";
	$result = mysql_query($sql, $db);
	$q_row  = mysql_fetch_assoc($result);
	$pkg    = $q_row['package_id'];

	$sql = "SELECT	item_id, scormtype, idx, title, href
		FROM	".TABLE_PREFIX."scorm_1_2_item
		WHERE	org_id = $_GET[org_id]
		ORDER	BY item_id
	";
	$result = mysql_query($sql, $db);

	$org = array();
	$iid = array();
	$ist = array();

	$i=0;
	while ($row = mysql_fetch_assoc($result)) {
		$org[$i]['id']    = $row['item_id'];
		$org[$i]['idx']   = $row['idx'];
		$org[$i]['type']  = $row['scormtype'];
		$org[$i]['title'] = $row['title'];
		$org[$i]['href']  = $row['href'];
		$iid[$row['item_id']] = $i;
		$ist[$i] = 'not attempted';
		$i++;
	}
	$c = sizeOf($org);

	$sql = "SELECT	c.item_id,
			c.rvalue
		FROM  	".TABLE_PREFIX."cmi c,
			".TABLE_PREFIX."scorm_1_2_item i,
			".TABLE_PREFIX."scorm_1_2_org  o
		WHERE 	o.org_id    = $_GET[org_id]
		AND	i.org_id    = o.org_id
		AND	i.item_id   = c.item_id
		AND	c.member_id = $_SESSION[member_id]
		AND	c.lvalue    = 'cmi.core.lesson_status'
	";
	$result = mysql_query($sql, $db);
	while ($row = mysql_fetch_assoc($result)) {
		$ist[$iid[$row['item_id']]] = $row['rvalue'];
	}

	$fil   = array();
	$tree  = array();
	$dtree = array();
	$tidx  = explode ('.', $org[$c-1]['idx']);
	$lvl   = sizeOf($tidx);
	$llvl  = 42;

	for ($l=0; $l<$lvl; $l++) array_push ($fil, treeEl ('space'));
	array_push ($fil, treeEl('end'));

	for ($i=$c-1; $i>=0; $i--) {
		$tidx = explode ('.', $org[$i]['idx']);
		$lvl = sizeOf($tidx);

		switch ($org[$i]['type']) {
		case 'organization':
			$_pages[$me]['title'] =$org[$i]['title'];

		case 'cluster':
			array_pop ($fil);
			array_pop ($fil);
			array_push ($fil, treeEl('disabled'));
			break;

		case 'sco':
		case 'asset':
			if ($org[$i]['idx'].'.1' == $org[$i+1]['idx']) {
				// cluster with resource
				array_pop ($fil);
				array_pop ($fil);
				array_push ($fil, treeEl('disabled'));
				break;
			}
			
			array_pop ($fil);
			if ($lvl <  $llvl) array_push ($fil, treeEl('end'));
			if ($lvl == $llvl) array_push ($fil, treeEl ('split'));
			if ($lvl >  $llvl) {
				 array_push ($fil, treeEl ('vertline'));
				 array_push ($fil, treeEl ('end'));
			}
			break;
		}

		if ($org[$i]['href']) {
			if ($org[$i]['type'] == 'sco') {
				array_push ($tree,
					implode ($fil) 
					. '<img id="im' . $i
					. '" name="im' . $i
					. '" src="' . $im
					. str_replace (' ', '-', $ist[$i])
					. '.png" alt="' . $ist[$i]
					. '" title="' . $ist[$i] . '">'
					. '<a href="javascript:void(0)" '
					. 'onclick="Launch(' . $i .');'
					. 'return false;">'
					. $org[$i]['title'] . '</a>'
				);
			} else {
				array_push ($tree,
					implode ($fil) 
					. '<img id="im' . $i
					. '" name="im' . $i
					. '" src="' . $im
					. 'asset.png" alt="">'
					. '<a href="javascript:void(0)" '
					. 'onclick="Launch(' . $i .');'
					. 'return false;">'
					. $org[$i]['title'] . '</a>'
				);
			}
		} else {
			array_push ($tree,
				implode ($fil)
				. '<b>' . $org[$i]['title'] . '</b>'
			);
		}

		$llvl = $lvl;
	}

	require(AT_INCLUDE_PATH.'header.inc.php');
	
?>

<div id="rte">
<applet code="ATutorApiAdapterApplet" 
id="RTE" name="RTE" mayscript="true"
codebase="tools/packages/scorm-1.2"
archive="java/ATutorApiAdapterApplet.jar,java/PfPLMS-API-adapter-core.jar,java/gnu.jar"
width="0" height="0" >
<param name="student_id"   value="<?php echo $_SESSION['member_id']?>" />
<param name="student_name" value="<?php echo $student_name?>" />
<?php	if ($prefs['show_rte_communication'] == 1) {
		echo '<param name="verbose" value="1" />' . "\n";
	}
?>"
       	echo ($prefs['show_rte_communication'] == 1?'true':'false');
</applet>
</div>

<script language="Javascript">

function getObj (o) {
	if(document.getElementById) return document.getElementById(o);
	if(document.all) return document.all[o];
}


scHREF = new Array();
scID   = new Array();
scType = new Array();

<?php
	$c = sizeOf ($org);
	for ($i=0; $i<=$c; $i++) {
		echo 'scHREF[' . $i ."] = '";
		if ($org[$i]['href'] != '') {
			echo AT_PACKAGE_URL_BASE
			. $_SESSION['course_id'] .'/' . $pkg .'/'
		       	. $org[$i]['href']
			;
		}
		echo "';\n"
		. 'scID[' . $i ."] = '" . $org[$i]['id'] . "';\n"
		. 'scType[' . $i ."] = '" . $org[$i]['type'] . "';\n"
		;
	}
?>


var scoidx      = 0;
var nextscoidx  = 0;
var autonext    = null;
var scowindow   = null;
var isRunning   = false;
var isLaunching = false;
var initstat    = '';

var scoWidth  = 800;
var scoHeight = 600;

var auto_advance = <?php
	echo ($prefs['auto_advance'] == 1 ?'true':'false');
?>;
var show_comm = <?php
       	echo ($prefs['show_rte_communication'] == 1?'true':'false');
?>;

function LMSInitialize (s) {
	isRunning   = true;
	isLaunching = false;

	scoidx     = nextscoidx;
	nextscoidx = null;
	autonext   = null;

	var o = getObj ('im'+scoidx);
	o.src = '<?php echo $im;?>busy.png';
	o.alt   = '<?php echo _AT('scorm_sco_is_running')?>';
	o.title = '<?php echo _AT('scorm_sco_is_running')?>';
	rv = window.document.RTE.LMSInitialize (s);
	initstat = window.document.RTE.ATutorGetValue (
		'cmi.core.lesson_status'
	);
	return rv;
}

function LMSFinish (s) {

	checkResize();

	var stat = window.document.RTE.ATutorGetValue ('cmi.core.lesson_status');
	if (stat != '') {
		var o = getObj ('im'+scoidx);
		o.alt = stat;
		o.title = stat;
		if (stat == 'not attempted') stat = 'not-attempted';
		o.src = '<?php echo $im;?>'+stat+'.png';
		if (!autonext && auto_advance && !initstat.equals(stat)) {
			if (stat == 'completed' ||
			    stat == 'passed'    ||
			    stat == 'browsed') {
				for (i=scoidx+1; i<scHREF.length; i++) {
					if (scHREF[i].length) {
						autonext = i;
						checkResize();
						break;
					}
				}
			}
		}
	}

	rv = window.document.RTE.LMSFinish (s);
	if (rv == 'true') {
		scowindow.close();
		scowindow = null;
		isRunning = false;
		scoidx    = null;
		if (autonext) setTimeout ('Launch (autonext)', 500);
	}
	return rv;
}

function LMSSetValue (l, r) {
	return window.document.RTE.LMSSetValue (l, r);
}

function LMSGetValue (l) {
	return window.document.RTE.LMSGetValue (l);
}

function LMSGetLastError () {
	return window.document.RTE.LMSGetLastError ();
}

function LMSGetErrorString (s) {
	return window.document.RTE.LMSGetErrorString (s);
}

function LMSGetDiagnostic (s) {
	return window.document.RTE.LMSGetDiagnostic (s);
}

function LMSCommit (s) {
	return window.document.RTE.LMSCommit (s);
}


function Launch (i) {
	if (autonext && autonext != i) return;
	nextscoidx = null; 
	if (i == scoidx) return;

	if (scowindow && scowindow.closed) {
		isLaunching = false;
		isRunning   = false;
		scowindow   = null;
		if (isRunning) {
			window.document.RTE.ATutorReset(scID[scoidx]);
			isRunning = false;
		}
	}

	if (isLaunching) return;

	if (scowindow != null) {
	       if (!isRunning) return;
	       scowindow.close();
	}

	isLaunching = true;
	if (scType[i] == 'sco') {
		try {
			window.document.RTE.ATutorPrepare(scID[i]);
		} catch (Exception) {
			alert ('Sorry, LiveConnect does not work');
			return;
		}
		checkResize();
		nextscoidx = i;
	} else {
		nextscoidx = null;
	}

	isLaunching = true;
	scowindow = window.open (
		scHREF[i],
		'ATutorSCO',
		'width='+scoWidth+',height='+scoHeight+','+
		'toolbar=no,menubar=no,status=no,scrollbars=yes'
	);
	if (scType[i] == 'sco') {
		this.API = this;
		if (scowindow) scowindow.API = this;
	}
	scowindow.focus();
}

function cleanup () {
	if (scowindow) scowindow.close();
}

function checkResize () {
	try {
		w = scowindow.innerWidth;
		h = scowindow.innerHeight;
		if (w >= 640 && h >= 480) {
			scoWidth  = w; 
			scoHeight = h;
		}
	} catch (Exception) {};
}

this.onunload=cleanup;
</script>

<?php
	$p = "\n" . '<div id="scorm_1_2_toc" style="display:block">' . "\n";
	for ($i=$c-1; $i>=0; $i--) {
		$p .= $tree[$i] . '<br />' . "\n";
	}
	$p .= '</div>' . "\n";
	echo utf8_decode($p); 
?>


