<?php

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

require_once(DIR_LIBRARY.'Controller.php');

class Vf_auth_Component extends Vf_Controller
{

	/**
	* Obiekt klasy Vf_User
	* @access public 
	* @var $user Vf_User object
	*/
	protected $user = null;
	
	/**
	* Obiekt klasy Vf_Auth
	* @access public 
	* @var $auth Vf_Auth
	*/
	protected $auth = null;
	
	
	public function __construct()
	{
		$this -> user = Vf_Core::getContainer()->user;
		$this -> auth = new Vf_Auth($this->user);
	}
	
	
	/**
	* Glowna akcja komponentu odpowiedzialna za logowanie
	* @access public 
	* @return string zawartosc widoku
	*/
	public function Index()
	{	
		$req = Vf_Core::getContainer()->request;
		$authCfg = new Vf_Config('config.auth', 'Xml');
		
		$translate = Vf_Language::instance();
		$translate->get()->load('components/auth/auth.php');
		Vf_Loader::loadHelper('Translate');
		
		//check if session is expired, if so delete session
		$this->checkExpirationOfSession($authCfg);
		//check if autologin attempts was performed more times than allowed in configuration
		$this->checkAutologinAttempts($authCfg);
		
		//view for guest login panel
		if ($this -> user -> is_guest()) {
			$view = new Vf_View('formAuth', 'component', 'auth');
			$view -> loadHelper('Form');
			$view -> importFunctions('common');
			$view -> remember = $authCfg -> remember;
			
			//if is guest and remeber is enabled so try to authenticate user
			if ($authCfg->remember == 1) {
				$this -> autoLogIn();
			}
		} else {
			$view = new Vf_View('menu', 'component', 'auth');
			$view -> loadHelper('User');
			$view -> user = $req->session('user');
		}
		
		//if anyone click on login button 
		if ($req->post('submit_log')) {
			$view = new Vf_View('formAuth', 'component', 'auth');
			$view->loadHelper('Form');
			$view->loadHelper('Box');
			$view->remember = $authCfg->remember;
			
			$this-> user -> set_login($req -> post('login'));
			$this-> user -> set_password($req -> post('password'));

			$validation = new Vf_Validator();
			$validation-> load('str');
			$validation-> load('user');
			$validation-> add_data($_POST);
			$validation-> add_rule('login', new str(array('field' => 'login', 'required' => true, 'alphadigit' => true)));
			
			if ($authCfg->account_ban == 1 && $authCfg->account_activate == 1) {
				$validation-> add_rule('login', new user(array('check_ban' => true, 'check_is_active' => true)));
			} elseif ($authCfg->account_ban == 1) {
				$validation->add_rule('login', new user(array('check_ban' => true)));
			} elseif ($authCfg->account_activate == 1) {
				$validation->add_rule('login', new user(array('check_is_active' => true)));
			}
			
			$validation->add_rule('password', new str(array('field' => 'password', 'required' => true, 'alphadigit' => true)));
			$validation->validation();
			
			if (sizeof($validation->get_errors()) == 0) {		
				if ($this->auth->login()) {
				
					$this->updateRestfulApi();
					
					if ($req->post('remember_me') && $authCfg->remember == 1) {
						$autologin = array(
							'login' => $req->post('login'),
							'pwdSha1' => $this->user->data['haslo'],
							'hash' => $this->user->data['hash'],
							'attempts' => 0
						);
						$req->response->setCookie('autologin', json_encode($autologin), $authCfg->remember_lifetime);
					}
					$this->redirect($req->referer());
				} else {
					$view->msg = Vf_Translate_Helper::__('Zly login lub haslo');
				}		
			} else {
				$view->errors = $validation->get_errors();
			}
		}
		return $view->render();
	}
	
	
	/**
	* Akcja odpowiedzialna za wylogowanie razem usunieciem cookie ktore odpowiada za autologin
	* @access public 
	*/
	public function logout()
	{
		if (!$this->user->is_guest()) {
			$authCfg = new Vf_Config('config.auth', 'Xml');
			$this->auth->logout();
			
			if ($authCfg->remember == 1 && Vf_Core::getContainer()->request->cookie('autologin')) {
				Vf_Core::getContainer()->request->response->setCookie('autologin', null, time() - 3600);
			}
			$this->redirect('./');
		}
	}
	
	
	/**
	* Akcja odpowiedzialna za wylogowywanie uzytkownika
	* @access protected
	*/
	protected function sessionOnlyLogout()
	{
		if (!$this->user->is_guest()) {
			$this->auth->logout();
		}
	}
	
	
	/**
	* Metoda sprawdza czy sesja wygasla jesli tak wylogowywuje uzytkownika
	* @access public 
	* @param object Vf_Config konfiguracja config/auth.php
	*/
	protected function checkExpirationOfSession($config)
	{
		if (Vf_Core::getContainer()->request->session('time') < time() - $config->session_lifetime) {
			$this->sessionOnlyLogout();
		}
	}
	
	
	/**
	* Metoda ktora sprawdza czy uzytkownik zostal zalogowany automatycznie okreslona ilosc razy jesli tak wyloguj go
	* @access public 
	* @param object Vf_Config konfiguracja config/auth.php
	*/
	protected function checkAutologinAttempts($config)
	{
		$autologinData = json_decode(Vf_Core::getContainer()->request->cookie('autologin', false));
		if ($config->autologin_attempt > 0 && $autologinData->attempts > $config->autologin_attempt) {
			$this->logout();
		}
	}
	
	
	/**
	* Aktualizujemy ip uzytkownika za kazdym logowaniem(nie autologowaniem) jesli nie posiada klucza restful tworzymy nowy
	* @access public 
	*/
	protected function updateRestfulApi()
	{
		$request = Vf_Core::getContainer()->request;
		$apiKey = Vf_Orm::factory('RestfulApiKey')->setPrimaryKey('username')->find($request->post('login'));
		
		if (!$apiKey -> isLoaded()) {
			$apiKey->api_key = substr(sha1($request->post('login') . $request->post('password') . time() . uniqid()), 0, 40);
			$apiKey->username = $request->post('login');
		}
		$apiKey->ip = $request->ip();
		$apiKey->save();
	}
	
	
	/**
	* Autologowanie uzytkownika na podstawie cookie
	* @access public
	*/
	protected function autoLogIn()
	{
		$authCfg = new Vf_Config('config.auth', 'Xml');
		//if user isnt logged in
		if (Vf_Core::getContainer()->request->cookie('autologin') !== null) {
			$autologinData = json_decode(Vf_Core::getContainer()->request->cookie('autologin', false));
			$user = Vf_Orm::factory('auth')->find($autologinData->login);

			if ($user -> login == $autologinData->login && $user->haslo == $autologinData->pwdSha1 && $user->hash == $autologinData->hash) {
				//set username and passwd from cookie
				$this->user->set_login($autologinData->login);
				$this->user->set_password($autologinData->pwdSha1);
				
				//disable hash procedure becouse password from cookie is hashed
				$this->auth->getAdapter()->hashPassword(false);
				if ($this->auth->login()) {
					//if autlogin attempts is set increment attempts in cookie on autologin action
					if ($authCfg->autologin_attempt > 0) {
						$autologin = array(
							'login' => $user->login,
							'pwdSha1' => $user->haslo,
							'hash' => $user->hash,
							'attempts' => $autologinData->attempts + 1
						);
						Vf_Core::getContainer()->request->response->setCookie('autologin', json_encode($autologin), $authCfg->remember_lifetime);
					}
					$this -> redirect('./');
				}
			}
		}
	}
}

?>