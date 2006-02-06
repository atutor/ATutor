<?php

if($_REQUEST['plog_sync'] == 1){
	//update the plog_users table with ATutor members

	$sql = "SELECT * from ".TABLE_PREFIX."members";
	$result = mysql_query($sql,$db);

	// But first check to see if the user already exists
	$sqlu = "SELECT user, password FROM ".PLOG_PREFIX."users";
	$resultu = mysql_query($sqlu,$db);
	$i = '';

	while($rowu = mysql_fetch_array($resultu)){
		$i++;
		$existing_users[$i]= $rowu[0];
		$existing_pwd[$i]=$rowu[1];
	}

	

	while ($row = mysql_fetch_array($result)){
		if(!in_array($row[1],$existing_users) || (in_array($row[1],$existing_users) && !in_array(md5($row[2]),$existing_pwd))){
			$sql2  = "REPLACE INTO ".PLOG_PREFIX."users VALUES ('$row[0]','$row[1]','".md5($row[2])."','$row[3]','$row[5]  $row[6]','','a:0:{}','1','0')";
			if(!$result5 = mysql_query($sql2)){	
				$msg->addError('PLOG_UPDATE_MEMBERS_FAILED');

			}else{	
				$msg->addFeedback('PLOG_UPDATE_MEMBERS_SAVED');
				$message = 1;
			}
		}
//debug(md5($row[2]));
 	}



//exit;
// The  synchronizing of admin accounts is a problem because they are not admin members
// and don't have a user ID. or a course they are associated to. The following code  was a first attempt
// at merging the admins into the plog_users table. 


/*
	$sql = "SELECT * from ".TABLE_PREFIX."admins";
	$result3 = mysql_query($sql);

	$sql5="SELECT user FROM ".PLOG_PREFIX."users";
 	$result5 = mysql_query($sql5,$db);
		
	$i='';
 	while($row5 = mysql_fetch_array($result5)){
			$i++;
 			$existing_admins[$i] = $row5[0];
		}
	//update the plog_users table with ATutor admins

	while ($row = mysql_fetch_array($result3)){

		$admin_pref = 's:0:"";';
		if(!in_array($row[0], $existing_admins)){
			$sql3  = "REPLACE INTO ".PLOG_PREFIX."users VALUES ( ";
		
			if($existing_admins != ''){
				$sql3 .= '  \''. $existing_admins[$row[0]].'\',';
			}else{
				$sql3 .= "'',";
			}

			$sql3  .= "'$row[0]','".md5($row[1])."','$row[3]','$row[2]','','$admin_pref ','1','0')";

			if(!$result4 = mysql_query($sql3, $db)){
				$msg->addError('PLOG_UPDATE_ADMINS_FAILED');
			}else{
				$msg->addFeedback('PLOG_UPDATE_ADMINS_SAVED');
				$message = 1;
			}
		}
 	}
*/


	// get a list of course titles and create values for the mangled_blog values in the plog_blogs table

	//update the plog_blogs table with ATutor courses. Creates one blog per course and assigns the instructor as the blog owner
	
	$sql5 = "SELECT * FROM ".TABLE_PREFIX."courses";
	$result5 = mysql_query($sql5,$db);

	// But first check to see if the course already exists
	$sqlb = "SELECT id FROM ".PLOG_PREFIX."blogs";
	$resultb = mysql_query($sqlb,$db);
	$i = '';
	while($rowb = mysql_fetch_array($resultb)){
		$i++;
		$existing_blogs[$i]= $rowb[0];
	}

	$default_blog_settings = 'O:12:"blogsettings":3:{s:6:"_objId";N;s:3:"log";N;s:6:"_props";a:14:{s:6:"locale";s:5:"en_UK";s:14:"show_posts_max";s:2:"15";s:8:"template";s:7:"blueish";s:17:"show_more_enabled";b:1;s:16:"recent_posts_max";s:2:"10";s:17:"xmlrpc_ping_hosts";a:2:{i:0;s:27:"http://rpc.weblogs.com/RPC2";i:1;s:0:"";}s:16:"htmlarea_enabled";b:1;s:16:"comments_enabled";b:1;s:16:"categories_order";s:1:"1";s:14:"comments_order";s:1:"1";s:11:"time_offset";s:2:"-5";s:21:"link_categories_order";s:1:"1";s:29:"show_future_posts_in_calendar";b:0;s:27:"new_drafts_autosave_enabled";b:0;}}';
	
	while ($row = mysql_fetch_array($result5)){
		//echo $row[0];

		if(!in_array($row[0], $existing_blogs)){
			$course_title = addslashes($row[6]);
			$spec_chars = array("'");
			$temp_course_title = str_replace($spec_chars, "","$row[6]"); 
			$temp_array = explode(" ", $temp_course_title);
			$temp_mangle = strtolower($temp_array[0]."_".$temp_array[1]);
			$sql2  = "REPLACE INTO ".PLOG_PREFIX."blogs VALUES ('$row[0]','$course_title','$row[1]','','$default_blog_settings','$temp_mangle','1','1')";

			if(!$result1 = mysql_query($sql2)){
				$msg->addError('PLOG_UPDATE_COURSE_FAILED');

			}else{
				$msg->addFeedback('PLOG_UPDATE_COURSE_SAVED');
					$message = 1;
			}
		}
 	}

	//update plog_users_permissions with enrolled courses. Allows course members to write to the course blog

	$sql6 = "SELECT * FROM ".TABLE_PREFIX."course_enrollment WHERE approved='y'";
	$result6 = mysql_query($sql6,$db);

	// first check if permission to write to course blogs already exists

	$sqlk = "SELECT * FROM ".PLOG_PREFIX."users_permissions";
	$resultk = mysql_query($sqlk,$db);
	$i = '';

	while($rowk = mysql_fetch_array($resultk)){
		$i++;
		$existing_permissions[$i]= $rowk[1].','.$rowk[2];
	}

	while ($row = mysql_fetch_array($result6)){
		$this_permission = $row[1].','.$row[0];
		if(!in_array($this_permission, $existing_permissions)) {
			$sqlx  = "REPLACE INTO ".PLOG_PREFIX."users_permissions VALUES ('','$row[1]','$row[0]','2')";
			if(!$resultx = mysql_query($sqlx,$db)){
				$msg->addError('PLOG_UPDATE_PERMISSIONS_FAILED');

			}else{
				$msg->addFeedback('PLOG_UPDATE_PERMISSIONS_SAVED');
					$message = 1;
			}
		}
 	}

	//create an initial category for each course
	$sqlcat = "SELECT * from ".TABLE_PREFIX."courses";
	$resultcat = mysql_query($sqlcat,$db);

	// But first check to see if a default course blog categoy already exists
	$sqlm = "SELECT blog_id FROM ".PLOG_PREFIX."articles_categories";
	$resultm = mysql_query($sqlm,$db);
	$i = '';

	while($rowm = mysql_fetch_array($resultm)){
		$i++;
		$existing_cats[$i]= $rowm[0];
	}


	$default_cat_properties = 's:0:"";';
	while($rowcat = mysql_fetch_array($resultcat)){
		if(!in_array($rowcat[0], $existing_cats)){
			$temp_title = str_replace("'","", $rowcat[6]);
			$temp_cat_array = explode(" ", $temp_title);
			$temp_cat_mangle = strtolower($temp_cat_array[0]."_".$temp_cat_array[1]);
			$course_title = addslashes($rowcat[6]);
			$sqlcat1  = "REPLACE INTO ".PLOG_PREFIX."articles_categories SET
				id = '',
				name = '$course_title',
				url = '',
				blog_id = '$rowcat[0]',
				last_modification = now(),
				in_main_page = '1',
				parent_id = '0',
				description = '$course_title',
				properties = '$default_cat_properties',
				mangled_name = '$temp_cat_mangle'";

			if(!$resultcat1 = mysql_query($sqlcat1,$db)){
					$msg->addError('PLOG_UPDATE_CATS_FAILED');

			}else{
					$msg->addFeedback('PLOG_UPDATE_CATS_SAVED');
					$message = 1;

			}
		}

	}
	if( !$msg->containsFeedbacks() &&  !$msg->containsErrors()){
		$msg->addFeedback('PLOG_UPDATE_NOT_REQUIRED');
	}

}

?>