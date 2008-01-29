<?php
/*==============================================================
  Photo Album
 ==============================================================
  Copyright (c) 2006 by Dylan Cheon & Kelvin Wong
  Institute for Assistive Technology / University of Victoria
  http://www.canassist.ca/                                    
                                                               
  This program is free software. You can redistribute it and/or
  modify it under the terms of the GNU General Public License  
  as published by the Free Software Foundation.                
 ==============================================================
 */
// $Id:

/**
 * @desc	This file defines all the functions that access the database.
 * @author	Dylan Cheon
 * @copyright	2006, Institute for Assistive Technology, University of Victoria 
 * @link	http://www.canassist.ca/                                    
 * @license GNU
 */ 
 
/** 
 * @desc	This function returns all the image rows for the course id
 * @param	int		$course_id	course id
 * @return	Array	array which contains all the image rows (mysql source)
 */
 function get_all_images($course_id){
   global $db;
   $table=get_table_name(IMAGE);
   $query="SELECT * FROM ".$table." WHERE course_id=".$course_id;
   $result=mysql_query($query, $db);
   return $result;
}
	
/**
 * @desc	This function returns the image array which contains the image information
 * @param	int		$course_id		course_id
 * @param	int		$status			image status
 * @param	int		$start_page		start page
 * @param	int		$display_limit	how many images should be displayed in the page
 * @param	boolean	$my_pic			if this value is true, it is set for my photo mode. Returns images for one user if enabled
 * @param	String	$login			login name 
 * @return	Array					the array which contains the data from database
 */ 
function get_image_array($state, $course_id, $image_status, $start_page, $display_limit, $login=''){
	 global $db;
	 $table=get_table_name(IMAGE);	
	 $start=($start_page-1)*$display_limit;	 
	 $query;	$i=0;	$array;
	 switch ($state){
		 case MY_PIC:
		 	//$query="SELECT * FROM ".$table." WHERE course_id=".$course_id."  AND status=".$image_status." AND login='{$login}' ORDER BY image_id DESC LIMIT ".$start.", ".$display_limit;
		// changes sorting to order from image_id
		$query="SELECT * FROM ".$table." WHERE course_id=".$course_id."  AND status=".$image_status." AND login='{$login}' ORDER BY 'order' ASC LIMIT ".$start.", ".$display_limit;
		 break;
		default:
		 	//$query="SELECT * FROM ".$table." WHERE course_id=".$course_id."  AND status=".$image_status." ORDER BY image_id DESC LIMIT ".$start.", ".$display_limit;
		// changes sorting to order from image_id
	 	$query="SELECT * FROM ".$table." WHERE course_id=".$course_id."  AND status=".$image_status." ORDER BY 'order' ASC LIMIT ".$start.", ".$display_limit;
	 	break;
	 }
	
	 $result=mysql_query($query, $db);
	 while ($row=mysql_fetch_array($result)){
		$array[$i]['title']=$row['title'];
		$array[$i]['description']=$row['description'];		
		$array[$i]['location']=$row['location'];
		$array[$i]['date']=$row['date'];
		$array[$i]['view_image_name']=$row['view_image_name'];
		$array[$i]['course_id']=$row['course_id'];
		$array[$i]['login']=$row['login'];
		$array[$i]['image_id']=$row['image_id'];
		$array[$i]['thumb_image_name']=$row['thumb_image_name'];
		$array[$i]['alt']=$row['alt'];
		$array[$i]['status']=$row['status'];	
		// gg added image order to array	
		$array[$i]['order']=$row['order'];				
		$array[$i]['link']=BASE_PATH.'view.php?image_id='.$row['image_id'];
		$i++;
 	 }
	 return $array;
}	
	

/**
 * @desc	This function returns the comment array which contains the comment information
 * @param	int		$course_id		course id
 * @param	int		$status			comment status
 * @param	int		$image_id		image id
 * @param	boolean	$index_admin	if this is true, then it returns the comments for admin/instructor view page
 * @param	boolean	$admin_request	if this is true, then it returns the comments for admin/instructor panel 
 * @param	int		$display_limit	number of comments to be returned
 * @param	int		$start			start page
 * @param	boolean	$my_comment		my comment mode is enabled or not. Returns comments for one user if enabled
 * @return	Array	the array which contains the comment data from the database
 */ 
