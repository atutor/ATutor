<?php
/*
 * OpenCaps
 * http://opencaps.atrc.utoronto.ca
 * 
 * Copyright 2009 Heidi Hazelton
 * Adaptive Technology Resource Centre, University of Toronto
 * 
 * Licensed under the Educational Community License (ECL), Version 2.0. 
 * You may not use this file except in compliance with this License.
 * http://www.opensource.org/licenses/ecl2.php
 * 
 */

/* an array of clips */
class clipCollection {
	
	public $clips = array();
	public $global_caption_styles = array(); //global caption styles
	
	public function __construct() {
	}
	
	public function addClip($clip) {
		$this->clips[] = $clip;
	} 
 
} 

/* an actual clip */
class clip {
	public $inTime;
	public $outTime;
	public $duration;
	
	public $inTimeMilli;
	public $outTimeMilli;	
	public $durationMilli;
	
	// should these two be in a caption object?
	public $caption_text; 
	public $captionStyles = array();
	
	//public $description;
	
	function __construct($in, $out, $caption) {
		$intime = new time($in, false);
		$this->inTime = $intime->formatted;
		$this->inTimeMilli = $intime->ms; 

		$outtime = new time($out, false);
		$this->outTime = $outtime->formatted;
		$this->outTimeMilli = $outtime->ms; 
		
		$this->durationMilli = $this->outTimeMilli - $this->inTimeMilli;
		$dur = new time($this->durationMilli, true);
		$this->duration = $dur->formatted;		
				
		$this->caption_text = $caption;
	}
}



?>