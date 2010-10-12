<?php
/****************************************************************/
/* Atutor-OpenCaps Module						
/****************************************************************/
/* Copyright (c) 2010                           
/* Written by Antonio Gamba						
/* Adaptive Technology Resource Centre / University of Toronto
/*
/* This program is free software. You can redistribute it and/or
/* modify it under the terms of the GNU General Public License
/* as published by the Free Software Foundation.
/****************************************************************/

// load AT vitals
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_OPEN_CAPS);
require (AT_INCLUDE_PATH.'header.inc.php');

// load ATutor-OpenCaps Module Vitals 
include_once('include/vitals.inc.php');

if ($ocAtSettings['contentUrlType']==0)
{
	$contentURL = AT_BASE_HREF.'get.php/'.''.'';
	
} else if ($ocAtSettings['contentUrlType']==1){
	$contentURL = AT_BASE_HREF.'content/'.$_SESSION['course_id'].'/'.'';
} 
		
if($_SESSION['course_id']==-1)
{
	$ocAtSettings['messages'][]= $ocAtSettings['lang']['atoc_notActiveCourseError'];
}
// update project meta data 
if ($ocAtAction=='updateProject' || $ocAtAction=='deleteProject')
{
	$myProjectManager = new ATOCProjectManager();
	$activeProjectData = $myProjectManager->_addEditProject($_POST['id'],$_SESSION['login'],$_SESSION['course_id'],$_POST['name'],$_POST['mediaFile'],$_POST['captionFile'],$_POST['width'],$_POST['height'],$ocAtAction);

	if ($ocAtAction=='deleteProject')
	{
		$ocAtSettings['messages'][]= $ocAtSettings['lang']['atoc_projectDeleted'];
	} else {
		$ocAtSettings['messages'][]= $ocAtSettings['lang']['atoc_projectUpdated'];
	}
	
	$ocAtAction='';
}

// addProject: if step 1 
if ($ocAtAction=='addProject' && $_POST['step']=='1')
{
	if($_POST['ccOption']==0)
	{
		$captionFile = "";
		
	} else {
		$captionFile = $_POST['captionFile'];
	}

	// run Project Manager Class
	$myProjectManager = new ATOCProjectManager();
	$myProjectManager->_addEditProject(0, $_SESSION['login'], $_SESSION['course_id'], $_POST['projectName'], $_POST['mediaFile'], $captionFile,$_POST['width'],$_POST['height']);
	$ocAtAction='';
	$ocAtSettings['messages'][] = $ocAtSettings['lang']['atoc_projectCreated'].': <br/><i>'.$_POST['projectName'].'</i>';
	
} // end step 1

if($ocAtSettings['ocWebPath'] == '')
{
	$ocWebPath_replace = str_replace('index.php','opencaps/',$_SERVER['SCRIPT_NAME']);
	$ocAtSettings['ocWebPath'] = 'http://'.$_SERVER['HTTP_HOST'].''.$ocWebPath_replace;
}

if ($ocAtSettings['debugMode'])
{
	echo '<h1>'.$ocAtSettings['lang']['atoc_debugModeActive'].'</h1>';
}
?>
<script src="mods/AtOpenCaps/include/basic.js"></script>
<script src="mods/AtOpenCaps/include/atoc.js"></script>
<script src="mods/AtOpenCaps/flowplayer/flowplayer-3.2.2.min.js"></script>
<link rel="stylesheet" type="text/css" href="mods/AtOpenCaps/module.css">

