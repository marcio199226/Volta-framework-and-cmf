<?php

/**
* Volta framework

* @author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
* @copyright Copyright (c) 2012, marcio
* @version 1.0
*/

require_once(DIR_LIBRARY . 'Controller.php');

class Vf_auth_Component extends Vf_Controller
{
	/**
	* Obiekt klasy Vf_User
	* @access protected
	* @var $user Vf_User object
	*/
	protected $user = null;
	
	/**
	* Obiekt klasy Vf_Auth
	* @access protected
	* @var $auth Vf_Auth
	*/
	protected $auth = null;
	
	/**
	* Obiekt klasy Vf_User
	* @access protected 
	* @var $captcha Vf_Captcha
	*/
	protected $captcha = null;
	
	
	public function __construct()
	{
		$this->user = Vf_Core::getContainer()->user;
		$this->auth = new Vf_Auth($this->user);
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
		if ($this->user->is_guest()) {
			$view = new Vf_View('formAuth', 'component', 'auth');
			$view->loadHelper('Form');
			$view->importFunctions('common');
			$view->remember = $authCfg->remember;
			
			//if is guest and remeber is enabled so try to authenticate user
			if ($authCfg->remember == 1) {
				$this->autoLogIn();
			}
			
			$this->captcha = new Vf_Captcha();
			$this->captcha->setSize(155, 35);
			if($this->detectBruteForce($authCfg)) {
				if (!$req->post('submit_log')) {
					if($this->isExpiredCaptcha($authCfg)) {
						$view->captcha = $this->captcha->getCaptcha();
					}
				}
			}
		} else {
			$view = new Vf_View('menu', 'component', 'auth');
			$view->loadHelper('User');
			$view->user = $req->session('user');
		}
		
		//if anyone click on login button 
		if ($req->post('submit_log')) {
			if ($this->checkCaptcha($authCfg)) {
				$view = new Vf_View('formAuth', 'component', 'auth');
				$view->loadHelper('Form');
				$view->loadHelper('Box');
				$view->remember = $authCfg->remember;
				
				$this->user->set_login($req->post('username'));
				$this->user->set_password($req->post('passwd'));

				$validation = new Vf_Validator();
				$validation->load('str');
				$validation->load('user');
				$validation->add_data($_POST);
				$validation->add_rule('username', new str(array('field' => 'login', 'required' => true, 'alphadigit' => true)));
				
				if ($authCfg->account_ban == 1 && $authCfg->account_activate == 1) {
					$validation-> add_rule('username', new user(array('check_ban' => true, 'check_is_active' => true)));
				} elseif ($authCfg->account_ban == 1) {
					$validation->add_rule('username', new user(array('check_ban' => true)));
				} elseif ($authCfg->account_activate == 1) {
					$validation->add_rule('username', new user(array('check_is_active' => true)));
				}
				
				$validation->add_rule('passwd', new str(array('field' => 'password', 'required' => true, 'alphadigit' => true)));
				$validation->validation();
				
				if (sizeof($validation->get_errors()) == 0) {		
					if ($this->auth->login()) {
					
						$this->updateRestfulApi();
						
						if ($req->post('remember_me') && $authCfg->remember == 1) {
							$autologin = array(
								'login' => $req->post('username'),
								'pwdSha1' => $this->user->data['haslo'],
								'hash' => $this->user->data['hash'],
								'attempts' => 0
							);
							$req->response->setCookie('autologin', json_encode($autologin), $authCfg->remember_lifetime);
						}
						$this->redirect($req->referer());
					} else {
						$this->setIncorrectAttempts($authCfg);
						if($this->detectBruteForce($authCfg)) {
							if($this->isExpiredCaptcha($authCfg)) {
								$view->captcha = $this->captcha->getCaptcha();
							}
						}
						$view->msg = Vf_Translate_Helper::__('Zly login lub haslo');
					}		
				} else {
					$view->errors = $validation->get_errors();
				}
			} else {
				if($this->detectBruteForce($authCfg)) {
					if($this->isExpiredCaptcha($authCfg)) {
						$view->captcha = $this->captcha->getCaptcha();
					}
				}
				$view->invalidCaptcha = Vf_Translate_Helper::__('Zla captcha');
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
	* @access protected
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
	* @access protected
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
	* Incrementuje sesje jesli sa dalsze proby niepoprawnego logowania
	* @access protected
	* @param object Vf_Config konfiguracja config/auth.php
	*/
	protected function setIncorrectAttempts($authCfg)
	{
		$request = Vf_Core::getContainer()->request;
		if ((bool)$authCfg->detect_bruteForce === true && $authCfg->bruteForce_attempts > 0) {
			$attempts = ($request->session('incorrectAttempts') === null) ? 1 : $request->session('incorrectAttempts') + 1;
			$request->response->setSession('incorrectAttempts', $attempts, true);
			if($request->session('incorrectAttempts') == $authCfg->bruteForce_attempts) {
				$request->response->setSession('authCaptchaTime', time(), true);
			}
		}
	}
	
	
	/**
	* Sprawdza czy sesja captchy wygasla
	* @access protected
	* @param object Vf_Config konfiguracja config/auth.php
	* @return boolean
	*/
	protected function isExpiredCaptcha($authCfg)
	{
		if((bool)$authCfg->detect_bruteForce === true && $authCfg->bruteForce_attempts > 0) {
			if (Vf_Core::getContainer()->request->session('authCaptchaTime') !== null) {
				if ($authCfg->bruteForceCaptcha_lifetime > 0
					&& Vf_Core::getContainer()->request->session('authCaptchaTime') > (time() - $authCfg->bruteForceCaptcha_lifetime)) {
					return true;
				} else {
					return false;
				}
			}
		}
		return false;
	}
	
	
	/**
	* Metoda ktora sprawdza czy captcha sie zgadza z ta podana przez uzytkownika
	* @access protected
	* @param object Vf_Config konfiguracja config/auth.php
	* @return boolean
	*/
	protected function checkCaptcha($authCfg)
	{
		if ((bool)$authCfg->detect_bruteForce === true
			&& $authCfg->bruteForce_attempts > 0
			&& Vf_Core::getContainer()->request->session('incorrectAttempts') >= $authCfg->bruteForce_attempts) {
			if ($this->captcha->checkCaptcha(Vf_Core::getContainer()->request->post('authCaptcha'))) {
				unset($_SESSION['incorrectAttempts']);
				unset($_SESSION['authCaptchaTime']);
				return true;
			} elseif ((bool)$authCfg->detect_bruteForce === true
					  && $authCfg->bruteForce_attempts > 0
					  && $authCfg->bruteForceCaptcha_lifetime > 0
					  && Vf_Core::getContainer()->request->session('authCaptchaTime') < (time() - $authCfg->bruteForceCaptcha_lifetime)) {
				unset($_SESSION['incorrectAttempts']);
				unset($_SESSION['authCaptchaTime']);
				return true;
			} else {
				return false;
			}
		}
		return true;
	}
	
	
	/**
	* Metoda ktora sprawdza czy uzytkownik podal niepoprawne dane do logowania wiecej razy niz jest to dozwolone w konfiguracji
	* @access protected 
	* @param object Vf_Config konfiguracja config/auth.php
	* @return boolean
	*/
	protected function detectBruteForce($authCfg)
	{
		if ((bool)$authCfg->detect_bruteForce === true
			&& $authCfg->bruteForce_attempts > 0
			&& Vf_Core::getContainer()->request->session('incorrectAttempts') >= $authCfg->bruteForce_attempts) {
			return true;
		} else {
				return false;
		}
		return false;
	}
	
	
	/**
	* Aktualizujemy ip uzytkownika za kazdym logowaniem(nie autologowaniem) jesli nie posiada klucza restful tworzymy nowy
	* @access protected
	*/
	protected function updateRestfulApi()
	{
		$request = Vf_Core::getContainer()->request;
		$apiKey = Vf_Orm::factory('RestfulApiKey')->setPrimaryKey('username')->find($request->post('username'));
		
		if (!$apiKey->isLoaded()) {
			$apiKey->api_key = substr(sha1($request->post('username') . $request->post('passwd') . time() . uniqid()), 0, 40);
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

			if ($user->login == $autologinData->login && $user->haslo == $autologinData->pwdSha1 && $user->hash == $autologinData->hash) {
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
					$this->redirect('./');
				}
			}
		}
	}
}

?>