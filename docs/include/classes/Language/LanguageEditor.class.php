<?php

class LanguageEditor {
	var $db;

	function LanguageManager() {
		global $lang_db;
		$this->db =& $lang_db;
	}


    // public
    function addLanguage($row) {
		if($_POST['code'] == '') {
			$errors[] = AT_ERROR_LANG_CODE_MISSING;
		}
		if ($_POST['charset'] == '') {
			$errors[] = AT_ERROR_LANG_CHARSET_MISSING;
		}
		if ($_POST['reg_exp'] == '') {
			$errors[] = AT_ERROR_LANG_REGEX_MISSING;
		}
		if ($_POST['native_name'] == '') {
			$errors[] = AT_ERROR_LANG_NNAME_MISSING;
		}
		if ($_POST['english_name'] == '') {
			$errors[] = AT_ERROR_LANG_ENAME_MISSING;
		}

		return $errors;
    }

	//import lang package (sql)

	//export lang package (sql)

}
?>