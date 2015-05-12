<?php

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/


class adminPages_Commons
{
	public function checkAdminPartComponents($components)
	{
		$checked = array();
		foreach ($components as $key => $component) {
			if( Vf_Loader::existsFile(DIR_COMPONENTS . $component . '/' . DIR_CTRL . $component.'_Admin.php')) {
				$cfg_name = DIR_CONFIG . $component;
				$cfg = new Vf_Config($cfg_name, 'Xml');
				
				if($cfg->admin_panel == 1) {
					$checked += array($component => 1);
				} else {
					$checked += array($component => 0);
				}
			} else {
				$checked += array($component => 0);
			}
		}
		return $checked;
	}
	
	
	public function getPluginsActionsDescriptions($before, $after)
	{
		$pluginsActionDescription = array();
		if (is_array($before)) {
			foreach ($before as $data) {
				foreach ($data['plugins'] as $pluginName => $values) {
					$pluginsActionDescription[$pluginName][] = $data['description'];
				}
			}
		}
		if (is_array($after)) {
			foreach ($after as $data) {
				foreach ($data['plugins'] as $pluginName => $values) {
					$pluginsActionDescription[$pluginName][] = $data['description'];
				}
			}
		}
		return $pluginsActionDescription;
	}
	
	
	public function checkIfAddonExists($name, $directory, $addon = 'component')
	{
		$addonName = explode('.', $name);
		
		if ($addon == 'component' || $addon == 'plugin') {
			if (is_dir( $directory . $addonName[0] . '/')) {
				if(@unlink($directory . $name)) {
					return true;
				}
				return true;
			}
		} else {
			if (Vf_Loader::existsFile($directory . $addonName . '.php')) {
				if (@unlink($directory . $addonName . '.php')) {
					return true;
				}
				return true;
			}
		}
		return false;
	}
	
	
	//ta metoda usuwa tez konfiguracje komponentu jesli istnieje
	public function deleteAddonFilesAndDirs($directory, $configPath)
	{
		$deleted = false;
		$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
		$dirs = new DirectoryIterator($directory);
		
		foreach ($files as $file) {
			if (@unlink($file)) {
				$deleted = true;
			} else {
					$deleted = false;
			}
		}
		
		if ($deleted === true) {
			foreach ($dirs as $dir) {
				if (!$dir->isDot()) {
					if ($dir->isDir()) {
						if (is_dir($dir->getPathname() . '/admin') || is_dir($dir->getPathname() . '/notify')) {
							@rmdir($dir->getPathname() . '/admin');
							@rmdir($dir->getPathname() . '/notify');
						}
						if (rmdir($dir->getPathname())) {
							$deleted = true;
						} else {
							$deleted = false;
						}
					}		
				}
			}
			
			if (@unlink($configPath . '/config.xml')) {
				if (substr($configPath, 0, 6) == 'config') {
					if (rmdir($configPath) && rmdir($directory)) {
						$deleted = true;
					} else {
						$deleted = false;
					}
				}
			} else if(rmdir($directory)) {
				$deleted = true;
			} else {
				$deleted = false;
			}
		}
		return $deleted;
	}
	
	
	public function deleteWidgetFiles($widget)
	{
		if (Vf_Loader::existsFile(DIR_WIDGETS_CTRL . $widget . '.php') && Vf_Loader::existsFile(DIR_WIDGETS_VIEWS . $widget . '.php')) {
			@unlink(DIR_WIDGETS_CTRL . $widget . '.php');
			@unlink(DIR_WIDGETS_VIEWS . $widget  .'.php');
			
			if (Vf_Loader::existsFile(DIR_WIDGETS_MODELS . $widget . '.php')) {
				@unlink(DIR_WIDGETS_MODELS.$widget.'.php');
			}
			if (Vf_Loader::existsFile(DIR_WIDGETS_ASSETS . DIR_CSS . $widget . '.css')) {
				@unlink(DIR_WIDGETS_ASSETS . DIR_CSS. $widget . '.css');
			}
			if (Vf_Loader::existsFile(DIR_WIDGETS_ASSETS . DIR_JS . $widget . '.js')) {
				@unlink(DIR_WIDGETS_ASSETS . DIR_JS . $widget . '.js');
			}
			return true;
		}
		return false;
	}
	
	
	public function setExtensionsAndLibraries($allowedPackageType)
	{
		$data = array();
		if($allowedPackageType == 'both') {
			$data['packageType'] = array('rar', 'zip');
			$data['msg'] = 'Upload-owane paczki moga byc spakowane za pomoca Rar i Zip';
		} else {
			$data['packageType'] = array($allowedPackageType);
			$data['msg'] = 'Upload-owane paczki moga byc spakowane za pomoca ' . $allowedPackageType;
		}
		return $data;
	}
	
	
	public function checkCompressionLib()
	{
		if (!extension_loaded('Zip') && !extension_loaded('Rar')) {
			return false;
		} elseif (extension_loaded('Zip') && extension_loaded('Rar')) {
			return 'both';
		} elseif (extension_loaded('Zip')) {
			return 'zip';
		} elseif (extension_loaded('Rar')) {
			return 'rar';
		}
	}
	
	
	public function ZipExtract($bundleExtract, $extractTo)
	{
		$zip = new ZipArchive();
		if ($zip->open($bundleExtract)) {
			if ($zip->extractTo($extractTo) && @unlink($bundleExtract)) {
				$zip->close();
				return true;
			}
		}
		return false;
	}
	
	
	public function RarExtract($bundleExtract, $extractTo)
	{
		if ($rar = rar_open($bundleExtract)) {
			$list = rar_list($rar);
			foreach ($list as $file)  {
				$entry = rar_entry_get($rar, $file);
				$entry->extract($extractTo); 
			}
			rar_close($rar);
			return true;
		}
		return false;
	}
	
