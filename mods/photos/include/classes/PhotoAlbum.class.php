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

	/** */
	function createAlbum($name, $location, $description, $type, $member_id, $photo_id=0){
		global $addslashes, $db;

		//handle input
		$name		= $addslashes($name);
		$locatoin	= $addslashes($location);
		$description = $addslashes($description);
		$type		= intval($type);
		$type		= ($type<=0)?AT_PA_TYPE_MY_ALBUM:$type;
		$member_id  = intval($member_id);
		$photo_id	= intval($photo_id);

		$sql = "INSERT INTO ".TABLE_PREFIX."pa_albums (name, location, description, type_id, member_id, photo_id, created_date, last_updated) VALUES ('$name', '$location', '$description', $type, $member_id, $photo_id, NOW(), NOW())";
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
	 * @param	string	album name
	 * @param	string	location of which the album is taken from
	 * @param	string	description of the album
	 * @param	int		album type
	 */
	function editAlbum($name, $location, $description, $type){
		global $db, $addslashes;
		$id			 = $this->id;
		$name		 = $addslashes($name);
		$location	 = $addslashes($location);
		$description = $addslashes($description);
		$type		 = ($type==AT_PA_TYPE_COURSE_ALBUM)?AT_PA_TYPE_COURSE_ALBUM:AT_PA_TYPE_MY_ALBUM;
		
		$sql = 'UPDATE '.TABLE_PREFIX."pa_albums SET name='$name', location='$location', description='$description', type_id=$type WHERE id=$id";
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
			$sql = 'SELECT albums.* FROM '.TABLE_PREFIX.'pa_albums albums, 
						(SELECT ca.* FROM '.TABLE_PREFIX.'course_enrollment enrollments
							RIGHT JOIN '.TABLE_PREFIX."pa_course_album ca 
							ON enrollments.course_id=ca.course_id
							WHERE member_id=$member_id AND ca.course_id=$_SESSION[course_id]
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
	 * @param	boolean	true if it is photo_id, false otherwise
	 */
	function addComment($id, $comment, $member_id, $isPhoto){
		global $addslashes, $db;

		$id = intval($id);
		$member_id = intval($member_id);
		$comment = $addslashes($comment);

		if(!$isPhoto){
			$sql =	'INSERT INTO '.TABLE_PREFIX."pa_album_comments (album_id, comment, member_id, created_date) VALUES ($id, '$comment', $member_id, NOW())";
		} else {
			$sql =	'INSERT INTO '.TABLE_PREFIX."pa_photo_comments (photo_id, comment, member_id, created_date) VALUES ($id, '$comment', $member_id, NOW())";
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
}
?>
