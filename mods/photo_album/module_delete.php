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
 * @desc	This file deletes all the course images/comments in the photo album when a course is deleted
 * @author	Dylan Cheon & Kelvin Wong
 * @copyright	2006, Institute for Assistive Technology / University of Victoria 
 * @link	http://www.canassist.ca/                                    
 * @license GNU
 */
 
require_once ('define.php');
require_once ('include/data_func.php');
require_once ('include/general_func.php');

function photo_album_delete($course){
	$images=get_all_images($course);
	while ($row=mysql_fetch_array($images)){
		delete_image($row['image_id'], $row['course_id']);
	}
}

?>