<?php


class GmCallbacksClass
{

	/*
	* Event callback functions send an email to a badge recipient with the new badge, 
	* and a list of badges earned so far
	* @params are defined in the events.php file in the call to each executeEvent()
	*/
	
    static function ReadPageCallback($params)
    {
        if ($params['badges']){     
            $feedback_img = '<div style="float:left;text-align:top;height:5em; width:5em; margin-left:1em; margin-right:1em;margin-top:-.5em;"><img src="'.self::getBadgeFile($params['alias']).'" alt="" style="float:left;"/></div>';  
             $feedback .= '<table>
             <tr><td>'.$feedback_img.'</td><td>'.self::getReachMessage('read_page').'</td></tr></table>';              
            //$feedback = "Congratulations, you have received a new badge for getting a good amount of course reading done. ";
            $message .= self::getNewBadge($params, $feedback);
            $message .= self::getCurrentBadges($params['badges']);
        } 
        if(!empty($message)){
        	self::SendMail($params, $message);
        	$_GET['fb'] = $feedback;
        }
        return true;
    }
    static function WelcomeCallback($params)
    {
        if ($params['firstname']){    
            $feedback_img = '<div style="float:left;text-align:top;height:5em; width:5em; margin-left:1em; margin-right:1em;margin-top:-.5em;"><img src="'.self::getBadgeFile($params['alias']).'" alt="" style="float:left;"/></div>';  
             $feedback .= '<table>
             <tr><td>'.$feedback_img.'</td><td>'.self::getReachMessage('welcome').'</td></tr></table>';        
            //$feedback = "Welcome to the course. You have earned your first badge by successfully logging in. Continue earning badges by using the features in the course, and participating in course activities.<br /><br />By participating in the course you can also earn points and advance through levels as your points grow. Follow the leader board to see your position among others in the course. Watch for hints after earning a badge, for earning additional badges and bonus points.";
            $message .= self::getNewBadge($params, $feedback);
        } 
        if(!empty($message)){
        	self::SendMail($params, $message);
        	$_GET['fb'] = $feedback;
        }
        return true;
    }
    static function LoginReachCallback($params)
    {
        if ($params['badges']){           
            $feedback_img = '<div style="float:left;text-align:top;height:5em; width:5em; margin-left:1em; margin-right:1em;margin-top:-.5em;"><img src="'.self::getBadgeFile($params['alias']).'" alt="" style="float:left;"/></div>';  
             $feedback .= '<table>
             <tr><td>'.$feedback_img.'</td><td>'.self::getReachMessage('login').'</td></tr></table>';                
            //$feedback = "Congratulations, you have received a new badge for logging into the course many times. You can also earn points by logging out of the course properly, clicking the logout link, instead of just leaving or letting your session timeout.";
            $message .= self::getNewBadge($params, $feedback);
            $message .= self::getCurrentBadges($params['badges']);
        } 
        if(!empty($message)){
        	self::SendMail($params, $message);
        	$_GET['fb'] = $feedback;
        }
        return true;
    }
    static function LogoutReachCallback($params)
    {   
        if ($params['badges']){                                 
            $feedback_img = '<div style="float:left;text-align:top;height:5em; width:5em; margin-left:1em; margin-right:1em;margin-top:-.5em;"><img src="'.$_base_href.self::getBadgeFile($params['alias']).'" alt="" style="float:left;"/></div>';  
             $feedback .= '<table>
             <tr><td>'.$feedback_img.'</td><td>'.self::getReachMessage('logout').'</td></tr></table>'; 
             //$feedback = "Congratulations, you have received a new badge for logging out properly, instead of leaving or letting your session timeout, maintaining your privacy and security. ";
            $message .= self::getNewBadge($params, $feedback);
            $message .= self::getCurrentBadges($params['badges']);
        } 
        if(!empty($message)){
        	self::SendMail($params, $message);
        	$_GET['fb'] = $feedback;
        }
        return true;
    }

