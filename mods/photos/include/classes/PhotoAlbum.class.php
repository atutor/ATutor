<?php
/***********************************************************************/
/* ATutor															   */
/***********************************************************************/
/* Copyright (c) 2002-2009											   */
/* Adaptive Technology Resource Centre / Inclusive Design Institution  */
/* http://atutor.ca													   */
/*																	   */
/* This program is free software. You can redistribute it and/or	   */
/* modify it under the terms of the GNU General Public License		   */
/* as published by the Free Software Foundation.					   */
/***********************************************************************/
// $Id$

/** 
  * Photo Album
  * Note: Using intval for photo id, if the system is large enough, int might run out of bound.
  */
class PhotoAlbum {
	var $id;

	/** Constructor */
	function PhotoAlbum($id=0){
		$this->id = intval($id);
	}

	/** */
	function addPhoto($name, $comment, $member_id){
		global $db, $addslashes;
		$name		= $addslashes($name);
		$comment	= $addslashes($comment);
		$member_id	= intval($member_id);
		$album_id	= $this->id;

		//get max order
		$sql = 'SELECT MAX(ordering) AS ordering FROM '.TABLE_PREFIX."pa_photos WHERE album_id=$album_id";
		$result = mysql_query($sql, $db);
		if ($result){
			$row = mysql_fetch_assoc($result);
			$ordering = intval($row['ordering']) + 1;
		} else {
			$ordering = 1;
		}
		
		$sql = "INSERT INTO ".TABLE_PREFIX."pa_photos (name, description, member_id, album_id, ordering, created_date, last_updated) VALUES ('$name', '$comment', $member_id, $album_id, $ordering, NOW(), NOW())";
		$result = mysql_query($sql, $db);
		return $result;
	}

	/** */
	function getPhotoInfo($id){
		global $db, $addslashes;
		$id = intval($id);
		$row = array();

		$sql = "SELECT * FROM ".TABLE_PREFIX."pa_photos WHERE id=$id";
		$result = mysql_query($sql, $db);
		if ($result){
			$row = mysql_fetch_assoc($result);
		} else {
			return false;
		}
		return $row;
	}

	/** 
	 * Edit the info of the photo.  (just description for now)
	 * @param	int		photo id
	 * @param	string	the caption of the photo
	 * @param	string	alternative text of the image.
	 */
	function editPhoto($id, $description, $alt_text){
		global $db, $addslashes;
		$id = intval($id);
		$description = $addslashes($description);
		$alt_text = $addslashes($alt_text);

		$sql = "UPDATE ".TABLE_PREFIX."pa_photos SET description='$description', alt_text='$alt_text', last_updated=NOW() WHERE id=$id";
		$result = mysql_query($sql);
		return $result;
	}

	/** 
	 * Edit the order of the photo.  
	 * @param	int		photo id
 	 * @param	int		the ordering of this photo within this album
	 */
	function editPhotoOrder($id, $ordering){
		global $db, $addslashes;
		$id = intval($id);
		$ordering = intval($ordering);

		$sql = "UPDATE ".TABLE_PREFIX."pa_photos SET ordering=$ordering, last_updated=NOW() WHERE id=$id";
		$result = mysql_query($sql);
		return $result;
	}

	/** 
	 * Delete photo
	 * @param	int		photo id
	 */
	function deletePhoto($id){
		global $db;
		$id = intval($id);
		//delete photo file
		$sql = 'SELECT a.id AS aid, p.name AS name, p.ordering AS ordering, a.created_date AS album_date, p.created_date AS photo_date FROM '.TABLE_PREFIX.'pa_photos p, '.TABLE_PREFIX."pa_albums a WHERE a.id=p.album_id AND p.id=$id";
		$result = mysql_query($sql, $db);
		if ($result){
			$row = mysql_fetch_assoc($result);
		}
		//if the aid don't match each other, there must be something wrong.
		if($row['aid']!=$this->id){
			return false;			
		}
		$albumpath = AT_PA_CONTENT_DIR.getAlbumFilePath($row['aid'], $row['album_date']);
		$filepath = $albumpath.DIRECTORY_SEPARATOR.getPhotoFilePath($id, $row['name'], $row['photo_date']);	//orig
		$filepath_tn = $albumpath.'_tn'.DIRECTORY_SEPARATOR.getPhotoFilePath($id, $row['name'], $row['photo_date']); //thumbnail
		if (is_file($filepath) && is_file($filepath_tn)){
			unlink($filepath);
			unlink($filepath_tn);
		}
		
		//delete photo comments
		$sql = 'DELETE FROM '.TABLE_PREFIX."pa_photo_comments WHERE photo_id=$id";
		mysql_query($sql, $db);

		//reorder images
		$sql = 'UPDATE '.TABLE_PREFIX.'pa_photos SET `ordering`=`ordering`-1 WHERE album_id='.$row['aid'].' AND `ordering` > '.$row['ordering'];
		mysql_query($sql, $db);

		//delete the photo from db
		$sql = "DELETE FROM ".TABLE_PREFIX."pa_photos WHERE id=$id";
		mysql_query($sql, $db);
		
		return true;
	}

