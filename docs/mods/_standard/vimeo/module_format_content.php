<?php

global $_input, $_content_base_href;

$media_replace = array();
$media_matches = array();

// vimeo videos
preg_match_all("/\[media(\|([0-9]+)\|([0-9]+))?\]http\:\/\/[0-9a-z.]*vimeo\.com\/(.*)\[\/media\]/i",$_input,$media_matches,PREG_SET_ORDER);

foreach ($media_matches as $media_match) {
	$width = $media_match[2];
	$height = $media_match[3];
	if($width == ''){
		$width = "425";
	}
	if($height == ''){
		$height = "350";
	}
	$video_id = $media_match[4];

	$media_replace ='<iframe src="http://player.vimeo.com/video/'.$video_id.'" width="'.$width.'" height="'.$height.'" frameborder="0"></iframe>';
	$_input = str_replace($media_match[0],$media_replace,$_input);
}

?>

