<?php

/**
*
* Subscription
*
* Used to check and set/unset email subscription status for a user for various feeds. Also used to send mails when feeds are fed.
*
* This class was written with pure intentions.
*
* @author   gorzan <gorzan@gmail.com>
* @access   public
*/

class subscription {
	
	public $member_id;
	public $entity_id;
	public $entity_type;
	private $ent_param = array(); 

	// Constructor. Does nothing at the moment.
	public function subscription() {
		return true;
	}
	
	
	// Checks if user is subscribed to feed.
	public function is_subscribed($entity_type, $member_id, $entity_id) {
		
		// Get appropriate sql parameters and write sql query
		$ent_param = $this->entity_switch($entity_type);
		$sql = ($ent_param) ? "SELECT COUNT(*) FROM $ent_param[sub_table] WHERE member_id = '$member_id' AND $ent_param[sub_id] = '$entity_id'" : false;
				
		// Run SQL and check if table is populated for given member id and entity id
		if ($sql){
			$result = mysql_fetch_array(mysql_query($sql));
			return (empty($result[0]))?false:true;
		}	else {
			return false;
		}
	}
	
	// Gets group name for blog posts
	private function get_group_title(){
		if (isset($_GET['oid'])){
			$oid = $_GET['oid'];
		} elseif (isset($_POST['oid'])){
			$oid = $_POST['oid'];
		}
		
		$gid = (is_array($oid))?$oid[0]:$oid;
		if (!empty($gid)){
			$sql = "SELECT title FROM ".TABLE_PREFIX."groups WHERE group_id='$gid'";
			$result = mysql_fetch_row(mysql_query($sql));
			return $result[0];
		} else {
			return false;
		}
	}
	
	// Gets email and site name
	private function get_system_email (){
		$sql = "SELECT * FROM ".TABLE_PREFIX."config WHERE name = 'site_name' OR name = 'contact_email'";
		$result = mysql_query($sql);
		while($row = mysql_fetch_row($result)){
			if ($row[0] == 'site_name'){
				$sysinfo['site_name'] = $row[1];
			} elseif ($row[0] == 'contact_email'){
				$sysinfo['contact_email'] = $row[1];
			}
		}
		return $sysinfo;
	}
	// Subscribes user to feed
	public function set_subscription($entity_type, $member_id, $entity_id){
		
		//Checks subscribability (only for blogs)
		if ($entity_type == 'blog' && !$this->check_blog_subscribability($entity_id,$member_id)){
			return false;
		}
		
		$ent_param = $this->entity_switch($entity_type);
		$sql = ($ent_param) ? "INSERT INTO $ent_param[sub_table] (member_id, $ent_param[sub_id]) VALUES('$member_id','$entity_id')" : false;
		return (mysql_query($sql))?true:false;
	}
	
	// Unsubscribes user to feed
	public function unset_subscription($entity_type, $member_id, $entity_id){
		$ent_param = $this->entity_switch($entity_type);
		$sql = ($ent_param) ? "DELETE FROM $ent_param[sub_table] WHERE member_id = '$member_id' AND $ent_param[sub_id] = '$entity_id'" : false;
		return (mysql_query($sql))?true:false;
	}
	
	// Sends mail to all subscribed users
	public function send_mail($entity_type,$entity_id,$post_id){
		// We need the automailer
		require(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');
		
		//Also, we need to know what ballpark we're in
		$ent_param = $this->entity_switch($entity_type);
		
		// Now, what are we going to send?
		$fetch = (!empty($ent_param[content_head]))?$ent_param[content_head].",".$ent_param[content_body]:$ent_param[content_body];
		$sql = "SELECT $fetch FROM $ent_param[content_table] WHERE $ent_param[content_id] = '$post_id'";
		$post = mysql_fetch_array(mysql_query($sql));
		
		//Get all subscribers
		$sql = "SELECT t1.email, t1.member_id FROM ".TABLE_PREFIX."members t1, $ent_param[sub_table] t2 WHERE t2.$ent_param[sub_id] = '$entity_id' AND t1.member_id=t2.member_id";
		$result = mysql_query($sql);
		
		//get system email
		$sysinfo = $this->get_system_email();
		
		//Send lots of mails
		while ($subscriber = mysql_fetch_array($result)){
			$mail = new ATutorMailer;
			$mail->AddAddress($subscriber['email'], get_display_name($subscriber['member_id']));
			$body = $ent_param[mail_header];
			$body .= "<hr />";
			$body .= _AT('posted_by').": ".get_display_name($_SESSION['member_id'])."<br />";
			$body .= (!empty($ent_param[content_head]))?"<h2>".$post[$ent_param[content_head]]."</h2><br />":'';
			$body .= format_content($post[$ent_param[content_body]],$_POST['formatting'],$glossary)."<br />";
			$mail->CharSet = 'utf-8';
			$mail->ContentType = 'text/html';
			$mail->FromName = $sysinfo['site_name'];
			$mail->From     = $sysinfo['contact_email'];
			$mail->Subject = $ent_param[mail_subject];
			$mail->Body    = $body;

			if(!$mail->Send()) {
				$msg->addError('SENDING_ERROR');
			}

			unset($mail);
		}
		
	}
	
	// Internal function used to set appropriate SQL parameters for a given entity type
	private function entity_switch($entity_type){
		switch($entity_type){
			case "blog":
				$param[sub_table] = TABLE_PREFIX.'blog_subscription';
				$param[sub_id] = 'group_id';
				$param[content_table] = TABLE_PREFIX.'blog_posts';
				$param[content_id] = 'post_id';
				$param[content_head] = 'title';
				$param[content_body] = 'body';
				$param[group_title] = $this->get_group_title();
				$param[mail_subject] = _AT('blog_notify_subject');
				$param[mail_header] = _AT('blog_notify_body', $param[group_title], AT_BASE_HREF.'bounce.php?course='.$_SESSION['course_id']);
			break;
			case "blogcomment":
				$param[sub_table] = TABLE_PREFIX.'blog_subscription';
				$param[sub_id] = 'group_id';
				$param[content_table] = TABLE_PREFIX.'blog_posts_comments';
				$param[content_id] = 'comment_id';
				//$param[content_head] = 'date';
				$param[content_body] = 'comment';
				$param[group_title] = $this->get_group_title();
				$param[mail_subject] = _AT('blogcomment_notify_subject');
				$param[mail_header] = _AT('blogcomment_notify_body', $param[group_title], AT_BASE_HREF.'bounce.php?course='.$_SESSION['course_id']);
				
			break;	
			case "course":
			break;
	
			case "forum":
			break;
	
			case "thread":
			break;
	
			default: //If unknown entity type, return false
				return false;
		}
		return $param;
	}

	private function check_blog_subscribability($group_id,$member_id){
		$sql="SELECT COUNT(*) FROM ".TABLE_PREFIX."groups t1 LEFT JOIN ".TABLE_PREFIX."groups_types t2 ON t1.type_id=t2.type_id LEFT JOIN ".TABLE_PREFIX."course_enrollment t3 ON t2.course_id=t3.course_id WHERE group_id='".$group_id."' AND member_id='".$member_id."'";
		$result = mysql_fetch_row(mysql_query($sql));
		return (empty($result[0]))?false:true;
	}
}
?>