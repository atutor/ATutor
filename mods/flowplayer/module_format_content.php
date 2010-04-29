<?php
// input string. DO NOT CHANGE.
global $_input, $_content_base_href;

//Output for flowplayer module.

// .flv - uses Flowplayer 3.0 from flowplayer.org (playing file via full URL)
preg_match_all("#\[media[0-9a-z\|]*\]http://([\w\./-]+)\.flv\[/media\]#i",$_input,$media_matches[0],PREG_SET_ORDER);
$media_replace[0] ="<a class=\"flowplayerholder\"
style=\"display:block;width:##WIDTH##px;height:##HEIGHT##px;\"
href=\"http://##MEDIA1##.flv\">
</a>";

// .flv - uses Flowplayer 3.0 from flowplayer.org (playing file from AT_content_dir)
preg_match_all("#\[media[0-9a-z\|]*\]([\w\./-]+)\.flv\[/media\]#i",$_input,$media_matches[1],PREG_SET_ORDER);
$media_replace[1] ="<a class=\"flowplayerholder\"
style=\"display:block;width:##WIDTH##px;height:##HEIGHT##px;\"
href=\"".AT_BASE_HREF."get.php/".$_content_base_href."##MEDIA1##.flv\">
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
		$f("a.flowplayerholder", "'.AT_BASE_HREF.'/mods/flowplayer/flowplayer-3.1.2.swf", { 
		 	clip: { autoPlay: false },  		
	        plugins:  { 
		        controls: { 
		            all: false,  
		            play: true,  
		            scrubber: true 
		        }         
		    }
		});
	</script>
	';
}
?>