    static function ProfileViewReachCallback($params)
    {
        if ($params['badges']){ 
            $feedback_img = '<div style="float:left;text-align:top;height:5em; width:5em; margin-left:1em; margin-right:1em;margin-top:-.5em;"><img src="'.self::getBadgeFile($params['alias']).'" alt="" style="float:left;"/></div>';  
             $feedback .= '<table>
             <tr><td>'.$feedback_img.'</td><td>'.self::getReachMessage('profile_view').'</td></tr></table>';                        
            //$feedback = "Congratulations, you have received a new badge for getting to know your classmates by viewing their profiles. You can earn additional points by sending a private message to a person through their profile page.";
            $message .= self::getNewBadge($params, $feedback);
            $message .= self::getCurrentBadges($params['badges']);
        } 
        if(!empty($message)){
        	self::SendMail($params, $message);
        	$_GET['fb'] = $feedback;
        }
        return true;
    }
    static function ProfileViewedReachCallback($params)
    {
    	if ($params['badges']){ 
            $feedback_img = '<div style="float:left;text-align:top;height:5em; width:5em; margin-left:1em; margin-right:1em;margin-top:-.5em;"><img src="'.self::getBadgeFile($params['alias']).'" alt="" style="float:left;"/></div>';  
             $feedback .= '<table>
             <tr><td>'.$feedback_img.'</td><td>'.self::getReachMessage('profile_viewed').'</td></tr></table>';                    
            //$feedback = "Congratulations, you have received a new badge because lots of people have been viewing your profile. ";
            $message .= self::getNewBadge($params, $feedback);
            $message .= self::getCurrentBadges($params['badges']);
        } 
        if(!empty($message)){
        	self::SendMail($params, $message);
        	$_GET['fb'] = $feedback;
        }
        return true;
    }
    static function ProfilePicUploadCallback($params)
    {      
        if ($params['badges']){     
            $feedback_img = '<div style="float:left;text-align:top;height:5em; width:5em; margin-left:1em; margin-right:1em;margin-top:-.5em;"><img src="'.self::getBadgeFile($params['alias']).'" alt="" style="float:left;"/></div>';  
             $feedback .= '<table>
             <tr><td>'.$feedback_img.'</td><td>'.self::getReachMessage('profile_pic_upload').'</td></tr></table>';                               
            //$feedback = "Congratulations, you have received a new badge for adding a profile picture. Update your profile picture occassionally to receive additional points. ";
            $message .= self::getNewBadge($params, $feedback);
            $message .= self::getCurrentBadges($params['badges']);
        } 
        if(!empty($message)){
        	self::SendMail($params, $message);
        	$_GET['fb'] = $feedback;
        }
        return true;
    }
    static function PreferencesUpdateCallback($params)
    { 
       if ($params['badges']){     
            $feedback_img = '<div style="float:left;text-align:top;height:5em; width:5em; margin-left:1em; margin-right:1em;margin-top:-.5em;"><img src="'.self::getBadgeFile($params['alias']).'" alt="" style="float:left;"/></div>';  
             $feedback .= '<table>
             <tr><td>'.$feedback_img.'</td><td>'.self::getReachMessage('prefs_update').'</td></tr></table>';                                              
            //$feedback = "Congratulations, you have received a new badge for updating your personal preference. ";
            $message .= self::getNewBadge($params, $feedback);
            $message .= self::getCurrentBadges($params['badges']);
        } 
        if(!empty($message)){
        	self::SendMail($params, $message);
        	$_GET['fb'] = $feedback;
        }
        return true;
    }
    static function FileStorageFolderCallback($params)
    {
        if ($params['badges']){     
            $feedback_img = '<div style="float:left;text-align:top;height:5em; width:5em; margin-left:1em; margin-right:1em;margin-top:-.5em;"><img src="'.self::getBadgeFile($params['alias']).'" alt="" style="float:left;"/></div>';  
             $feedback .= '<table>
             <tr><td>'.$feedback_img.'</td><td>'.self::getReachMessage('new_folder').'</td></tr></table>';                                                              
            //$feedback = "Congratulations, you have received a new badge for learning how to create folders to organize your files. You can also earn points and badges by adding files to those folders";
            $message .= self::getNewBadge($params, $feedback);
            $message .= self::getCurrentBadges($params['badges']);
        } 
        if(!empty($message)){
        	self::SendMail($params, $message);
        	$_GET['fb'] = $feedback;
        }
        return true;
    }
    static function UploadFilesCallback($params)
    {
        if ($params['badges']){         
            $feedback_img = '<div style="float:left;text-align:top;height:5em; width:5em; margin-left:1em; margin-right:1em;margin-top:-.5em;"><img src="'.self::getBadgeFile($params['alias']).'" alt="" style="float:left;"/></div>';  
             $feedback .= '<table>
             <tr><td>'.$feedback_img.'</td><td>'.self::getReachMessage('upload_file').'</td></tr></table>';           
            //$feedback = "Congratulations, you have received a new badge for learning how to use file storage to store your files. Create additional folders to organize your files for additional points and badges.";
            $message .= self::getNewBadge($params, $feedback);
            $message .= self::getCurrentBadges($params['badges']);
        } 
        if(!empty($message)){
        	self::SendMail($params, $message);
        	$_GET['fb'] = $feedback;
        }
        return true;
    } 
    static function CreateFilesCallback($params)
    {
        if ($params['badges']){        
            $feedback_img = '<div style="float:left;text-align:top;height:5em; width:5em; margin-left:1em; margin-right:1em;margin-top:-.5em;"><img src="'.self::getBadgeFile($params['alias']).'" alt="" style="float:left;"/></div>';  
             $feedback .= '<table>
             <tr><td>'.$feedback_img.'</td><td>'.self::getReachMessage('create_file').'</td></tr></table>';                  
            //$feedback = "Congratulations, you have received a new badge for learning how to create new files in file storage.";
            $message .= self::getNewBadge($params, $feedback);
            $message .= self::getCurrentBadges($params['badges']);
        } 
        if(!empty($message)){
        	self::SendMail($params, $message);
        	$_GET['fb'] = $feedback;
        }
        return true;
    }  
    static function ForumViewCallback($params)
    {
        if ($params['badges']){          
            $feedback_img = '<div style="float:left;text-align:top;height:5em; width:5em; margin-left:1em; margin-right:1em;margin-top:-.5em;"><img src="'.self::getBadgeFile($params['alias']).'" alt="" style="float:left;"/></div>';  
             $feedback .= '<table>
             <tr><td>'.$feedback_img.'</td><td>'.self::getReachMessage('forum_view').'</td></tr></table>';               
            //$feedback = "Congratulations, you have received a new badge for keeping up with reading forum posts. Continue reading forum posts, start new threads, and reply to others posts to earn additional points and badges.";
            $message .= self::getNewBadge($params, $feedback);
            $message .= self::getCurrentBadges($params['badges']);
        } 
        if(!empty($message)){
        	self::SendMail($params, $message);
        	$_GET['fb'] = $feedback;
        }
        return true;
    }  
    static function ForumPostsCallback($params)
    {
        if ($params['badges']){      
            $feedback_img = '<div style="float:left;text-align:top;height:5em; width:5em; margin-left:1em; margin-right:1em;margin-top:-.5em;"><img src="'.self::getBadgeFile($params['alias']).'" alt="" style="float:left;"/></div>';  
             $feedback .= '<table>
             <tr><td>'.$feedback_img.'</td><td>'.self::getReachMessage('forum_post').'</td></tr></table>';                   
            //$feedback = "Congratulations, you have received a new badge for contributing new threads to the discussion forums. Continue reading forum posts, start new threads, and reply to others posts to earn additional points and badges.";
            $message .= self::getNewBadge($params, $feedback);
            $message .= self::getCurrentBadges($params['badges']);
        } 
        if(!empty($message)){
        	self::SendMail($params, $message);
        	$_GET['fb'] = $feedback;
        }
        return true;
    } 
    static function ForumReplyCallback($params)
    {
        if ($params['badges']){      
            $feedback_img = '<div style="float:left;text-align:top;height:5em; width:5em; margin-left:1em; margin-right:1em;margin-top:-.5em;"><img src="'.self::getBadgeFile($params['alias']).'" alt="" style="float:left;"/></div>';  
             $feedback .= '<table>
             <tr><td>'.$feedback_img.'</td><td>'.self::getReachMessage('forum_reply').'</td></tr></table>';                    
            //$feedback = "Congratulations, you have received a new badge for contributing good feedback to discussion forums. Continue reading forum posts, start new threads, and reply to others posts to earn additional points and badges.";
            $message .= self::getNewBadge($params, $feedback);
            $message .= self::getCurrentBadges($params['badges']);
        } 
        if(!empty($message)){
        	self::SendMail($params, $message);
        	$_GET['fb'] = $feedback;
        }
        return true;
    } 
    static function BlogAddCallback($params)
    {
        if ($params['badges']){
            $feedback_img = '<div style="float:left;text-align:top;height:5em; width:5em; margin-left:1em; margin-right:1em;margin-top:-.5em;"><img src="'.self::getBadgeFile($params['alias']).'" alt="" style="float:left;"/></div>';  
             $feedback .= '<table>
             <tr><td>'.$feedback_img.'</td><td>'.self::getReachMessage('blog_add').'</td></tr></table>';                           
            //$feedback = "Congratulations, you have received a new badge for contributing a good collection of blog posts. Continue adding to your blog, and comments on others' blogs to earn additional points and badges.";
            $message .= self::getNewBadge($params, $feedback);
            $message .= self::getCurrentBadges($params['badges']);
        } 
        if(!empty($message)){
        	self::SendMail($params, $message);
        	$_GET['fb'] = $feedback;
        }
        return true;
    }   
    static function BlogCommentsCallback($params)
    {
        if ($params['badges']){           
            $feedback_img = '<div style="float:left;text-align:top;height:5em; width:5em; margin-left:1em; margin-right:1em;margin-top:-.5em;"><img src="'.self::getBadgeFile($params['alias']).'" alt="" style="float:left;"/></div>';  
             $feedback .= '<table>
             <tr><td>'.$feedback_img.'</td><td>'.self::getReachMessage('blog_comment').'</td></tr></table>';                
            //$feedback = "Congratulations, you have received a new badge for contributing good feedback, and commenting on blog posts. Continue posting to your blog, and commenting on others' blog posts to earn additional points.";
            $message .= self::getNewBadge($params, $feedback);
            $message .= self::getCurrentBadges($params['badges']);
        } 
        if(!empty($message)){
        	self::SendMail($params, $message);
        	$_GET['fb'] = $feedback;
        }
        return true;
    } 
    static function ChatLoginCallback($params)
    {
        if ($params['badges']){      
            $feedback_img = '<div style="float:left;text-align:top;height:5em; width:5em; margin-left:1em; margin-right:1em;margin-top:-.5em;"><img src="'.self::getBadgeFile($params['alias']).'" alt="" style="float:left;"/></div>';  
             $feedback .= '<table>
             <tr><td>'.$feedback_img.'</td><td>'.self::getReachMessage('chat_login').'</td></tr></table>';                  
            //$feedback = "Congratulations, you have received a new badge for logging into the chat regularly. Just using the chat helps accumulate points.";
            $message .= self::getNewBadge($params, $feedback);
            $message .= self::getCurrentBadges($params['badges']);
        } 
        if(!empty($message)){
        	self::SendMail($params, $message);
        	$_GET['fb'] = $feedback;
        }
        return true;
    }
    static function ChatPostCallback($params)
    {
        if ($params['badges']){    
            $feedback_img = '<div style="float:left;text-align:top;height:5em; width:5em; margin-left:1em; margin-right:1em;margin-top:-.5em;"><img src="'.self::getBadgeFile($params['alias']).'" alt="" style="float:left;"/></div>';  
             $feedback .= '<table>
             <tr><td>'.$feedback_img.'</td><td>'.self::getReachMessage('chat_post').'</td></tr></table>';                    
           // $feedback = "Congratulations, you have received a new badge for keeping conversation going in the chat room. Returning to the chat room regularly earns additional points.";
            $message .= self::getNewBadge($params, $feedback);
            $message .= self::getCurrentBadges($params['badges']);
        } 
        if(!empty($message)){
        	self::SendMail($params, $message);
        	$_GET['fb'] = $feedback;
        }
        return true;
    }
    static function LinkAddCallback($params)
    {
        if ($params['badges']){     
            $feedback_img = '<div style="float:left;text-align:top;height:5em; width:5em; margin-left:1em; margin-right:1em;margin-top:-.5em;"><img src="'.self::getBadgeFile($params['alias']).'" alt="" style="float:left;"/></div>';  
             $feedback .= '<table>
             <tr><td>'.$feedback_img.'</td><td>'.self::getReachMessage('link_add').'</td></tr></table>';                          
            $feedback = "Congratulations, you have received a new badge for making a good contribution to the course links. View links others have posted to earn additional points.";
            $message .= self::getNewBadge($params, $feedback);
            $message .= self::getCurrentBadges($params['badges']);
        } 
        if(!empty($message)){
        	self::SendMail($params, $message);
        	$_GET['fb'] = $feedback;
        }
        return true;
    }
    static function PhotoAlbumCallback($params)
    { 
        if ($params['badges']){    
            $feedback_img = '<div style="float:left;text-align:top;height:5em; width:5em; margin-left:1em; margin-right:1em;margin-top:-.5em;"><img src="'.self::getBadgeFile($params['alias']).'" alt="" style="float:left;"/></div>';  
             $feedback .= '<table>
             <tr><td>'.$feedback_img.'</td><td>'.self::getReachMessage('photo_create_album').'</td></tr></table>';                   
            //$feedback = "Congratulations, you have received a new badge for creating a photo album. Continue adding photos to earn more points and badges. ";
            $message .= self::getNewBadge($params, $feedback);
            $message .= self::getCurrentBadges($params['badges']);
        } 
        if(!empty($message)){
        	self::SendMail($params, $message);
        	$_GET['fb'] = $feedback;
        }
        return true;
    }
    static function PhotoAlbumsCallback($params)
    {
        if ($params['badges']){        
            $feedback_img = '<div style="float:left;text-align:top;height:5em; width:5em; margin-left:1em; margin-right:1em;margin-top:-.5em;"><img src="'.self::getBadgeFile($params['alias']).'" alt="" style="float:left;"/></div>';  
             $feedback .= '<table>
             <tr><td>'.$feedback_img.'</td><td>'.self::getReachMessage('photo_create_albums').'</td></tr></table>';                  
           // $feedback = "Congratulations, you have received a new badge for creating multiple photo albums to organize your photos. Continue adding photos to earn more points. ";
            $message .= self::getNewBadge($params, $feedback);
            $message .= self::getCurrentBadges($params['badges']);
        } 
        if(!empty($message)){
        	self::SendMail($params, $message);
        	$_GET['fb'] = $feedback;
        }
        return true;
    }
    static function PhotoUploadCallback($params)
    {
        //global $msg;
        if ($params['badges']){     
            $feedback_img = '<div style="float:left;text-align:top;height:5em; width:5em; margin-left:1em; margin-right:1em;margin-top:-.5em;"><img src="'.self::getBadgeFile($params['alias']).'" alt="" style="float:left;"/></div>';  
             $feedback .= '<table>
             <tr><td>'.$feedback_img.'</td><td>'.self::getReachMessage('photo_upload').'</td></tr></table>';                 
            //$feedback = "Congratulations, you have received a new badge for uploading a good collection of photos. Continue adding photos to earn more points. Create additional albums to organize your photos for bonus points.";
            $message .= self::getNewBadge($params, $feedback);
            $message .= self::getCurrentBadges($params['badges']);
        } 
        if(!empty($message)){
        	self::SendMail($params, $message);
        	$_GET['fb'] = $feedback;
        }
        return true;
    }
    static function PhotoAlbumCommentCallback($params)
    {
        global $msg;
        if ($params['badges']){    
            $feedback_img = '<div style="float:left;text-align:top;height:5em; width:5em; margin-left:1em; margin-right:1em;margin-top:-.5em;"><img src="'.self::getBadgeFile($params['alias']).'" alt="" style="float:left;"/></div>';  
             $feedback .= '<table>
             <tr><td>'.$feedback_img.'</td><td>'.self::getReachMessage('photo_album_comment').'</td></tr></table>';                   
            //$feedback = "Congratulations, you have received a new badge for providing comments on your's, and other's albums. Continue commenting about albums for additional points.";
            $message .= self::getNewBadge($params, $feedback);
            $message .= self::getCurrentBadges($params['badges']);
        } 
        if(!empty($message)){
        	
        	//$badge_file= '<img src="'.self::getBadgeFile($params['alias']).'" style="float:left;text-align:top;"/>';
        	//$msg->addFeedback('Congratulations, you have received a new badge for uploading a good collection of photos. Continue adding photos to earn more points. Create additional folders to organize your photos for ponus points.');
        	//$msg->addFeedback(array('GM_PA_COMMENTS', $badge_file));
            self::SendMail($params, $message);
            $_GET['fb'] = $feedback;
        }
        return true;
    }
    static function PhotoDescriptionCallback($params)
    {
        global $msg;
        if ($params['badges']){       
            $feedback_img = '<div style="float:left;text-align:top;height:5em; width:5em; margin-left:1em; margin-right:1em;margin-top:-.5em;"><img src="'.self::getBadgeFile($params['alias']).'" alt="" style="float:left;"/></div>';  
             $feedback .= '<table>
             <tr><td>'.$feedback_img.'</td><td>'.self::getReachMessage('photo_description').'</td></tr></table>';                     
            //$feedback = "Congratulations, you have received a new badge for providing descriptions for your photos. Add alternative text to make your photos accessible to blind classmates, and earn bonus points and a badge";
            $message .= self::getNewBadge($params, $feedback);
            $message .= self::getCurrentBadges($params['badges']);
        } 
        if(!empty($message)){
            self::SendMail($params, $message);
            $_GET['fb'] = $feedback;
        }
        return true;
    }
    static function PhotoAltTextCallback($params)
    {
        global $msg;
        if ($params['badges']){         
            $feedback_img = '<div style="float:left;text-align:top;height:5em; width:5em; margin-left:1em; margin-right:1em;margin-top:-.5em;"><img src="'.self::getBadgeFile($params['alias']).'" alt="" style="float:left;"/></div>';  
             $feedback .= '<table>
             <tr><td>'.$feedback_img.'</td><td>'.self::getReachMessage('photo_alt_text').'</td></tr></table>';                    
            //$feedback = "Congratulations, you have received a new badge for providing alternative text for your photos. This makes photos accessible to blind classmates using a screen reader to access the course. Providing descriptions for your photos can also earn points, and a badge. ";
            $message .= self::getNewBadge($params, $feedback);
            $message .= self::getCurrentBadges($params['badges']);
        } 
        if(!empty($message)){
            self::SendMail($params, $message);
            $_GET['fb'] = $feedback;
        }
        return true;
    }
    static function PhotoCommentCallback($params)
    {
        global $msg;
        if ($params['badges']){        
            $feedback_img = '<div style="float:left;text-align:top;height:5em; width:5em; margin-left:1em; margin-right:1em;margin-top:-.5em;"><img src="'.self::getBadgeFile($params['alias']).'" alt="" style="float:left;"/></div>';  
             $feedback .= '<table>
             <tr><td>'.$feedback_img.'</td><td>'.self::getReachMessage('photo_comment').'</td></tr></table>';                     
            //$feedback = "Congratulations, you have received a new badge for providing comments on yours, and others photos. Continue commenting to earn additional points. You can also comment on photo albums as a whole, to earn bonus points. ";
            $message .= self::getNewBadge($params, $feedback);
            $message .= self::getCurrentBadges($params['badges']);
        } 
        if(!empty($message)){
            self::SendMail($params, $message);
            $_GET['fb'] = $feedback;
        }
        return true;
    }
    /*
    * Helper functions used in the Callback functions above, to gather
    * badge details, and to send the email.
    * @$params are passed from the events.php file to the callback, fowarded 
    * to the SendMail method along with a matching feedback message
    */
    public static function SendMail($params, $message){
            global $_config, $_base_path,$msg;
            if(!isset($params['email'])){
                $sql = "SELECT email FROM %smembers WHERE member_id =%d";
                $user_email = queryDB($sql, array(TABLE_PREFIX, $_SESSION['member_id']), TRUE);
                $to_email = $user_email['email'];
            } else {
                $to_email = $params['email'];
            }
            
            $root_path =  preg_replace ('#/get.php#','',$_base_path);
            $this_path =  preg_replace ('#/get.php#','',$_SERVER['DOCUMENT_ROOT'].$root_path);
            require_once($this_path.'mods/_standard/gameme/gamify.lib.php');
            $from_email = $_config['contact_email'];
        
            if ($to_email != '') {
				$tmp_message = '<div style="width:98%; background-color:#eeeeee;padding:1em;"><div style="width:80%; margin-left:auto;margin-right:auto;border:thin solid #cccccc;padding:.5em;background-color:#ffffff;">'.$message.'</div></div>';
				require($this_path.'mods/_standard/gameme/atutormailer.class.php');

				$mail = new ATutorMailer;
                $mail->IsHTML(true);
				$mail->From = $from_email;
				$mail->AddAddress($to_email);
				$mail->Subject = _AT('gm_gameme_notification');
				$mail->Body    = $tmp_message;

				if(!$mail->Send()) {
					//echo 'There was an error sending the message';
				   //$msg->addError('SENDING_ERROR');
				   //exit;
				   // INSTEAD FAIL QUIETLY
				} 
				unset($mail);
			} else {
			    $msg->addError('no email provided');
			}
	return true;
    
    }
    