function get_comment_array($state, $course_id, $comment_status, $image_id=NOT_SET, $display_limit=NOT_SET, $start=NOT_SET){
  global $db;
  $table=get_table_name(COMMENT);
  $start=($start-1)*$display_limit;	 
  $query;	$i=0;	$array;

  switch ($state){
	  case ADMIN_VIEW:
  		$query="SELECT * FROM ".$table." WHERE course_id=".$course_id." AND image_id=".$image_id." ORDER BY date DESC";
  	 break;
  	 case ADMIN_PANEL:
  		$query="SELECT * FROM ".$table." WHERE course_id=".$course_id."  AND status=".$comment_status." ORDER BY date DESC LIMIT ".$start.", ".$display_limit;
  	break;
  	case MY_COMMENT:
	    $query="SELECT * FROM ".$table." WHERE course_id=".$course_id." AND status=".$comment_status." AND login='{$_SESSION['login']}' ORDER BY date DESC LIMIT ".$start.", ".$display_limit;
	break;
	case STUDENT:
    	$query="SELECT * FROM ".$table." WHERE course_id=".$course_id."  AND status=".$comment_status." AND image_id=".$image_id." ORDER BY date DESC";
    break;
  }
  
  $result=mysql_query($query, $db);
  while ($row=mysql_fetch_array($result)){
	$array[$i]['date']=$row['date'];
	$array[$i]['course_id']=$row['course_id'];
	$array[$i]['login']=$row['login'];
	$array[$i]['image_id']=$row['image_id'];
	$array[$i]['comment_id']=$row['comment_id'];
	$array[$i]['comment']=$row['comment'];
	$array[$i]['status']=$row['status'];
	$i++;
  }
  return $array;
}	
	
	
	
/**
 * @desc	This function stores the given image input data to the database
 * @param	int		$course_id			course id
 * @param	String	$login				login name
 * @param	String	$title				title string
 * @param	String	$description		image description
 * @param	String	$view_image_name	view image file name
 * @param	String	$location			location string
 * @param	String	$thumb_image_name	thumb image file name
 * @param	String	$alt				alt string
 * @param	int		$status				image status
 * @return	boolean						returns true if the given input data is stored in the database successfully
 */
function store_image_in_database($course_id, $login, $title, $description, $view_image_name, $location, $thumb_image_name, $alt, $status){
	global $db;
	$table=get_table_name(IMAGE);
	$success=false;
	$query="INSERT INTO ".$table." SET course_id='{$course_id}', title='{$title}', description='{$description}', view_image_name='{$view_image_name}', location='{$location}', date=NOW(), login='{$login}', thumb_image_name='{$thumb_image_name}', alt='{$alt}', status='{$status}'";		
	$result=mysql_query($query, $db);
	$count=mysql_affected_rows();
	if (($count==1) && (!mysql_error())){
		$success=true;
	}
	return $success;
}		


/**
 * @desc	This function stores the given comment input data to the database
 * @param	int		$course_id		course id
 * @param	String	$login			login name
 * @param	String	$comment		user comment
 * @param	int		$image_id		image id
 * @param	int		$status			comment status
 * @return	boolean					returns true if the given input data is stored in the database successfully
 */	
function store_comment_in_database($course_id, $login, $comment, $image_id, $status){
	global $db;
	$table=get_table_name(COMMENT);
	$success=false;
	$query="INSERT INTO ".$table." SET course_id='{$course_id}', comment='{$comment}', date=NOW(), login='{$login}', status='{$status}', image_id='{$image_id}'";				
	$result=mysql_query($query, $db);
	$count=mysql_affected_rows();
	if ($count==1){ //should affect just one row
		$success=true;
	}
	return $success;
}		


/**
 * @desc	This function updates the image data in the database
 * @param	int		$course_id			course id
 * @param	String	$title				title string
 * @param	String	$description		description
 * @param	String	$view_image_name	file name of the full size image 
 * @param	String	$image_id			image id
 * @param	String	$thumb_image_name	file name of the thumbnail image 
 * @param	String	$alt				alt string
 * @param	int		$status				image status
 * @return	boolean						true if update finished successfully
 */
