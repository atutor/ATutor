<?php
/**
 * Scc Class
 */
class Scc extends CaptionFormat
{
	private $textStyles = array();

	/**
	 * Imports a caption string into a CaptionCollection 
	 *
	 * @param String $theCCString the caption file as string
	 * @return CaptionCollection $myCcCollection A CaptionCollection Object
	 */
	public function importCC($theCCString)
	{
		$ccTarget = '';
		
	} // end importCC()

	/**
	 * Exports a CaptionCollection object into a string
	 *
	 * @param CaptionCollection $theCollection A CaptionCollection Object
	 * @return String $captionString The caption as a String
	 */
	public function exportCC($theCollection)
	{
		/*
		 * <br/>Based on the SCC convertion class by Colin McFadden at <br/>
		 * <a href="http://blog.lib.umn.edu/mcfa0086/discretecosine/2008_10.html">http://blog.lib.umn.edu/mcfa0086/discretecosine/2008_10.html</a>'; 
		*/
		
		$ccExport = '';
		
		$myCollection = $theCollection->getCollection();
		
		// include SCC external convertor
		include("include/classes/ext_SccConvert_class.php");
		
		$startTC = "00:00:00:00";
		
		//create a SCC captionConvert instance
		$sccCaptions = new captionConvert($startTC);


		// Building Sami caption 
		foreach ($myCollection as $captionObj)
		{
	
			//$capCount++;

	        $beginTime = $captionObj->getInTime();
	        $captionContents = $captionObj->getCaption();
	        
	        // clean any xml style
	        $toSearch = array('<b>','</b>','<u>','</u>','<i>','</i>','<br/>','<br>','<BR/>','\n',chr(10)); 
	        $toReplace = '';
			$captionContents = str_replace($toSearch,$toReplace,$captionContents);

			// add SCC captions 
	        $sccCaptions->addCaption($beginTime, $captionContents);
			
			//$fixCap = CcUtil::ccNewLineToBr($captionObj->getCaption(),' <br/>');
       
		} // end for each caption 
				
		// ading caption to String 
		$ccExport .= ''.$sccCaptions->outputCaptions();

		
		return $ccExport;
		
	} // end  exportCC()
	
	/**
	 * Verify if the caption file is a SAMI caption file 
	*/
	public function checkFormat($theCCString)
	{
		
	} // end checkFormat()

	/*
	 * Here functions to re-define
	 */
	public function getName()
	{
		return 'SCC - Scenarist Closed Caption';
	}
	
	public function getAbout()
	{
		return 'This caption format is one of the standards in the TV industry. <br>SCC is used by M4V movies targeted for the Apple iPhone, iPod Touch and iPod Nano.<br/> As documented in <a href="http://ncam.wgbh.org/mm/m4vcaps.html">http://ncam.wgbh.org/mm/m4vcaps.html</a>';
	}
	
	public function getVersion()
	{
		return '1.0';
	}
		
	public function getFileExtension()
	{
		return 'scc';
	}
	
	public function getIdPattern()
	{
		$idPattern = '/(Scenarist_SCC V1.0)/';
		
		return $idPattern;
	}

	public function allowsTextStyles()
	{
		return '0';
	}
	
	public function template()
	{
		$ccTemplate = '
Scenarist_SCC V1.0

00:00:00:00 942c 942c

00:00:00:-21 94ae 94ae 9420 9420 10d0 10d0 a8c4 d629 2020 57e5 ece3 ef6d e520 f4ef 2061 2080 13d0 13d0 64e5 6def 6e73 f4f2 61f4 e9ef 6e20 efe6 2061 e3e3 e573 73e9 62ec e520 1370 1370 76e9 64e5 efae 2054 e5f8 f420 bc62 3e62 efec 6420 54e5 f8f4 2020 942f 942f

00:00:02:00 94ae 94ae 9420 9420 10d0 10d0 a8c4 d629 2020 5468 e973 20e9 7320 6120 e3ef 6d6d e5f2 e3e9 61ec 2080 13d0 13d0 e6ef f220 c7f2 6170 e56e 75f4 7320 e3e5 f2e5 61ec 2c20 942f 942f

00:00:04:03 94ae 94ae 9420 9420 10d0 10d0 a8c4 d629 2020 f7e9 f468 2061 6464 e564 20e3 ecef 73e5 6420 13d0 13d0 e361 70f4 e9ef 6e73 2020 942f 942f

00:00:05:06 94ae 94ae 9420 9420 10d0 10d0 a8c4 d629 2020 616e 6420 64e5 73e3 f2e9 70f4 e976 e520 76e9 64e5 ef2c 2020 13d0 13d0 2075 73e9 6e67 2073 70e5 e5e3 6820 7379 6ef4 68e5 73e9 7320 1370 1370 f4e5 e368 6eef ecef 6779 ae20 942f 942f

00:00:08:03 94ae 94ae 9420 9420 10d0 10d0 a8c4 d629 2020 c4e5 7075 f479 20c2 61f2 6ee5 7920 46e9 e6e5 2080 13d0 13d0 e56e f4e5 f273 2020 20f4 68e5 20cd 6179 62e5 f2f2 7920 1370 1370 d368 e5f2 e9e6 e6a7 7320 4fe6 e6e9 e3e5 2c20 942f 942f

00:00:11:02 94ae 94ae 9420 9420 10d0 10d0 a8c4 d629 2020 f7e9 f468 2061 20f4 f261 7920 e3ef 6ef4 61e9 6ee9 6e67 2020 13d0 13d0 f468 e520 c7f2 6170 e56e 75f4 7320 62f2 e561 6be6 6173 f4ae 2080 942f 942f

00:00:12:28 94ae 94ae 9420 9420 10d0 10d0 a8c4 d629 2020 d368 e5f2 e9e6 e620 c16e 6479 2054 6179 ecef f220 e973 2080 13d0 13d0 ece5 616e e96e 6720 20ef 6e20 f468 e520 e6f2 ef6e f420 efe6 2068 e973 2080 1370 1370 64e5 736b 2c20 942f 942f

00:00:15:21 94ae 94ae 9420 9420 10d0 10d0 a8c4 d629 2020 f2e5 6164 e96e 6720 f468 e520 6ee5 f773 7061 70e5 f2ae 2080 942f 942f

';
		
		return $ccTemplate;
	}

	
} // end Scc Class
?>