<div id="AtOpenCaps">
	<?php
	if (count($ocAtSettings['messages'])>0)
	{
		echo '<div id="info">';
		
		for($i=0;$i<count($ocAtSettings['messages']);$i++)
		{
			echo '<p>'.$ocAtSettings['messages'][$i].'</p>';
		}
		echo '</div>';
	}

	// show AT vars
	if($ocAtSettings['showAtVars'])
	{
		AtOpenCapsDebug::_seeAlSessionVars();
	} 
	 ?>
	
	<div id="ATOC_links">
		<div id="subnavlistcontainer">
			<div id="subnavbacktopage">
				<a href="<?php echo AT_BASE_HREF; ?>mods/_core/content/index.php">
				<img height="11" border="0" width="10" style="float: left;" src="<?php echo AT_BASE_HREF; ?>images/arrowicon.gif" alt="<?php echo _AT('atoc_backToContentLinkAlt'); ?>"></a>&nbsp;
			</div>
		</div> 

	<ul id="subnavlist">
	 <li <?php if($ocAtAction=='') {?> class="active" <?php }?>><a href="mods/AtOpenCaps/index.php"><?php echo _AT('atoc_myCaptionProjectsLink'); ?>	</a></li>
	 <li <?php if($ocAtAction=='fileManager') {?> class="active" <?php }?>><a href="mods/AtOpenCaps/index.php?action=fileManager"><?php echo _AT('atoc_uploadMediaLink'); ?></a></li>
	 <li <?php if($ocAtAction=='addProject') {?> class="active" <?php }?>><a href="mods/AtOpenCaps/index.php?action=addProject&step=0"><?php echo _AT('atoc_addProjectLink'); ?></a></li>
	 <li <?php if($ocAtAction=='ccEditor') {?> class="active" <?php }?>><a href="mods/AtOpenCaps/index.php?action=ccEditor<?php 
	 		if ($_SESSION['ATOC_activeProjectId']!='')
	 		{
	 			echo '&id='.$_SESSION['ATOC_activeProjectId'];
	 		} 
	 		
	 		?>"><?php echo _AT('atoc_captionEditorLink'); ?></a></li>
	 <li <?php if($ocAtAction=='preview') {?> class="active" <?php }?>><a href="mods/AtOpenCaps/index.php?action=preview<?php 
	 		if ($_SESSION['ATOC_activeProjectId']!='')
	 		{
	 			echo '&id='.$_SESSION['ATOC_activeProjectId'];
	 		} 
	 		
	 		?>"><?php echo _AT('atoc_previewLink'); ?></a></li>
	<li <?php if($ocAtAction=='atOcAbout') {?> class="active" <?php }?>><a href="mods/AtOpenCaps/index.php?action=atOcAbout"><?php echo _AT('atoc_helpLink'); ?></a></li>
	</ul> 
	</div>

	<?php
	if ($ocAtAction=='ccEditor')
	{
		// run Project Manager Class
		if($_SESSION['ATOC_activeProjectId']!='')
		{
			$activeProjectId = $_GET['id'];
			
			// set the active id in session
			$_SESSION['ATOC_activeProjectId'] = $activeProjectId; 
			
		} else {
			// get the last project
			$myProjectManager = new ATOCProjectManager();
			$myOcProjects = $myProjectManager->_loadProjects($_SESSION['login'],$_SESSION['course_id'],0);
			$activeProjectId = $myOcProjects[0]['id']; 
			
			// get the last project
			$myProjectManager = null;
		}
		

		// set active session ID
		$myProjectManager = new ATOCProjectManager();
		$myProjectManager->_setActiveProject($activeProjectId,$_SESSION['login'],$_SESSION['course_id'],$_SESSION['token']);
		$activeProjectId .= '-'.$_SESSION['token'];

		if($ocAtSettings['debugMode'])
		{
			// testing service before sending data 
			$theServiceUrl = AT_BASE_HREF.'mods/AtOpenCaps/service.php?id='.$activeProjectId.'&action=getMedia'; 
			$theJson = file_get_contents($theServiceUrl);
			$media_info = json_decode($theJson);
			$JsonDebug = '';
			$JsonDebug .= '
			<h3>Open Caps service: getMedia</h3>
			<form name="debugJson" id="debugJson" method="post" action="">
			  <textarea name="jsonArray" cols="80" rows="10" id="jsonArray">
URL: '.$theServiceUrl;
			foreach ($media_info as $name=>$value)
			{
				$JsonDebug .= '

'.$name.': '.$value;
			} 
			$JsonDebug .='
</textarea>
			</form>
			';
			echo $JsonDebug;
		} // end debug
		
		
		$ccEditorHtml = '<div id="ATOC_editor" class="input-form">
<iframe style="overflow-y: scroll;" scrolling="no" height="780px" frameborder="0" width="100%" align="top" class="wrapper" 
src="'.$ocAtSettings['ocWebPath'].'/index.php?id='.$activeProjectId.'&athome='.AT_BASE_HREF.'" name="AtOpenCaps" id="AtOpenCaps">
'._AT('atoc_noIframeSupportedError').'
</iframe>
		</div>';
		
		echo $ccEditorHtml;
				
		
	} // end if ccEditor

	else if ($ocAtAction=='fileManager')
	{
		echo '<div id="ATOC_fileManager" class="input-form">';
		echo '
<iframe style="overflow-y: scroll;" scrolling="no" height="600px" frameborder="0" width="100%" align="top" 
src="'.AT_BASE_HREF.'mods/_core/file_manager/index.php?framed=1&popup=0" name="ATFileManager" id="ATFileManager">
'._AT('atoc_noIframeSupportedError').'
</iframe>
</div>
';
	}

	else if ($ocAtAction=='preview')
	{
		$myProjectManager = new ATOCProjectManager();
		$myPreviewProject = $myProjectManager->_loadProjects($_SESSION['login'],$_SESSION['course_id'],$_GET['id']);
		
		// set width and  height
		if ($myPreviewProject[0]['width']=='')
		{
			$playerWidth=320;
			
		} else {
			$playerWidth = $myPreviewProject[0]['width'];
		}
		
			// set width and  height
		if ($myPreviewProject[0]['height']=='')
		{
			$playerHeight=240;
			
		} else {
			$playerHeight = $myPreviewProject[0]['height'];
		}

		$playerHtml = '
<div id="ATOC_preview" class="input-form">';
		$playerHtml .='
<iframe scrolling="yes" width="100%" height="'.($playerHeight+50).'px" frameborder="0" align="top" 
src="'.AT_BASE_HREF.'mods/AtOpenCaps/player.php?mediaFile='.$contentURL.$myPreviewProject[0]['mediaFile'].'&captionFile='.$contentURL.$myPreviewProject[0]['captionFile'].'
&width='.$playerWidth.'&height='.$playerHeight.'" name="ATPlayer" id="ATPlayer">
	This option will not work correctly. 
	Unfortunately, your browser does not support inline frames.
</iframe>
</div>
';
		echo $playerHtml;		
		
	}

	else if ($ocAtAction=='addProject')
	{
	?>
	<div id="ATOC_addCcProject" class="input-form">
	<form name="addProject" id="addProject" method="post" action="<?php echo $_SERVER['PHP_SELF']?>">
	<?php	
		// step 0
		if (isset($_GET['step']) && $_GET['step']=='0')
		{
			// start server files class 
			$theServerDir = AT_CONTENT_DIR.''.$_SESSION['course_id'];
			$myServerFiles = new ServerFiles($theServerDir);
			$myFileArray = $myServerFiles->directoryToArray($theServerDir, true);
		
			// if not media files found
			if (count($myFileArray)==0)
			{
				echo '<p>'._AT('atoc_noMediaFileFound').'</p>';
				echo '<p><a href="mods/AtOpenCaps/index.php?action=fileManager">'._AT('atoc_uploadMediaMsg').'</a></p>';
			} else {
				//echo "<p>Select one of the available media files:</p>";
			?>
			  <p><strong><?php echo _AT('atoc_projectName');?>:</strong> 
			    <input name="projectName" id="projectName" value="" type="text" size="35"/>
			  </p>
  			  <p><strong><?php echo _AT('atoc_mediaName'); ?>:</strong> 
    		  <select name="mediaFile" id="mediaFile">
				<?php 
				for ($i=0;$i<count($myFileArray);$i++)
				{
					echo '<option value="'.$myFileArray[$i].'">'.$myFileArray[$i].'</option>';
				} // end for
				?>
    		  </select>
    		  </p>
    		  <p>
    		  <strong><?php echo _AT('atoc_mediaWidth'); ?>:</strong> <input name="width" id="width" value="" type="text" size="4"/>
    		   <strong><?php echo _AT('atoc_mediaHeight'); ?>:</strong> <input name="height" id="height" value="" type="text" size="4"/>
    		  </p>
			  <p><strong> 
			    <input name="ccOption" type="radio" value="0" checked>
			    <?php echo _AT('atoc_newCaptionFileMsg'); ?><br>
			    <input type="radio" name="ccOption" value="1">
			    <?php echo _AT('atoc_existCaptionFileMsg'); ?>:  
			    <input name="captionFile" id="captionFile" type="text"/>
			    </strong></p>
			  <p> 
			    <input name="step" id="step" value="1" type="hidden"/>
			    <input name="action" id="action" value="addProject" type="hidden"/>
			    <input name="addProject" type="submit" class="button" id="addProject" value="<?php echo _AT('atoc_addProjectButtonLabel');?>" />
			  </p>
			<?php				
			} // end if not files
		} // end step 0
		?>
	
</form>
</div>
<?php

	} // end addProject

	// listing my current projects
	else if ($ocAtAction=='' || (($ocAtAction=='editProject')&& $_GET['id']!=''))
	{
		//echo "<h4>My Projects</h4>";
		echo '<div id="ATOC_projects" class="input-form">';
		// run Project Manager Class
		$myProjectManager = new ATOCProjectManager();
		$myOcProjects = $myProjectManager->_loadProjects($_SESSION['login'],$_SESSION['course_id'],0);
		//print_r($myOcProjects);
		
		if (count($myOcProjects)==0)
		{
			echo '<p>'._AT('atoc_projectsNotFoundError').'.<p/>';
			echo '<p><a href="mods/AtOpenCaps/index.php?action=addProject&step=0">'._AT('atoc_addProjectLink').'</a><p/>';
		} else {
		//echo '<br/>Total Projects: '.count($myOcProjects);
			
		$myProjectsHtml = '';
		$myProjectsHtml .= '
		<form name="atocForm" id="atocForm" method="post" action="'.$_SERVER['PHP_SELF'].'">
		  <table width="100%" border="0" cellspacing="2" cellpadding="5">
		    <tr class="ATOC_labels"> 
		      <td width="40%">'._AT('atoc_projectName').'</td>
		      <td width="25%">'._AT('atoc_mediaName').'</td>
		      <td width="25%">'._AT('atoc_captionName').'</td>
		      <td width="5%">&nbsp;</td>
		      <td width="5%">&nbsp;</td>
		    </tr>
		';
			// load project in a table
			$flip = 1;
			for ($i=0; $i<count($myOcProjects);$i++)
			{
				if ($flip==1)
				{
					$bgColor = 'bgcolor="#EAEBD8"';
				} else {
					$bgColor = '';
				}
				$flip *= -1;
				
			      if($ocAtAction=='editProject' && $myOcProjects[$i]['id']==$_GET['id'])
			      {
			      	$myProjectsHtml .= '
			    <tr '.$bgColor.'>
			      	<td><a name="oc_'.$myOcProjects[$i]['id'].'"></a><input name="id" id="id" value="'.$myOcProjects[$i]['id'].'" type="hidden"/>
			      	<input name="name" id="name" value="'.$myOcProjects[$i]['name'].'" type="text" size="35"/></td>
			      	<td><input name="mediaFile" id="mediaFile" value="'.$myOcProjects[$i]['mediaFile'].'" type="text" size="35"/>
			      	<br/>'._AT('atoc_mediaWidth').': <input name="width" id="width" value="'.$myOcProjects[$i]['width'].'" type="text" size="4"/>
			      	<br/>'._AT('atoc_mediaHeight').': <input name="height" id="height" value="'.$myOcProjects[$i]['height'].'" type="text" size="4"/>
			      	</td>
			      	<td><input name="captionFile" id="captionFile" value="'.$myOcProjects[$i]['captionFile'].'" type="text" size="35"/></td>
			      	<td><input name="editProject" type="submit" class="button" id="editProject" value="'._AT('atoc_saveProjectButtonLabel').'" />
			      	<input name="action" id="action" value="updateProject" type="hidden"/>
			      	</td>
			      	<td><input name="deleteProject" type="button" class="button" id="deleteProject" value="'._AT('atoc_deleteProjectButtonLabel').'" onClick="confirmDelete(\'action\')" /></td>
			    </tr>
			      	
			      	';
			      
			      } else {
				      $myProjectsHtml .= '
				 <tr '.$bgColor.'>'.
				    '<td class="ATOC_projecLink"><a href="mods/AtOpenCaps/index.php?action=ccEditor&id='.$myOcProjects[$i]['id'].'">'.$myOcProjects[$i]['name'].'</a></td>
				    <td><a href="'.$contentURL.''.$myOcProjects[$i]['mediaFile'].'">'.$myOcProjects[$i]['mediaFile'].'</a>&nbsp;</td>
				    <td><a href="'.$contentURL.''.$myOcProjects[$i]['captionFile'].'">'.$myOcProjects[$i]['captionFile'].'</a>&nbsp;</td>
			      	<td><a href="'.$_SERVER['PHP_SELF'].'?action=editProject&id='.$myOcProjects[$i]['id'].'#'.'oc_'.$myOcProjects[$i]['id'].'">['._AT('atoc_editProjectLink').']</a></td>
			      	<td><a href="'.$_SERVER['PHP_SELF'].'?action=preview&id='.$myOcProjects[$i]['id'].'">['._AT('atoc_previewProjectLink').']</a></td>
			    </tr>
				     ';
			      } // end if edit project
			            
			} // end for
			
			
		$myProjectsHtml .= '
		  </table>
		  </form>
		';
		echo $myProjectsHtml;
		
		} //  end if not projects found
		
		echo '</div>';
	} // end listing 
	
	// if about/help
	else if ($ocAtAction == 'atOcAbout')
	{
		$atOc_readme = file_get_contents('README');
		$atOc_readme = str_replace(chr(13),'<br/>',$atOc_readme);		
		$atOc_readme = str_replace(chr(32),'&nbsp;',$atOc_readme);
		$atOc_readme = str_replace(chr(9),'&nbsp;&nbsp;&nbsp;',$atOc_readme);
		$atOcAboutHtml = '<div id="ATOC_about" class="input-form">
		<p>'.$atOc_readme.'</p>
		
		</div>
		';
		echo $atOcAboutHtml;
		
	} // end if about/help
	?>

</div>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>