	/** 
	 * Create an album
	 * @param	string		name of the album
	 * @param	string		location of where this album took place
	 * @param	string		descriptive text of this album
	 * @param	int			check include/constants.inc.php
	 * @param	int			permission, 0 for private, 1 for shared
	 * @param	int			album author
	 * @param	int			OPTIONAL, Photo cover for this album
	 */
	function createAlbum($name, $location, $description, $type, $permission, $member_id, $photo_id=0){
		global $addslashes, $db;

		//handle input
		$name		= $addslashes($name);
		$locatoin	= $addslashes($location);
		$description = $addslashes($description);
		$type		= intval($type);
		$type		= ($type<=0)?AT_PA_TYPE_MY_ALBUM:$type;
		$permission	= intval($permission);
		$member_id  = intval($member_id);
		$photo_id	= intval($photo_id);

		$sql = "INSERT INTO ".TABLE_PREFIX."pa_albums (name, location, description, type_id, member_id, permission, photo_id, created_date, last_updated) VALUES ('$name', '$location', '$description', $type, $member_id, $permission, $photo_id, NOW(), NOW())";
		$result = mysql_query($sql, $db);

		//if course album, add a record.
		if ($type==AT_PA_TYPE_COURSE_ALBUM){
			$aid = mysql_insert_id();
			$sql = "INSERT INTO ".TABLE_PREFIX."pa_course_album (course_id, album_id) VALUES ($_SESSION[course_id], $aid)";
			$result = mysql_query($sql, $db);
		}
		return $result;
	}

	/** 
	 * Updating album cover.
	 * @param	int		photo id (the album cover)	 
	 * @precondition	user has the ability to edit the album.
	 */
	function editAlbumCover($pid){
		global $db;

		//safe guard
		$pid = intval($pid);
		$aid = $this->id;

		//pid and aid cannot be empty
		if ($pid<=0 || $aid<=0){
			return false;
		}
		
		$sql = "UPDATE ".TABLE_PREFIX."pa_albums SET photo_id=$pid WHERE id=$aid";
		$result = mysql_query($sql, $db);
		return $result;
	}
	
	/** 
	 * Update album
	 * @param	string		name of the album
	 * @param	string		location of where this album took place
	 * @param	string		descriptive text of this album
	 * @param	int			check include/constants.inc.php
	 * @param	int			permission, 0 for private, 1 for shared
	 */
	function editAlbum($name, $location, $description, $type, $permission){
		global $db, $addslashes;
		$id			 = $this->id;
		$name		 = $addslashes($name);
		$location	 = $addslashes($location);
		$description = $addslashes($description);
		$type		 = ($type==AT_PA_TYPE_COURSE_ALBUM)?AT_PA_TYPE_COURSE_ALBUM:AT_PA_TYPE_MY_ALBUM;
		$permission	 = ($permission==AT_PA_SHARED_ALBUM)?AT_PA_SHARED_ALBUM:AT_PA_PRIVATE_ALBUM;
		$info		 = $this->getAlbuminfo();

		//if type has been changed, run the query to update the course_album table
		if ($info['type_id'] != $type){
			//if course album, add a record.		
			if ($type==AT_PA_TYPE_COURSE_ALBUM){
				$sql = "INSERT INTO ".TABLE_PREFIX."pa_course_album (course_id, album_id) VALUES ($_SESSION[course_id], $id)";
				$result = mysql_query($sql, $db);
			} else {
				$sql = 'DELETE FROM '.TABLE_PREFIX."pa_course_album WHERE course_id=$_SESSION[course_id] AND album_id=$id";
				$result = mysql_query($sql, $db);
			}
		}

		$sql = 'UPDATE '.TABLE_PREFIX."pa_albums SET name='$name', location='$location', description='$description', type_id=$type, permission=$permission WHERE id=$id";
		$result = mysql_query($sql, $db);
		return $result;
	}

