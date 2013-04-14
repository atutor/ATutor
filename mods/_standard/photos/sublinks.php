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

if (!defined('AT_INCLUDE_PATH')) { exit; }
require(AT_PA_INCLUDE.'classes/PhotoAlbum.class.php');
require(AT_PA_INCLUDE.'lib.inc.php');

//Comparison Function, return reverse results 
function cmp($a, $b){
	$a = strtotime($a['created_date']);
	$b = strtotime($b['created_date']);
	if ($a==$b){
		return 0;
	}
	 return ($a < $b) ? 1 : -1;
}

global $db, $_base_href;

$record_limit = 3;		//Numero massimo dei possibili sottocontenuti visualizzabili nella home-page
$cnt = 0;               // count number of returned forums

//grab comments and if new course album/photo
$pa = new PhotoAlbum();
$visible_albums = array_merge($pa->getAlbums($_SESSION['member_id'], AT_PA_TYPE_COURSE_ALBUM),
					$pa->getAlbums($_SESSION['member_id'], AT_PA_TYPE_MY_ALBUM));
//check if there are any albums
if (empty($visible_albums)){
	return 0;
}

foreach($visible_albums as $album){
	$album_ids .= $album['id'] . ', ';
}
$album_ids = substr($album_ids, 0, strlen($albums_ids) - 2);

//album comments
$sql = 'SELECT * FROM '.TABLE_PREFIX.'pa_album_comments WHERE album_id IN ('.$album_ids.') ORDER BY created_date DESC';
$result = mysql_query($sql, $db);
while($row = mysql_fetch_assoc($result)){
	$all_comments[] = $row;
}

//photo comments
$sql = 'SELECT c.*, p.album_id FROM '.TABLE_PREFIX.'pa_photo_comments c LEFT JOIN '.TABLE_PREFIX.'pa_photos p ON p.id=c.photo_id WHERE p.album_id IN ('.$album_ids.') ORDER BY created_date DESC';
$result = mysql_query($sql, $db);
while($row = mysql_fetch_assoc($result)){
	$all_comments[] = $row;
}

if (empty($all_comments)){
	return 0;
}

//sort the comments by date in decrement order
uasort($all_comments, 'cmp');

//assign proper link to the comment list.
foreach($all_comments as $comment){
	if (isset($comment['photo_id'])){
		$list[] = _AT('comment').': <a href="'.$_base_href.AT_PA_BASENAME.'photo.php?aid='.$comment['album_id'].SEP.'pid='.$comment['photo_id'].'">'.AT_print($comment['comment'], 'photos.comment').'</a>';
	} elseif (isset($comment['album_id'])){
		$list[] = _AT('comment').': <a href="'.$_base_href.AT_PA_BASENAME.'albums.php?id='.$comment['album_id'].'">'.AT_print($comment['comment'], 'photos.comment').'</a>';
	}
	if (++$cnt >= $record_limit) break;
}

if (count($list) > 0) {
	return $list;
} else {
	return 0;
}
?>
