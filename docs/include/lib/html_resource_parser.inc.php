<?php

function get_html_resources($text) {
	$resources = array();

	$handler =& new XML_HTMLSax_Handler();

	$parser =& new XML_HTMLSax();
	$parser->set_object($handler);
	$parser->set_element_handler('openHandler','closeHandler');

	$parser->parse($text);

	foreach ($handler->resources as $resource) {
		$url_parts = @parse_url($resource);

		if (isset($url_parts['scheme'])) {
			// we don't want full urls
			continue;
		}

		if ((substr($resource, 0, 1) == '/')) {
			// we don't want absolute urls
			continue;
		}

		// make sure this resource exists in this course's content directory:
		$resource_server_path = realpath(AT_CONTENT_DIR . $_SESSION['course_id']. '/' . $resource);
		if (file_exists($resource_server_path) && is_file($resource_server_path)) {
			$resources[$resource] = $resource_server_path;
		}
	}

	return $resources;
}

/*
	the following resources are to be identified:
	even if some of these can't be images, they can still be files in the content dir.
	theoretically the only urls we wouldn't deal with would be for a <!DOCTYPE and <form>

	img		=> src
	a		=> href				// ignore if href doesn't exist (ie. <a name>)
	object	=> data | classid	// probably only want data
	applet	=> classid | archive			// whatever these two are should double check to see if it's a valid file (not a dir)
	link	=> href
	script	=> src
	form	=> action
	input	=> src
	iframe	=> src
*/
class XML_HTMLSax_Handler {
	var $elements = array(	'img'	 => 'src',
							'a'		 => 'href',				
							'object' =>  array('data',    'classid'),
							'applet' =>  array('classid', 'archive'),
							'link'	 => 'href',
							'script' => 'src',
							'form'	 => 'action',
							'input'	 => 'src',
							'iframe' => 'src',
							'embed'	 => 'src',
							'param'	 => 'value');
	var $resources = array();

    function XML_HTMLSax_Handler() { 
		$this->resources = array();
	}

    function openHandler(& $parser,$name,$attrs) {
		$name = strtolower($name);
		$attrs = array_change_key_case($attrs, CASE_LOWER);

		/* check if this attribute specifies the files in different ways: (ie. java) */
		if (is_array($this->elements[$name])) {
			$items = $this->elements[$name];

			foreach ($items as $item) {
				if ($attrs[$item] != '') {
					/* some attributes allow a listing of files to include seperated by commas (ie. applet->archive). */
					if (strpos($attrs[$item], ',') !== false) {
						$files = explode(',', $attrs[$item]);
						foreach ($files as $file) {
							$this->resources[] = trim($file);
						}
					} else {
						$this->resources[] = $attrs[$item];
					}
				}
			}
		} else if (isset($this->elements[$name]) && ($attrs[$this->elements[$name]] != '')) {
			/* we know exactly which attribute contains the reference to the file. */
			$this->resources[] = $attrs[$this->elements[$name]];
		}
    }
    function closeHandler(& $parser,$name) { }
}
?>