    /* Gets a list of the badges a user has already earned
    * @$badges is an array of badges passed from events.php, to the callback,
    * forwarded to this funciton to turn into a table of badges to be 
    * sent in the email
    */
    public static function getCurrentBadges($badges){
        global $_base_href;
    	if(!empty($badges)){
    		$current_badges .='<h3>'._AT('gm_badges_so_far').'</h3>'."\n";                  
            $current_badges .= '<table style="border:1px solid #eeeeee;width:100%;">';     
            foreach ($badges as $badge) {
                $current_badges .=  '<tr><td style="background-color:#eeeee;"><img src="'.self::getBadgeFile($badge->getBadge()->getAlias()).'" alt="'.$badge->getBadge()->getTitle().'" style="vertical-align:top"/></td>';
                $current_badges .=  '<td style="background-color:#efefef; padding:.3em;"><strong>'.$badge->getBadge()->getTitle().'</strong><br/>'.$badge->getBadge()->getDescription().'</td></tr>'."\n";

            }
            $current_badges .= "</table>";
    	} 
    	return $current_badges;
    }
    /* Gets the details for the badge just earned
    * @$params bassed from events.php, to the callback, forwared to this function
    * @$feedback a feedback message to be sent along in the email, defined in the 
    * callback functions above.
    */
    public static function getNewBadge($params, $feedback){
    		$earned_badge = self::getBadge($params['alias']);
        	$new_badge .='<div style="width:auto;padding-left: 2em;border:1px solid #cccccc;background-color:#f6f4da;"><h2>'.$_SESSION['course_title'].'</h2></div>';
            $new_badge .= '<p> Hi '.$params['firstname']."!</p>\n\n";
            $new_badge .= "<p>".$feedback." <br /></p>" ;
            return $new_badge;
    }
    