function update_image_in_database($course_id, $title, $description, $view_image_name, $image_id, $thumb_image_name, $alt, $status){
	global $db;
	$table=get_table_name(IMAGE);
	$query="UPDATE ".$table." SET title='{$title}', description='{$description}', thumb_image_name='{$thumb_image_name}', view_image_name='{$view_image_name}', date=NOW(), alt='{$alt}', status='{$status}' WHERE course_id='{$course_id}' AND image_id='{$image_id}'";	
	$result=mysql_query($query, $db);
	$count=mysql_affected_rows();
	if ($count==1){	//should affect just one row
		return true;
	} else {
		return false;
	}				
}

/**
 * @desc	This function updates the comment data in the database
 * @param	int		$course_id		course id
 * @param	String	$comment		comment string
 * @param	int		$image_id		image id
 * @param	int		$comment_id		comment id
 * @param	int		$status			comment status
 * @return	boolean					true if update finished successfully
 */	
function update_comment_in_database($course_id, $comment, $image_id, $comment_id,$status){
	global $db;
	$table=get_table_name(COMMENT);
	$query="UPDATE ".$table." SET comment='{$comment}', status='{$status}', date=NOW() WHERE course_id='{$course_id}' AND image_id='{$image_id}' AND comment_id='{$comment_id}'";	
	$result=mysql_query($query, $db);
	$count=mysql_affected_rows();
	if ($count==1){	//should affect just one row
		return true;
	} else {
		return false;
	}				
}
	
	
/**
 * @desc	This function returns the complete data row from the database
 * @param	int		$choose			choose to search either IMAGE and COMMENT data	
 * @param	int		$image_id		image id
 * @param	int		$course_id		course id
 * @param	int		$comment_id		comment id
 * @return	Array					the complete data row array
 */
function get_single_data($choose, $image_id, $course_id, $comment_id=NOT_SET){
	global $db;	$query;
	$table=get_table_name($choose);
	switch ($choose){
		case IMAGE:
			$query="SELECT * FROM ".$table." WHERE course_id=".$course_id." AND image_id=".$image_id;
		break;
		case COMMENT:
			$query="SELECT * FROM ".$table." WHERE course_id=".$course_id." AND image_id=".$image_id." AND comment_id=".$comment_id;
		break;
	}
	$result=mysql_query($query, $db);
	$result=mysql_fetch_array($result);
	return $result;
}
	

/**
 * @desc	This function deletes the image.  Images and blogs which have the same course id and image id should also be deleted 
 * @param	int		$image_id	image id
 * @param	int		$course_id	course id
 * @return	boolean				returns true if deletion is successful
 */
function delete_image($image_id, $course_id){
	global $db;
	$image_table=get_table_name(IMAGE);
	$blog_table=get_table_name(COMMENT);
	$delete_image_query="DELETE FROM ".$image_table." WHERE course_id=".$course_id." AND image_id=".$image_id;
	$image_query="SELECT view_image_name, location, thumb_image_name FROM ".$image_table." WHERE course_id=".$course_id." AND image_id=".$image_id;
	$blog_query="DELETE FROM ".$blog_table." WHERE course_id=".$course_id." AND image_id=".$image_id;	
	
	$image_array=mysql_query($image_query, $db);	
	delete_image_files($image_array);		//delete the physical image file
	mysql_query($blog_query, $db);			//deletes comments
	mysql_query($delete_image_query, $db);	//deletes the images from database
	$count=mysql_affected_rows();
	if ($count ==1){	//should be one image
		return true;
	} else {
		return false;
	}
}
		
	
	
/**
 * @desc	This function deletes the given blog comment 
 * @param	int		$image_id		image id
 * @param	int		$course_id		course id
 * @param	int		$comment_id		comment id
 * @return	boolean					returns true if deletion is completed.  
 */
function delete_blog($image_id, $course_id, $comment_id){
	global $db;
	$table=get_table_name(COMMENT);
	$query="DELETE FROM ".$table." WHERE course_id=".$course_id." AND image_id=".$image_id." AND comment_id=".$comment_id;
	mysql_query($query, $db);
	$count=mysql_affected_rows();
	if ($count==1){	//should be only one comment
		return true;
	} else {
		return false;
	}
}
	
	
	
