<?php
// input string. DO NOT CHANGE.
global $_input, $_content_base_href;

//Output for flowplayer module.
$media_replace = array();
$media_matches = array();
$flowplayerholder_class = "atutor.flowplayerholder";  // style class used to play flowplayer medias
$flowplayerholder_def = '$f("*.'.$flowplayerholder_class.'"';   // javascript definition for atutor.flowplayerholder 

// .flv
preg_match_all("#\[media[0-9a-z\|]*\]([.\w\d]+[^\s\"]+)\.flv\[/media\]#i",$_input,$media_matches[],PREG_SET_ORDER);
$media_replace[] ="  <div>\n".
                  "    <a class=\"".$flowplayerholder_class."\" style=\"display:block;width:##WIDTH##px;height:##HEIGHT##px;\" href=\"##MEDIA1##.flv\"></a>\n".
                  "  </div>\n".
                  "  <div style=\"margin-top:-2em;\">\n".
                  "    <a href=\"##MEDIA1##.flv\">##MEDIA1##.flv</a>\n".
                  "  </div>\n";

// .mp4
preg_match_all("#\[media[0-9a-z\|]*\]([.\w\d]+[^\s\"]+)\.mp4\[/media\]#i",$_input,$media_matches[],PREG_SET_ORDER);
//$media_replace[] ="<a class=\"".$flowplayerholder_class."\" style=\"display:block;width:##WIDTH##px;height:##HEIGHT##px;\" href=\"".AT_BASE_HREF."get.php/".$_content_base_href."##MEDIA1##.mp4\"></a>";
$media_replace[] ="  <div>\n".
                  "    <a class=\"".$flowplayerholder_class."\" style=\"display:block;width:##WIDTH##px;height:##HEIGHT##px;\" href=\"##MEDIA1##.mp4\"></a>\n".
                  "  </div>\n".
                  "  <div style=\"margin-top:-3em;\">\n".
                  "    <a href=\"##MEDIA1##.mp4\">##MEDIA1##.mp4</a>\n".
                  "  </div>\n";

//// .mov
//preg_match_all("#\[media[0-9a-z\|]*\]([.\w\d]+[^\s\"]+)\.mov\[/media\]#i",$_input,$media_matches[],PREG_SET_ORDER);
//$media_replace[] ="<a class=\"".$flowplayerholder_class."\" style=\"display:block;width:##WIDTH##px;height:##HEIGHT##px;\" href=\"".AT_BASE_HREF."get.php/".$_content_base_href."##MEDIA1##.mov\"></a>";
//
//// .mp3
//preg_match_all("#\[media[0-9a-z\|]*\](.+[^\s\"]+)\.mp3\[/media\]#i",$_input,$media_matches[],PREG_SET_ORDER);
//$media_replace[] ="<a class=\"".$flowplayerholder_class."\" style=\"display:block;width:##WIDTH##px;height:##HEIGHT##px;\" href=\"".AT_BASE_HREF."get.php/".$_content_base_href."##MEDIA1##.mp3\"></a>";

// Executing the replace
for ($i=0;$i<count($media_replace);$i++){
	foreach($media_matches[$i] as $media)
	{
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

// Include the javascript only if:
// 1. $flowplayerholder_class is used but not defined
// 2. exclude from export common cartridge or content package
if (strpos($_input, $flowplayerholder_class) 
    && !strpos($_input, $flowplayerholder_def)
    && !strpos($_SERVER['PHP_SELF'], "ims_export.php"))
{
	$_input .= '<script type="text/javascript">
'.$flowplayerholder_def.', "'.AT_BASE_HREF.'mods/_standard/flowplayer/flowplayer-3.2.4.swf", { 
  clip: { 
  autoPlay: false,
  baseUrl: \''.AT_BASE_HREF.'get.php/'.$_content_base_href.'\'},
  plugins:  { 
    controls: { 
      buttons:true, 
      play: true,  
      scrubber: true, 
      autoHide:false
    }         
  }
});
</script>'."\n";
}
?>