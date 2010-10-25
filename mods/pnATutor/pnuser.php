<?php
function pnATutor_user_main() {
$loc1=pnModGetVar('pnATutor', '_loc');
$loc=trim($loc1);
$window=pnModGetVar('pnATutor', '_window');
$wrap=pnModGetVar('pnATutor', '_wrap');
$guest=pnModGetVar('pnATutor', '_guest');
$users=pnModGetVar('pnATutor', '_users');
$db=pnModGetVar('pnATutor', '_db');
$version=pnModGetVar('pnATutor', '_version');

$username=pnUserGetVar(uname);
$usermail=pnUserGetVar(email);

if (!pnUserLoggedIn()) {
	if ($guest == 1){
		$username= "Guest" ;
	} else {
		$username= "" ;
	}
}
$home = pnGetBaseURL() ;
$parm = $username ;
$parm .="|";
$parm .= $usermail;
$parm .="|";
$parm .= $users;
$parm .="|";
$parm .= $db;
$parm .="|";
$parm .= $home ;
$parm .="|";
$parm .= 0;
$parm .="|";
$parm .= 0 ;
$parm .="|";
$parm .= $version ;


$url="$loc/index_pn.php?parm=$parm";

if ($window == 1 ) {
	$url="$loc/index_pn.php?parm=$parm&check=$check";
	?>
	<script type="text/javascript">
	window.open('<?php echo $url;?>')
	</script>
	<?php
}

$go = pnATutor_user_go($url) ;

return true;

}


function pnATutor_user_view() {
$cid = pnVarCleanFromInput('id');

$loc1=pnModGetVar('pnATutor', '_loc');
$loc=trim($loc1);
$window=pnModGetVar('pnATutor', '_window');
$wrap=pnModGetVar('pnATutor', '_wrap');
$guest=pnModGetVar('pnATutor', '_guest');
$users=pnModGetVar('pnATutor', '_users');
$db=pnModGetVar('pnATutor', '_db');
$version=pnModGetVar('pnATutor', '_version');

$username=pnUserGetVar(uname);
$usermail=pnUserGetVar(email);

if (!pnUserLoggedIn()) {
	if ($guest == 1){
		$username= "Guest" ;
	} else {
		$username= "" ;
	}
}
$home = pnGetBaseURL() ;
$parm = $username ;
$parm .="|";
$parm .= $usermail;
$parm .="|";
$parm .= $users;
$parm .="|";
$parm .= $db;
$parm .="|";
$parm .= $home ;
$parm .="|";
$parm .= $cid;
$parm .="|";
$parm .= 0 ;
$parm .="|";
$parm .= $version ;


$url="$loc/index_pn.php?parm=$parm";

if ($window == 1 ) {
	$url="$loc/index_pn.php?parm=$parm&check=$check";
	?>
	<script type="text/javascript">
	window.open('<?php echo $url;?>')
	</script>
	<?php
}
$go = pnATutor_user_go($url) ;

return true;

}

function pnATutor_user_go($url=""){

$guest=pnModGetVar('pnATutor', '_guest');
$window=pnModGetVar('pnATutor', '_window');
$home = pnGetBaseURL() ;
$home .= "user.php?op=loginscreen&module=NS-User" ;

include("header.php");
if ($window != 1 ) {
echo "<script language='javascript' type='text/javascript'>";
echo "function iFrameHeight() {";
echo "  var h = 0;";
echo "	if ( !document.all ) {";
echo "		h = document.getElementById('blockrandom').contentDocument.height;";
echo "		document.getElementById('blockrandom').style.height = h + 60 + 'px';";
echo "	} else if( document.all ) {";
echo "		h = document.frames('blockrandom').document.body.scrollHeight;";
echo "		document.all.blockrandom.style.height = h + 20 + 'px';";
echo "	}";
echo "}";
echo "</script>";
echo "<iframe onload='iFrameHeight()' id='blockrandom' name='pnSMF'";
echo "  src='$url' width='100%' height='400' scrolling='auto' align='top' frameborder='0'>";
echo "</iframe>";
}else{
echo pnVarPrepHTMLDisplay(_PNSMF_LAUNCHED);
}
include("footer.php");
return true;


}

?>