/**
 * @desc	This function returns the table name from the database based on a switch variable
 * @param	int		$choose 	choose either IMAGE, COMMENT or CONFIG
 * @return	String				table name as a string
 */
function get_table_name($choose){
	$table_name;
	switch ($choose){
		case IMAGE:
			$table_name=TABLE_PREFIX.'pa_image';
		break;
		case COMMENT:
			$table_name=TABLE_PREFIX.'pa_comment';
		break;
		case CONFIG:
			$table_name=TABLE_PREFIX.'pa_config';
		break;
	}
	return $table_name;
}
	
	
	
/**
 * @desc	This function returns the total number of course images
 * @param	int			$course_id		course id
 * @param	int			$status			image status
 * @param	boolean		$mypic			mypic mode enabled or not. Returns number for one user if enabled
 * @return	int							the total number of images
 */
function get_total_image_number($state, $course_id, $status){
	global $db;
	$table=get_table_name(IMAGE);
	switch ($state){
		case MY_PIC:
			$query="SELECT image_id FROM ".$table." WHERE course_id=".$course_id." AND status=".$status." AND login='{$_SESSION['login']}'";
		break;
		default:
			$query="SELECT image_id FROM ".$table." WHERE course_id=".$course_id." AND status=".$status;
		break;
	}
	
	$result=mysql_query($query, $db);
	$count=mysql_affected_rows();
	return $count;
}

/**
 * @desc	This function returns the total number of comments
 * @param	int		$course_id		course id
 * @param	int		$status			comment status
 * @param	boolean	$my_comment		mycomment mode is enabled or not. Returns number for one user if enabled
 * @param	int		$image_id		image id
 * @return	int						the total number of comments
 */	
function get_total_comment_number($state, $course_id, $status, $image_id=NOT_SET){
	global $db;
	$table=get_table_name(COMMENT);
	switch ($state){
		case MY_COMMENT:
			$login=$_SESSION['login'];
			$query="SELECT comment_id FROM ".$table." WHERE course_id=".$course_id." AND status=".$status." AND login='{$login}'"; 
		break;
		case ADMIN_PANEL:
			$query="SELECT comment_id FROM ".$table." WHERE course_id=".$course_id." AND status=".$status;
		break;
		default:	
		  $query="SELECT comment_id FROM ".$table." WHERE course_id=".$course_id." AND image_id=".$image_id." AND status=".$status;
		break;
	}
	$result=mysql_query($query, $db);
	$count=mysql_affected_rows();
	return $count;
}

	
	
/**
 * @desc	This function returns the last page number to be used for the page table 
 * @param	int		$display_limit		how many should be displayed in the page
 * @param	int		$total				total number
 * @return	int							last page number
 */
function get_last_page($display_limit, $total){
	$total=doubleval($total);
	$last_page;	
	if ($total < $display_limit){
		$last_page=FIRST_PAGE;
	} else {
		$last_page=ceil($total/$display_limit);
		$last_page=intval($last_page);
	} 		
	return $last_page;
}
	
	
/**
 * @desc	This function returns the full name of the member.  The return string syntax is => (First_name)? First_name.Last_name_initial : Anonymous
 * @param	String	$login	login name string
 * @return	String			the full name of the member
 */
function get_member_name($login){
	global $db;
	$query="SELECT last_name, first_name FROM ".TABLE_PREFIX."members WHERE login='{$login}'";
	$result=mysql_query($query, $db);
	$count=mysql_affected_rows();
	if ($count == 1) {	//in this case, the login is instructor or student
		$result=mysql_fetch_array($result);
		if (empty($result['first_name'])){
			$name=_AT('anonymous');
		} else if (empty($result['last_name'])){
			$name=$result['first_name'];
		} else {
			$temp=substr($result['last_name'], 0, 1);
			$name=$result['first_name'].'.'.$temp;
		}
		return $name;
	}  else if ($count==0){	//found nobody, check if the user is administrator
		$query="SELECT login FROM ".TABLE_PREFIX."admins WHERE login='{$login}'";
		$result=mysql_query($query, $db);
		$count=mysql_affected_rows();
		if ($count==1){	//admin is detected
		  return _AT('pa_tag_administrator');
		}
	}
}
	
	
	
