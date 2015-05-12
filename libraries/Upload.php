<?php

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/


class Vf_Upload
{
	protected $data = array();
	
	protected $rename = false;
	
	protected $overwrite = false;
	
	protected $renamed = array();
	
	protected $path;
	
	protected $translate = null;
	
	
	public function __construct()
	{
		$this->translate = Vf_Language::instance();
		$this->translate->get()->load('Upload.php');
		Vf_Loader::loadHelper('Translate');
	}
	
	
	public function send($field)
	{
		if (!isset($_FILES[$field])) {
			return false;
		}
		if (empty($_FILES[$field]['name'])) {
			return false;
		}
		$this->data = $_FILES[$field];
		
		if (is_uploaded_file($_FILES[$field]['tmp_name'])) {
			if (Vf_Loader::existsFile($this->path . $this->getFileName())) {
				if(!$this->rename) {
					if(!$this->overwrite) {
						throw new Vf_Upload_File_Exception(sprintf(Vf_Translate_Helper::__('uploadedFileExists'), $this->getFileName(), $this->getPath()));
					} else {
						unlink($this->path . $this->data['name']);
						$filename = $this->data['name'];
					}
				} else {
					$filename = substr(md5(time()), 0, 10) . $this->getExtension($this->data['name']);
					$this->renamed[] = $filename;
				}
			} else {
				$filename = $_FILES[$field]['name'];
			}
			
			if (!move_uploaded_file($_FILES[$field]['tmp_name'], $this->path . $filename)) {
				throw new Vf_Upload_File_Exception(Vf_Translate_Helper::__('Wystapil blad podczas upload-u pliku'));
			}
			return true;
		}
	}
	
	
	public function multiSend($field)
	{
		if (!isset($_FILES[$field])) {
			return false;
		}
		if (empty($_FILES[$field]['name'][0])) {
			return false;
		}
		
		$uploaded = false;
		$this->data = $_FILES[$field];
		$files = sizeof($this->data['name']);
		
		for ($i = 0; $i < $files; $i++) {
			if (is_uploaded_file($this->data['tmp_name'][$i])) {
				if (Vf_Loader::existsFile($this->path . $this->getFileName($i))) {
					if (!$this->rename) {
						if (!$this->overwrite) {
							throw new Vf_Upload_File_Exception(sprintf(Vf_Translate_Helper::__('uploadedFileExists'), $this->getFileName($i), $this->getPath()));
							return false;
						} else {
							unlink($this->path . $this->data['name'][$i]);
							$filename = $this->data['name'][$i];
						}
					} else {
						$extension = $this->getExtension($this->data['name'][$i]);
						$filename = substr(md5(time() . $extension), 0, 10) . $extension;
						$this->renamed[] = $filename;
					}
				} else {
					$filename = $this->data['name'][$i];
				}
			
				if(!move_uploaded_file($this->data['tmp_name'][$i], $this->path . $filename)) {
					throw new Vf_Upload_File_Exception(Vf_Translate_Helper::__('Wystapil blad podczas upload-u pliku'));
				} else {
					$uploaded = true;
				}
			}
		}
		return $uploaded;
	}
	
	
	public function setPath($path)
	{
		if (!is_dir($path)) {
			throw new Vf_Upload_File_Exception(Vf_Translate_Helper::__('uploadPathNotExists'));
		}
		if (!is_writable($path)) {
			throw new Vf_Upload_Path_Exception(sprintf(Vf_Translate_Helper::__('uploadPathNoPermission'), $path));
		}
		$this->path = $path;
	}
	
	
	public function setRenameFile($rename)
	{
		$this->rename = $rename;
	}
	
	
	public function setOverwriteFile($overwrite)
	{
		$this->overwrite = $overwrite;
	}
	
	
	public function getPath()
	{
		return $this->path;
	}
	
	
	public function getFileInfo()
	{
		return $this->data;
	}
	

	public function getRenamed()
	{
		return $this->renamed;
	}
	
	
	public function getFileName($key = null)
	{
		if($key === null) {
			return $this->data['name'];
		} else {
			return $this->data['name'][$key];
		}
	}
	
	
	public function getFileSize($key = null)
	{
		if($key === null) {
			return $this->data['size'];
		} else {
			return $this->data['size'][$key];
		}
	}
	
	
	public function getFileMime($key = null)
	{
		if ($key === null) {
			return $this->data['type'];
		} else {
			return $this->data['type'][$key];
		}
	}
	
	
	public function getFileError($key = null)
	{
		if ($key === null){
			return $this->data['error'];
		} else {
			return $this->data['error'][$key];
		}
	}	
	
	private function getExtension($filename)
	{
		$extension = explode('.', $filename);
		return '.' . $extension[sizeof($extension)-1];
	}
}

?>
