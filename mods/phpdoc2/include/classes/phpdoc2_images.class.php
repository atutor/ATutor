<?php


class Phpdoc_images {
	// Variables
	var $imap	= array();	//Hash,  <key>:type, <value>: array
	var $max_col = 0;		//Max column for the display, TODO: define in constance instead?

	// Constructor
	function Phpdoc_images() {
		/* 
		 * $imap_<type> defines the definition of each of the images 
		 * <key>	:	The definition of the image
		 * <value>	:	File name of the image; images must be in the same folder
		 */
		 //class related
		$imap_class		= array(
							'Class'						=> 'class.png', 
							'Private Class'				=> 'private_class.png', 
							'Abstract Class'			=> 'abstract_class.png', 
							'Abstract Private Class'	=> 'abstract_private_class.png'
							);

		//method related
		$imap_method	= array(
							'Method'					=> 'method.png', 
							'Private Method'			=> 'private_method.png', 
							'Abstract Method'			=> 'abstract_method.png', 
							'Constructor'				=> 'constructor_method.png', 
							'Destructor'				=> 'destructor_method.png', 
							'Function'					=> 'function.png'
							);

		//folder related
		$imap_folder	= array(
							'Folder'					=> 'folder.png', 
							'Class Folder'				=> 'class_folder.png', 
							'Function Folder'			=> 'function_folder.png', 
							'Tutorial Folder'			=> 'tutorial_folder.png', 
							'Package Folder'			=> 'package_folder.png'
							);

		//variables
		$imap_variable	= array(
							'Variables'					=> 'variable.png', 
							'Private Variable'			=> 'private_variable.png', 
							'Global Variable'			=> 'global.png', 
							'Constant'					=> 'constant.png'
							);

		//others 
		$imap_others	= array( 
							'File'						=> 'file.png', 
							'Index'						=> 'index.png', 
							'Tutorial'					=> 'tutorial.png', 
							'Package'					=> 'package.png'
							);

		//Instantiate variables
		$this->max_col	= 3;
		$this->imap		= array(
							'Class'						=> $imap_class,
							'Method'					=> $imap_method,
							'Folder'					=> $imap_folder,
							'Variable'					=> $imap_variable,
							'Others'					=> $imap_others
							);
	}


	/**
	 * Generate images dynamically.
	 * @param	the path to the image, used by the <a> tag
	 */
	function printImageTable($image_dir){
		$html = '';	//html code
		$counter = 1;	//row counter
		$html = '<table id="api_legend_outter"><tr>';

		//Loop through level
		foreach ($this->imap as $type=>$type_array){
			if ($counter > $this->max_col){
				$html .= "</tr><tr>";
				$counter = 1;		//reset counter once it overloads
			}
			$html .= "<td>";

			//Run a loop through each of the images inside the array
			$html .= '<table id="api_legend_inner">';
			$html .= '<tr><th colspan="2">'.$type.'</th></tr>';
			foreach($type_array as $image_name => $file_name){
				$html .= '<tr><td>'.$image_name.'</td>';
				$html .= '<td>'.$this->getImgTag($image_name, $image_dir.$file_name).'</td></tr>';
			}
			$html .= '</table>';
			
			$html .= "</td>";
			$counter++;
		}
		$html .= "</tr></table>";
		return $html;
	}


	/**
	 * Get image tag
	 * @param	String  the name of the image which will be shown in the title and alt
	 * @param	String	the path for the image
	 */
	function getImgTag($image_name, $file_name, $width='16'){
		return '<img src="'.$file_name.'" title="'.$image_name.'" title="'.$image_name.'" width="'.$width.'" />';
	}
}
?>