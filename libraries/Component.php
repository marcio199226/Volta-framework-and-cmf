<?php

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

class Vf_Component
{
	/**
	* Komponenty do zaladowania
	* @access protected
	* @var array $components
	*/
	protected $components = array();
	
	/**
	* Pluginy dla aktualnej strony
	* @access protected
	* @var array $plugins
	*/
	protected $plugins = array();
	
	/**
	* Komponenty z gotowym kodem html
	* @access protected
	* @var array $renderedComponents
	*/
	protected $renderedComponents = array();
	
	/**
	* Obiekt acl-a
	* @access private
	* @var object $acl Vf_Acl
	*/
	private $acl = null;
	
	/**
	* Obiekt uzytkownika
	* @access private
	* @var object $user Vf_User
	*/
	private $user = null;
	
	/**
	* Obiekt router-a
	* @access protected
	* @var object $router Vf_Router
	*/
	private $router = null;
	
	
	/**
	* Tworzymy potrzebne nam istancje
	* @access public 
	*/
	public function __construct()
	{
		$this->router = Vf_Core::getContainer()->router;
		$this->user =  Vf_Core::getContainer()->user;
		$this->acl = Vf_Core::getContainer()->aclCore;
	}
	
	
	/**
	* Setter
	* @access public 
	* @param array $components komponenty do zaladowania
	*/
	public function toLoad($components)
	{
		$this->components = $components;
	}
	
	
	/**
	* Zwraca tablice z kodem html kazdego komponentu/widget-u gotowych do wyswietlenia
	* @access public 
	* @return array
	*/
	public function display()
	{
		if (sizeof($this->components) == 0) {
			return;
		}
		
		$page = $this->router->getFrontController();
		$module = $this->router->getFrontControllerAction();
		$action = $this->router->getComponentAction();
		$config = new Vf_Config(DIR_CONFIG, 'Xml');
		
		$pluginsModel = new Vf_Component_Model();
		$this->plugins = $pluginsModel->getPlugins($page, $module);

		foreach ($this->components as $cmp) {
			$component = $this->setComponentControllerName($page, $cmp['component']);
			$this->loadController($cmp['component'], $component);

			$classComponent = 'Vf_' . $component . '_Component';
			$classWidget = 'Vf_' . $cmp['component'] . '_Widget';

			if (class_exists($classComponent)) {	
				$configFile = DIR_CONFIG . $cmp['component'];
				$configComponent = new Vf_Config($configFile, 'Xml');
				
				$object = new $classComponent();
				
				if (!method_exists($object, $action)) {
					$componentAction = $config->componentAction;
				} else {
					$componentAction = $action;
				}
				
				if (($this->user->is_guest() && $configComponent->guest == 0)) {
					continue;
				}

				try {
					if ($this->acl->is_allowed($component, $componentAction)) {
						if ($object instanceof Vf_Controller) {
							$cache = new Vf_Cache();
							if ($configComponent->cache == 1) {
								if (in_array($componentAction, $configComponent->cached_action)) {	
									$output[0] = $this->loadPlugins('before', $configComponent->plugins_settings, $component, $componentAction);
									$output[1] = $cache->getCache($cmp['component'] . $componentAction, $configComponent->lifetime);
									
									if (!$output[1]) {
										$data = $object->$componentAction();
										$cache->setCache($cmp['component'] . $componentAction, $data, $configComponent->lifetime);
										$output[1] = $data;
									}
									$output[2] = $this->loadPlugins('after', $configComponent->plugins_settings, $component, $componentAction);
								} else {
									$output[0] = $this->loadPlugins('before', $configComponent->plugins_settings, $component, $componentAction);
									$output[1] = $object->$componentAction();
									$output[2] = $this->loadPlugins('after', $configComponent->plugins_settings, $component, $componentAction);
								}
							} else {
								$output[0] = $this->loadPlugins('before', $configComponent->plugins_settings, $component, $componentAction);
								$output[1] = $object->$componentAction();
								$output[2] = $this->loadPlugins('after', $configComponent->plugins_settings, $component, $componentAction);
							}
							$this->renderedComponents[$cmp['place']][] = implode('', $output);
						}
					}
				} catch (Volta_Acl_Deny_Exception $e) {
					Vf_Loader::loadFile('helpers/Box.php');
					$this->renderedComponents[$cmp['place']][] = Vf_Box_Helper::error($e->getMessage());
				} catch (Vf_Component_Exception $e) {
					Vf_Loader::loadFile('helpers/Box.php');
					$this->renderedComponents[$cmp['place']][] = Vf_Box_Helper::error($e->getMessage());
				}
			} elseif (class_exists($classWidget)) {
				$object = new $classWidget();
				if ($object instanceof IWidget) {
					if ($object instanceof Vf_Widget_Abstract) {
						$object->setCoreContainer();
					}
					$this->renderedComponents[$cmp['place']][] = $object->display();
				} else {
					throw new Vf_Widget_Exception('Widget: ' . $classWidget . ' must implement IWidget interface');
				}
			} else {
					Vf_Loader::loadFile('helpers/Box.php');
					$this->renderedComponents[$cmp['place']][] = Vf_Box_Helper::error('Nie znaleziono kontrolera lub komponentu: ' . $component);
			}
		}
		return $this->renderedComponents;
	}
	
	
	/**
	* Zwraca nazwy wszystkich dostepnych komponentow
	* @access public 
	* @return array
	*/
	public function getComponentsNames()
	{
		$dirs = glob(DIR_COMPONENTS . '*', GLOB_ONLYDIR);
		if (sizeof($dirs) > 0) {
			foreach ($dirs as $dir) {
				$components = explode('/', $dir);
				$componentsNames[] = $components[sizeof($components)-1];
			}
		}
		return $componentsNames;
	}
	