    /* Figures out where to get the badge image from, either 
    * 1. a custom badge created by the instructor
    * 2. a custom badge created by the administrator
    * 3. the default badge that comes with the module
    * -in that order, whichever come first-
    * @$alias the alias for the badge defined in the gm_badges table, 
    * and passed from the events.php file 
    */    
    public static function getBadgeFile($alias){
        global $_base_href;
        $sql = "SELECT id, alias, image_url, description FROM %sgm_badges WHERE alias='%s' AND course_id=%d";
        $badge_image = queryDB($sql, array(TABLE_PREFIX, $alias,$_SESSION['course_id']), TRUE);
        
        if(!empty($badge_image)){
            // Course badge
            $badge_file_array = explode('/',$badge_image['image_url']);
            array_shift($badge_file_array);
            array_shift($badge_file_array);
            $badge_file_stem = implode('/',$badge_file_array);

            if(is_file(AT_CONTENT_DIR.$_SESSION['course_id'].'/'.$badge_file_stem)){
                $badge_file = $_base_href.'mods/_standard/gameme/get_course_icon.php?badge_id='.$badge_image['id'].SEP.'course_id='.$_SESSION['course_id'];
            } else {
                $badge_file = $_base_href.$badge_image['image_url'];
            }

        } else{
            // Not a course badge, so check for default badge
            $sql = "SELECT id, alias, image_url, description FROM %sgm_badges WHERE alias='%s' AND course_id=%d";
            $badge_image_default = queryDB($sql, array(TABLE_PREFIX, $alias, 0), TRUE);

            if(strstr($badge_image_default['image_url'], "content")){
                // Custom default badge
                $badge_file = $_base_href.'mods/_standard/gameme/get_badge_icon.php?badge_id='.$badge_image_default['id'];
            }else{
                // Default badge
                $badge_file = $_base_href.$badge_image_default['image_url'];
            }
        }
         return $badge_file;
    }
    
