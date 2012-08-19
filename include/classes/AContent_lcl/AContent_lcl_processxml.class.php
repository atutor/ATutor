<?php
/************************************************************************/
/* AContent                                                             */
/************************************************************************/
/* Copyright (c) 2012                                                   */
/* Inclusive Design Institute                                           */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/

	/*	
	 * AContent lcl process xml
	 * 
	 * This class parses the XML data requested to the Tool Provider (TP)
	 * and transforms the structure into an array directly usable for the creation
	 * of the tree view
	 * 
	*/

	class AContent_lcl_processxml{

		private $_tree	= null;

		public function __construct(){
		}

		/**
		 * Transform the XML data to an array
		 * @access  public
		 * @param   xml data
		 * @return  tree
		 * @author  Mauro Donadio
		 */
		public function XMLtoArray($xmlFile){

			$xml	= simplexml_load_string('<?xml version="1.0" encoding="UTF-8" ?>' . $xmlFile);

			if($xml)
				$this->_recursiveParse($xml);
			/*
			else
				die('DEBUG: XML EMPTY');
			*/

			return $this->_tree;
		}

		/**
		 * Transform recursively the XML data into an array
		 * @access  private
		 * @param   root pointer, parent id, branch height
		 * @return  void
		 * @author  Mauro Donadio
		 */
		private function _recursiveParse($root, $dad = null, $height = 0){

			// for each item to import (child)
			for($i = 0; $i < count($root->content_id); $i++){

				// get current branch
				$branch		= $root->content_id[$i];

				// current branch id
				$bid	= (string)$branch['id'];

				// set the
				if(isset($branch->content_parent_id) AND $branch->content_parent_id == 0){

					$this->_tree[0][$i]['content_id']	= $bid;
					$this->_tree[0][$i]['ordering']		= (string)$branch->ordering;
					$this->_tree[0][$i]['title']		= (string)$branch->title;
					$this->_tree[0][$i]['content_type']	= (string)$branch->content_type;

					// if has children
					if(count($branch->content_id) > 0)
						$this->_recursiveParse($branch, $bid, ($height + 1));
				}
				
				if($dad != null AND $height > 0){

					$this->_tree[$dad][$i]['content_id']	= $bid;
					$this->_tree[$dad][$i]['ordering']		= (string)$branch->ordering;
					$this->_tree[$dad][$i]['title']		= (string)$branch->title;
					$this->_tree[$dad][$i]['content_type']	= (string)$branch->content_type;

					$this->_recursiveParse($branch, $bid, ($height + 1));
				}
			}

			$height--;

			return;
		}

	}
?>