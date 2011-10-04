<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2009										*/
/* Inclusive Design Institute                                   */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

//Constances
define('AT_QTI_REPONSE_GRP',    1);
define('AT_QTI_REPONSE_LID',    2);
define('AT_QTI_REPONSE_STR',    3);

/**
* QTIParser
* Class for parsing XML language info and returning a QTI Object
* @access	public
* @author	Harris Wong
*/
class QTIParser {
	// all private
	var $parser; // the XML handler
	var $qti_type; // QTI specification versoin, imsqti_xmlv1p1, imsqti_item_xmlv2p1, imsqti_xmlv1p2
	var $character_data; // tmp variable for storing the data
	var $element_path; // array of element paths (basically a stack)
	var $title;	//title for this question test
	var $q_identifiers	= array();		//The identifier of the choice. This identifier must not be used by any other choice or item variable.
	var $question = '';					//question of this QTI
	var $response_type	= array();		//detects what type of question this would be.
	var $relative_path	= '';			//the relative path to all resources in this xml.

	//stacks
	var $choices		= array();	//answers array that keep tracks of all the correct answers
	var $groups			= array();	//groups for matching, the left handside to match with the different choices
	var $attributes		= array();	//tag attribute
	var $answers		= array();	//correct answers 
	var $response_label = array();	//temporary holders for response labels
	var $field_label	= array();		//fields label
	var $field_entry	= array();	//fields entry
	var $feedback		= array();		//question feedback
	var $item_num		= 0;		//item number
	var $items			= array();	//stacks of media items, ie. img, embed, ahref etc. 
	var $qmd_itemtype	= -1;		//qmd flag
	var $temp_answer	= array();	//store the temp answer stack
	var $answers_for_matching	= array();
	var $weights		= array();	//the weight of each question

