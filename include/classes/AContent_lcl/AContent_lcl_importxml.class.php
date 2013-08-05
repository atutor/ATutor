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

	define('TR_INCLUDE_PATH','../../../../include');
	require_once('ContentDAO.class.php');

	/*	
	 * AContent lcl import xml
	 * 
	 * This class parses the XML data requested to the Tool Provider (TP)
	 * and stores them into the ATutor database.
	 * 
	*/

	class AContent_lcl_importxml{

		private static $_singleton	= null;
		public $tree				= null;
		private $_course_id			= null;

		public function __construct(){}

		/**
		 * Singleton Design Pattern
		 * Return the instance of the class DAO.class.php
		 * @access  private
		 * @return  DAO class instance
		 * @author  Mauro Donadio
		 */
		private static function _getInstance(){

			if (AContent_lcl_importxml::$_singleton == null){
				require_once('ContentDAO.class.php');

				AContent_lcl_importxml::$_singleton = new ContentDAO();
			}

			return AContent_lcl_importxml::$_singleton;
		}


		/**
		 * Writes the data into the database
		 * @access  public
		 * @param   xml structure, course id
		 * @return  bool
		 * @author  Mauro Donadio
		 */
		public function importXML($dataStructure, $course_id){
			
			$this->_course_id	= $course_id;

			$xml	= simplexml_load_string($dataStructure);

			// id of the current course parent
			if(isset($_GET['cid']))
				$import_into_id	= htmlentities($_GET['cid']);
			else
				$import_into_id	= 0;

			$this->_recursiveFolderScan($xml, $import_into_id);

			return true;
		}

		/**
		 * Recursively writes the data into the database
		 * @access  private
		 * @param   current node pointer, parent node id
		 * @return  void
		 * @author  Mauro Donadio
		 */
		private function _recursiveFolderScan($current_node, $import_into_id){

			if($import_into_id == NULL)
				$import_into_id = 0;

			// for each item to import (child)
			for($i = 0; $i < count($current_node->content_id); $i++){

				$current	= $current_node->content_id[$i];

				$new_parent_id = $this->_storeData($current, $this->_course_id, $import_into_id);

				$this->_recursiveFolderScan($current, $new_parent_id);
			}

			return;
		}

		/**
		 * Writing of the new data in the ATutor database
		 * @access  private
		 * @param   current item pointer, course id, parent id
		 * @return  last inserted row id
		 * @author  Mauro Donadio
		 */
		private function _storeData($current_item, $course_id, $content_parent_id){

			$ContentDAO					= self::_getInstance();

			$url						= explode('home/course', $current_item->text);
			
			$uri						= $GLOBALS['_config']['transformable_uri'] . 'home/course' . $url[1];

			$current_item->text			= $uri;
			$current_item->content_path = $uri;

			if($current_item->content_type == 0){
				$current_item->content_type = 2;
				$current_item->formatting	= 2;
			}

			$ContentDAO->Create($this->_course_id,
								$content_parent_id,
								$current_item->ordering,
								$current_item->revision,
								$current_item->formatting,
								$current_item->keywords,
								$current_item->content_path,
								$current_item->title,
								$current_item->text,
								$current_item->head,
								$current_item->use_customized_head,
								$current_item->test_message,
								$current_item->content_type);

			return at_insert_id();
		}

	}
?>