	/** 
	 * Delete an album and all associations
	 */
	function deleteAlbum(){
		//TODO Error checking on each step, if anyone fails, should report it to user
		global $db;
		$id = $this->id;

		//clean directory		
		$sql = 'SELECT created_date FROM '.TABLE_PREFIX."pa_albums WHERE id=$id";
		$result = mysql_query($sql, $db);
		if ($result){
			$row = mysql_fetch_assoc($result);
		}
		$filepath = AT_PA_CONTENT_DIR . getAlbumFilePath($id, $row['created_date']);	//orig
		$filepath_tn = $filepath.'_tn';	//thumbnails
		//delete files
		if (is_dir($filepath) && is_dir($filepath_tn)){
			clr_dir($filepath);
			clr_dir($filepath_tn);
		}

		//delete all photo comments
		$sql = 'DELETE c.* FROM '.TABLE_PREFIX.'pa_photo_comments c LEFT JOIN '.TABLE_PREFIX."pa_photos p ON c.photo_id=p.id WHERE p.album_id=$id";
		mysql_query($sql, $db);

		//delete all photos within this album
		$sql = "DELETE FROM ".TABLE_PREFIX."pa_photos WHERE album_id=$id";
		mysql_query($sql, $db);

		//delete all album comments
		$sql = 'DELETE FROM '.TABLE_PREFIX."pa_album_comments WHERE album_id=$id";
		mysql_query($sql, $db);

		//delete album
		$sql = "DELETE FROM ".TABLE_PREFIX."pa_albums WHERE id=$id";
		mysql_query($sql, $db);
	}

	/** 
	 * Get album photos
	 */
	function getAlbumPhotos($offset=-1){
		global $db;
		$id = $this->id;
		$offset = intval($offset);
		$rows = array();

		$sql = "SELECT photos.* FROM " .TABLE_PREFIX."pa_photos photos LEFT JOIN ".TABLE_PREFIX."pa_albums albums ON albums.id=photos.album_id WHERE albums.id=$id ORDER BY ordering";
		if ($offset >= 0){
			$sql .= " LIMIT $offset ,".AT_PA_PHOTOS_PER_PAGE;
		}

		$result = mysql_query($sql, $db);
		if ($result){
			while ($row = mysql_fetch_assoc($result)){
				$rows[] = $row;
			}
		}
		return $rows;
	}

	/** 
	 * Get album information
	 * @param	int	 album id
	 * @return  the album row, false on error
	 */
	function getAlbumInfo(){
		global $db;
		$id = $this->id;
		$sql = "SELECT * FROM ".TABLE_PREFIX."pa_albums WHERE id=$id";
		$result = mysql_query($sql, $db);
		if ($result){
			$row = mysql_fetch_assoc($result);
			return $row;
		}
		return false;
	}

	/** 
	 * Get a list of album by the given type (profile/my albums/class albums)
	 * Default to be all.
	 */
	function getAlbums($member_id, $type_id=-1, $offset=-1){
		global $db;
		$type_id = intval($type_id);
		$member_id = intval($member_id);
		$offset = intval($offset);		
		$rows = array();
				
		$sql = "SELECT * FROM ".TABLE_PREFIX."pa_albums WHERE member_id=$member_id";
		if($type_id==AT_PA_TYPE_COURSE_ALBUM){
			//if inside the course scope, get this course's albums only
			//if in my_start_page, get all enrolled course
			$course_sql = ($_SESSION['course_id']==0)?'':'AND ca.course_id='.$_SESSION['course_id'];

			$sql = 'SELECT albums.* FROM '.TABLE_PREFIX.'pa_albums albums, 
						(SELECT ca.* FROM '.TABLE_PREFIX.'course_enrollment enrollments
							RIGHT JOIN '.TABLE_PREFIX."pa_course_album ca 
							ON enrollments.course_id=ca.course_id
							WHERE member_id=$member_id $course_id
						) AS allowed_albums
						WHERE albums.id=allowed_albums.album_id";
		}
		elseif($type_id > 0){
			$sql .= " AND type_id=$type_id";
		}
		if ($offset > -1){
			$sql .= " LIMIT $offset ," . AT_PA_ALBUMS_PER_PAGE;
		}
		$result = mysql_query($sql, $db);
		if($result){
			while($row = mysql_fetch_assoc($result)){
				$rows[$row['id']] = $row;
			}
		}
		return $rows;
	}

