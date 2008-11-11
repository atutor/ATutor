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

require("classes/MerlotResultParser.class.php");

$keywords = trim($_REQUEST['keywords']);
$title = trim($_REQUEST['title']);
$description = trim($_REQUEST['description']);
$author = trim($_REQUEST['author']);
$results_per_page = intval(trim($_REQUEST['results_per_page']));

if($keywords <> "" || $title <> "" || $description <> "" || $author <> "" || $_REQUEST["creativeCommons"] == "true")
{
	$page = intval($_REQUEST['p']);
	if (!$page) {
		$page = 1;
	}	

	if ($results_per_page > $default_results_per_page || $results_per_page == 0)
		$results_per_page = $default_results_per_page;
	
	$page_str = "results_per_page=".$results_per_page;
	$url_search = "&size=".$results_per_page."&firstRecNumber=".$results_per_page*($page - 1);

	if ($keywords <> "")
	{
		$page_str .= SEP."keywords=".urlencode($keywords);
		$url_search .= "&keywords=".urlencode($keywords);
	}
	if ($title <> "") 
	{
		$page_str .= SEP."title=".urlencode($title);
		$url_search .= "&title=".urlencode($title);
	}
	if ($description <> "") 
	{
		$page_str .= SEP. "description=".urlencode($description);
		$url_search .= "&description=".urlencode($description);
	}
	if ($author <> "") 
	{
		$page_str .= SEP. "author=".urlencode($author);
		$url_search .= "&author=".urlencode($author);
	}
	
	if (isset($_REQUEST["search_type"])) 
		$page_str .= SEP."search_type=".$_REQUEST["search_type"];
	
	if ($_REQUEST["search_type"] == 0) $url_search .= "&allKeyWords=true";
	if ($_REQUEST["search_type"] == 1) $url_search .= "&anyKeyWords=true";
	if ($_REQUEST["search_type"] == 2) $url_search .= "&exactPhraseKeyWords=true";
	if ($_REQUEST["creativeCommons"] == "true") 
	{
		$page_str .= SEP. "creativeCommons=true";
		$url_search .= "&creativeCommons=true";
	}
	
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

		if ($result_list['status'] == 'failed')  // failed, display error
			echo "<span style='color:red'>"._AT('error').": ".$result_list['error']."</span>";
		else  // success, display results
		{
			if (is_array($result_list))
			{
				$num_results = $result_list["summary"]["totalCount"];
				$num_pages = max(ceil($num_results / $results_per_page), 1);
				
				echo '	<div id="search_results">';
				echo "		<h2>". _AT('results')." <small>(".$result_list["summary"]["resultCount"]." out of ".$num_results.")</small></h2>";

				print_paginator($page, $num_results, htmlspecialchars($page_str), $results_per_page);

				foreach ($result_list as $key=>$result)
				{
					if (is_int($key))
					{
?>

		<dl class="browse-result">

			<dt></dt>
			<dd><h3><a href="<?php echo $result['detailURL']; ?>"><?php echo htmlspecialchars($result['title']); ?></a></h3></dd>
						
			<dt><?php echo _AT("author"); ?></dt>
			<dd><?php if ($result['authorName']=='') echo _AT('unknown'); else echo htmlspecialchars($result['authorName']); ?></dd>

			<dt><?php echo _AT("merlot_creation_date"); ?></dt>
			<dd><?php echo date('Y-m-d', round($result['creationDate']/1000)); ?></dd>

			<dt><?php echo _AT("description"); ?></dt>
			<dd><?php if ($result['description']=='') echo _AT('na'); else if (strlen($result['description']) > 120) echo substr(htmlspecialchars($result['description']), 0, 120). "..."; else echo htmlspecialchars($result['description']); ?></dd>

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