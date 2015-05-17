<?php

/**
* Volta framework

* @author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
* @copyright Copyright (c) 2012, marcio
* @version 1.0
*/


require_once(DIR_LIBRARY . 'Controller.php');
require_once(DIR_COMPONENTS.'register/' . DIR_LIBRARY.DIR_NOTIFY . 'User_Add.php');

class Vf_register_Component extends Vf_Controller
{
	protected $uri = null;
	
	public function __construct()
	{
		$this->uri = new Vf_Router();
	}
	
	
	public function Index()
	{
		$request = Vf_Core::getContainer()->request;
		$csrf = Vf_Core::getContainer()->csrf;
		Vf_Core::getContainer()->language->get()->load('components/register/newAccount.php');
		
		Vf_Loader::loadHelper('Translate');
		
		$view = new Vf_View('registerForm', 'component', 'register');
		$view->loadHelper('Form');
		$view->loadHelper('Box');
		$view->importFunctions('common');
		
		if ($request->post('submit_register')) {
			if ($csrf->csrf_check_token($request->post('csrf_token'))) {
				$authorizationCfg = new Vf_Config('config.Authorization');
				$registerCfg = new Vf_Config(DIR_CONFIG . 'register', 'Xml');

				$validation = new Vf_Validator();
				$validation->load('str');
				$validation->load('user');
				$validation->load('email');
				$validation->add_data($_POST);
				$validation->add_rule('login', new str(array('field' => 'login', 'required' => true, 'alphadigit' => true, 'between' => array(3, 20))));
				$validation->add_rule('login', new user(array('check_user' => 'login')));
				$validation->add_rule('password', new str(array('field' => 'haslo', 'required' => true, 'compare_pwd' => 're_password', 'alphadigit' => true)));
				$validation->add_rule('re_password', new str(array('field' => 'powtorz haslo', 'required' => true, 'alphadigit' => true)));
				$validation->add_rule('email', new str(array('field' => 'email', 'required' => true, 'email' => true)));
				//$validation->add_rule('email', new email(array('check_email' => 'email')));
				$validation->validation();
				
				if (sizeof($validation->get_errors()) == 0) {
					$code = substr(sha1(time() . uniqid()), 0, 20);
					$account = array(
						'id' => null,
						'login' => $request->post('login'),
						'code' => $code
					);
					
					$account['active'] = ($registerCfg->account_activate == 0) ? 1 : 0;
									
					$registerModel = Vf_Orm::factory('register');
					$registerModel->id = null;
					$registerModel->login = $request->post('login');
					$registerModel->haslo = sha1($authorizationCfg->salt . $request->post('password'));
					$registerModel->hash = substr(md5(time() . uniqid()), 0, 15);
					$registerModel->email = $request->post('email');
					$registerModel->role = 'user';
					$registerModel->save();

					if ($registerModel->isSaved()) {
						//generate and save api key for restful resource
						$apiKey = $this->createRestfulApiKey();
						
						if ($registerCfg->account_activate == 0) {
							$registerModel->addAccountData($account);
							$view->success_add_user = Vf_Translate_Helper::__('Zostales zarejestrowany, mozesz sie zalogowac');
						} else {
							$notify = new Vf_Notify_User_Add();
							$notify->setRecipients($request->post('email'));
							$notify->setSender('admin@eoskar.pl');
							$notify->setSubject(Vf_Translate_Helper::__('Link Aktywacyjny'));
							$notify->setUrl('<a target="_blank" href="http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . ',activeAccount,' . $code.'">'.Vf_Translate_Helper::__('Aktywuj konto').'</a>'); 
							$notify->setMessage(sprintf(
								Vf_Translate_Helper::__('registerMessageEmail')
								, $notify->getUrl(), $apiKey)
							);
							$notify->notify();
							$registerModel->addAccountData($account);
							$view->success_add_user = Vf_Translate_Helper::__('Zostales zarejestrowany, na twoj adres email zostal wyslany link aktywujacy konto');
						}
					} else {
						$view->error_add_user = Vf_Translate_Helper::__('Wystapil blad podczas rejestracji');
					}
				} else {
					$view->errors = $validation->get_errors();
				}
			} else {
				$view->error_add_user = Vf_Translate_Helper::__('Zly token');
			}
		}
		return $view->render();
	}
	
	
	public function activeAccount()
	{
		Vf_Core::getContainer()->language->get()->load('components/register/activation.php');
		Vf_Loader::loadHelper('Translate');
		
		if (strlen($this->uri->getSegment(3)) != 20) {
			throw new Vf_Component_Exception(Vf_Translate_Helper::__('Podany kod aktywacyjny jest nieprawidlowy'));
		}
		
		$accountModel = Vf_Orm::factory('register');
		$isActive = $accountModel->isActiveAccount($this->uri->getSegment(3));
		if (!$isActive) {
			if($accountModel->activeAccount($this->uri->getSegment(3))) {
				$view = new Vf_View('accountActivateConfirm', 'component', 'register');
				$view->loadHelper('Box');
				$view->isDisactive = Vf_Translate_Helper::__('Konto zostalo aktywowane teraz mozesz sie zalogowac');
				return $view->render();
			}
		} else {
			$view = new Vf_View('accountActivateConfirm', 'component', 'register');
			$view->loadHelper('Box');
			$view->isActive = Vf_Translate_Helper::__('To konto zostalo juz wczesniej aktywowane');
			return $view->render();
		}
	}
	
	
	/**
	* Tworzy klucz restful do uzywania api
	* @access protected
	* @return string
	*/
	protected function createRestfulApiKey()
	{
		$request = Vf_Core::getContainer()->request;
		$apiKey = substr(sha1(Vf_Core::getContainer()->request->post('login') . Vf_Core::getContainer()->request->post('password') . time() . uniqid()), 0, 40);
		$apiKeyModel = Vf_Orm::factory('RestfulApiKey');
		$apiKeyModel->api_key = $apiKey;
		$apiKeyModel->username = $request->post('login');
		$apiKeyModel->ip = $request->ip();
		$apiKeyModel->save();
		return $apiKey;
	}
}

?>