	//plik znajduje sie w: components/news/translations.zip a katalogu jest news/ i w nim pliki z tlumaczen
	public function extractTranslations($package)
	{
		if (Vf_Loader::existsFile($package)) {
			$type = $this->checkCompressionLib();
		
			switch ($type) {
				case 'zip':
					return $this->ZipExtract($package, DIR_LANG);
					break;
							
				case 'rar':
					return $this->RarExtract($package, DIR_LANG);
					break;
							
				default:
					return $this->ZipExtract($package, DIR_LANG);
					break;
			}
		}
	}
	
	
	public function deleteTranslations($path)
	{
		if (is_dir(DIR_LANG . $path)) {
			$deleted = false;
			$translationsFiles = glob(DIR_LANG . $path.'/*');
			
			foreach ($translationsFiles as $file) {
				if (is_file($file) && @unlink($file)) {
					$deleted = true;
				} else {
					$deleted = false;
				}
			}
			if($deleted) {
				rmdir(DIR_LANG . $path);
			}
		}
		return $deleted;
	}
	
	
	public function extractConfig($package)
	{
		if(Vf_Loader::existsFile($package)) {
			$type = $this->checkCompressionLib();
		
			switch ($type) {
				case 'zip':
					return $this->ZipExtract($package, DIR_CONFIG);
					break;
							
				case 'rar':
					return $this->RarExtract($package, DIR_CONFIG);
					break;
							
				default:
					return $this->ZipExtract($package, DIR_CONFIG);
					break;
			}
		}
	}
	
	
	public function extractUploadedPackage($bundle, $extractToPath)
	{
		$extractMode = $this->checkCompressionLib();
		
		switch ($extractMode) {
			case 'zip':
				return $this->ZipExtract($bundle, $extractToPath);
				break;
						
						
			case 'rar':
				return $this->RarExtract($bundle, $extractToPath);
				break;
						
						
			case 'both':
				$libType = end(explode('.', $bundle));
				if ($libType == 'zip') {
					return $this->ZipExtract($bundle, $extractToPath);
				} elseif ($libType == 'rar') {
					return $this->RarExtract($bundle, $extractToPath);
				}
				break;
						
			default:
				throw new Exception('Brak biblioteki Zip/Rar nie mozna zainstalowac plugin-u');
				break;
		}
	}
	