    /* Gets the badge details from the database, either
    * 1. a badge created by the instructor for a particular course
    * 2. a custom badge create by the administrator
    * 3. the default badge that come with the module
    * -in that order, whichever comes first-
    * @$alias the alias for the badge defined in the gm_badges table, 
    * and passed from the events.php file 
    */
    public static function getBadge($alias){
        global $_base_href;
        if($_SESSION['course_id'] > 0){
            $is_course = " AND course_id=".$_SESSION['course_id'];
        } else{
            $is_course = " AND course_id=0";
        }
        $sql = "SELECT * from %sgm_badges WHERE alias = '%s' $is_course";
        if($badge = queryDB($sql, array(TABLE_PREFIX, $alias), TRUE)){
            // all good
        }else{
            // course badge does not exist so get the system default
            $sql = "SELECT * from %sgm_badges WHERE alias = '%s' AND course_id=0";
            $badge = queryDB($sql, array(TABLE_PREFIX, $alias), TRUE);
        }
        $badge['image_url']= $_base_href.self::getBadgeFile($badge['alias']);
        return $badge;
    }
	 /* Gets the message to display for the reach email alert, either
    * 1. a message created by the instructor for a particular course
    * 2. a custom message created by the administrator
    * 3. the default message that come with the module
    * -in that order, whichever come first-
    * @$alias the alias for the event defined in the gm_events table, 
    * and passed from the events.php file 
    */
    public static function getReachMessage($alias){
         if($_SESSION['course_id'] > 0){
            $is_course = " AND course_id=".$_SESSION['course_id'];
        } else{
            $is_course = " AND course_id=0";
        }
        
        $sql = "SELECT reach_message from %sgm_events WHERE alias = '%s' $is_course";
        
        if($reach_message = queryDB($sql, array(TABLE_PREFIX, $alias), TRUE)){
            // all good
        }else{
            // reach message does not exist so get the system default
            $sql = "SELECT reach_message from %sgm_events WHERE alias = '%s' AND course_id=0";
            $reach_message = queryDB($sql, array(TABLE_PREFIX, $alias), TRUE);
        }
        return $reach_message['reach_message'];
    }
}