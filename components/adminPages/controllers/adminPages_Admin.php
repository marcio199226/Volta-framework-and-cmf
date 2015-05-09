<?php

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

require_once(DIR_LIBRARY.'Controller.php');
require_once(DIR_COMPONENTS.'adminPages/'.DIR_LIBRARY.'Commons.php');

class Vf_adminPages_Admin_Component extends Vf_Controller
{
	private $uri = null;
	
	private $request = null;
	
	private $commons = null;
	
	public function __construct()
	{
		$this -> uri = Vf_Core::getContainer() -> router;
		$this -> request = Vf_Core::getContainer() -> request;
		$this -> commons = new adminPages_Commons();
	}
	
	
	public function Index()
	{	
		$component = new Vf_Component();
		
		$pages = Vf_Orm::factory('adminPages') -> getAll();
		$view = new Vf_View('admin/listAllPages', 'component', 'adminPages');
		$view -> loadHelper('Form');
		$view -> loadHelper('Box');
		$view -> loadHelper('Uri');
		$view -> pages = $pages;
		$view -> fronts = $component -> getFrontControllersNames();
		$view -> components = array_merge($component -> getComponentsNames(), $component -> getWidgetsNames());
		$view -> places = $component -> getPlacesNames();
		$view -> cmpAdmin = $this -> commons -> checkAdminPartComponents($component -> getComponentsNames());
		
		if($this -> request -> post('submit_add_page'))
		{
			$validation = new Vf_Validator();
			$validation -> load('str');
			$validation -> add_data($_POST);
			$validation -> add_rule('module', new str(array('field' => 'modul', 'required' => true, 'alpha' => true, 'max' => 20)));
			$validation -> validation();
			
			if(sizeof($validation -> get_errors()) == 0)
			{
				$page = array(
					'id' => null,
					'page' => $this -> request -> post('page'),
					'module' => $this -> request -> post('module'),
					'place' => $this -> request -> post('place'),
					'component' => $this -> request -> post('component')
				);
							
				$model = Vf_Orm::factory('adminPages');
							
				if($model -> addPage($page))
				{
					$this -> redirect($this -> request -> referer());
				}
				else
				{
					$viewError = new Vf_View('admin/addPageError', 'component', 'adminPages');
					$viewError -> loadHelper('Box');
					$viewError -> message = 'Wystapil blad podczas zapisywania do bazy.';
					$view -> error_view = $viewError -> render();
				}
			}
			else
			{
				$viewError = new Vf_View('admin/addPageError', 'component', 'adminPages');
				$viewError -> loadHelper('Box');
				$viewError -> message = $validation -> get_errors();
				$view -> error_view = $viewError -> render();
			}
		}
		return $view -> render();
	}
	
	
	public function deletePage()
	{
		$view = new Vf_View('admin/deletePage', 'component', 'adminPages');
		$view -> loadHelper('Box');
		$view -> message = 'Czy na pewno chcesz usunac ta podstrone?';
		
		if($this -> request -> post('submit_no'))
		{
			$this -> redirect('./');
		}
		else if($this -> request -> post('submit_yes'))
		{
			if(Vf_Orm::factory('adminPages') -> deletePageId($this -> uri -> getSegment(3)))
			{
				$this -> redirect('./');
			}
			else
			{
				$this -> redirect('./');
			}
		}
		return $view -> render();
	}
	
	
	public function changePassword()
	{
		$view = new Vf_View('admin/changePass', 'component', 'adminPages');
		$view -> loadHelper('Form');
		$view -> loadHelper('Box');
		
		if($this -> request -> post('submit_edit_password'))
		{
			$currentUser = Vf_Orm::factory('adminPages') -> find($this -> request -> session('hash'));
			
			if($this -> request -> post('new_password') == $this -> request -> post('new_password_re'))
			{
				$validation = new Vf_Validator();
				$validation -> load('str');
				$validation -> add_data($_POST);
				$validation -> add_rule('old_password', new str(array('field' => 'Stare Haslo', 'required' => true, 'alphadigit' => true, 'between' => array(5, 20))));
				$validation -> add_rule('new_password', new str(array('field' => 'Nowe Haslo', 'required' => true, 'alphadigit' => true, 'between' => array(5, 20))));
				$validation -> add_rule('new_password_re', new str(array('field' => 'Nowe Haslo 2', 'required' => true, 'alphadigit' => true, 'between' => array(5, 20))));
				$validation -> validation();
				
				if(sizeof($validation -> get_errors()) == 0)
				{
					$auth_config = new Vf_Config('config.Authorization');
					
					if(sha1($auth_config -> salt.$this -> request -> post('old_password')) == $currentUser -> haslo)
					{
						$currentUser -> haslo = sha1($auth_config -> salt.$this -> request -> post('new_password'));
						$currentUser -> save();
						
						if($currentUser -> isSaved())
						{
							$view -> success = 'Haslo zostalo zmienione.';
						}
						else
						{
							$view -> error_edit = 'Blad podczas zmiany hasla.';
						}
					}
					else
					{
						$view -> error_edit = 'Podano nieprawidlowe stare haslo.';
					}
				}
				else
				{
					$view -> errors = $validation -> get_errors();
				}
			}
			else
			{
				$view -> error_edit = 'Nowe haslo nie zgadza sie w obydwoch polach.';
			}
		}
		return $view -> render();
	}
	
	
	public function managePlugins()
	{
		$page = $this -> uri -> getSegment(3);
		$module = $this -> uri -> getSegment(4);
		$component = $this -> uri -> getSegment(5);
		$env = ($page == 'Admin') ? 'backend' : 'frontend';
		$model = new Vf_adminPages_Model();
		$plugins = $model -> getComponentPlugins($page, $module, $component);
		
		try
		{
			$componentConfig = new Vf_Config(DIR_CONFIG.$component, 'Xml');
		}
		catch(Vf_Config_Xml_Adapter_Exception $e)
		{
			$view = new Vf_View('admin/managePlugins', 'component', 'adminPages');
			$view -> loadHelper('Box');
			$view -> error = 'Brak pliku konfiguracyjnego lun komponentu';
			return $view -> render();
		}

		$compatible = $componentConfig -> plugins_settings;

		$plugged = array();
		$toPluggedIn = array();
		if(sizeof($plugins) > 0)
		{
			foreach($plugins as $k => $plugin)
			{
				if(in_array($plugin['plugin'], $compatible[$env]['compatible']))
				{
					try
					{
						$toPluggedIn[] = $plugin['plugin'];
						$cfg = new Vf_Config(DIR_PLUGINS.$plugin['plugin'], 'Xml');
						
						$plugged[$plugin['plugin']] = array(
							'id' => $plugin['p_id'],
							'author' => $cfg -> author, 
							'description' => $cfg -> description, 
							'version' => $cfg -> version, 
							'name' => $plugin['plugin'],
							'active' => $plugin['active'],
							'dependsOn' => $cfg -> depends['on'],
							'dependsOff' => $cfg -> depends['off']
						); 
					}
					catch(Vf_Config_Xml_Adapter_Exception $e)
					{
						continue;
					}
				}
			}
		}
		
		if(isset($compatible[$env]['compatible']))
		{
			foreach($compatible[$env]['compatible'] as $pname)
			{
				if(in_array($pname, $toPluggedIn))
					continue;
			
				try
				{
					$cfg = new Vf_Config(DIR_PLUGINS.$pname, 'Xml');
					$plugged[$pname] = array(
						'author' => $cfg -> author, 
						'description' => $cfg -> description, 
						'version' => $cfg -> version, 
						'name' => $pname,
						'page' => $page,
						'module' => $module,
						'component' => $component,
						'active' => 2
					); 
				}
				catch(Vf_Config_Xml_Adapter_Exception $e)
				{
					continue;
				}
			}
		}
		
		$view = new Vf_View('admin/managePlugins', 'component', 'adminPages');
		$view -> loadHelper('Box');
		$view -> plugins = $plugged;
		$view -> component = $component;
		$view -> pluginsActions = $this -> commons -> getPluginsActionsDescriptions($compatible[$env]['before'], $compatible[$env]['after']);
		return $view -> render();
	}

	
	public function disactivePlugin()
	{
		$model = new Vf_adminPages_Model();
		if($model -> turnOffPlugin($this -> uri -> getSegment(3)))
		{
			$this -> redirect($this -> request -> referer());
		}
		else
		{
			$this -> redirect($this -> request -> referer());
		}
	}
	
	
	public function activePlugin()
	{
		$model = new Vf_adminPages_Model();
		if($model -> turnOnPlugin($this -> uri -> getSegment(3)))
		{
			$this -> redirect($this -> request -> referer());
		}
		else
		{
			$this -> redirect($this -> request -> referer());
		}
	}
	
	
	public function addPlugin()
	{
		//$component = (strstr($this -> uri -> getSegment(3), 'Admin')) ? $this -> uri -> getSegment(5).'_Admin' : $this -> uri -> getSegment(5);
		$model = new Vf_adminPages_Model();
		$plugin = array(
			'p_id' => null,
			'p_page' => $this -> uri -> getSegment(3),
			'p_module' => $this -> uri -> getSegment(4),
			'p_component' => $this -> uri -> getSegment(5),
			'plugin' => $this -> uri -> getSegment(6),
			'active' => 1
						
		);
						
		if($model -> addPlugin($plugin))
		{
			$this -> redirect($this -> request -> referer());
		}
		else
		{
			$this -> redirect($this -> request -> referer());
		}
	}
	
	
	public function componentManager()
	{
		$component = new Vf_Component();
		$availableComponents = $component -> getComponentsNames();

		$partial = new Vf_View('admin/_partialUploadPackage', 'component', 'adminPages');
		$partial -> title = 'Zainstaluj komponent';
		$partial -> submitName = 'install_component';
		$partial -> loadHelper('Form');
		
		$view = new Vf_View('admin/addonsManager', 'component', 'adminPages');
		$view -> loadHelper('Box');
		$view -> title = 'Zarzadznie komponentami';
		$view -> table_title = 'Zainstalowane';
		$view -> delete_action = 'deleteComponent';
		$view -> unistall_action = 'unistallComponent';
		$view -> delete_explain = 'Usun* usuwa wszystkie pliki komponentu';
		$view -> unistall_explain = 'Odinstaluj** usuwa wszystkie pliki komponentu razem ze wszystkimi wpisami w bazie danych';
		$view -> addonsManager = 'components';
		$view -> error_msg = 'Brak zainstalowanych komponentow';
		$view -> data = $availableComponents;
		
		$allowedPackageType = $this -> commons -> checkCompressionLib();
		$data = $this -> commons -> setExtensionsAndLibraries($allowedPackageType);
		$valid_extensions = $data['packageType'];
		$partial -> availableLibraries = $data['msg'];
		
		if($this -> request -> post('submit_install_component'))
		{
			$mimes = array(
				'zip' => array('application/zip', 'application/octet-stream'),
				'rar' => array('application/x-rar-compressed', 'application/octet-stream'),
				'both' => array('application/x-rar-compressed', 'application/octet-stream', 'application/zip')
			);
			
			if(!$allowedPackageType)
			{
				$partial -> error = 'Brak bibliolteki Zip i Rar instalacja online niedostepna';
			}
			else
			{
				$validation = new Vf_Validator();
				$validation -> load('upload');
				$validation -> add_data($_FILES);
				$validation -> add_rule('package', new upload(array('extensions' => $valid_extensions, 'mimes' => $mimes[$allowedPackageType])));
				$validation -> validation();
				
				if(sizeof($validation -> get_errors()) == 0)
				{
					$uploader = new Vf_Upload();
					
					try
					{
						$uploader -> setPath(DIR_COMPONENTS);
						$uploader -> send('package');
						
						if(!$this -> commons -> checkIfAddonExists($uploader -> getFileName(), DIR_COMPONENTS))
						{
							if($this -> commons -> extractUploadedPackage(DIR_COMPONENTS.$uploader -> getFileName(), DIR_COMPONENTS))
							{
								$componentName = explode('.', $uploader -> getFileName());
								$schemaPath = DIR_COMPONENTS.$componentName[0].'/schema.txt';
								$translationsPackage = DIR_COMPONENTS.$componentName[0].'/translations.'.end($componentName);
								$cfgPackage = DIR_COMPONENTS.$componentName[0].'/config.'.end($componentName);
							
								$this -> commons -> extractTranslations($translationsPackage);
								$this -> commons -> extractConfig($cfgPackage);
							
								Vf_Loader::loadHelper('Sql');
								if(Vf_Sql_Helper::import($schemaPath))
								{
									$partial -> success = 'Komponent zostal poprawnie zainstalowany';
								}
								else
								{
									$partial -> success = 'Paczka zostala upload-owana poprawnie';
								}
							}
							else
							{
								$partial -> error = 'Blad podczas rozpakowywania archiwum paczki';
							}
						}
						else
						{
							$partial -> error = 'Ten komponent juz jest zainstalowany';
						}
					}
					catch(Exception $e)
					{
						$partial -> error = $e -> getMessage();
					}
					catch(Vf_Upload_Path_Exception $e)
					{
						$partial -> error = $e -> getMessage();
					}
					catch(Vf_Upload_File_Exception $e)
					{
						$partial -> error = $e -> getMessage();
					}
				}
				else
				{
					$partial -> errors = $validation -> get_errors();
				}
			}
		}
		
		$view -> upload_package_form = $partial -> render();
		return $view -> render();
	}
	
	
	public function deleteComponent()
	{	
		$componentDir = $this -> uri -> getSegment(3);
		$deleted = $this -> commons -> deleteAddonFilesAndDirs(DIR_COMPONENTS.$componentDir, DIR_CONFIG.$componentDir);
		$this -> commons -> deleteTranslations($componentDir);
		
		$component = new Vf_Component();
		$availableComponents = $component -> getComponentsNames();
		
		$partial = new Vf_View('admin/_partialUploadPackage', 'component', 'adminPages');
		$partial -> title = 'Zainstaluj komponent';
		$partial -> submitName = 'install_component';
		$partial -> loadHelper('Form');
		
		$view = new Vf_View('admin/addonsManager', 'component', 'adminPages');
		$view -> loadHelper('Box');
		$view -> upload_package_form = $partial -> render();
		$view -> title = 'Zarzadznie komponentami';
		$view -> table_title = 'Zainstalowane';
		$view -> delete_explain = 'Usun* usuwa wszystkie pliki komponentu';
		$view -> unistall_explain = 'Odinstaluj** usuwa wszystkie pliki komponentu razem ze wszystkimi wpisami w bazie danych';
		$view -> error_msg = 'Brak zainstalowanych komponentow';
		$view -> data = $availableComponents;
		
		if($deleted === true)
		{
			$view -> successUD = 'Wszystkie pliki zostaly usuniete';
		}
		else
		{
			$view -> errorUD = 'Wystapil blad podczas usuwania plikow, czesc plikow mogla zostac usunieta';
		}	

		return $view -> render();
	}
	
	
	public function unistallComponent()
	{
		$model = Vf_Orm::factory('adminPages');
		
		$componentName = $this -> uri -> getSegment(3);
		$cfg = new Vf_Config(DIR_CONFIG.$componentName, 'Xml');
		$deleteComponent = $this -> commons -> deleteAddonFilesAndDirs(DIR_COMPONENTS.$componentName, DIR_CONFIG.$componentName);
		$this -> commons -> deleteTranslations($componentName);
		
		$component = new Vf_Component();
		$availableComponents = $component -> getComponentsNames();
		
		$partial = new Vf_View('admin/_partialUploadPackage', 'component', 'adminPages');
		$partial -> title = 'Zainstaluj komponent';
		$partial -> submitName = 'install_component';
		$partial -> loadHelper('Form');
		
		$view = new Vf_View('admin/addonsManager', 'component', 'adminPages');
		$view -> loadHelper('Box');
		$view -> upload_package_form = $partial -> render();
		$view -> title = 'Zarzadznie komponentami';
		$view -> table_title = 'Zainstalowane';
		$view -> delete_explain = 'Usun* usuwa wszystkie pliki komponentu';
		$view -> unistall_explain = 'Odinstaluj** usuwa wszystkie pliki komponentu razem ze wszystkimi wpisami w bazie danych';
		$view -> error_msg = 'Brak zainstalowanych komponentow';
		$view -> data = $availableComponents;
		
		$deleteComponentDb = false;
		if($model -> deleteAddonTable($cfg -> db_table) && $model -> deleteComponentRecords($componentName))
		{
			$deleteComponentDb = true;
		}
		else
		{
			$deleteComponentDb = false;
		}
		
		if($deleteComponent === true && $deleteComponentDb === true)
		{
			$view -> successUD = 'Wszystkie pliki i wpisy zostaly usuniete';
		}
		else
		{
			$view -> errorUD = 'Wystapil blad podczas usuwania plikow i wpisow, ich czesc mogla zostac usunieta';
		}
		
		return $view -> render();
	}
	
	
	public function pluginManager()
	{
		$plugins = new Vf_Component();
		$availablePlugins = $plugins -> getPluginsNames();

		$partial = new Vf_View('admin/_partialUploadPackage', 'component', 'adminPages');
		$partial -> title = 'Zainstaluj plugin';
		$partial -> submitName = 'install_plugin';
		$partial -> loadHelper('Form');
		
		$view = new Vf_View('admin/addonsManager', 'component', 'adminPages');
		$view -> loadHelper('Box');
		$view -> title = 'Zarzadznie plugin-ami';
		$view -> table_title = 'Zainstalowane';
		$view -> delete_action = 'deletePlugin';
		$view -> unistall_action = 'unistallPlugin';
		$view -> delete_explain = 'Usun* usuwa wszystkie pliki plugin-u';
		$view -> unistall_explain = 'Odinstaluj** usuwa wszystkie pliki plugin-u razem ze wszystkimi wpisami w bazie danych';
		$view -> addonsManager = 'plugins';
		$view -> error_msg = 'Brak zainstalowanych plugin-ow';
		$view -> data = $availablePlugins;
		
		$allowedPackageType = $this -> commons -> checkCompressionLib();
		$data = $this -> commons -> setExtensionsAndLibraries($allowedPackageType);
		$valid_extensions = $data['packageType'];
		$partial -> availableLibraries = $data['msg'];
		
		if($this -> request -> post('submit_install_plugin'))
		{
			$mimes = array(
				'zip' => array('application/zip', 'application/octet-stream'),
				'rar' => array('application/x-rar-compressed', 'application/octet-stream'),
				'both' => array('application/x-rar-compressed', 'application/octet-stream', 'application/zip')
			);
			
			if(!$allowedPackageType)
			{
				$partial -> error = 'Brak bibliolteki Zip i Rar instalacja online niedostepna';
			}
			else
			{
				$validation = new Vf_Validator();
				$validation -> load('upload');
				$validation -> add_data($_FILES);
				$validation -> add_rule('package', new upload(array('extensions' => $valid_extensions, 'mimes' => $mimes[$allowedPackageType])));
				$validation -> validation();
				
				if(sizeof($validation -> get_errors()) == 0)
				{
					$uploader = new Vf_Upload();
					
					try
					{
						$uploader -> setPath(DIR_PLUGINS);
						$uploader -> send('package');
						
						if(!$this -> commons -> checkIfAddonExists($uploader -> getFileName(), DIR_PLUGINS))
						{
							if($this -> commons -> extractUploadedPackage(DIR_PLUGINS.$uploader -> getFileName(), DIR_PLUGINS))
							{
								$pluginName = explode('.', $uploader -> getFileName());
								$schemaPath = DIR_PLUGINS.$pluginName[0].'/schema.txt';
								$translationsPackage = DIR_PLUGINS.$pluginName[0].'/translations.'.end($pluginName);
								
								$this -> commons -> extractTranslations($translationsPackage);
							
								Vf_Loader::loadHelper('Sql');
								if(Vf_Sql_Helper::import($schemaPath))
								{
									$partial -> success = 'Plugin zostal poprawnie zainstalowany';
								}
								else
								{
									$partial -> success = 'Paczka zostala upload-owana poprawnie';
								}
							}
							else
							{
								$partial -> error = 'Blad podczas rozpakowywania archiwum paczki';
							}
						}
						else
						{
							$partial -> error = 'Taki plugin jest juz zainstalowany';
						}
					}
					catch(Exception $e)
					{
						$partial -> error = $e -> getMessage();
					}
					catch(Vf_Upload_Path_Exception $e)
					{
						$partial -> error = $e -> getMessage();
					}
					catch(Vf_Upload_File_Exception $e)
					{
						$partial -> error = $e -> getMessage();
					}
				}
				else
				{
					$partial -> errors = $validation -> get_errors();
				}
			}
		}
		
		$view -> upload_package_form = $partial -> render();
		return $view -> render();
	}
	
	
	public function deletePlugin()
	{	
		$pluginDir = $this -> uri -> getSegment(3);
		$deleted = $this -> commons -> deleteAddonFilesAndDirs(DIR_PLUGINS.$pluginDir, DIR_PLUGINS.$pluginDir);
		$this -> commons -> deleteTranslations($pluginDir);
		
		$plugins = new Vf_Component();
		$availablePlugins = $plugins -> getPluginsNames();

		$partial = new Vf_View('admin/_partialUploadPackage', 'component', 'adminPages');
		$partial -> title = 'Zainstaluj plugin';
		$partial -> submitName = 'install_plugin';
		$partial -> loadHelper('Form');
		
		$view = new Vf_View('admin/addonsManager', 'component', 'adminPages');
		$view -> loadHelper('Box');
		$view -> title = 'Zarzadznie plugin-ami';
		$view -> table_title = 'Zainstalowane';
		$view -> delete_action = 'deletePlugin';
		$view -> unistall_action = 'unistallPlugin';
		$view -> delete_explain = 'Usun* usuwa wszystkie pliki plugin-u';
		$view -> unistall_explain = 'Odinstaluj** usuwa wszystkie pliki plugin-u razem ze wszystkimi wpisami w bazie danych';
		$view -> error_msg = 'Brak zainstalowanych plugin-ow';
		$view -> data = $availablePlugins;
		
		if($deleted === true)
		{
			$view -> successUD = 'Wszystkie pliki zostaly usuniete';
		}
		else
		{
			$view -> errorUD = 'Wystapil blad podczas usuwania plikow, czesc plikow mogla zostac usunieta';
		}	

		$view -> upload_package_form = $partial -> render();
		return $view -> render();
	}
	
	
	public function unistallPlugin()
	{
		$model = Vf_Orm::factory('adminPages');
		
		$pluginDir = $this -> uri -> getSegment(3);
		$cfg = new Vf_Config(DIR_PLUGINS.$pluginDir, 'Xml');
		$deleted = $this -> commons -> deleteAddonFilesAndDirs(DIR_PLUGINS.$pluginDir, DIR_PLUGINS.$pluginDir);
		$this -> commons -> deleteTranslations($pluginDir);
		
		$plugins = new Vf_Component();
		$availablePlugins = $plugins -> getPluginsNames();

		$partial = new Vf_View('admin/_partialUploadPackage', 'component', 'adminPages');
		$partial -> title = 'Zainstaluj plugin';
		$partial -> submitName = 'install_plugin';
		$partial -> loadHelper('Form');
		
		$view = new Vf_View('admin/addonsManager', 'component', 'adminPages');
		$view -> loadHelper('Box');
		$view -> title = 'Zarzadznie plugin-ami';
		$view -> table_title = 'Zainstalowane';
		$view -> delete_action = 'deletePlugin';
		$view -> unistall_action = 'unistallPlugin';
		$view -> delete_explain = 'Usun* usuwa wszystkie pliki plugin-u';
		$view -> unistall_explain = 'Odinstaluj** usuwa wszystkie pliki plugin-u razem ze wszystkimi wpisami w bazie danych';
		$view -> error_msg = 'Brak zainstalowanych plugin-ow';
		$view -> data = $availablePlugins;
		
		$deletePluginDb = false;
		if($model -> deleteAddonTable($cfg -> db_table) && $model -> deletePluginRecords($pluginDir))
		{
			$deletePluginDb = true;
		}
		else
		{
			$deletePluginDb = false;
		}
		
		if($deleted === true && $deletePluginDb === true)
		{
			$view -> successUD = 'Wszystkie pliki i wpisy zostaly usuniete';
		}
		else
		{
			$view -> errorUD = 'Wystapil blad podczas usuwania plikow i wpisow, ich czesc mogla zostac usunieta';
		}
		
		$view -> upload_package_form = $partial -> render();
		return $view -> render();
	}
	
	
	public function widgetManager()
	{
		$widgets = new Vf_Component();
		$availableWidgets = $widgets -> getWidgetsNames();

		$partial = new Vf_View('admin/_partialUploadPackage', 'component', 'adminPages');
		$partial -> title = 'Zainstaluj widget';
		$partial -> submitName = 'install_widget';
		$partial -> loadHelper('Form');
		
		$view = new Vf_View('admin/addonsManager', 'component', 'adminPages');
		$view -> loadHelper('Box');
		$view -> title = 'Zarzadznie widget-ami';
		$view -> table_title = 'Zainstalowane';
		$view -> delete_action = 'deleteWidget';
		$view -> delete_explain = 'Usun* usuwa wszystkie pliki widget-u';
		$view -> addonsManager = 'widgets';
		$view -> error_msg = 'Brak zainstalowanych widget-ow';
		$view -> data = $availableWidgets;
		
		$allowedPackageType = $this -> commons -> checkCompressionLib();
		$data = $this -> commons -> setExtensionsAndLibraries($allowedPackageType);
		$valid_extensions = $data['packageType'];
		$partial -> availableLibraries = $data['msg'];
		
		if($this -> request -> post('submit_install_widget'))
		{
			$mimes = array(
				'zip' => array('application/zip', 'application/octet-stream'),
				'rar' => array('application/x-rar-compressed', 'application/octet-stream'),
				'both' => array('application/x-rar-compressed', 'application/octet-stream', 'application/zip')
			);
			
			if(!$allowedPackageType)
			{
				$partial -> error = 'Brak bibliolteki Zip i Rar instalacja online niedostepna';
			}
			else
			{
				$validation = new Vf_Validator();
				$validation -> load('upload');
				$validation -> add_data($_FILES);
				$validation -> add_rule('package', new upload(array('extensions' => $valid_extensions, 'mimes' => $mimes[$allowedPackageType])));
				$validation -> validation();
				
				if(sizeof($validation -> get_errors()) == 0)
				{
					$uploader = new Vf_Upload();
					
					try
					{
						$uploader -> setPath(DIR_WIDGETS);
						$uploader -> send('package');
						
						if(!$this -> commons -> checkIfAddonExists($uploader -> getFileName(), DIR_WIDGETS_CTRL, 'widget'))
						{
							if($this -> commons -> extractWidgetBundle(DIR_WIDGETS.$uploader -> getFileName()))
							{
								$widgetName = explode('.', $uploader -> getFileName());
								$translationsPackage = DIR_WIDGETS.$widgetName[0].'/translations.'.end($widgetName);
								
								$this -> commons -> extractTranslations($translationsPackage);
							
								$partial -> success = 'Paczka zostala upload-owana poprawnie';
							}
							else
							{
								$partial -> error = 'Blad podczas rozpakowywania archiwum paczki';
							}
						}
						else
						{
							$partial -> error = 'Taki widget jest juz zainstalowany';
						}
					}
					catch(Exception $e)
					{
						$partial -> error = $e -> getMessage();
					}
					catch(Vf_Upload_Path_Exception $e)
					{
						$partial -> error = $e -> getMessage();
					}
					catch(Vf_Upload_File_Exception $e)
					{
						$partial -> error = $e -> getMessage();
					}
				}
				else
				{
					$partial -> errors = $validation -> get_errors();
				}
			}
		}
		
		$view -> upload_package_form = $partial -> render();
		return $view -> render();
	}
	
	
	public function deleteWidget()
	{	
		$widgetName = $this -> uri -> getSegment(3);
		$deleted = $this -> commons -> deleteWidgetFiles($widgetName);
		$this -> commons -> deleteTranslations($widgetName);
		
		$widgets = new Vf_Component();
		$availableWidgets = $widgets -> getWidgetsNames();

		$partial = new Vf_View('admin/_partialUploadPackage', 'component', 'adminPages');
		$partial -> title = 'Zainstaluj widget';
		$partial -> submitName = 'install_widget';
		$partial -> loadHelper('Form');
		
		$view = new Vf_View('admin/addonsManager', 'component', 'adminPages');
		$view -> loadHelper('Box');
		$view -> title = 'Zarzadznie widget-ami';
		$view -> table_title = 'Zainstalowane';
		$view -> delete_action = 'deleteWidget';
		$view -> delete_explain = 'Usun* usuwa wszystkie pliki widget-u';
		$view -> addonsManager = 'widgets';
		$view -> error_msg = 'Brak zainstalowanych widget-ow';
		$view -> data = $availableWidgets;
		
		if($deleted === true)
		{
			$view -> successUD = 'Wszystkie pliki zostaly usuniete';
		}
		else
		{
			$view -> errorUD = 'Wystapil blad podczas usuwania plikow, czesc plikow mogla zostac usunieta';
		}	

		$view -> upload_package_form = $partial -> render();
		return $view -> render();
	}
	
	
	public function editConfig()
	{	
		$view = new Vf_View('admin/editComponentConfigFile', 'component', 'adminPages');
		$view -> loadHelper('Form');
		$view -> loadHelper('Box');
		
		if(Vf_Loader::existsFile(DIR_CONFIG.$this -> uri -> getSegment(3).'/config.xml'))
		{
			$view -> config_content = file_get_contents(DIR_CONFIG.$this -> uri -> getSegment(3).'/config.xml');
		}
		else
		{
			$view -> config_content = 'Brak zawartosci';
			$view -> error_edit = 'Nie ma takiego pliku konfiguracyjnego';
		}
		
		if($this -> request -> post('submit_edit_config_file'))
		{
			if(file_put_contents(DIR_CONFIG.$this -> uri -> getSegment(3).'/config.xml', stripslashes($this -> request -> post('cfg_content', false))))
			{
				$view -> success = 'Plik konfiguracyjny zostal edytowany';
			}
			else
			{
				$view -> error_edit = 'Blad podczas edycji pliku';
			}
		}
		return $view -> render();
	}
	
	
	public function usersManager()
	{
		Vf_Loader::loadHelper('Uri');
		$configAuth = new Vf_Config('config.auth', 'Xml');
		
		$model = Vf_Orm::factory('adminPages');
		$pager = new Vf_Pagination();
		$pager -> setTotal($model -> countUsers());
		$pager -> setPerPage(10);
		$pager -> setUriSegment($this -> uri -> getSegment());
		$pager -> setBaseUrl(Vf_Uri_Helper::site(true, $this -> uri -> getDelimiter(), true));
		
		$view = new Vf_View('admin/usersManager', 'component', 'adminPages');
		$view -> loadHelper('Form');
		$view -> loadHelper('Box');
		$view -> users = $model -> getAllUsers($pager -> getOffset(), $pager -> getPerPage());
		$view -> pager = $pager -> display(true);
		
		
		if($this -> request -> post('submit_search_user'))
		{
			if($model -> setPrimaryKey('login') -> find($this -> request -> post('username')) -> isLoaded())
			{
				$base = Vf_Uri_Helper::base(true);
				Vf_Uri_Helper::redirect($base.'Admin,Index,editUserData,'.$model -> id);
			}
			else
			{
				$view -> userNotExists = 'Nie ma uzytkowika o takiej nazwie';
			}
		}
		else if($this -> request -> isAjax()) 
		{
			$user = $this -> request -> post('user');
			
			//ban user
			if($this -> request -> post('action_type') == 'ban_user')
			{
				$expire = $this -> request -> post('expire');
			
				$data = array(
					'ban_id' => null,
					'ban_user' => $user,
					'ban_expire' => $expire
				);
			
				if($model -> banUser($data))
				{
					return json_encode(array('msg' => 'Uzytkownik zostal zbanowany'));
				}
				else
				{
					return json_encode(array('msg' => 'Wyspil blad podczas banowania uzytkownika'));
				}
			}
			//unban user
			else if($this -> request -> post('action_type') == 'unban_user')
			{	
				if($model -> unbanUser($user))
				{
					return json_encode(array('msg' => 'Uzytkownik zostal odbanowany'));
				}
				else
				{
					return json_encode(array('msg' => 'Wyspil blad podczas odbanowania uzytkownika'));
				}
			}
			//active user account
			else if($this -> request -> post('action_type') == 'active_account')
			{
				$user_id = $this -> request -> post('user_id');
				$data = array(
					'login' => $user,
					'code' => substr(sha1(time().uniqid()), 0, 20),
					'active' => 1
				);
				
				if($model -> activeAccount($data, $user_id))
				{
					return json_encode(array('msg' => 'Konto uzytkownika zostalo aktywowane'));
				}
				else
				{
					return json_encode(array('msg' => 'Konto uzytkownika zostalo dezaktywowane'));
				}
			}
			//disable user account
			else if($this -> request -> post('action_type') == 'disable_account')
			{
				$user_id = $this -> request -> post('user_id');
				$data = array(
					'login' => $user,
					'code' => substr(sha1(time().uniqid()), 0, 20),
					'active' => 0
				);
				
				if($model -> disableAccount($data, $user_id))
				{
					return json_encode(array('msg' => 'Konto uzytkownika zostalo aktywowane'));
				}
				else
				{
					return json_encode(array('msg' => 'Konto uzytkownika zostalo dezaktywowane'));
				}
			}
		}
		
		return $view -> render();
	}
	
	
	public function deleteUser()
	{
		Vf_Loader::loadHelper('Uri');
		$model = Vf_Orm::factory('adminPages');
		$pager = new Vf_Pagination();
		$pager -> setTotal($model -> countUsers());
		$pager -> setPerPage(10);
		$pager -> setUriSegment($this -> uri -> getSegment());
		$pager -> setBaseUrl(Vf_Uri_Helper::site(true, $this -> uri -> getDelimiter(), true));
		
		$view = new Vf_View('admin/usersManager', 'component', 'adminPages');
		$view -> loadHelper('Box');
		$view -> loadHelper('Form');
		$view -> users = $model -> getAllUsers($pager -> getOffset(), $pager -> getPerPage());
		
		if($model -> removeUser($this -> uri -> getSegment(3)))
		{
			$view -> msg_user = 'Konto uzytkownika zostalo usuniete';
		}
		else
		{
			$view -> error_user = 'Uzytkownik o takim id nie istnieje';
		}
		
		$view -> pager = $pager -> display(true);
		return $view -> render();
	}
	
	
	public function editUserData()
	{
		$csrf = new Vf_Security();
		
		$view = new Vf_View('admin/addOrEditUser', 'component', 'adminPages');
		$view -> loadHelper('Box');
		$view -> loadHelper('Form');
		$view -> header = 'Edycja uzytkownika';
		
		$userData = Vf_Orm::factory('adminPages') -> setPrimaryKey('id') -> find($this -> uri -> getSegment(3));
		
		if($userData -> isLoaded())
		{
			$view -> action = 'edit';
			$view -> userData = $userData; //if exists return object with user's values
			$view -> aclGroups = $userData -> getAclGroups();
		}
		else
		{
			$view -> userNotExists = 'Uzytkownik o takim id nie istnieje';
		}
		
		if($this -> request -> post('submit_edit_user'))
		{
			if($csrf -> csrf_check_token($this -> request -> post('csrf_token')))
			{
				$validation = new Vf_Validator();
				$validation -> load('str');
				$validation -> load('user');
				$validation -> add_data($_POST);
				$validation -> add_rule('login', new str(array('field' => 'Haslo', 'required' => true, 'alphadigit' => true, 'between' => array(3, 20))));
				$validation -> add_rule('password', new str(array('field' => 'Haslo', 'required' => true, 'alphadigit' => true, 'between' => array(5, 20))));
				$validation -> add_rule('email', new str(array('field' => 'Haslo', 'required' => true, 'email' => true)));
				$validation -> validation();
				
				if(sizeof($validation -> get_errors()) == 0)
				{
					$auth_config = new Vf_Config('config.Authorization');
				
					$userData -> id = $this -> request -> post('id');
					$userData -> login = $this -> request -> post('login');
					$userData -> haslo = sha1($auth_config -> salt.$this -> request -> post('password'));
					$userData -> hash = $this -> request -> post('hash');
					$userData -> email = $this -> request -> post('email');
					$userData -> role = $this -> request -> post('group');
					$userData -> save();
					
					if($userData -> isSaved())
					{
						$view -> success_on_user = 'Konto uzytkownika zostalo edytowane';
					}
					else
					{
						$view -> error_on_user = 'Blad podczac edycji konta';
					}
				}
				else
				{
					$view -> errors = $validation -> get_errors();
				}
			}
			else
			{	
				$view -> error_on_user = 'Zly token';
			}
		}
		
		return $view -> render();
	}
	
	
	public function addNewUser()
	{
		$csrf = new Vf_Security();
		$model = Vf_Orm::factory('adminPages');
		
		$view = new Vf_View('admin/addOrEditUser', 'component', 'adminPages');
		$view -> loadHelper('Box');
		$view -> loadHelper('Form');
		$view -> action = 'add';
		$view -> header = 'Dodaj uzytkownika';
		$view -> aclGroups = $model -> getAclGroups();
		
		if($this -> request -> post('submit_add_user'))
		{
			if($csrf -> csrf_check_token($this -> request -> post('csrf_token')))
			{
				$validation = new Vf_Validator();
				$validation -> load('str');
				$validation -> load('user');
				$validation -> add_data($_POST);
				$validation -> add_rule('login', new str(array('field' => 'Haslo', 'required' => true, 'alphadigit' => true, 'between' => array(3, 20))));
				$validation -> add_rule('login', new user(array('check_user' => 'login')));
				$validation -> add_rule('password', new str(array('field' => 'Haslo', 'required' => true, 'alphadigit' => true, 'between' => array(5, 20))));
				$validation -> add_rule('email', new str(array('field' => 'Haslo', 'required' => true, 'email' => true)));
				$validation -> validation();
				
				if(sizeof($validation -> get_errors()) == 0)
				{
					$auth_config = new Vf_Config('config.Authorization');
			
					$model -> id = null;
					$model -> login = $this -> request -> post('login');
					$model -> haslo = sha1($auth_config -> salt.$this -> request -> post('password'));
					$model -> hash = substr(md5(time().uniqid()), 0, 15);
					$model -> email = $this -> request -> post('email');
					$model -> role = $this -> request -> post('group');
					$model -> save();
					
					//data for insert user as activated account
					$data = array(
						'login' => $this -> request -> post('login'),
						'code' => substr(sha1(time().uniqid()), 0, 20),
						'active' => 1
					);
								
					if($model -> isSaved() && $model -> addAccountData($data))
					{
						$view -> success_on_user = 'Konto uzytkownika zostalo dodane';
					}
					else
					{
						$view -> error_on_user = 'Blad podczac tworzenia konta';
					}
				}
				else
				{
					$view -> errors = $validation -> get_errors();
				}
			}
			else
			{	
				$view -> error_on_user = 'Zly token';
			}
		}
		
		return $view -> render();
	}
	
	
	public function languageManager()
	{
		$translate = Vf_Language::instance();
		$translate -> get() -> load('components/adminPages/manageLanguages.php');
		
		Vf_Loader::loadHelper('Translate');
		
		$locales = new Vf_Language_Model();
		
		$view = new Vf_View('admin/manageLocales', 'component', 'adminPages');
		$view -> loadHelper('Box');
		$view -> loadHelper('Uri');
		$view -> loadHelper('Form');
		$view -> addFlash();
		$view -> importFunctions('common');
		$view -> locales = $locales -> getLocales();
		
		if($this -> request -> post('submit_addLocale'))
		{
			$model = Vf_Orm::factory('adminPages');
			
			$validation = new Vf_Validator();
			$validation -> load('str');
			$validation -> add_data($_POST);
			$validation -> add_rule('locale', new str(array('field' => Vf_Translate_Helper::__('adminPagesLocalesLocale'), 'required' => true, 'alpha' => true, 'max' => 3)));
			$validation -> add_rule('language', new str(array('field' => Vf_Translate_Helper::__('adminPagesLocalesLanguage'), 'required' => true, 'alpha' => true, 'max' => 15)));
			$validation -> validation();
				
			if(sizeof($validation -> get_errors()) == 0)
			{
				$data = array(
					'id' => null,
					'locale' => $this -> request -> post('locale'),
					'language' => $this -> request -> post('language')
				);
								
				if($model -> addLocale($data))
				{
					$this -> redirect($this -> request -> referer());
				}
			}
			else
			{
				$view -> errors = $validation -> get_errors();
			}
		}
		
		return $view -> render();
	}
	
	
	public function deleteLanguage()
	{
		$translate = Vf_Language::instance();
		$translate -> get() -> load('components/adminPages/manageLanguages.php');
		Vf_Loader::loadHelper('Translate');
		
		$model = Vf_Orm::factory('adminPages');
		
		if($model -> removeLocale($this -> uri -> getSegment(3)))
		{
			$this -> request -> response -> flash -> add('languageDeleted', Vf_Translate_Helper::__('adminPagesLocalesDeleted'));
		}
		else
		{
			$this -> request -> response -> flash -> add('languageDeleted', Vf_Translate_Helper::__('adminPagesLocalesNotDeleted'), Vf_Flash::ERROR);
		}
		
		//if we have to redirect call save() method for register all flash messages becouse normally this method is called by system.post.action event but redirect have exit statement
		//added event system.redirect adn registered as save function
		//$this -> request -> response -> flash -> save();
		$this -> redirect('Admin,Index,languageManager');
	}
	
	
	public function clearCache()
	{
		$cache = new Vf_Cache();
		$cache -> DeleteAllCache();
		$this -> redirect($this -> request -> referer());
	}
}

?>