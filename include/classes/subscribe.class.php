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
		$sql = ($ent_param) ? "SELECT COUNT(*) as count FROM %s WHERE member_id = %d AND  %s = %d" : false;
				
		// Run SQL and check if table is populated for given member id and entity id
		if ($sql){
		    $result = queryDB($sql, array($ent_param['sub_table'], $member_id ,  $ent_param['sub_id'], $entity_id), TRUE);
			return (empty($result['count']))?false:true;
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
			$sql = "SELECT title FROM %sgroups WHERE group_id=%d";
			$result = queryDB($sql, array(TABLE_PREFIX, $gid), TRUE);

		} else {
			return false;
		}
	}
	
	// Gets email and site name
	private function get_system_email(){
		$sql = "SELECT * FROM %sconfig WHERE name = 'site_name' OR name = 'contact_email'";
		$rows_config = queryDB($sql, array(TABLE_PREFIX));
		foreach($rows_config as $row){
			if ($row['name'] == 'site_name'){
				$sysinfo['site_name'] = $row[1];
			} elseif ($row['name'] == 'contact_email'){
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
        $sql = ($ent_param) ? "INSERT INTO %s (member_id,  %s) VALUES(%d,%d)" : false;
        $result = queryDB($sql, array($ent_param['sub_table'], $ent_param['sub_id'], $member_id, $entity_id));
        return ($result > 0)?true:false;
	}
	
	// Unsubscribes user to feed
	public function unset_subscription($entity_type, $member_id, $entity_id){
		$ent_param = $this->entity_switch($entity_type);
		$sql = ($ent_param) ? "DELETE FROM %s WHERE member_id = %d AND %s = %d" : false;
		$result = queryDB($sql, array($ent_param['sub_table'], $member_id, $ent_param['sub_id'], $entity_id));
        return ($result > 0)?true:false;
	}
	
	// Sends mail to all subscribed users
	public function send_mail($entity_type,$entity_id,$post_id){
		// We need the automailer
		require(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');
		
		//Also, we need to know what ballpark we're in
		$ent_param = $this->entity_switch($entity_type);
		
		// Now, what are we going to send?
		$fetch = (!empty($ent_param[content_head]))?$ent_param[content_head].",".$ent_param[content_body]:$ent_param[content_body];	
		$sql = "SELECT %s FROM %s WHERE %s = %d";
		$post = queryDB($sql, array($fetch, $ent_param['content_table'], $ent_param['content_id'], $post_id));		
		
		//Get all subscribers
		$sql = "SELECT t1.email, t1.member_id FROM %smembers t1, %s t2 WHERE t2.%s = %d AND t1.member_id=t2.member_id";
		$rows_subscribers = queryDB($sql, array(TABLE_PREFIX,  $ent_param['sub_table'], $ent_param['sub_id'],  $entity_id));
		
		//get system email
		$sysinfo = $this->get_system_email();

		//Send lots of mails
		foreach($rows_subscribers as $subscriber){
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
		$sql="SELECT COUNT(*) FROM %sgroups t1 LEFT JOIN %sgroups_types t2 ON t1.type_id=t2.type_id LEFT JOIN %scourse_enrollment t3 ON t2.course_id=t3.course_id WHERE group_id=%d AND member_id=%d";
		$result = queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX, TABLE_PREFIX, $group_id, $member_id));
		return (empty($result[0]))?false:true;
	}
}
?>