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
	 * Twitauth
	 * 
	 * This class gets the data structure as an array and prints it
	 * like a tree or checkbox tree or in other many ways
	 * 
	*/

	class TreeGenerator{

		// xml structure
		private $_struct		= null;
		// tree images array
		private $_tree_images	= null;

		// current branch id
		private $_branch_id		= null;
		// temporary branch tree
		private $_temp_tree		= array();


		/**
		 * Constructor
		 * @access  public
		 * @param   XML data structure
		 * @return  void
		 * @author  Mauro Donadio
		 */
		public function __construct($struct){

			$this->_tree_images	= array('tree_space'		=> 'images/tree/tree_space.gif',
										'tree_end'			=> 'images/tree/tree_end.gif',
										'tree_split'		=> 'images/tree/tree_split.gif',
										'tree_vertline'		=> 'images/tree/tree_vertline.gif',
										'tree_horizontal'	=> 'images/tree/tree_horizontal.gif',
										'tree_collapse'		=> 'images/tree/tree_collapse.gif');

			$this->_struct	= $struct;

			$this->_recursive();
			
			return;
		}

		/**
		 * Recursive method which analyzes the array data structure
		 * @access  private
		 * @param   branch index, depth of the branch
		 * @return  void
		 * @author  Mauro Donadio
		 */
		private function _recursive($branch_index = 0, $depth = 0){

			for($i = 0; $i < count($this->_struct[$branch_index]); $i++){

				$new_branch_id	= $this->_struct[$branch_index][$i]['content_id'];
				$j		= $new_branch_id;

				$this->_temp_tree[$j]['title']			= $this->_struct[$branch_index][$i]['title'];
				$this->_temp_tree[$j]['depth']			= $depth;
				$this->_temp_tree[$j]['children']		= count($this->_struct[$new_branch_id]);
				$this->_temp_tree[$j]['father']			= $branch_index;
				$this->_temp_tree[$j]['content_type']	= $this->_struct[$branch_index][$i]['content_type'];

				// if has children
				if($this->_temp_tree[$j]['children'] > 0)
					$this->_recursive($new_branch_id, ($depth + 1));
			}
			return;
		}


		/**
		 * Output a plain tree
		 * @access  public
		 * @param   void
		 * @return  void
		 * @author  Mauro Donadio
		 */
		public function plainTree(){

			foreach($this->_temp_tree as $key => $params){

				for($i = 0; $i < $params['depth']; $i++)
					echo '<img src="'.$this->_tree_images['tree_space'].'" />';

				echo $params['title'];

				echo '<br />';
			}

			return;
		}


		/**
		 * Output a folder markers tree
		 * @access  public
		 * @param   void
		 * @return  void
		 * @author  Mauro Donadio
		 */
		public function plainFolderTree(){

			foreach($this->_temp_tree as $key => $params){

				for($i = 0; $i < $params['depth']; $i++)
					echo '<img src="'.$this->_tree_images['tree_space'].'" />';

				// if folder
				if($params['content_type'] == 1)
					echo '<img src="'.$this->_tree_images['tree_collapse'].'" />';
				else
					echo '<img src="'.$this->_tree_images['tree_space'].'" />';

				echo $params['title'];

				echo '<br />';
			}

			return;
		}

		/**
		 * Output a checkbox tree
		 * @access  public
		 * @param   checkbox input name
		 * @return  void
		 * @author  Mauro Donadio
		 */
		public function checkBoxTree($inputName = 'checkboxTree'){

			$j	= 0;
			$k	= 0;

			foreach($this->_temp_tree as $key => $params){

				for($i = 0; $i < $params['depth']; $i++)
					echo '<img src="'.$this->_tree_images['tree_space'].'" />';

				// if father == root
				if($params['father'] == 0){
					$j++;
					echo '<label for="'.$j.'|'.$i.'|'.$key.'"><input type="checkbox" id="'.$j.'|'.$i.'|'.$key.'" name="'.$inputName.'[]" value="'.$j.'|'.$i.'|'.$key.'" /> ';
				}else
					echo '<label for="'.$j.'|'.$i.'|'.$key.'"><input type="checkbox" id="'.$j.'|'.$i.'|'.$key.'" name="'.$inputName.'[]" value="'.$j.'|'.$i.'|'.$key.'" /> ';

				echo $params['title'].'</label>';

				echo '<br />';
				
				$k++;
			}

			// check all / uncheck all

			echo '<script type="text/javascript">';
				echo 'function checkAll(tree_box) {';
					echo 'var tab	= document.getElementById(tree_box);';
					echo 'var chk	= tab.getElementsByTagName("input");';

					echo 'for(i = 0; i < chk.length; i++) {';
						echo 'chk[i].checked = true;';
					echo '}';

				echo '}';

				echo 'function uncheckAll(tree_box) {';
					echo 'var tab	= document.getElementById(tree_box);';
					echo 'var chk	= tab.getElementsByTagName("input");';

					echo 'for(i = 0; i < chk.length; i++) {';
						echo 'chk[i].checked = false;';
					echo '}';

				echo '}';
			echo '</script>';

			echo '<div style="paddig-top:10px"><br />( <a href="javascript:checkAll(\'tree_box\');">check all</a> / <a href="javascript:uncheckAll(\'tree_box\')">uncheck all</a> )</div>';

			return;
		}

		/**
		 * Output a checkbox tree
		 * @access  public
		 * @param   checkbox input name
		 * @return  void
		 * @author  Mauro Donadio
		 */
		public function radioButtonTree($inputName = 'radioButtonTree'){

			$j	= 0;
			$k	= 0;

			foreach($this->_temp_tree as $key => $params){

				for($i = 0; $i < $params['depth']; $i++)
					echo '<img src="'.$this->_tree_images['tree_space'].'" />';

				// if father == root
				if($params['father'] == 0){
					$j++;
					echo '<input type="radio" id="checkbox'.$k.'" name="'.$inputName.'[]" value="'.$j.'|'.$i.'|'.$key.'" /> ';
				}else
					echo '<input type="radio" id="checkbox'.$k.'" name="'.$inputName.'[]" value="'.$j.'|'.$i.'|'.$key.'" /> ';

				echo $params['title'];

				echo '<br />';
				
				$k++;
			}

			return;
		}
	}
?>