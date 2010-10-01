<?php
// input string. DO NOT CHANGE.
global $_input, $_content_base_href;

//Output for flowplayer module.
$media_replace = array();
$media_matches = array();
// .flv - uses Flowplayer 3.0 from flowplayer.org (playing file via full URL)
preg_match_all("#\[media[0-9a-z\|]*\]http://([\w\./-]+)\.flv\[/media\]#i",$_input,$media_matches[],PREG_SET_ORDER);
$media_replace[] ="<a class=\"flowplayerholder\"
style=\"display:block;width:##WIDTH##px;height:##HEIGHT##px;\"
href=\"http://##MEDIA1##.flv\">
</a>";

// .flv - uses Flowplayer 3.0 from flowplayer.org (playing file from AT_content_dir)
preg_match_all("#\[media[0-9a-z\|]*\]([\w\./-]+)\.flv\[/media\]#i",$_input,$media_matches[],PREG_SET_ORDER);
$media_replace[] ="<a class=\"flowplayerholder\"
style=\"display:block;width:##WIDTH##px;height:##HEIGHT##px;\"
href=\"".AT_BASE_HREF."get.php/".$_content_base_href."##MEDIA1##.flv\">
</a>";

// .mp4 - uses Flowplayer 3.0 from flowplayer.org (playing file via full URL)
preg_match_all("#\[media[0-9a-z\|]*\]http://([\w\./-]+)\.mp4\[/media\]#i",$_input,$media_matches[],PREG_SET_ORDER);
$media_replace[] ="<a class=\"flowplayerholder\"
style=\"display:block;width:##WIDTH##px;height:##HEIGHT##px;\"
href=\"http://##MEDIA1##.mp4\">
</a>";

// .mp4 - uses Flowplayer 3.0 from flowplayer.org (playing file from AT_content_dir)
preg_match_all("#\[media[0-9a-z\|]*\]([\w\./-]+)\.mp4\[/media\]#i",$_input,$media_matches[],PREG_SET_ORDER);
$media_replace[] ="<a class=\"flowplayerholder\"
style=\"display:block;width:##WIDTH##px;height:##HEIGHT##px;\"
href=\"".AT_BASE_HREF."get.php/".$_content_base_href."##MEDIA1##.mp4\">
</a>";

// .mov - uses Flowplayer 3.0 from flowplayer.org (playing file via full URL)
preg_match_all("#\[media[0-9a-z\|]*\]http://([\w\./-]+)\.mov\[/media\]#i",$_input,$media_matches[],PREG_SET_ORDER);
$media_replace[] ="<a class=\"flowplayerholder\"
style=\"display:block;width:##WIDTH##px;height:##HEIGHT##px;\"
href=\"http://##MEDIA1##.mov\">
</a>";

// .mov - uses Flowplayer 3.0 from flowplayer.org (playing file from AT_content_dir)
preg_match_all("#\[media[0-9a-z\|]*\]([\w\./-]+)\.mov\[/media\]#i",$_input,$media_matches[],PREG_SET_ORDER);
$media_replace[] ="<a class=\"flowplayerholder\"
style=\"display:block;width:##WIDTH##px;height:##HEIGHT##px;\"
href=\"".AT_BASE_HREF."get.php/".$_content_base_href."##MEDIA1##.mov\">
</a>";

// .mp3 - uses Flowplayer 3.0 from flowplayer.org (playing file via full URL)
preg_match_all("#\[media[0-9a-z\|]*\]http://([\w\./-]+)\.mp3\[/media\]#i",$_input,$media_matches[],PREG_SET_ORDER);
$media_replace[] ="<a class=\"flowplayerholder\"
style=\"display:block;width:##WIDTH##px;height:##HEIGHT##px;\"
href=\"http://##MEDIA1##.mp3\">
</a>";

// .mp3 - uses Flowplayer 3.0 from flowplayer.org (playing file from AT_content_dir)
preg_match_all("#\[media[0-9a-z\|]*\]([\w\./-]+)\.mp3\[/media\]#i",$_input,$media_matches[],PREG_SET_ORDER);
$media_replace[] ="<a class=\"flowplayerholder\"
style=\"display:block;width:##WIDTH##px;height:##HEIGHT##px;\"
href=\"".AT_BASE_HREF."get.php/".$_content_base_href."##MEDIA1##.mp3\">
</a>";


$has_flv = false;
// Executing the replace
for ($i=0;$i<count($media_replace);$i++){
	foreach($media_matches[$i] as $media)
	{
		if (is_array($media)) $has_flv = true;
		
		//find width and height for each matched media
		if (preg_match("/\[media\|([0-9]*)\|([0-9]*)\]*/", $media[0], $matches)) 
		{
			$width = $matches[1];
			$height = $matches[2];
		}
		else
		{
			$width = 425;
			$height = 350;
		}
		
		//replace media tags with embedded media for each media tag
		$media_input = $media_replace[$i];
		$media_input = str_replace("##WIDTH##","$width",$media_input);
		$media_input = str_replace("##HEIGHT##","$height",$media_input);
		$media_input = str_replace("##MEDIA1##","$media[1]",$media_input);
		$media_input = str_replace("##MEDIA2##","$media[2]",$media_input);
		$_input = str_replace($media[0],$media_input,$_input);
	}
}

if ($has_flv)
{
	$_input .= '
	<script language="JavaScript">
		$f("*.flowplayerholder", "'.AT_BASE_HREF.'mods/_standard/flowplayer/flowplayer-3.2.5.swf", { 
		 	clip: { autoPlay: false },  		
	        plugins:  { 
		        controls: { 
		            buttons:true, 
		            play: true,  
		            scrubber: true 
		        }         
		    }
		});
	</script>
	';
}
?>