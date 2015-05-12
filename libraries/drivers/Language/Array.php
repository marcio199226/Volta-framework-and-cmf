<?php

/**
* Volta framework

* @author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
* @copyright Copyright (c) 2012, marcio
* @version 1.0
*/

require_once(DIR_ABSTRACT . 'Language.php');

class Vf_Language_Array_Adapter extends Vf_Language_Abstract
{
	public function load($file)
	{
		if (Vf_Loader::existsFile(DIR_LANG . $file)) {
			include(DIR_LANG . $file);
			$this->data = array_merge_recursive($this->data, $translate);
		}
	}
}

?>