/**
 * @desc	This pagination function returns an array which has the start and end information to be used 
 * @param	int		$display_limit	maximum number of images displayed in the page
 * @param	int		$page_limit		maximum number of pages displayed in the page
 * @param	int		$course_id		course id
 * @param	int		$current		current page 
 * @param	int		$last_page		last_page number
 * @return	Array					array contains the start and end information for page table
 */
function get_page_array($display_limit, $page_limit, $current, $last_page){
	$start=1;	
	if ($last_page <= ($start+$page_limit-1)){     //initialize the end variable if the last page is less than start+display-1
		$end=$last_page;
	} else {
		$end=$start+$page_limit-1;
	}
	
	$process=true;
	while ($process==true){
		if (($current >= $start) && ($current <= $end)){
			$array['start']=$start;
			$array['end']=$end;
			$process=false;
		} else {
			$start=$end+1;
			$end=$start+$page_limit-1;
			if ($end > $last_page){
				$end=$last_page;
			}
		}
	}
	$array['last_page']=$last_page;
	$array['previous']=$current-1;
	$array['next']=$current+1;
	$array['current']=$current;
	return $array;
}

		
/**
 * @desc	This function checks if the image exists in the database
 * @param	int	$image_id	image id
 * @param	int	$course_id	course id
 * @return	boolean			it returns true if the image exists in database.  Otherwise, it returns false
 */
function image_exist($image_id, $course_id){
	global $db;
	$table_name=get_table_name(IMAGE);
	$query="SELECT * FROM ".$table_name." WHERE course_id=".$course_id." AND image_id=".$image_id;
	$result=mysql_query($query, $db);
	$count=mysql_affected_rows();
	if ($count == 1){	// there should be only 1 image
		return true;
	} else {
		return false;
	}
}	


/**
 * @desc	This function checks if the comment exists in the database
 * @param	int	$comment_id		comment id
 * @param	int	$course_id		course id
 * @return	boolean				returns true if the comment exists in database.  Otherwise, it returns false
 */
function comment_exist($comment_id, $course_id){
	global $db;
	$table_name=get_table_name(COMMENT);
	$query="SELECT * FROM ".$table_name." WHERE course_id=".$course_id." AND comment_id=".$comment_id;
	$result=mysql_query($query, $db);
	$count=mysql_affected_rows();
	if ($count == 1){	//there should be only 1 comment
		return true;
	} else {
		return false;
	}  
}	


/**
 * @desc	This function returns an array which has a list of the courses
 * @return	Array	array which contains title and course_id
 */
function get_course_list(){
	global $db;
	$table=TABLE_PREFIX.'courses';
	$query="SELECT course_id, title FROM ".$table." ORDER BY created_date";
	$result=mysql_query($query, $db);
	$i=0;
	while($row=mysql_fetch_array($result)){
		$array[$i]['title']=$row['title'];
		$array[$i]['id']=$row['course_id'];
		$i++;
	}
	return $array;
}


/**
 * @desc	This function returns the course title 
 * @param	int		$course_id	course id
 * @return	String				course title	
 */
function get_course_title($course_id){
  global $db;
  $table=TABLE_PREFIX.'courses';
  $query="SELECT title FROM ".$table." WHERE course_id=".$course_id;
  $result=mysql_query($query, $db);
  $result=mysql_fetch_array($result);
  return $result['title'];	 
}


/**
 * @desc	This function checks if the course exists or not 
 * @param	int	$course_id	course id
 * @return	Boolean			true if exist
 */	
function course_exist($id){
  global $db;
  $table=TABLE_PREFIX.'courses';
  $query="SELECT course_id FROM ".$table." WHERE course_id='{$id}'";
  $result=mysql_query($query, $db);
  $count=mysql_affected_rows();
  if ($count==1){ // It should affect only one record
    return true;
  } else {
    return false;   
  }
}


/**
 * @desc	This function checks whether the given input belongs to the user
 * @param	int		$choose		IMAGE or COMMENT to choose
 * @param	int		$image_id	image id
 * @param	int		$course_id	course id
 * @param	int		$comment_id	comment_id
 * @return	Boolean				true if the input belongs to the user
 */	