	public function extractWidgetBundle($bundle)
	{
		$extracted = $this->extractUploadedPackage($bundle, DIR_WIDGETS);
		
		$bundleName = explode('/', $bundle);
		$bundleName = explode('.', $bundleName[1]);
		
		if ($extracted) {
			if (copy(DIR_WIDGETS  .$bundleName[0] . '/' . DIR_CTRL . $bundleName[0] . '.php', DIR_WIDGETS_CTRL . $bundleName[0] . '.php') 
				&& copy(DIR_WIDGETS . $bundleName[0] . '/' . DIR_VIEWS . $bundleName[0] . '.php', DIR_WIDGETS_VIEWS . $bundleName[0] . '.php')
			) {
				if (Vf_Loader::existsFile(DIR_WIDGETS.$bundleName[0].'/'.DIR_MODELS.$bundleName[0].'.php')) {
					if (copy(DIR_WIDGETS . $bundleName[0] . '/' . DIR_MODELS . $bundleName[0] . '.php', DIR_WIDGETS_MODELS . $bundleName[0] . '.php')) {
						@unlink(DIR_WIDGETS . $bundleName[0] . '/' . DIR_MODELS . $bundleName[0] . '.php');
					}
				}
			
				if (Vf_Loader::existsFile(DIR_WIDGETS . $bundleName[0] . '/' . DIR_ASSETS . DIR_CSS . $bundleName[0] . '.css')) {
					if (copy(DIR_WIDGETS . $bundleName[0] . '/' . DIR_ASSETS . DIR_CSS . $bundleName[0] . '.css', DIR_WIDGETS_ASSETS . DIR_CSS . $bundleName[0] . '.css')) {
						@unlink(DIR_WIDGETS . $bundleName[0] . '/' . DIR_ASSETS . DIR_CSS . $bundleName[0] . '.css');
					}
				}
				
				if (Vf_Loader::existsFile(DIR_WIDGETS . $bundleName[0] . '/' . DIR_ASSETS . DIR_JS . $bundleName[0] . '.js')) {
					if (copy(DIR_WIDGETS . $bundleName[0] . '/' . DIR_ASSETS . DIR_JS . $bundleName[0].'.js', DIR_WIDGETS_ASSETS . DIR_JS . $bundleName[0] . '.js')) {
						@unlink(DIR_WIDGETS . $bundleName[0] . '/' . DIR_ASSETS . DIR_JS . $bundleName[0] . '.js');
					}
				}
			
				@unlink(DIR_WIDGETS . $bundleName[0] . '/' . DIR_CTRL . $bundleName[0] . '.php');
				@unlink(DIR_WIDGETS . $bundleName[0] . '/' . DIR_VIEWS . $bundleName[0] . '.php');
				@rmdir(DIR_WIDGETS . $bundleName[0] . '/' . DIR_CTRL);
				@rmdir(DIR_WIDGETS . $bundleName[0] . '/' . DIR_VIEWS);
				@rmdir(DIR_WIDGETS . $bundleName[0] . '/' . DIR_MODELS);
				@rmdir(DIR_WIDGETS . $bundleName[0] . '/' . DIR_ASSETS . DIR_CSS);
				@rmdir(DIR_WIDGETS . $bundleName[0] . '/' . DIR_ASSETS . DIR_JS);
				@rmdir(DIR_WIDGETS . $bundleName[0] . '/' . DIR_ASSETS);
				@rmdir(DIR_WIDGETS . $bundleName[0] . '/');
				
				return true;
			}
			return false;
		} else {
			return false;
		}
	}	
}

?>