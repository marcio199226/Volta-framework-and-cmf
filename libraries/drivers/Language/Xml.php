<?php

/**
* Volta framework

* @author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
* @copyright Copyright (c) 2012, marcio
* @version 1.0
*/

require_once(DIR_ABSTRACT . 'Language.php');

class Vf_Language_Xml_Adapter extends Vf_Language_Abstract
{
	public function load($file)
	{
		$cache = new Vf_Cache();
		if ($cachedTranslation = $cache->getCache(DIR_LANG . $file, 86400)) {
			$this->data = $cachedTranslation;
			return;
		} elseif (Vf_Loader::existsFile(DIR_LANG . $file)) {
			$translate = array();
			$xml = new SimpleXMLElement(DIR_LANG . $file, 0, true);

			foreach ($xml->children() as $child) {
				foreach ($child ->children() as $element => $subchild) {
					if (sizeof($subchild) > 0) {
						foreach ($subchild->children() as $key => $values) {
							if ($key == 'pluralize') {
								foreach ($values->children() as $pluralizes => $val) {
									foreach ($val as $transKey => $transValue) {
										foreach ($transValue->attributes() as $keyName => $valueKey) {
											$translate[$child->getName()][$subchild->getName()]['pluralize']['%' . $pluralizes . '%'][(int)$valueKey] = (string)$transValue;
										}
									}
								}
							} else {
								$translate[$child->getName()][$subchild->getName()][$key] = (string)$values;
							}
						}
					} elseif (!is_array($subchild)) {
						$translate[$child->getName()][$subchild->getName()] = (string)$subchild;
					}
				}
			}
			$cache->setCache(DIR_LANG . $file, $translate, 86400);
			$this->data = $translate;
		}
	}
}

?>