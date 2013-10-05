<?php
/***********************************************************************/
/* ATutor															   */
/***********************************************************************/
/* Copyright (c) 2002-2010                                             */
/* Inclusive Design Institute	                                       */
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

    /** 
      * Add a photo.  
      * @param	string	filename
      * @param	string	description of the photo
      * @param	int		author of this photo
      * @return	the photo id that's in the database.
      */
    function addPhoto($name, $comment, $member_id){
        global $db, $addslashes;
        $name		= $addslashes($name);
        $comment	= $addslashes($comment);
        $member_id	= intval($member_id);
        $album_id	= $this->id;

        //get max order
        $sql = "SELECT MAX(ordering) AS ordering FROM %spa_photos WHERE album_id=$album_id";
        $row_album = queryDB($sql, array(TABLE_PREFIX, $album_id), TRUE);
        
        if(count($row_album) >0){
            $row = $row_album;
            $ordering = intval($row['ordering']) + 1;
        } else {
            $ordering = 1;
        }
        
        $sql = "INSERT INTO %spa_photos (name, description, member_id, album_id, ordering, created_date, last_updated) VALUES ('%s', '%s', %d, %d, %d, NOW(), NOW())";
        $result = queryDB($sql, array(TABLE_PREFIX, $name, $comment, $member_id, $album_id, $ordering));


        //update album last_updated
        if ($result > 0){
            $photo_id = at_insert_id();
            $this->updateAlbumTimestamp();
        }

        return $photo_id;
    }

    /** */
    function getPhotoInfo($id){
        $id = intval($id);
        $row = array();

         $sql = "SELECT * FROM %spa_photos WHERE id=%d";
        $row_photo = queryDB($sql, array(TABLE_PREFIX, $id), TRUE);
        
        if (count($row_photo) > 0){
            return $row_photo;
        } else {
            return false;
        }
    }

    /** 
     * Edit the info of the photo.  
     * @param	int		photo id
     * @param	string	the caption of the photo
     * @param	string	alternative text of the image.
     */
    function editPhoto($id, $description, $alt_text){
        $id = intval($id);

        $sql = "UPDATE %spa_photos SET description='%s', alt_text='%s', last_updated=NOW() WHERE id=%d";
        $result = queryDB($sql, array(TABLE_PREFIX, $description, $alt_text, $id));

        //update album last_updated
        if ($result > 0){
            $this->updateAlbumTimestamp();
        }

        return $result;
    }

    /** 
     * Edit the order of the photo.  
     * @param	int		photo id
     * @param	int		the ordering of this photo within this album
     */
    function editPhotoOrder($id, $ordering){
        $id = intval($id);
        $ordering = intval($ordering);

        $sql = "UPDATE %spa_photos SET ordering=%d, last_updated=NOW() WHERE id=%d";
        $result = queryDB($sql, array(TABLE_PREFIX, $ordering, $id));

        //update album last_updated
        if ($result > 0){
            $this->updateAlbumTimestamp();
        }

        return $result;
    }

    /** 
     * Delete photo
     * @param	int		photo id
     */
    function deletePhoto($id){
        $id = intval($id);
        //delete photo file
         $sql = "SELECT a.id AS aid, p.name AS name, p.ordering AS ordering, a.created_date AS album_date, p.created_date AS photo_date FROM %spa_photos p, %spa_albums a WHERE a.id=p.album_id AND p.id=%d";
        $row = queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX, $id), TRUE);

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
        $sql = "DELETE FROM %spa_photo_comments WHERE photo_id=%d";
        queryDB($sql, array(TABLE_PREFIX, $id));

        //reorder images
        $sql = 'UPDATE %spa_photos SET `ordering`=`ordering`-1 WHERE album_id=%d AND `ordering` > %d';
        queryDB($sql, array(TABLE_PREFIX, $row['aid'], $row['ordering']));

        //delete the photo from db
        $sql = "DELETE FROM %spa_photos WHERE id=%d";
        queryDB($sql, array(TABLE_PREFIX, $id));

        //update album last_updated
        if (count($row) > 0){
            $this->updateAlbumTimestamp();
        }        
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
     * @return  int         album_id, FALSE if failed.
     */
    function createAlbum($name, $location, $description, $type, $permission, $member_id, $photo_id=0){

        $type		= intval($type);
        $type		= ($type<=0)?AT_PA_TYPE_MY_ALBUM:$type;

        $sql = "INSERT INTO %spa_albums (name, location, description, type_id, member_id, permission, photo_id, created_date, last_updated) VALUES ('%s', '%s', '%s', %d, %d, %d, %d, NOW(), NOW())";
        $result = queryDB($sql, array(TABLE_PREFIX, $name, $location, $description, $type, $member_id, $permission, $photo_id));
        $aid = at_insert_id();

        //if course album, add a record.
        if ($type==AT_PA_TYPE_COURSE_ALBUM){			
            $sql = "INSERT INTO %spa_course_album (course_id, album_id) VALUES (%d,%d)";
            $result = queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id'], $aid));
        }
        if($result == 0){
            return false;
        }
        return $aid;
    }

    /** 
     * Updating album cover.
     * @param	int		photo id (the album cover)	 
     * @precondition	user has the ability to edit the album.
     */
    function editAlbumCover($pid){
        //safe guard
        $pid = intval($pid);
        $aid = $this->id;

        //pid and aid cannot be empty
        if ($pid<=0 || $aid<=0){
            return false;
        }

        $sql = "UPDATE %spa_albums SET photo_id=%d, last_updated=NOW() WHERE id=%d";
        $result = queryDB($sql, array(TABLE_PREFIX, $pid, $aid));
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
        $id			 = $this->id;

        $type		 = ($type==AT_PA_TYPE_COURSE_ALBUM)?AT_PA_TYPE_COURSE_ALBUM:AT_PA_TYPE_MY_ALBUM;
        $permission	 = ($permission==AT_PA_SHARED_ALBUM)?AT_PA_SHARED_ALBUM:AT_PA_PRIVATE_ALBUM;
        $info		 = $this->getAlbuminfo();

        //if type has been changed, run the query to update the course_album table
        if ($info['type_id'] != $type){
            //if course album, add a record.		
            if ($type==AT_PA_TYPE_COURSE_ALBUM){

                $sql = "INSERT INTO %spa_course_album (course_id, album_id) VALUES (%d, %d)";
                $result = queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id'], $id));
            } else {

                $sql = "DELETE FROM %spa_course_album WHERE course_id=%d AND album_id=%d";
                $result = queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id'], $id));
            }
        }

        $sql = "UPDATE %spa_albums SET name='%s', location='%s', description='%s', type_id=%d, permission=%d, last_updated=NOW() WHERE id=%d";
        $result = queryDB($sql, array(TABLE_PREFIX, $name, $location, $description, $type, $permission, $id));
        return $result;
    }

    /** 
     * Delete an album and all associations
     */
    function deleteAlbum(){
        //TODO Error checking on each step, if anyone fails, should report it to user
        $id = $this->id;

        //clean directory		

        $sql = "SELECT created_date FROM %spa_albums WHERE id=%d";
        $row = queryDB($sql, array(TABLE_PREFIX, $id), TRUE);

        $filepath = AT_PA_CONTENT_DIR . getAlbumFilePath($id, $row['created_date']);	//orig
        $filepath_tn = $filepath.'_tn';	//thumbnails
        //delete files
        if (is_dir($filepath) && is_dir($filepath_tn)){
            clr_dir($filepath);
            clr_dir($filepath_tn);
        }

        //delete all photo comments
        $sql = "DELETE c.* FROM %spa_photo_comments c LEFT JOIN %spa_photos p ON c.photo_id=p.id WHERE p.album_id=%d";
        queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX, $id));

        //delete all photos within this album
        $sql = "DELETE FROM %spa_photos WHERE album_id=%d";
        queryDB($sql, array(TABLE_PREFIX, $id));

        //delete all album comments
        $sql = "DELETE FROM %spa_album_comments WHERE album_id=%d";
        queryDB($sql, array(TABLE_PREFIX, $id));

        //delete album
        $sql = "DELETE FROM %spa_albums WHERE id=%d";
        queryDB($sql, array(TABLE_PREFIX, $id));
    }

    /**
     * Update album last_updated column to the current timestamp.
     * @return	null
     * @access	private
     */
    private function updateAlbumTimestamp(){
        if($this->id <= 0){
            //quit if album id is not set.
            return;
        }

        $sql = 'UPDATE %spa_albums SET last_updated=NOW() WHERE id=%d';
        queryDB($sql, array(TABLE_PREFIX, $this->id));
    }

    /** 
     * Get album photos
     */
    function getAlbumPhotos($offset=-1){
        $id = $this->id;
        $offset = intval($offset);
        $rows = array();

        $sql = "SELECT photos.* FROM %spa_photos photos LEFT JOIN %spa_albums albums ON albums.id=photos.album_id WHERE albums.id=%d ORDER BY ordering";
        
        if ($offset >= 0){
            $sql .= " LIMIT $offset ,".AT_PA_PHOTOS_PER_PAGE;
        }

        $rows = queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX, $id));

        return $rows;
    }

    /** 
     * Get album information
     * @param	int	 album id
     * @return  the album row, false on error
     */
    function getAlbumInfo(){
        $id = $this->id;

        $sql = "SELECT * FROM %spa_albums WHERE id=%d";
        $row = queryDB($sql, array(TABLE_PREFIX, $id), TRUE);  
             
        if(count($row) > 0){
            return $row;
        }
        return false;
    }

    /** 
     * Get a list of album by the given type (profile/my albums/class albums)
     * Default to be all.
     */
    function getAlbums($member_id, $type_id=-1, $offset=-1){
        $type_id = intval($type_id);
        $member_id = intval($member_id);
        $offset = intval($offset);	
        $rows = array();
                
        $sql = "SELECT * FROM ".TABLE_PREFIX."pa_albums WHERE member_id=$member_id";
        if($type_id==AT_PA_TYPE_COURSE_ALBUM){
            //if inside the course scope, get this course's albums only
            //if in my_start_page, get all enrolled course

            $sql = "SELECT albums.* FROM %spa_albums albums,
                    (SELECT ca.* FROM %scourse_enrollment enrollments
                    RIGHT JOIN %spa_course_album ca
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
        
        $rows_albums = queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX, TABLE_PREFIX));
        $rows = $this->filterAlbums($rows_albums);
    
        return $rows;
    }
    private function filterAlbums($rows){
        // This function is a hack to remove groups from courses they don't belong to
        // Should be replaced with a proper sql join in getAlbums() above;
        if(count($_SESSION['groups']) >0){
            $pa_groups = implode(",", $_SESSION['groups']);
        }
        //list group albums for this course
        if($pa_groups != ''){
        
            $sql = "SELECT album_id FROM %spa_groups WHERE group_id IN (%s) ";
            $pa_group_rows = queryDB($sql, array(TABLE_PREFIX, $pa_groups));
            
            if(count($pa_group_rows) > 0){  
                foreach($pa_group_rows as $g_row){
                    $group_albums1[$g_row['album_id']] = $g_row['album_id'];
                }
                if(count($group_albums1) > 0){
                    foreach($rows as $row){
                        if(in_array($row['id'], $group_albums1)){
                                $rows_clean[$row['id']] = $row;
                        }
                    }
                }
            }
        }
        // list albums for this course
        if(isset($_SESSION['course_id'])){
        
            $sql = "SELECT album_id FROM %spa_course_album WHERE course_id = %d";
            $pa_course_rows = queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id'])); 

            if(count($pa_course_rows) > 0){
                foreach($pa_course_rows as $c_row){
                    $course_albums1[$c_row['album_id']] = $c_row['album_id'];
                }
            }

            if(count($course_albums1) > 0){
               foreach($rows as $row){
                    if(in_array($row['id'], $course_albums1)){
                        $rows_clean[$row['id']] = $row;
                    }
                }
            }
        }
        // list albums for the current user
        if($_SESSION['member_id']){
            $sql = "SELECT * FROM %spa_albums WHERE member_id=%d";
            $pa_myalbum_rows = queryDB($sql, array(TABLE_PREFIX, $_SESSION['member_id'])); 
            if(count($pa_myalbum_rows) > 0){
                foreach($pa_myalbum_rows as $my_row){
                    $my_albums1[$my_row['id']] = $my_row['id'];
                }
            }
            if(count($my_albums1) > 0){
               foreach($rows as $row){
                    if(in_array($row['id'], $my_albums1)){
                        $rows_clean[$row['id']] = $row;
                    }
                } 
            }       
        }

        return $rows_clean;
    
    }

    /**
     * Get all albums, used by Admin only.
     */
    function getAllAlbums($offset=-1){
        $offset = intval($offset);
        $sql = 'SELECT * FROM %spa_albums';
        
        if ($offset > -1){
             $sql .= " LIMIT $offset ," . AT_PA_ADMIN_ALBUMS_PER_PAGE;
        }
        
        $rows_albums = queryDB($sql, array(TABLE_PREFIX));

        return $rows_albums;
    }


    /**
     * Get all private/shared albums (ignore album type)
     * @param	boolean		True to get all shared album; false to get all private album, default: true
     * @param	int			Resultset's limit
     */
    function getSharedAlbums($isShared=true, $offset=-1){
        $offset = intval($offset);
        $permission = ($isShared)? 1 : 0;

        $sql = "SELECT * FROM %spa_albums WHERE permission=%d";
        if ($offset > -1){
             $sql .= " LIMIT $offset ," . AT_PA_ALBUMS_PER_PAGE;
        }
        $rows = queryDB($sql, array(TABLE_PREFIX, $permission));

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
        $album_id = $this->id;
        //if admin
        if (admin_authenticate(AT_ADMIN_PRIV_PHOTO_ALBUM, true)){
            return true;
        }

        $sql = "SELECT member_id FROM %spa_albums WHERE id=%d";
        $row = queryDB($sql, array(TABLE_PREFIX, $album_id), TRUE);
        if (count($row) > 0){
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
        $member_id = intval($member_id);

        $sql = "SELECT member_id FROM %spa_photos WHERE id=%d";
        $row_member = queryDB($sql, array(TABLE_PREFIX, $photo_id), TRUE);
        
        if(count($row_member) > 0){
            return ($row_member['member_id']==$member_id);
        }
        return false;
    }


    /**
     * Get the owner of the comment
     */
    function checkCommentPriv($comment_id, $member_id, $isPhoto){
        if (isset($isPhoto)){
             $sql = "SELECT member_id FROM %spa_photo_comments WHERE id=%d";
        } else {
             $sql = "SELECT member_id FROM %spa_album_comments WHERE id=%d";
        }

        $row_comment = queryDB($sql, array(TABLE_PREFIX, $comment_id), TRUE);
        if(count($row_comment) > 0){       
            return ($row_comment['member_id']==$member_id);
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
        if(!$isPhoto){
            $sql =	"INSERT INTO %spa_album_comments (album_id, comment, member_id, created_date) VALUES (%d, '%s', %d, NOW())";
        } else {
            $sql =	"INSERT INTO %spa_photo_comments (photo_id, comment, member_id, created_date) VALUES (%d, '%s', %d, NOW())";
        }
        $result = queryDB($sql, array(TABLE_PREFIX, $id, $comment, $member_id));
        
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
        if($id<1 || $comment==''){
            return false;
        }

        if (!$isPhoto){
            $sql = "UPDATE %spa_album_comments SET comment='%s' WHERE id=%d";
        } else {
             $sql = "UPDATE %spa_photo_comments SET comment='%s' WHERE id=%d";
        }
        $result = queryDB($sql, array(TABLE_PREFIX, $comment, $id));
        return $result;
    }


    /**
     * Get comments
     * @param	int		id (can be photo_id, or album_id)
     * @param	boolean	true of it is photo_id, false otherwise.
     */
    function getComments($id, $isPhoto){
        if ($isPhoto){
            $sql = "SELECT * FROM %spa_photo_comments WHERE photo_id=%d";
        } else {
            $sql = "SELECT * FROM %spa_album_comments WHERE album_id=%d";
        }
        $sql .= ' ORDER BY created_date';
        $rows_comments = queryDB($sql, array(TABLE_PREFIX, $id));

        return $rows_comments;
    }

    /**
     * Delete photo comment 
     */
    function deleteComment($id, $isPhoto){
        if ($isPhoto){
            $sql = "DELETE FROM %spa_photo_comments WHERE id=%d";
        } else {
            $sql = "DELETE FROM %spa_album_comments WHERE id=%d";
        }
        queryDB($sql, array(TABLE_PREFIX, $id));
    }

    /**
     * Search and return list of albums, and list of photos 
     * Note: Speed and ranks are of priority here.
     * @param	Array			The unescaped array of search phrases.
     * @return	[Array, Array]	First array is albums, second array are matched photos
     */
    function search($words){
        //init
        $visible_photos = array();
        $visible_albums = array();

        //validate input
        if (!is_array($words) || empty($words)){
            return null;
        }

        //filter 
        foreach($words as $k=>$v){
            $v = trim($v);
            $query .= "(description LIKE '%%$v%%' OR name LIKE '%%$v%%' OR alt_text LIKE '%%$v%%') OR ";	//for sql
            $pattern .= $v.'|';	//regex for albums
        }
        $pattern = str_replace (array('>', '<', '/', '\\'), "", $pattern);
        $pattern = substr($pattern, 0, -1);
        
        //TODO: Optimize SQL, UNION is slow, but I think this is the fastest I can get, prove me wrong.
        //@harris
        /** Get all visible albums */
         $sql = "SELECT albums.* FROM %spa_albums albums, 
                    (SELECT ca.* FROM %scourse_enrollment enrollments
                        RIGHT JOIN %spa_course_album ca 
                        ON enrollments.course_id=ca.course_id
                        WHERE member_id=%d
                    ) AS allowed_albums
                    WHERE albums.id=allowed_albums.album_id
                UNION
                SELECT * FROM AT_pa_albums WHERE member_id=%d OR permission=1";
        $rows_albums = queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX, TABLE_PREFIX, $_SESSION['member_id'], $_SESSION['member_id']));
        
        if(count($rows_albums) == 0){
            return null;
        }
        foreach($rows_albums as $row){
            $visible_albums[$row['id']] = $row;
        }
        $visible_albums_ids = implode(', ', array_keys($visible_albums));
        
        /** Get all photos from these albums */	
        $sql = "SELECT * FROM %spa_photos WHERE album_id IN (%s)";		
        $query = ' AND ' . substr($query, 0, -3);
        $sql = $sql . $query . ' LIMIT ' . AT_PA_PHOTO_SEARCH_LIMIT;
        $rows_photos = queryDB($sql, array(TABLE_PREFIX, $visible_albums_ids));
        
        if(count($rows_photos) == 0){
            return null;
        }
        foreach($rows_photos as $row){
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
