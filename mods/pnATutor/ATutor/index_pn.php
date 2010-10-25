<?php
$reqVar = '_' . $_SERVER['REQUEST_METHOD'];
$form_vars = $$reqVar;
$parm = $form_vars['parm'] ;

$url = explode("|", $parm);

$f_username = $url[0];
$f_usermail = $url[1];
$f_users = $url[2];
$db = $url[3] ;
$home= $url[4] ;
$home .= "user.php?op=loginscreen&module=NS-User" ;
$cid = $url[5] ;
$srt= $url[6] ;
$version= $url[7] ;

if ($f_username==""){
	header ("Location : $home") ;
}

$_public	= true;
$page	 = 'PostNuke_start';
$_user_location	= 'public';


if ($version == '1.3.0'){
	$_include_path = 'include/';
	require ($_include_path.'vitals.inc.php');
} else {
	define('AT_INCLUDE_PATH', 'include/');
	require(AT_INCLUDE_PATH.'vitals.inc.php');
}

$pwd = '';

$sql = "SELECT password FROM ".TABLE_PREFIX."members WHERE login='$f_username' ";
$result = mysql_query($sql);
$num_rows = mysql_num_rows($result);


if ($num_rows < 1){
	if ($f_users != ''){
		// Insert the new user
		$new_pwd = ranpass() ;
		$adding = "INSERT INTO ".TABLE_PREFIX."members (login, password,email,language,preferences) VALUES ( '$f_username','$new_pwd','$f_username', 'en',' ')";
		$added = mysql_query($adding) or mysql_error();
		// Run query again
		$sql = "SELECT password FROM ".TABLE_PREFIX."members WHERE login='$f_username' ";
		$result = mysql_query($sql);
		$row99 = mysql_fetch_row($result) ;
		$pwd = $row99[0];
	}
} else {
	$row99 = mysql_fetch_row($result) ;
	$pwd = $row99[0];
}

/* form post login */
$this_login	= $f_username;
$this_password  = $pwd;
$auto_login		= 0;
$used_cookie	= false;

$sql = "SELECT member_id, login, preferences, PASSWORD(password) AS pass, language FROM ".TABLE_PREFIX."members WHERE login='$this_login' AND PASSWORD(password)=PASSWORD('$this_password')";

$result = mysql_query($sql);
if ($row = mysql_fetch_array($result)) {
	$_SESSION['login']		= $row['login'];
	$_SESSION['valid_user'] = TRUE;
	$_SESSION['member_id']	= intval($row['member_id']);
	assign_session_prefs(unserialize(stripslashes($row['preferences'])));
	$_SESSION['is_guest']	= 0;
	$_SESSION['lang']		= $row['language'];

	$_POST['form_course_id'] = $url[5] ;
	Header('Location: ./bounce.php?course='.$_POST['form_course_id']);
	exit ;
} else {
	$errors[] = AT_ERROR_INVALID_LOGIN;
	Header('Location: ./login.php');
}
Header('Location: ./login.php');
exit ;


function ranpass($len = "8"){
 $pass = NULL;
 for($i=0; $i<$len; $i++) {
   $char = chr(rand(48,122));
   while (!ereg("[a-zA-Z0-9]", $char)){
     if($char == $lchar) continue;
     $char = chr(rand(48,90));
   }
   $pass .= $char;
   $lchar = $char;
 }
 return $pass;
}
?>