	/**
	 * Get all albums, used by Admin only.
	 */
	function getAllAlbums($offset=-1){
		global $db;
		$offset = intval($offset);

		$sql = 'SELECT * FROM '.TABLE_PREFIX.'pa_albums';
		
		if ($offset > -1){
			 $sql .= " LIMIT $offset ," . AT_PA_ADMIN_ALBUMS_PER_PAGE;
		}

		$result = mysql_query($sql, $db);
		if($result){
			while($row = mysql_fetch_assoc($result)){
				$rows[$row['id']] = $row;
			}
		}
		return $rows;
	}


	/**
	 * Get all private/shared albums (ignore album type)
	 * @param	boolean		True to get all shared album; false to get all private album, default: true
	 * @param	int			Resultset's limit
	 */
	function getSharedAlbums($isShared=true, $offset=-1){
		global $db;
		$offset = intval($offset);
		$permission = ($isShared)? 1 : 0;

		$sql = 'SELECT * FROM '.TABLE_PREFIX."pa_albums WHERE permission=$permission";
		if ($offset > -1){
			 $sql .= " LIMIT $offset ," . AT_PA_ALBUMS_PER_PAGE;
		}
		$result = mysql_query($sql, $db);
		if ($result){
			while ($row = mysql_fetch_assoc($result)){
				$rows[$row['id']] = $row;
			}
		}
		return $rows;
	}

	/** 
	 * Get album type names
	 * @param	int		album types, check constants.inc.php
	 * @return	the string representation of this album type
	 */
	function getAlbumTypeName($type){
		switch ($type){
			case AT_PA_TYPE_MY_ALBUM:
				return _AT('pa_my_albums');
			case AT_PA_TYPE_COURSE_ALBUM:
				return _AT('pa_course_albums');
			case AT_PA_TYPE_PERSONAL:
				return _AT('pa_profile_album');
			default:
				return false;
		}
	}

	/**
	 * Get the owner of this album
	 * @param	int		album_id
	 * @param	int		member_id
	 * @return	True if the given user has the privilege to delete/edit.
	 */
	function checkAlbumPriv($member_id){
		global $db;
		$album_id = $this->id;
		$member_id = intval($member_id);

		//if admin
		if (admin_authenticate(AT_ADMIN_PRIV_PHOTO_ALBUM, true)){
			return true;
		}

		$sql = "SELECT member_id FROM ".TABLE_PREFIX."pa_albums WHERE id=$album_id";
		$result = mysql_query($sql, $db);
		if ($result){
			$row = mysql_fetch_assoc($result);
			return ($row['member_id']==$member_id);
		}
		return false;
	}

	/**
	 * Get the owner of this photo
	 * @param	int		photo_id
	 * @param	int		member_id
	 * @return	True if the given user has the privilege to delete/edit.
	 */
	function checkPhotoPriv($photo_id, $member_id){
		global $db;
		$photo_id = intval($photo_id);
		$member_id = intval($member_id);

		$sql = "SELECT member_id FROM ".TABLE_PREFIX."pa_photos WHERE id=$photo_id";
		$result = mysql_query($sql, $db);
		if ($result){
			$row = mysql_fetch_assoc($result);
			return ($row['member_id']==$member_id);
		}
		return false;
	}


