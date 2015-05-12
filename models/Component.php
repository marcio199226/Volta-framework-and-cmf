<?php 

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

require_once(DIR_LIBRARY . 'Model.php');

class Vf_Component_Model extends Vf_Model
{
	public function getComponents($page, $module)
	{
		$cmp = $this->db->Select(array('place', 'component'), 'components')
			->Where(array('page' => $page, 'module' => $module))
			->Execute();
						   
		if ($fetch = $this->db->FetchAllAssoc($cmp)) {
			return $fetch;
		} else {
			throw new Exception("Nie znaleziono komponentow dla strony: {$page} modulu: {$module}");
		}
	}
	
	
	public function getPlugins($page, $module)
	{
		$plugins = $this->db->Select('*', 'components as cmp')
			->Join('pm_component_plugins as plugins', array('plugins.p_component' => 'cmp.component'))
			->Where(array('cmp.page' => $page, 'cmp.module' => $module))
			->Execute();
		
		if ($this->db->CountRows($plugins) > 0) {
			$pluginsData = $this->db->FetchAllAssoc($plugins);
			foreach ($pluginsData as $cmpPlugins) {
				if ($cmpPlugins['p_page'] == $page && $cmpPlugins['p_module'] == $module) {
					$active[$cmpPlugins['component']][$cmpPlugins['plugin']] = $cmpPlugins['active'];
				}
			}
			return $active;
		}
		return array();
	}
}

?>