function user_own($choose, $image_id, $course_id, $comment_id=NOT_SET){
	global $db;
	$bool=false;
	$table=get_table_name($choose);
	switch ($choose){
		case IMAGE:
			$query="SELECT login FROM ".$table." WHERE course_id=".$course_id." AND image_id=".$image_id;
		break;
		case COMMENT:
			$query="SELECT login FROM ".$table." WHERE course_id=".$course_id." AND image_id=".$image_id." AND comment_id=".$comment_id;
		break;
	}		
	$result=mysql_query($query, $db);
	$result=mysql_fetch_array($result);
	$count=mysql_affected_rows();
	if (is_admin_for_course()){
		$bool=true;
	} else if ($count!=1) {  //there should be only one owner 
		global $msg;
		$msg->addError('pa_func_user_own');
		redirect('index.php');
	} else {
		if ($result['login']==$_SESSION['login']){
			$bool=true;
		}
	} 
	return $bool;
}


/**
 * @desc	This function changes the image status
 * @param	int	$image_id		image id
 * @param	int	$course_id		course id
 * @param	int	$status			image status, APPROVED, DISAPPROVED or POSTED_NEW
 */	
function modify_image_status($image_id, $course_id, $status){
  If (($status==APPROVED || $status==DISAPPROVED || $status==POSTED_NEW) && image_exist($image_id, $course_id)){
  	global $db;
  	$table=get_table_name(IMAGE);	  
  	$query="UPDATE ".$table." SET status=".$status." WHERE image_id=".$image_id." AND course_id=".$course_id;
  	$result=mysql_query($query, $db);
  }
}


/**
 * @desc	This function changes the comment status
 * @param	int		$comment_id		comment id	
 * @param	int		$course_id		course id
 * @param	int		$status			comment status, APPROVED, DISAPPROVED or POSTED_NEW
 */	
function modify_comment_status($comment_id, $course_id, $status){
  if (($status==APPROVED || $status==DISAPPROVED || $status==POSTED_NEW) && comment_exist($comment_id, $course_id)){
  	global $db;
	$table=get_table_name(COMMENT);
	$query="UPDATE ".$table." SET status=".$status." WHERE course_id=".$course_id." AND comment_id=".$comment_id;
	$result=mysql_query($query, $db);     
  }
}


/**
 * @desc	This function returns the moderation status for the course. If unmoderated, the course adds user submissions immediately
 * @param	int	$course_id	course_id
 * @return	int				moderation status for the course, ENABLED or DISABLED
 */
function get_config_mode($course_id){
  global $db;
  $table=get_table_name(CONFIG);
  $query="SELECT status FROM ".$table." WHERE course_id=".$course_id;
  $result=mysql_query($query, $db);
  $count=mysql_affected_rows();
 
  if ($count==1){	//should be one configuration for the course
  	$result=mysql_fetch_array($result);
	return $result['status'];  
  } else {	//configuration does not exist, so make one
    $query="INSERT INTO ".$table." SET course_id=".$course_id.", status=".CONFIG_DISABLED.", date=NOW()";
    mysql_query($query, $db);
    return CONFIG_DISABLED;
  }
}


/**
 * @desc	This function modifies the moderation status
 * @param	int	$course_id	course id
 * @param	int	$status		moderation status
 */
function modify_config_mode($course_id, $status){
  global $db;
  $table=get_table_name(CONFIG);
  $query="UPDATE ".$table." SET status=".$status." WHERE course_id=".$course_id;
  mysql_query($query, $db);	  
}


/*
 * @desc	This function returns the maximum file size for the course
 * @param	int	$course_id	course id
 * @return	int 			max_file_size
 */
function get_max_file_size($course_id){
	global $db;
	$query="SELECT max_file_size FROM ".TABLE_PREFIX."courses WHERE course_id=".$course_id;
	$result=mysql_query($query, $db);
	$count=mysql_affected_rows();
	if ($count==1){
		$result=mysql_fetch_array($result);
		if ($result['max_file_size']>0){
			return $result['max_file_size'];
		} else {
			return NOT_SET;
		}
	} else {
		return NOT_SET;
	}
}


?>
