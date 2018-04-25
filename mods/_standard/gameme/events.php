<?php
namespace gameme\PHPGamification;

// PHP Gamification Block
// Set enviroment

use Exception;
use gameme\PHPGamification;
use gameme\PHPGamification\Model;
use gameme\PHPGamification\Model\Event;

if(isset($_SESSION['member_id'])){
    global $_base_path;
    global $_config;
    // Hack to fix the get.php appending issue
    $this_path =  preg_replace ('#/get.php#','',$_SERVER['DOCUMENT_ROOT'].$_base_path);
    if($_SESSION['course_id'] > 0){
        $course_id = $_SESSION['course_id'];
    }else if($_REQUEST['course_id'] >0){
        $course_id = $_REQUEST['course_id'];
    }else{
        $course_id = 0;
    } 
    require_once(AT_INCLUDE_PATH.'../mods/_standard/gameme/GmCallbacksClass.php');
    require_once(AT_INCLUDE_PATH.'../mods/_standard/gameme/PHPGamification/PHPGamification.class.php');

    $gamification = new PHPGamification();
    $gamification->setDAO(new DAO(DB_HOST, DB_NAME, DB_USER, DB_PASSWORD));
    $gamification->setUserId($_SESSION['member_id'], $course_id);
    $sql = "SELECT email, first_name FROM %smembers WHERE member_id =%d";
    $gm_member = queryDB($sql, array(TABLE_PREFIX, $_SESSION['member_id']), TRUE);
    
     ////////
    // LOGIN TO OR ENTER COURSE
    
    // SEE MANTIS 5733 FOR DETAILS ON LOGIN EVENT BUG. NEED A NEW TRIGGER
/*    if(strpos($_SERVER['PHP_SELF'], "bounce.php") && $_REQUEST['course'] >0){
        $_SESSION['course_id'] = $_REQUEST['course'];
        $gamification->executeEvent('login', array(
                    'user_id'=>$_SESSION['member_id'], 
                    'alias'=>'login_badge',
                    'email'=>$gm_member['email'],
                    'firstname'=>$gm_member['first_name'],
                    'contact_email'=>$_config['contact_email'],
                    'badges'=>$gamification->getUserBadges()));
    } */
    
    // LOGIN TO OR ENTER A COURSE FOR THE FIRST TIME
    if(strpos($_SERVER['PHP_SELF'], "bounce.php")&& $_REQUEST['course'] > 0){
        $_SESSION['course_id'] = $_REQUEST['course'];
        $gamification->executeEvent('welcome', array(
                    'user_id'=>$_SESSION['member_id'], 
                    'alias'=>'welcome_badge',
                    'email'=>$gm_member['email'],
                    'firstname'=>$gm_member['first_name'],
                    'contact_email'=>$_config['contact_email'],
                    'badges'=>$gamification->getUserBadges()));
    }    
    
    ////////
    // LOGOUT PROPERLY
    if(strpos($_SERVER['PHP_SELF'], "logout.php")){
        $gamification->executeEvent('logout', array(
                    'user_id'=>$_SESSION['member_id'], 
                    'alias'=>'logout_badge',
                    'email'=>$gm_member['email'],
                    'firstname'=>$gm_member['first_name'],
                    'contact_email'=>$_config['contact_email'], 
                    'course_id'=>$_SESSION['course_id'],
                    'badges'=>$gamification->getUserBadges()));
    }
    
    ////////
    // CONTENT
    // View content page, but prevent reloading for points
    if(strpos($_SERVER['PHP_SELF'], "content.php")&& isset($_REQUEST['cid'])){
        // Prevent points by page reloading
        if($_SESSION['content_id'] != $_REQUEST['cid']){
            $_SESSION['content_id'] = $_REQUEST['cid'];
            $gamification->executeEvent('read_page', 
                    array('user_id'=>$_SESSION['member_id'], 
                    'email'=>$gm_member['email'],
                    'alias'=>'read_page_badge',
                    'firstname'=>$gm_member['first_name'],
                    'contact_email'=>$_config['contact_email'],
                    'badges'=>$gamification->getUserBadges()));
        }
        // Time on page
        if(isset($_SESSION['timer'])){
            $duration = (time() - $_SESSION['timer']);
            // If on a page 30 seconds or more, extra points
            if($duration > 30){
                $gamification->executeEvent('read_time');
            }
            // Reset timer
            $_SESSION['timer'] = time();

        } else {
            // Set new timer
            $_SESSION['timer'] = time();
        }          
    }
    if(!strpos($_SERVER['PHP_SELF'], "content.php")){
        unset( $_SESSION['timer'] );
        unset( $_SESSION['content_id'] );
    }
    
    ///////
    // FORUM
    // Reply to thread
     if(strpos($_SERVER['PHP_SELF'], "forum")){
        if(strpos($_SERVER['PHP_SELF'], "forum/new_thread.php") && isset($_POST['body']) && isset($_POST['subject']) && (isset($_POST['parent_id']) && $_POST['parent_id'] > 0)){     
            $gamification->executeEvent('forum_reply', 
                    array('user_id'=>$_SESSION['member_id'], 
                    'email'=>$gm_member['email'],
                    'alias'=>'forum_reply_badge',
                    'firstname'=>$gm_member['first_name'],
                    'contact_email'=>$_config['contact_email'],
                    'badges'=>$gamification->getUserBadges()));
    // Post new thread
        }else if(strpos($_SERVER['PHP_SELF'], "forum/new_thread.php") && isset($_POST['body']) && isset($_POST['subject'])){     
            $gamification->executeEvent('forum_post', 
                    array('user_id'=>$_SESSION['member_id'], 
                    'email'=>$gm_member['email'],
                    'alias'=>'forum_post_badge',
                    'firstname'=>$gm_member['first_name'],
                    'contact_email'=>$_config['contact_email'],
                    'badges'=>$gamification->getUserBadges()));
    // View forum post
        }else  if(strpos($_SERVER['PHP_SELF'], "forum/view.php") && isset($_REQUEST['fid'])){
            if($_SESSION['forum_view'] != $_REQUEST['fid']){
                $_SESSION['forum_view'] = $_REQUEST['fid'];
                $gamification->executeEvent('forum_view', 
                    array('user_id'=>$_SESSION['member_id'], 
                    'email'=>$gm_member['email'],
                    'alias'=>'forum_view_badge',
                    'firstname'=>$gm_member['first_name'],
                    'contact_email'=>$_config['contact_email'],
                    'badges'=>$gamification->getUserBadges()));
            }
        }
        
        }
    if(!strpos($_SERVER['PHP_SELF'], "forum/view.php") && !strpos($_SERVER['PHP_SELF'], 'get_profile_img.php') && !strpos($_SERVER['PHP_SELF'], 'index.php')){
           unset( $_SESSION['forum_view'] );
    }
    
    ////////
    // BLOG
    // Blog posts and comments
    if(strpos($_SERVER['PHP_SELF'], "blogs/add_post.php") && isset($_POST['body']) && isset($_POST['title'])){
            $gamification->executeEvent('blog_add', 
                    array('user_id'=>$_SESSION['member_id'], 
                    'email'=>$gm_member['email'],
                    'alias'=>'blog_add_badge',
                    'firstname'=>$gm_member['first_name'],
                    'contact_email'=>$_config['contact_email'],
                    'badges'=>$gamification->getUserBadges()));
    // Comment on a blog post        
    } else if(strpos($_SERVER['PHP_SELF'], "blogs/post.php") && isset($_POST['body']) && isset($_POST['id'])){
    //} else if(strpos($_SERVER['PHP_SELF'], "blogs/post.php") && isset($_POST['ot']) && isset($_POST['oid']) && isset($_POST['id'])){
        $gamification->executeEvent('blog_comment', 
                    array('user_id'=>$_SESSION['member_id'], 
                    'email'=>$gm_member['email'],
                    'alias'=>'blog_comment_badge',
                    'firstname'=>$gm_member['first_name'],
                    'contact_email'=>$_config['contact_email'],
                    'badges'=>$gamification->getUserBadges()));

    // View a single blog posts
    } else if(strpos($_SERVER['PHP_SELF'], "blogs/post.php") && isset($_REQUEST['oid']) && isset($_REQUEST['id'])){
        $gamification->executeEvent('blog_post_view');
    // View the full blog postings
    } else if(strpos($_SERVER['PHP_SELF'], "blogs/view.php") && isset($_REQUEST['oid']) && isset($_REQUEST['ot'])){
        // Prevent points by reloading
        if($_SESSION['blog_id'] != $_REQUEST['oid']){
            $_SESSION['blog_id'] = $_REQUEST['oid'];
            $gamification->executeEvent('blog_view');
        }
    }
    if(!strpos($_SERVER['PHP_SELF'], "blogs/view.php")){
        unset( $_SESSION['blog_id'] );
    }
    
    ////////
    // CHAT
    // Login to the chat
    if(strpos($_SERVER['PHP_SELF'], "chat/chat.php") && isset($_REQUEST['firstLoginFlag'])){
        $gamification->executeEvent('chat_login', 
                    array('user_id'=>$_SESSION['member_id'], 
                    'email'=>$gm_member['email'],
                    'alias'=>'chat_login_badge',
                    'firstname'=>$gm_member['first_name'],
                    'contact_email'=>$_config['contact_email'],
                    'badges'=>$gamification->getUserBadges()));
    //Post to chat
    } else if(isset($_REQUEST['tempField'])){
        $gamification->executeEvent('chat_post', 
                    array('user_id'=>$_SESSION['member_id'], 
                    'email'=>$gm_member['email'],
                    'alias'=>'chat_post_badge',
                    'firstname'=>$gm_member['first_name'],
                    'contact_email'=>$_config['contact_email'],
                    'badges'=>$gamification->getUserBadges()));
    }
    
    /////////
    // LINKS
    // Add link
   if(strpos($_SERVER['PHP_SELF'], "links/add.php") && isset($_POST['title']) && isset($_POST['cat']) && isset($_POST['url']) && isset($_POST['description'])){
        $gamification->executeEvent('link_add', 
                    array('user_id'=>$_SESSION['member_id'], 
                    'email'=>$gm_member['email'],
                    'alias'=>'link_add_badge',
                    'firstname'=>$gm_member['first_name'],
                    'contact_email'=>$_config['contact_email'],
                    'badges'=>$gamification->getUserBadges()));
    // View Link
    } else if(strpos($_SERVER['PHP_SELF'], "links/index.php") && isset($_REQUEST['view'])){
        $gamification->executeEvent('link_view', 
                    array('user_id'=>$_SESSION['member_id'], 
                    'email'=>$gm_member['email'],
                    'alias'=>'link_view_badge',
                    'firstname'=>$gm_member['first_name'],
                    'contact_email'=>$_config['contact_email'],
                    'badges'=>$gamification->getUserBadges()));
    }
    
    ////////
    // POLL
    // Post to a poll
   if(isset($_POST['poll_id']) && isset($_POST['choice'])){
        $gamification->executeEvent('poll_post');
    }    
    
    /////////
    // PHOTO GALLERY
    // Create Album
   if(strpos($_SERVER['PHP_SELF'], "photos/create_album.php") && isset($_POST['album_name']) && isset($_POST['album_type'])){
        $gamification->executeEvent('photo_create_album', 
                    array('user_id'=>$_SESSION['member_id'], 
                    'email'=>$gm_member['email'],
                    'alias'=> 'photo_create_album_badge',
                    'firstname'=>$gm_member['first_name'],
                    'contact_email'=>$_config['contact_email'],
                    'badges'=>$gamification->getUserBadges()));
    } 
    // Create multiple albums
    if(strpos($_SERVER['PHP_SELF'], "photos/create_album.php") && isset($_POST['album_name']) && isset($_POST['album_type'])){
        $gamification->executeEvent('photo_create_albums', 
                    array('user_id'=>$_SESSION['member_id'], 
                    'email'=>$gm_member['email'],
                    'alias'=> 'photo_create_albums_badge',
                    'firstname'=>$gm_member['first_name'],
                    'contact_email'=>$_config['contact_email'],
                    'badges'=>$gamification->getUserBadges()));
    } 
    // Upload photo, does not consider multiple files can be uploaded at a time. 
    // Should link into the ajax doing the uploading
    if(strpos($_SERVER['PHP_SELF'], "photos/albums.php") && isset($_POST['upload'])){
        $gamification->executeEvent('photo_upload', 
                    array('user_id'=>$_SESSION['member_id'], 
                    'email'=>$gm_member['email'],
                    'alias'=>'photo_upload_badge',
                    'firstname'=>$gm_member['first_name'],
                    'contact_email'=>$_config['contact_email'],
                    'badges'=>$gamification->getUserBadges()));
    // Comment on Album
    } else if(strpos($_SERVER['HTTP_REFERER'], "photos/albums.php") && strpos($_SERVER['PHP_SELF'], "photos/addComment.php") && isset($_REQUEST['comment'])){
        $gamification->executeEvent('photo_album_comment', 
                    array('user_id'=>$_SESSION['member_id'], 
                    'email'=>$gm_member['email'],
                    'alias'=>'photo_album_comment_badge',
                    'firstname'=>$gm_member['first_name'],
                    'contact_email'=>$_config['contact_email'],
                    'badges'=>$gamification->getUserBadges()));
    // View Album
    }else if(strpos($_SERVER['PHP_SELF'], "photos/albums.php") && isset($_REQUEST['id'])){
        // This does not work as expected. unset() below kills the album_viewed session var,
        // even though it does not seem to reach the else condition below, while remaining on albums.php.
        // For now grant points for viewing an album only once per session.
        if($_SESSION['album_viewed'] != $_REQUEST['id']){
                $_SESSION['album_viewed'] =  $_REQUEST['id'];
                $gamification->executeEvent('photo_view_album');
        }
    } 
    // Prevent reloading album for points
    if(!isset($_REQUEST['id'])){
        // why does this unset run when album.php is the active page
        //unset($_SESSION['album_viewed']);
    }
    // Comment on Photo
    if(strpos($_SERVER['HTTP_REFERER'], "photos/photo.php") && strpos($_SERVER['PHP_SELF'], "photos/addComment.php") && isset($_REQUEST['comment'])){
        $gamification->executeEvent('photo_comment', 
                    array('user_id'=>$_SESSION['member_id'], 
                    'email'=>$gm_member['email'],
                    'alias'=>'photo_comment_badge',
                    'firstname'=>$gm_member['first_name'],
                    'contact_email'=>$_config['contact_email'],
                    'badges'=>$gamification->getUserBadges()));
    } 
     // View Photo
    if(strpos($_SERVER['PHP_SELF'], "photos/photo.php") && isset($_REQUEST['pid']) && isset($_REQUEST['aid'])){
        if($_SESSION['view_photo'] != $_REQUEST['pid']){
            $_SESSION['view_photo'] = $_REQUEST['pid'];
            $gamification->executeEvent('photo_view_photo');
        }
    }
    // Prevent photo reloading for points
    if(!isset($_REQUEST['pid'])){
        unset($_SESSION['view_photo']);
    }
    // Edit photo description and alt text
    if(strpos($_SERVER['PHP_SELF'], "photos/edit_photos.php") && !empty($_POST)){
        foreach($_POST as $key => $value){
            // For each new description, give points
            if(strstr($key, 'description')){
               $this_description = explode("_",$key);
                if(strlen($value) >9){
                    // Check for existing description, points only for new
                    $sql = "SELECT description from %spa_photos WHERE id = %d";
                    $desc_exists = queryDB($sql, array(TABLE_PREFIX, $this_description[1]), TRUE);
                    if($desc_exists['description'] == ''){
                        $gamification->executeEvent('photo_description', 
                            array('user_id'=>$_SESSION['member_id'], 
                            'email'=>$gm_member['email'],
                            'alias'=>'photo_description_badge',
                            'firstname'=>$gm_member['first_name'],
                            'contact_email'=>$_config['contact_email'],
                            'badges'=>$gamification->getUserBadges()));
                    }
                }
            }
            // For each new alt text, give points
            if(strstr($key, 'alt_text')){
                $this_alt = explode("_",$key);
                if(strlen($value) >9){
                     // Check for existing alt text, points only for new
                    $sql = "SELECT alt_text from %spa_photos WHERE id = %d";
                    $alt_exists = queryDB($sql, array(TABLE_PREFIX, $this_alt[1]), TRUE);
                    if($alt_exists['alt_text'] == ''){
                        $gamification->executeEvent('photo_alt_text', 
                            array('user_id'=>$_SESSION['member_id'], 
                            'email'=>$gm_member['email'],
                            'alias'=>'photo_alt_text_badge',
                            'firstname'=>$gm_member['first_name'],
                            'contact_email'=>$_config['contact_email'],
                            'badges'=>$gamification->getUserBadges()));
                    }
                }        
            }
        }
    }
    
    ////////
    // PROFILE
    // View other's profile
    if(strpos($_SERVER['PHP_SELF'], "profile.php") && !empty($_REQUEST['id'])){
        if($_REQUEST['id'] != $_SESSION['member_id']){
            if($_SESSION['view_profile'] != $_REQUEST['id']){
                $_SESSION['view_profile'] = $_REQUEST['id'];
                $gamification->executeEvent('profile_view', 
                    array('user_id'=>$_SESSION['member_id'], 
                    'email'=>$gm_member['email'],
                    'alias'=> 'profile_view_badge',
                    'firstname'=>$gm_member['first_name'],
                    'contact_email'=>$_config['contact_email'],
                    'badges'=>$gamification->getUserBadges()));
            }
        }
    }
    // Other's view your profile
    if(strpos($_SERVER['PHP_SELF'], "profile.php") && !empty($_REQUEST['id'])){
        if($_REQUEST['id'] != $_SESSION['member_id']){
            if($_SESSION['profile_viewed'] != $_REQUEST['id']){
                $_SESSION['profile_viewed'] = $_REQUEST['id'];
                $sql = "SELECT email, first_name FROM %smembers WHERE member_id =%d";
                $gm_member = queryDB($sql, array(TABLE_PREFIX, $_REQUEST['id']), TRUE);
                // temporarily set user id to that of the profile being viewed
                $gamification->setUserId($_REQUEST['id']);
                $gamification->executeEvent('profile_viewed' ,                    
                    array('user_id'=>$_REQUEST['id'], 
                    'email'=>$gm_member['email'],
                    'alias'=> 'profile_viewed_badge',
                    'firstname'=>$gm_member['first_name'],
                    'contact_email'=>$_config['contact_email'],
                    'badges'=>$gamification->getUserBadges()));
                // Switch back to the session's owner
                $gamification->setUserId($_SESSION['member_id']);
            }
        }
    }
    // Upload a profile picture
     if(!strpos($_SERVER['PHP_SELF'], "profile.php") && !empty($_POST['id']) && !empty($_POST['upload'])){
        $gamification->executeEvent('profile_pic_upload', array(
                    'user_id'=>$_SESSION['member_id'], 
                    'alias'=> 'profile_pic_upload_badge',
                    'email'=>$gm_member['email'],
                    'firstname'=>$gm_member['first_name'],
                    'contact_email'=>$_config['contact_email'],
                    'badges'=>$gamification->getUserBadges()));
     }
    
    // Prevent profile reloading for points
    if(!strpos($_SERVER['PHP_SELF'], "profile_pictures/profile_picture.php") && empty($_REQUEST['id'])){

    }
    
    //////////
    // INBOX
    // private message someone
    if(strpos($_SERVER['PHP_SELF'], "inbox/send_message.php") && !empty($_POST['to']) && !empty($_POST['subject']) && !empty($_POST['message'])){
        $gamification->executeEvent('sent_message');
    }

    //////////
    // READING LIST
    // view reading list details
    if(strpos($_SERVER['PHP_SELF'], "reading_list/display_resource.php") && !empty($_REQUEST['id'])){
        if($_SESSION['read_list_view'] != $_REQUEST['id']){
            $_SESSION['read_list_view'] = $_REQUEST['id'];
            $gamification->executeEvent('read_list_view');
        }
    }
    // Leave this set TRUE to prevent back/forth to generate points
    // Must move between two reading list items to get points again for 
    // one viewed already this session
    //if(!strpos($_SERVER['PHP_SELF'], "reading_list/display_resource.php")){
    //     unset($_SESSION['read_list_view']);
    // }
    
    ///////////
    // PERSONAL PREFERENCES
     if(strpos($_SERVER['PHP_SELF'], "pref_wizard/index.php") && !empty($_POST['done'])){
        $gamification->executeEvent('prefs_update', array(
                    'user_id'=>$_SESSION['member_id'], 
                    'alias'=> 'prefs_update_badge',
                    'email'=>$gm_member['email'],
                    'firstname'=>$gm_member['first_name'],
                    'contact_email'=>$_config['contact_email'],
                    'badges'=>$gamification->getUserBadges())); 
     }
     
     /////////
     // FILE STORAGE
     // Create folder
     if(strpos($_SERVER['PHP_SELF'], "file_storage/index.php") && !empty($_POST['new_folder_name'])){
        $gamification->executeEvent('new_folder', array(
                    'user_id'=>$_SESSION['member_id'], 
                    'alias'=> 'new_folder_badge',
                    'email'=>$gm_member['email'],
                    'firstname'=>$gm_member['first_name'],
                    'contact_email'=>$_config['contact_email'],
                    'badges'=>$gamification->getUserBadges()));
     }
    // Upload file
     if(strpos($_SERVER['PHP_SELF'], "file_storage/index.php") && !empty($_POST['upload'])){
        $gamification->executeEvent('upload_file', array(
                    'user_id'=>$_SESSION['member_id'], 
                    'alias'=> 'upload_file_badge',
                    'email'=>$gm_member['email'],
                    'firstname'=>$gm_member['first_name'],
                    'contact_email'=>$_config['contact_email'],
                    'badges'=>$gamification->getUserBadges())); 
     }
    // Create new file
    if(strpos($_SERVER['PHP_SELF'], "file_storage/new.php") && !empty($_POST['body'])){
        $gamification->executeEvent('create_file', array(
                    'user_id'=>$_SESSION['member_id'], 
                    'alias'=> 'create_file_badge',
                    'email'=>$gm_member['email'],
                    'firstname'=>$gm_member['first_name'],
                    'contact_email'=>$_config['contact_email'],
                    'badges'=>$gamification->getUserBadges())); 
    }
    // Download file NOT WORKING, NO PAGE RELOAD TO TRIGGER
    //if(strpos($_SERVER['PHP_SELF'], "file_storage/index.php") && !empty($_POST['download'])){
    //   $gamification->executeEvent('download_file'); 
    //}
    // File description
    if((strpos($_SERVER['PHP_SELF'], "file_storage/edit.php") || strpos($_SERVER['PHP_SELF'], "file_storage/new.php")) && !empty($_POST['description'])){
        $gamification->executeEvent('file_description'); 
    }   
    // Version comments
    if((strpos($_SERVER['PHP_SELF'], "file_storage/edit.php") || strpos($_SERVER['PHP_SELF'], "file_storage/new.php")) && !empty($_POST['comment'])){
        $gamification->executeEvent('file_description'); 
    }  
    
    ///////////
    // TESTS & SURVEYS
    //
    if((strpos($_SERVER['PHP_SELF'], "tests/take_test.php")) && !empty($_POST['submit']) && !empty($_POST['tid'])){
        $gamification->executeEvent('submit_test'); 
    } 
}
?>