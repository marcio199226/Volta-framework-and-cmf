<?php

/**
* Volta framework

* @author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
* @copyright Copyright (c) 2012, marcio
* @version 1.0
*/


class Vf_Upload
{
	/**
	* Skladowa ktora trzyma zmienna globalna $_FILES[$name]
	* @access protected
	* @var array
	*/
	protected $data = array();
	
	/**
	* Czy plik ma zmienic nazwe lub trzymac oryginalna
	* @access protected
	* @var boolean
	*/
	protected $rename = false;
	
	/**
	* Czy nadpisac plik jesli istnieje taki z taka sama nazwa
	* @access protected
	* @var boolean
	*/
	protected $overwrite = false;
	
	/**
	* Tablica z nazwami plikow ktorym trzeba zmienic nazwe
	* @access protected
	* @var boolean
	*/
	protected $renamed = array();

	/**
	* Katalog docelowy
	* @access protected
	* @var string
	*/
	protected $path;
	
	/**
	* Skladowa ktora trzyma obiekt Vf_Language
	* @access protected
	* @var object Vf_Langiage
	*/
	protected $translate = null;
	
	
	/**
	* Tworzy potrzebne obiekty dla klasy
	* @access public
	*/
	public function __construct()
	{
		$this->translate = Vf_Language::instance();
		$this->translate->get()->load('Upload.php');
		Vf_Loader::loadHelper('Translate');
	}
	
	
	/**
	* Wysyla plik na server
    * @access public
	* @param string $field klucz do ktorego ma sie odwolywac metoda w $_FILES[$field]
	* @throws Vf_Upload_File_Exception jesli plik istnieje i nie mozna go nadpisac lub gdy wystapil blad podczas upload-u
	* @return boolean
	*/
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
	
	
	/**
	* Wysyla wiele plikow na server
    * @access public
	* @param string $field klucz do ktorego ma sie odwolywac metoda w $_FILES[$field]
	* @throws Vf_Upload_File_Exception jesli plik istnieje i nie mozna go nadpisac lub gdy wystapil blad podczas upload-u
	* @return boolean
	*/
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
	
	
	/**
	* Ustawia sciezke do upload-u
    * @access public
	* @param string $path sciezka do katalogu
	* @throws Vf_Upload_File_Exception jesli sciezka ni jest katalogiem
	* @throws Vf_Upload_Path_Exception jesli katalog docelowy nie ma praw zapisu
	*/
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
	
	
	/**
	* Setter ktory uwzglednia czy bedzie mozna zmienic nazwe plikom
    * @access public
	* @param boolean $rename
	*/
	public function setRenameFile($rename)
	{
		$this->rename = $rename;
	}
	
	
	/**
	* Setter ktory uwzglednia czy bedzie mozna nadpisac istniejacy plik
    * @access public
	* @param boolean $rename
	*/
	public function setOverwriteFile($overwrite)
	{
		$this->overwrite = $overwrite;
	}
	
	
	/**
	* Getter zwraca sciezke do upload-u
    * @access public
	* @return string 
	*/
	public function getPath()
	{
		return $this->path;
	}
	
	
	/**
	* Getter zwraca $_FILES[$field]
    * @access public
	* @return array 
	*/
	public function getFileInfo()
	{
		return $this->data;
	}
	

	/**
	* Getter zwraca pliki ktorym trzeba zmienic nazwy
    * @access public
	* @return array
	*/
	public function getRenamed()
	{
		return $this->renamed;
	}
	
	
	/**
	* Getter zwraca nazwe upload-owanego pliku
    * @access public
	* @return string 
	*/
	public function getFileName($key = null)
	{
		if($key === null) {
			return $this->data['name'];
		} else {
			return $this->data['name'][$key];
		}
	}
	
	
	/**
	* Getter zwraca wielkosc pliku 
    * @access public
	* @return int
	*/
	public function getFileSize($key = null)
	{
		if($key === null) {
			return $this->data['size'];
		} else {
			return $this->data['size'][$key];
		}
	}
	
	
	/**
	* Getter zwraca typ mime pliku
    * @access public
	* @return string 
	*/
	public function getFileMime($key = null)
	{
		if ($key === null) {
			return $this->data['type'];
		} else {
			return $this->data['type'][$key];
		}
	}
	
	
	/**
	* Getter zwraca blad z $_FILES
    * @access public
	* @return string 
	*/
	public function getFileError($key = null)
	{
		if ($key === null){
			return $this->data['error'];
		} else {
			return $this->data['error'][$key];
		}
	}	
	
	
	/**
	* Getter zwraca rozszerzenie pliku
    * @access public
	* @return string 
	*/
	private function getExtension($filename)
	{
		$extension = explode('.', $filename);
		return '.' . $extension[sizeof($extension)-1];
	}
}

?>
