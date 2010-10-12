<?php
class TimeUtil 
{
	
	/**
	 * Converts milliseconds time mark into 00:00:00:000 format (QT native format)
	 *
	 * @param int $miliSecTime A time format in miliseconds (e.g. 26070) 1000 = 1 second
	 * @return String $qtTimeString A QT mark in 00:00:00:000 format
	 */
	static public function timeSamiToQt($miliSecTime)
	{
		$qtTimeString = '';
		//  milliseconds holder 
		$myMiliSecTime = $miliSecTime;
		
		// basic constant knowledge, values in milliseconds
		$anHour = 3600000; // 60*60*1000
		$aMin = 60000; // 60*1000
		$aSec = 1000; // 1 * 1000
		
		// temp holders for to store equivalent  hh, mm, sec, milisec
		$myHours = 0;
		$myMins = 0;
		$mySec = 0;
				//$myMsec = 0; /// not using it.!!! 
		
        // initialize timeArray
		$timeArray = array();
		$timeArray['hour'] = '';
		$timeArray['min'] = '';
		$timeArray['sec'] = '';
		$timeArray['msec'] = '';		
		
		// parsing millisecodns QT time format 00:00:00.000

		// is time mark at least an hour?
		if($miliSecTime>=$anHour)
		{
			// get only the int value
			$myHours = intval($miliSecTime/$anHour);

			// set the tot milliseconds left after removing number of hour(s) 
			$myMiliSecTime -= ($myHours*$anHour);

			// set the current hours add leading 0 if needed
			if ($myHours<10)
			{
				$timeArray['hour'] = '0'.$myHours;
			} else {
				$timeArray['hour'] = ''.$myHours; 
			}
		} else {
			$timeArray['hour'] = '00';
		}
		
		// Is time mark at least a minute? or rather, how many minutes are left?
		if($myMiliSecTime>=$aMin-1)
		{
			// get only the int value
			$myMins = intval($myMiliSecTime/$aMin);
						
			// set the milliseconds left after removing total of minute(s) 
			$myMiliSecTime -= ($myMins*$aMin);
			
			// set the current minutes and add leading 0 if needed
			if ($myMins<10)
			{
				$timeArray['min'] = '0'.$myMins;
			} else {
				$timeArray['min'] = ''. $myMins;
			}
		} else {
			$timeArray['min'] = '00';
		}
		
		// does it have seconds, or rather, how many seconds are left
		if($myMiliSecTime>=$aSec)
		{
			// get only the int value
			$mySec = intval($myMiliSecTime/$aSec);

			// set the milliseconds left after removing total of seconds
			$myMiliSecTime -= ($mySec*$aSec);
			 
			// set the current number of seconds in time array, and add leading 0 if needed
			if ($mySec<10)
			{
				$timeArray['sec'] = '0'.$mySec;
			} else {
				$timeArray['sec'] = ''.$mySec;
			}
		} else {
			$timeArray['sec'] = '00';
		}
		
		// here a fix for adding leading zeros to milliseconds (e.g. 1=001, 10=010)  
		if($myMiliSecTime>0)
		{
			$tempMilliSec = 0 + (0.001 * $myMiliSecTime);
			$tempMilliSecArray = explode('.',$tempMilliSec); // split using '.' as separator
			$myMiliSecTimeString = ''. $tempMilliSecArray[1]; // get only the decimal value as a string

			// add one zero after the sting value if $myMiliSecTimeString has 2 character
			if (strlen($myMiliSecTimeString)==2)
			{
				$myMiliSecTimeString .= '0';
			} 
			// add two zeros after the sting value if $myMiliSecTimeString has 1 character
			else if (strlen($myMiliSecTimeString)==1)
			{
				$myMiliSecTimeString .= '00';
			}
			
			// set  millisecodns  
			$timeArray['msec'] = $myMiliSecTimeString;
		} else {
			$timeArray['msec'] = '000'; // no milliseconds left
		} 

		// concatenate values
		$qtTimeString = ''.$timeArray['hour'].':'.$timeArray['min'].':'.$timeArray['sec'].'.'.$timeArray['msec'];
		
		return $qtTimeString;
		
	} // end samiToQtTime()

	/**
	 * Converts QT time to miliseconds format (Accepted by SAMI 1.0 and other CCformats)
	 * @return int $samiTime Time in miliseconds format; 1000 = 1. sec
	 * @param String $qtTime QT time Format; (e.g. "00:01:10.280")
	 */
	static public function timeQtToSami($qtTime)
	{
	    // Known patterns: 1, 2, or 3 decimals for millisecond definition
		$pattern_time_000 = "\[([0-9]{2}:[0-9]{2}:[0-9]{2}.[0-9]{3})\]";
    	$pattern_time_00 = "\[([0-9]{2}:[0-9]{2}:[0-9]{2}.[0-9]{2})\]";
    	$pattern_time_0 = "\[([0-9]{2}:[0-9]{2}:[0-9]{2}.[0-9]{1})\]";
    	$pattern_selected = '';
	        
    	// If pattern is 3 digit
    	if(preg_match('/'.$pattern_time_000.'/',$qtTime))
	    {
	    	$pattern_selected = $pattern_time_000;
	    }
	    // If pattern is 2 digit
    	if(preg_match('/'.$pattern_time_00.'/',$qtTime))
	    {
	    	$pattern_selected = $pattern_time_00;
	    }
	    // If pattern is 1 digit
    	if(preg_match('/'.$pattern_time_0.'/',$qtTime))
	    {
	    	$pattern_selected = $pattern_time_0;
	    }
	    
	    $t1 = 0; // hours (e.g. [01])
	    $t2 = 0; // minutes (e.g. [12])
	    $t3 = 0; // seconds (e.g. [01.123])
	    
	    $qtTimeParts = split(':',$qtTime); // split QT time mark into an array
	    
	    $t1 += $qtTimeParts[0]; // adding hours
	    $t2 += $qtTimeParts[1]; // adding minutes
	    $t3 += $qtTimeParts[2]; // adding seconds and miliseconds

	    // millisecond equivalents
	    $t1 *= 3600000; // 1 hour = 60*60*1000
	    $t2 *= 60000; // 1 minute = 60*1000
	    $t3 *= 1000; // 1 second = 1*1000 
	    
	    // get time in milliseconds
	    $samiTime = $t1 + $t2 + $t3;

	    return $samiTime;
	
	} // end timeQtToSami()	

	
	
	/********************************
	 * still working on more time functions 
	 *********************************/ 	

} // end TimeUtil
?>