	/**
	 * Get the owner of the comment
	 */
	function checkCommentPriv($comment_id, $member_id, $isPhoto){
		global $db;
		$comment_id = intval($comment_id);
		$member_id = intval($member_id);

		if ($isPhoto){
			$sql = "SELECT member_id FROM ".TABLE_PREFIX."pa_photo_comments WHERE id=$comment_id";
		} else {
			$sql = "SELECT member_id FROM ".TABLE_PREFIX."pa_album_comments WHERE id=$comment_id";
		}
		$result = mysql_query($sql, $db);
		if ($result){
			$row = mysql_fetch_assoc($result);
			return ($row['member_id']==$member_id);
		}
		return false;
	}

	/**
	 * Add comment
	 * @param	int		id (can be photo_id, or album_id)
	 * @param	string	comment	
	 * @param	int		user id
	 * @param   string  author
	 * @param	boolean	true if it is photo_id, false otherwise
	 */
	function addComment($id, $comment, $member_id, $author, $isPhoto){
		global $addslashes, $db;

		$id = intval($id);
		$member_id = intval($member_id);
		$comment = $addslashes($comment);
		$author = $addslashes($author);

		if(!$isPhoto){
			$sql =	'INSERT INTO '.TABLE_PREFIX."pa_album_comments (album_id, comment, member_id, author, created_date) VALUES ($id, '$comment', $member_id, '$author', NOW())";
		} else {
			$sql =	'INSERT INTO '.TABLE_PREFIX."pa_photo_comments (photo_id, comment, member_id, author, created_date) VALUES ($id, '$comment', $member_id, '$author', NOW())";
		} 
		$result = mysql_query($sql, $db);
		return $result;
	}

	/**
	 * Edit comment
	 * @param	int		comment id
	 * @param	string	comment
	 * @param	boolean	true if it is photo_id, false otherwise
	 * @precondition	this->member_id has the privilige to edit comment.
	 */
	function editComment($id, $comment, $isPhoto){
		global $addslashes, $db;

		$id = intval($id);
		$comment = $addslashes($comment);
		if($id<1 || $comment==''){
			return false;
		}

		if (!$isPhoto){
			$sql = 'UPDATE '.TABLE_PREFIX."pa_album_comments SET comment='$comment' WHERE id=$id";
		} else {
			$sql = 'UPDATE '.TABLE_PREFIX."pa_photo_comments SET comment='$comment' WHERE id=$id";
		}
		$result = mysql_query($sql, $db);
		return $result;
	}


	/**
	 * Get comments
	 * @param	int		id (can be photo_id, or album_id)
	 * @param	boolean	true of it is photo_id, false otherwise.
	 */
	function getComments($id, $isPhoto){
		global $db;
		
		$id = intval($id);

		if ($isPhoto){
			$sql = 'SELECT * FROM '.TABLE_PREFIX."pa_photo_comments WHERE photo_id=$id";
		} else {
			$sql = 'SELECT * FROM '.TABLE_PREFIX."pa_album_comments WHERE album_id=$id";
		}
		$sql .= ' ORDER BY created_date';

		$result = mysql_query($sql, $db);
		if($result){
			while ($row = mysql_fetch_assoc($result)){
				$rows[] = $row;
			}
		}
		return $rows;
	}

	/**
	 * Delete photo comment 
	 */
	function deleteComment($id, $isPhoto){
		global $db;
		$id = intval($id);
		
		if ($isPhoto){
			$sql = "DELETE FROM ".TABLE_PREFIX."pa_photo_comments WHERE id=$id";
		} else {
			$sql = "DELETE FROM ".TABLE_PREFIX."pa_album_comments WHERE id=$id";
		}
		mysql_query($sql, $db);
	}

