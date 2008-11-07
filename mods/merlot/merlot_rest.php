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
// $Id: merlot_rest.php 6614 2006-09-27 19:32:29Z greg $

require("./classes/MerlotResultParser.class.php");

$keywords = trim($_POST['keywords']);
$title = trim($_POST['title']);
$description = trim($_POST['description']);
$author = trim($_POST['author']);
$num_of_results = intval(trim($_POST['num_of_results']));

if($keywords <> "" || $title <> "" || $description <> "" || $author <> "" || $_POST["creativeCommons"] == "true")
{
	if ($keywords <> "") $url_search = "&keywords=".urlencode($keywords);
	if ($title <> "") $url_search .= "&title=".urlencode($title);
	if ($description <> "") $url_search .= "&description=".urlencode($description);
	if ($author <> "") $url_search .= "&author=".urlencode($author);
	if ($_POST["search_type"] == 0) $url_search .= "&allKeyWords=true";
	if ($_POST["search_type"] == 1) $url_search .= "&anyKeyWords=true";
	if ($_POST["search_type"] == 2) $url_search .= "&exactPhraseKeyWords=true";
	if ($_POST["creativeCommons"] == "true") $url_search .= "&creativeCommons=true";
	if ($num_of_results > 25 || $num_of_results == 0) $url_search .= "&size=".$default_num_of_results;
	else $url_search .= "&size=".$num_of_results;

	$url = $_config['merlot_location']."?licenseKey=".$_config['merlot_key'].$url_search;

	$xml_results = file_get_contents($url);
	
	if (!$xml_results)
	{
		$infos = array('CANNOT_CONNECT_SERVER', $_config['merlot_location']);
		$msg->addInfo($infos);
	}
	else
	{
		$MerlotResultParser =& new MerlotResultParser();
		$MerlotResultParser->parse($xml_results);
		$result_list = $MerlotResultParser->getParsedArray();
//		debug($result_list);
		if ($result_list['status'] == 'failed')  // failed, display error
			echo "<span style='color:red'>"._AT('error').": ".$result_list['error']."</span>";
		else  // success, display results
		{
//			debug($result_list);
			if (is_array($result_list))
			{
				echo '	<div id="search_results">';
				echo "		<h2>". _AT('results')." <small>(".$result_list["summary"]["resultCount"]." out of ".$result_list["summary"]["totalCount"].")</small></h2>";
				foreach ($result_list as $key=>$result)
				{
					if (is_int($key))
					{
?>

		<dl class="browse-course">

			<dt></dt>
			<dd><h3><a href="<?php echo $result['detailURL']; ?>"><?php echo $result['title']; ?></a></h3></dd>
						
			<dt><?php echo _AT("author"); ?></dt>
			<dd><?php echo $result['authorName']; ?></dd>

			<dt><?php echo _AT("merlot_creation_date"); ?></dt>
			<dd><?php echo $result['creationDate']; ?></dd>

			<dt><?php echo _AT("description"); ?></dt>
			<dd><?php if (strlen($result['description']) > 120) echo substr($result['description'], 0, 120). "..."; ?></dd>

			<dt><?php echo _AT("merlot_creative_commons"); ?></dt>
			<dd><?php if ($result['creativeCommons'] == "true") echo _AT('yes'); else echo _AT('no'); ?></dd>
		</dl>
		<br />
<?php
					}
				}
				echo '</div>';
			}
		}
	}
}
?>