	/**
	* Zwraca nazwy wszystkich dostepnych widget-ow
	* @access public 
	* @return array
	*/
	public function getWidgetsNames()
	{
		$files = glob(DIR_WIDGETS_CTRL . '*.php');
		if (sizeof($files) > 0) {
			foreach ($files as $widget) {
				$widget = explode('/', $widget);
				$widgetName = explode('.', $widget[sizeof($widget)-1]);
				$widgetsNames[] = $widgetName[0];
			}
		}
		return $widgetsNames;
	}
	
	
	/**
	* Zwraca nazwy wszystkich dostepnych frontcontroller-ow
	* @access public 
	* @return array
	*/
	public function getFrontControllersNames()
	{
		$files = glob(DIR_FRONT . '*.php');
		if (sizeof($files) > 0) {
			foreach ($files as $file) {
				$pathFile = explode('/', $file);
				$front = explode('.', $pathFile[sizeof($pathFile)-1]);
				$frontsControllersNames[] = $front[0];
			}
		}
		return $frontsControllersNames;
	}
	
	
	/**
	* Zwraca dostepne miejsca w template
	* @access public 
	* @return array
	*/
	public function getPlacesNames()
	{
		$config = new Vf_Config('config.Component');
		return $config->places;
	}
	
	
	/**
	* Zwraca nazwy wszystkich dostepnych plugin-ow
	* @access public 
	* @return array
	*/
	public function getPluginsNames()
	{
		$dirs = glob(DIR_PLUGINS . '*', GLOB_ONLYDIR);
		if (sizeof($dirs) > 0) {
			foreach ($dirs as $dir) {
				$plugins = explode('/', $dir);
				$pluginsNames[] = $plugins[sizeof($plugins)-1];
			}
		}
		return $pluginsNames;
	}
	
	
	/**
	* Laduje plugin dla komponentu na podstawie konfiguracji
	* @access public 
	* @param string $when before/after
	* @param array $config konfiguracja komponentu
	* @param string $component nazwa komponentu
	* @param string $componentAction nazwa akcji do wykonania
	* @return string kod html zwrocony przez metode plugin-u
	*/
	public function loadPlugins($when, $config, $component, $componentAction)
	{
		if(strstr($component, 'Admin')) {
			$data = explode('_', $component);
			$fromAdminToNormalComponent = $data[0];
		}
		
		$env = (strstr($component, 'Admin')) ? 'backend' : 'frontend';
		$component = (strstr($component, 'Admin')) ? $fromAdminToNormalComponent : $component;
		
		if (isset($config[$env][$when])) {
			foreach ($config[$env][$when] as $action => $plugin_cfg) {
				if ($componentAction == $action) {
					foreach ($plugin_cfg['plugins'] as $name => $cfg) {
						if ($this->plugins[$component][$name] == 1) {
							if(file_exists(DIR_PLUGINS.$name.'/'.DIR_CTRL.$name.'.php')) {
								include(DIR_PLUGINS . $name . '/' . DIR_CTRL . $name . '.php');
								$pName = 'Vf_' . $name . '_Plugin';
								$plugin = new $pName();
								if ($plugin instanceof Vf_Plugin) {
									if (isset($cfg['settings'])) {
										$plugin->configure($cfg['settings']);
									}
									foreach ($cfg['actions'] as $cmpey => $act) {
										try {
											if (method_exists($plugin, $act)) {
												if ($this->acl->is_allowed($name, $act)) {
													$html .= $plugin->$act();
												}
											} else {
												continue;
											}
										} catch (Volta_Acl_Deny_Exception $e) {
											continue;
										}
									}
								} else {
									throw new Exception("Plugin $name nie dziedziczy po klasie Vf_Plugin");
								}
							}
						}
					}
				}	
			}
		}
		return $html;
	}
	
	
	/**
	* Zwraca nazwe kontrolera i jego typ
	* @access private
	* @param string $page
	* @param string $componentName
	* @return string nazwa kontrolera
	*/
	private function setComponentControllerName($page, $componentName)
	{
		if ($page == 'Admin') {
			$component = $componentName . '_Admin';
		}
		else {
			$component = $componentName;
		}
		return $component;
	}
	
	
	/**
	* Laduje plugin dla komponentu na podstawie konfiguracji
	* @access public 
	* @param string $component
	* @param string $componentController
	*/
	private function loadController($component, $componentController)
	{
		if(Vf_Loader::existsFile(DIR_COMPONENTS . $component . '/' . DIR_CTRL . $componentController . '.php')) {
			require_once(DIR_COMPONENTS . $component . '/' . DIR_CTRL . $componentController . '.php');
		} elseif (Vf_Loader::existsFile(DIR_WIDGETS_CTRL . $component . '.php')) {
			require_once(DIR_WIDGETS_CTRL . $component . '.php');
		}
	}
}

?>