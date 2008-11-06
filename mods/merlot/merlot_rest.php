<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: merlot.php 6614 2006-09-27 19:32:29Z greg $

// Check if links is turned on for this course.




$url = $merlot_location."?licenseKey=".$_GET['licenseKey'];
if($_GET['keywords'] != ""){
$keyword = "&keywords=".$_GET['keywords'];
$url .=$keyword;
}



$xml_results = file_get_contents($url);

debug($xml_results);

//$p = xml_parser_create();
//xml_parse_into_struct($p, $xml_results, &$vals, &$index);
//xml_parse($xml_results);



//xml_parser_free($p);

?>

<h2>Results <?php echo $index['TOTALCOUNT']['0']; ?></h2> 

<?php
/*
foreach($index['RESULTS'] as $result){

echo $result;

}

foreach($vals as $records => $record){

echo $record['title']."";
}

echo "<pre>";

echo "Index array\n";
debug($index);
echo "\nVals array\n";
debug($vals);
echo "</pre>";

*/
$xml_parser = xml_parser_create( 'UTF-8' ); // UTF-8 or ISO-8859-1
xml_parser_set_option( $xml_parser, XML_OPTION_CASE_FOLDING, 0 );
xml_parser_set_option( $xml_parser, XML_OPTION_SKIP_WHITE, 1 );
xml_parse_into_struct( $xml_parser, $xml_results, $vals );
xml_parser_free($xml_parser);
//debug($aryXML);
//now use aryXML array to xml string:
        $o='';
foreach( $vals as $tag ){
    //tab space:
    for($i=1; $i < $tag['level'];$i++)
        $o.="\t";
    if($tag['type']!='close'){
        if($tag['type']!='cdata')
            $o.='<'.$tag['tag'];
        if(isset($tag['attributes'])){
            foreach($tag['attributes'] as $attr=>$aval){
                $o.=' '.$attr.'="'.$aval.'"';
            }
        }
        if($tag['type']!='cdata'){
            $o.=($tag['type']=='complete' && (!isset($tag['value']) || $tag['value']==''))?'/>':'>';
        }
        $o.=(isset($tag['value']))?$tag['value']:'';
        if($tag['type']!='cdata'){
            $o.=($tag['type']=='complete' && (isset($tag['value']) && $tag['value']<>''))?'</'.$tag['tag'].'>':'';
        }
        $o.="\n";
    }else{
        $o.='</'.$tag['tag'].'>'."\n";
    }
}
?>

<?php
//debug($o);
$file=$xml_results;

//debug($file);
$file = str_replace("<?xml version=\"1.0\" encoding=\"UTF-8\"?>" ,"<?xml version=\"1.0\" encoding=\"UTF-8\"?><?xml-stylesheet type=\"text/xsl\" href=\"".$_base_href."mods/merlot/result.xsl\"?>", $file);
//debug($file);
//$file = preg_replace("\<?", "\<?", $file);



if($handle = fopen(AT_CONTENT_DIR."tmp_merlot_results.xml", "w+")){

fwrite($handle, $file);
fclose($handle);

}else{
echo "failed";

}
$merlot_results = $_base_href."content/tmp_merlot_results.xml";
//echo file_get_contents("http://localhost/atutorsvn/mods/merlot/test.xml");
?>


