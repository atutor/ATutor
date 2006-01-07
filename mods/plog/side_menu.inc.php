<?php
/*
This is a simple ATutor sidemenu block for LifeType/pLog blog application. I gathers
the lastest10 posts form the course currently being viewed, as well as private posts, and posts
form other enrolled courses specific to the current users.
*/


global $savant, $_config, $_base_href;
ob_start(); 
//Get the list of entries associated with the current user

$sql = "SELECT id, user_id,blog_id FROM ".PLOG_PREFIX."articles WHERE user_id = '$_SESSION[member_id]' OR blog_id='$_SESSION[course_id]' ORDER BY date DESC LIMIT 10";
$result = mysql_query($sql,$db);

if(mysql_num_rows($result) > 0){
	echo '<ul style="margin-left:-2em;">';
	while($row = mysql_fetch_array($result)){
		$sql2 = "SELECT * FROM ".PLOG_PREFIX."articles_text WHERE article_id = '$row[0]'";
		$result2 = mysql_query($sql2,$db);
		while($row2 = mysql_fetch_array($result2)){
			echo '<li><a href="'.$_base_href.'mods/plog/index.php?blogId='.$row[2]. SEP.'op=ViewArticle'.SEP.'article_Id='.$row[0].'">'.$row2[3].'</a></li>';
		}
	}
echo '</ul>';
 }else{
 	echo '<em>'._AT('none_found').'</em>';
 }
?>

<?php
$savant->assign('dropdown_contents', ob_get_contents());
ob_end_clean();

$savant->assign('title', _AT('plog_current'));
$savant->display('include/box.tmpl.php');

?>