	//constructor
	function QTIParser($qti_type='') {
		$this->qti_type = $qti_type;
		$this->parser = xml_parser_create(); 

		xml_set_object($this->parser, $this);
		xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, false); /* conform to W3C specs */
		xml_set_element_handler($this->parser, 'startElement', 'endElement');
		xml_set_character_data_handler($this->parser, 'characterData');
	}

	// public
	// @return	true if parsed successfully, false otherwise
	function parse($xml_data) {
		$this->element_path   = array();
		$this->character_data = '';
		xml_parse($this->parser, $xml_data, TRUE);

		//Loop thru each item and replace if existed
		foreach ($this->answers_for_matching as $afm_k => $afk_v){
			if (!empty($this->answers_for_matching[$afm_k])){
				$this->answers[$afm_k] = $afk_v;
			}
		}

		if(in_array('questestinterop', $this->element_path) ||
			in_array('assessment', $this->element_path)){
			//this is a v2.1+ package
			return false;
		} else {
			return true;
		}
	}

	// private
	function startElement($parser, $name, $attributes) {
		global $msg;
//		debug($attributes, $name );
		//save attributes.
		switch($name) {
			case 'section':
				$this->title = $attributes['title'];
				break;
			case 'response_lid':
				if ($this->response_type[$this->item_num] <= 0) {
					$this->response_type[$this->item_num] = AT_QTI_REPONSE_LID;
				}
			case 'response_grp':
				if ($this->response_type[$this->item_num] <= 0) {
					$this->response_type[$this->item_num] = AT_QTI_REPONSE_GRP;
				}
			case 'response_str':
				$this->attributes[$this->item_num][$name]['ident'] = $attributes['ident'];
				$this->attributes[$this->item_num][$name]['rcardinality'] = $attributes['rcardinality'];
				if ($this->response_type[$this->item_num] <= 0) {
					$this->response_type[$this->item_num] = AT_QTI_REPONSE_STR;
				}
				break;
			case 'response_label':
					if(!isset($this->choices[$this->item_num][$attributes['ident']])){
						if (!is_array($this->response_label[$this->item_num])){
							$this->response_label[$this->item_num] = array();	
						}
						array_push($this->response_label[$this->item_num], $attributes['ident']);
					}
				break;
			case 'varequal':
				$this->attributes[$this->item_num][$name]['respident'] = $attributes['respident'];
				break;
			case 'setvar':
				$this->attributes[$this->item_num][$name]['varname'] = $attributes['varname'];
				break;
			case 'render_choice':
				$this->attributes[$this->item_num][$name]['shuffle'] = $attributes['shuffle'];
				$this->attributes[$this->item_num][$name]['minnumber'] = $attributes['minnumber'];
				$this->attributes[$this->item_num][$name]['maxnumber'] = $attributes['maxnumber'];
				break;
			case 'render_fib':
				$rows = intval($attributes['rows']);
				$property = 1;

				//1,2,3,4 according to tools/tests/create_question_long.php
				if ($rows == 1){
					$property = 2;
				} elseif ($rows > 1 && $rows <= 5){
					$property = 3;
				} elseif ($rows > 5){
					$property = 4;
				}
				$this->attributes[$this->item_num][$name]['property'] = $property;
				break;
			case 'matimage':
				$this->attributes[$this->item_num][$name]['imagtype'] = $attributes['imagtype'];
				$this->attributes[$this->item_num][$name]['uri'] = $attributes['uri'];
				break;
			case 'mataudio':
				$this->attributes[$this->item_num][$name]['audiotype'] = $attributes['audiotype'];
				$this->attributes[$this->item_num][$name]['uri'] = $attributes['uri'];
				break;
			case 'matvideo':
				$this->attributes[$this->item_num][$name]['videotype'] = $attributes['videotype'];
				$this->attributes[$this->item_num][$name]['uri'] = $attributes['uri'];
				break;
			case 'matapplet':
				$this->attributes[$this->item_num][$name]['uri'] = $attributes['uri'];
				$this->attributes[$this->item_num][$name]['width'] = intval($attributes['width']);
				$this->attributes[$this->item_num][$name]['height'] = intval($attributes['height']);
				break;
			case 'setvar':
				$this->attributes[$this->item_num][$name]['varname'] = $attributes['varname'];
				$this->attributes[$this->item_num][$name]['action'] = $attributes['action'];
				break;
			case 'itemproc_extension':
				if (preg_match('/imsqti_xmlv1p2\/imscc_xmlv1p0(.*)/', $this->qti_type)){
					$msg->addError('QTI_WRONG_PACKAGE');
				}
				break;
		}
		array_push($this->element_path, $name);
   }

	// private
	/* called when an element ends */
	/* removed the current element from the $path */
	function endElement($parser, $name) {
		global $msg;
		//check element path
		$current_pos = count($this->element_path) - 1;
		$last_element = $this->element_path[$current_pos - 1];

		switch($name) {
			case 'item':
				$this->item_num++;
				break;
			case 'mattext':
				$this->mat_content[$this->item_num] .= $this->reconstructRelativePath($this->character_data);
				break;
			case 'matimage':
				$this->mat_content[$this->item_num] .= '<img src="'.$this->attributes[$this->item_num][$name]['uri'].'" alt="Image Not loaded:'.$this->attributes[$this->item_num][$name]['uri'].'" />';
				break;
			case 'mataudio':
				$this->mat_content[$this->item_num] .= '<embed SRC="'.$this->attributes[$this->item_num][$name]['uri'].'" autostart="false" width="145" height="60"><noembed><bgsound src="'.$this->attributes[$this->item_num][$name]['uri'].'"></noembed></embed>';
				break;
			case 'matvideo':
				if ($this->attributes[$this->item_num][$name]['videotype'] == 'type/swf'){
					$this->mat_content[$this->item_num] .= '<object type="application/x-shockwave-flash" data="' . $this->attributes[$this->item_num][$name]['uri'] . '" width="550" height="400"><param name="movie" value="'. $this->attributes[$this->item_num][$name]['uri'] .'" /></object>';					
				} elseif ($this->attributes[$this->item_num][$name]['videotype'] == 'type/mov'){
					$this->mat_content[$this->item_num] .= '<object classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" width="550" height="400" codebase="http://www.apple.com/qtactivex/qtplugin.cab"><param name="src" value="'. $this->attributes[$this->item_num][$name]['uri'] . '" /><param name="autoplay" value="true" /><param name="controller" value="true" /><embed src="' . $this->attributes[$this->item_num][$name]['uri'] .'" width="550" height="400" controller="true" pluginspage="http://www.apple.com/quicktime/download/"></embed></object>';
				}
				break;
			case 'matapplet':
				(($this->attributes[$this->item_num][$name]['width'] != 0)? $width = $this->attributes[$this->item_num][$name]['width'] : $width = 460);
				(($this->attributes[$this->item_num][$name]['height'] != 0)? $height = $this->attributes[$this->item_num][$name]['height'] : $height = 160);
				$this->mat_content[$this->item_num] .= '<applet code="'.$this->attributes[$this->item_num][$name]['uri'].'" width="'.$width.'" height="'.$height.'" alt="Applet not loaded."></applet>';
				break;
			case 'material':
				//check who is mattext's ancestor, started from the most known inner layer
				if (in_array('response_label', $this->element_path)){
					if(!in_array($this->mat_content, $this->choices)){
						//This is one of the choices.
						if (!empty($this->response_label[$this->item_num])){
							$this->choices[$this->item_num][array_pop($this->response_label[$this->item_num])] = $this->mat_content[$this->item_num];
						}
					}
				} elseif (in_array('response_grp', $this->element_path) || in_array('response_lid', $this->element_path)){
					//for matching, where there are groups
					//keep in mind that Respondus handles this by using response_lid
					$this->groups[$this->item_num][] = $this->reconstructRelativePath($this->mat_content[$this->item_num]);
//					debug($this->character_data, 'harris - groups');
				} elseif (in_array('presentation', $this->element_path)){
					$this->question[$this->item_num] = $this->reconstructRelativePath($this->mat_content[$this->item_num]);
				} elseif (in_array('itemfeedback', $this->element_path)){
					$this->feedback[$this->item_num] = $this->mat_content[$this->item_num];
				}
				//once material is closed, reset the mat_content variable.
				$this->mat_content[$this->item_num] = '';
				break;
			case 'varequal':
				//stores the answers (either correct or incorrect) into a stack
				if (in_array('not', $this->element_path)) {				
				    //if there is a "not", it's a multiple answer, and this should be included to the answer
				    break;
                }
				$this->temp_answer[$this->attributes[$this->item_num][$name]['respident']]['name'][] = $this->character_data;
				//responses handling, remember to save the answers or match them up
				if (!is_array($this->answers[$this->item_num])){
					$this->answers[$this->item_num] = array();
				}
				array_push($this->answers[$this->item_num], $this->reconstructRelativePath($this->character_data));
				break;
			case 'setvar':
				$this->temp_answer[$this->attributes[$this->item_num]['varequal']['respident']]['value'][] = $this->character_data;
				$this->temp_answer[$this->attributes[$this->item_num]['varequal']['respident']]['attribute'][] = $this->attributes[$this->item_num]['setvar']['varname'];
				break;
			case 'respcondition':
				if (empty($this->temp_answer)) {
					break;
				}

				//closing this tag means a selection of choices have ended.  Assign the correct answer in this case.
				$tv = $this->temp_answer[$this->attributes[$this->item_num]['varequal']['respident']];
				//debug($tv, 'harris'.$this->item_num);
                //debug($this->choices[$this->item_num], 'choices');
				//debug($this->answers_for_matching[$this->item_num], 'answers for matching');

				//If matching, then attribute = 'Respondus_correct'; otherwise it is 'que_score'
				if ($this->getQuestionType($this->item_num) == 5){
					if ($tv['answerAdded']!=true && !empty($tv['attribute'])){
						foreach ($tv['attribute'] as $att_id => $att_value){
							//Handles Respondus' (and blakcboard, angels, etc) responses schemas
							if (strtolower($att_value)=='respondus_correct'){
								//Then this is the right answer
								if (!is_array($this->answers_for_matching[$this->item_num])){
									$this->answers_for_matching[$this->item_num] = array();
								}
								//The condition here is to check rather the answers have been duplicated, otherwise the indexing won't be right.
								//sizeof[answers] != sizeof[questions], then the index matching is wrong.
								//Created a problem though, which is then many-to-1 matching fails, cuz answers will be repeated.
								//Sep 2,08, Fixed by adding a flag into the array
	//							if (!in_array($tv['name'][$att_id], $this->answers_for_matching[$this->item_num])){
									array_push($this->answers_for_matching[$this->item_num], $tv['name'][$att_id]);
									$this->temp_answer[$this->attributes[$this->item_num]['varequal']['respident']]['answerAdded'] = true;
									
									//add mark
									$this->weights[$this->item_num] = floatval($tv['value'][$att_id]);
	//							} 
								break;
							} 
						}
					}
				} else {
					$pos = sizeof($tv['value']) - 1;	//position of the last entry of the "temp answer's value" array
					//Retrieve the last entry of the "temp answer's value" array
					$current_answer = $tv['value'][$pos];
					if (floatval($current_answer) > 0){
						if (!is_array($this->answers_for_matching[$this->item_num])){
							$this->answers_for_matching[$this->item_num] = array();
						}							
//							if (!in_array($tv['name'][$val_id], $this->answers_for_matching[$this->item_num])){
							array_push($this->answers_for_matching[$this->item_num], $tv['name'][sizeof($tv['name'])-1]);
							//add mark
							$this->weights[$this->item_num] += floatval($current_answer);
//							} 
					}
				} 
				break;
			case 'fieldlabel':
				$this->field_label[$this->item_num] = $this->character_data;
				break;
			case 'fieldentry':
				$this->field_entry[$this->item_num][$this->field_label[$this->item_num]] = $this->character_data;
				break;
			case 'qmd_itemtype':
				//Deprecated as of QTI 1.2.
				if (empty($this->field_entry[$this->item_num][$name])){
					$this->field_entry[$this->item_num][$name] = $this->character_data;
				} 
				break;
			default:
				break;
		}
//		debug($this->element_path, "Ele Path");

		//pop stack and reset character data, o/w it will stack up
		array_pop($this->element_path);
		$this->character_data = '';
	}

	// private	
   	function characterData($parser, $data){
		global $addslashes;
		if (trim($data)!=''){
			$this->character_data .= $addslashes(preg_replace('/[\t\0\x0B(\r\n)]*/', '', $data));
//			$this->character_data .= trim($data);
		}
	}

	/*
	 * This function returns the question type of this XML.
	 * @access	public 
	 * @param	the item_num
	 * @return  1: m/c
     *          2: t/f
     *          3: open ended question
     *          4: likert
     *          5: s match
     *          6: order
     *          7: m/a
     *          8: g match
     *          false for not found.
	 */
	function getQuestionType($item_num){
		switch ($this->field_entry[$item_num]['qmd_questiontype']){
			case 'Multiple-choice':
				//1, 4
				//likert have no answers
				if (empty($this->answers)){
					return 4;
				}
				return 1;
				break;
			case 'True/false':
				return 2;
				break;
			case 'FIB-string':
				return 3;
				break;
			case 'Drag-and-drop':
				return 5;
				break;
			case 'Multiple-response':
				return 7;
				break;
		} 

		switch ($this->field_entry[$item_num]['qmd_itemtype']){
			case 'Matching':
				//matching
				return 5;
				break;
		}

		//handles CC packages
		switch ($this->field_entry[$item_num]['cc_profile']){
			case 'cc.multiple_choice.v0p1':
				return 1;
				break;
			case 'cc.true_false.v0p1':
				return 2;
				break;
			case 'cc.fib.v0p1':
				return 3;
				break;
			case 'cc.multiple_response.v0p1':
				return 7;
				break;
		}
	

		//Check if this is an ordering, or matching
		$response_obj;
		switch ($this->response_type[$item_num]){
			case AT_QTI_REPONSE_LID:
				$response_obj = $this->attributes[$item_num]['response_lid'];
				break;
			case AT_QTI_REPONSE_GRP:
				$response_obj = $this->attributes[$item_num]['response_grp'];
				break;
			case AT_QTI_REPONSE_STR:
				$response_obj = $this->attributes[$item_num]['response_str'];
				return 3;	//no need to parse the rcardinality?
				break;
		}
		if ($response_obj['rcardinality'] == 'Ordered'){
			return 6;
		} elseif ($response_obj['rcardinality'] == 'Multiple'){
			//TODO Multiple answers, Simple matching and Graphical matching
			if (empty($this->field_entry[$item_num])){
				return 7;
			}
			return 5;
		} elseif ($response_obj['rcardinality'] == 'Single'){
			return 1; //assume mc
		}

		//None found.
		return false;
	}


	//set relative path
	//must be used before calling parse.  Otherwise it will be null.
	//private
	function setRelativePath($path){
		if ($path != ''){
			if ($path[-1] != '/'){
				$path .= '/'; 
			}
			$this->relative_path = $path;
		}
	}


	//private
	//when importing, the path of the images are all changed.  Have to parse them out and add the extra path in.
	//No longer needed to reconstruct, just needed to save the path, as of Aug 25th, 08.  Decided to overwrite files if the same name exist.  
	function reconstructRelativePath($path){
		//match img tag, all.
//		if (preg_match_all('/\<img(\s[^\>])*\ssrc\=[\\\\]?\"([^\\\\^\"]+)[\\\\]?\".*\/?\>/i', $path, $matches) > 0){
//fixes multiple image tags within a $path
		if (preg_match_all('/\<img(\s[\w^img]+\=[\\\\]?\"[^\\\\^\"]+[\\\\]?\")*\ssrc\=[\\\\]?\"([^\\\\^\"]+)[\\\\]?\"/i', $path, $matches) > 0){
			foreach ($matches[2] as $k=>$v){
				if(strpos($v, 'http://')===false && !in_array($v, $this->items)) {
					$this->items[] = $v;	//save the url of this media.
	//				$path = str_replace($v, $this->relative_path.$v, $path);
				}
			}
			return $path;
		} elseif (preg_match_all('/\<embed(\s[^\>])*\ssrc\=[\\\\]?\"([^\\\\^\"]+)[\\\\]?\".*/i', $path, $matches) > 0){
			foreach ($matches[2] as $k=>$v){
				if(strpos($v, 'http://')===false && !in_array($v, $this->items)) {
					$this->items[] = $v;	//save the url of this media.
	//				$path = str_replace($v, $this->relative_path.$v, $path);
				}
			}
			return $path;
		} else {
			return $path;	
		}
	}


	//public
	function close(){
		//Free the XML parser
		unset($this->response_label);
		unset($this->field_label);
		unset($this->temp_answer);
		xml_parser_free($this->parser);
	}

}

?>