	/**
	 * Search and return list of albums, and list of photos 
	 * Note: Speed and ranks are of priority here.
	 * @param	Array			The unescaped array of search phrases.
	 * @return	[Array, Array]	First array is albums, second array are matched photos
	 */
	function search($words){
		global $db, $addslashes;
		
		//init
		$visible_photos = array();
		$visible_albums = array();

		//validate input
		if (!is_array($words) || empty($words)){
			return null;
		}
		//filter 
		foreach($words as $k=>$v){
			$v = $addslashes(trim($v));
			$query .= "(description LIKE '%$v%' OR name LIKE '%$v%' OR alt_text LIKE '%$v%') OR ";	//for sql
			$pattern .= $v.'|';	//regex for albums
		}
		$pattern = substr($pattern, 0, -1);
		
		//TODO: Optimize SQL, UNION is slow, but I think this is the fastest I can get, prove me wrong.
		//@harris
		/** Get all visible albums */
		$sql = 'SELECT albums.* FROM '.TABLE_PREFIX.'pa_albums albums, 
					(SELECT ca.* FROM '.TABLE_PREFIX.'course_enrollment enrollments
						RIGHT JOIN '.TABLE_PREFIX."pa_course_album ca 
						ON enrollments.course_id=ca.course_id
						WHERE member_id=$_SESSION[member_id]
					) AS allowed_albums
					WHERE albums.id=allowed_albums.album_id
				UNION
				SELECT * FROM AT_pa_albums WHERE member_id=$_SESSION[member_id] OR permission=1";
		$result = mysql_query($sql, $db);
		if (!$result){
			return null;
		}
		while($row = mysql_fetch_assoc($result)){
			$visible_albums[$row['id']] = $row;
		}
		$visible_albums_ids = implode(', ', array_keys($visible_albums));
		
		/** Get all photos from these albums */
		$sql = 'SELECT * FROM '.TABLE_PREFIX."pa_photos WHERE album_id IN ($visible_albums_ids)";		
		$query = ' AND ' . substr($query, 0, -3);
		$sql = $sql . $query . ' LIMIT ' . AT_PA_PHOTO_SEARCH_LIMIT; 
		$result = mysql_query($sql, $db);
		if (!$result){
			return null;
		}
		while($row = mysql_fetch_assoc($result)){
			$visible_photos[$row['id']] = $row;
		}

		/** Point system*/
		//photos
		if (!empty($visible_photos)){
			$album_photos = array();	//keep track of the # of photos inside an album, should match a 'count(*) group by'
			foreach($visible_photos as $photo_id=>$photo){
				$match_flag = false;

				if (preg_match("/$pattern/i", $photo['name'])){
					$visible_photos[$photo_id]['point'] += 1;
					$match_flag = true;
				} 
				if (preg_match("/$pattern/i", $photo['alt_text'])){
					$visible_photos[$photo_id]['point'] += 1;
					$match_flag = true;
				} 
				if (preg_match("/$pattern/i", $photo['description'])){
					$visible_photos[$photo_id]['point'] += 2;
					$match_flag = true;
				}
				//total photo points within an album
				if ($match_flag){
					$album_photos[$photo['album_id']] += 1;
				}
			}
		}

		//albums
		foreach($visible_albums as $album_id=>$album){
			if (preg_match("/$pattern/i", $album['name'])){
				$visible_albums[$album_id]['point'] += 3;
			} 
			if (preg_match("/$pattern/i", $album['location'])){
				$visible_albums[$album_id]['point'] += 1;
			} 
			if (preg_match("/$pattern/i", $album['description'])){
				$visible_albums[$album_id]['point'] += 1;
			}
			//every photo has a certain value to the album, and is calculated as follow 
			//[# of matched photo in an album] / [total number of matched photos] *4
			//4 is the total matched photo score (ie. all album's photo score should add up to 4)
			if (isset($album_photos[$album_id])){
				$visible_albums[$album_id]['point'] += $album_photos[$album_id]/sizeof($visible_photos) * 4;
			}
			//If no point in the album, most likely it's irrelevant and not of interest, take it out
			if (!isset($visible_albums[$album_id]['point'])){
				unset($visible_albums[$album_id]);
			}
		}

		/** sort and return */
		usort($visible_photos, array('PhotoAlbum', 'search_cmp'));
		usort($visible_albums, array('PhotoAlbum', 'search_cmp'));
//		debug($visible_photos, 'visible_photos');
//		debug($visible_albums, 'visible albums');

		return array($visible_albums, $visible_photos);
	}
	
	/**
	 * Compare functino for usort, used by search (descending)
	 */
	function search_cmp($k1, $k2){
		if(!isset($k1['point'])){
			$k1['point'] = 0;
		}
		if(!isset($k2['point'])){
			$k2['point'] = 0;
		}

		if ($k1['point'] == $k2['point']) return 0;
		if ($k1['point'] > $k2['point']) return -1;
		else return 1;
	}
}
?>
