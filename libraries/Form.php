<?php 

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

class Vf_Form
{
	const SELFACTION = true; //$_SERVER['PHP_SELF'];
	
	protected $request = null;
	
	protected $validation = null;
	
	protected $security = null;
	
	protected $captcha = null;
	
	protected $enctype = null;
	
	protected $method = 'post';
	
	protected $action = '';
	
	protected $widgets = array();
	
	protected $attributes = array();
	
	protected $formName = false;
	
	protected $errors = array();
	
	protected $useCaptcha = false;
	
	protected $captchaView = null;
	
	protected $captchaField = 'vf_captcha';
	
	protected $captchaAttributes = array();
	
	protected $csrf = false;
	
	protected $csrfField = 'vf_csrfToken';
	
	protected $useTemplate = false;
	
	protected $template = 'forms/default.php';
	
	protected $withErrors = false; //errors per widget
	
	
	public function __construct()
	{
		$this->request = Vf_Core::getContainer()->request;
		$this->security = Vf_Core::getContainer()->csrf;
		$this->validation = new Vf_Validator();
		$this->captcha = new Vf_Captcha();
		$translate = Vf_Language::instance();
		$translate->get()->load('forms.php');
		Vf_Loader::loadHelper('Translate');
		Vf_Loader::loadHelper('Html');
		Vf_Loader::loadHelper('Forms');
		return $this;
	}
	
	
	public function loadWidgets($widgets)
	{
		if (is_array($widgets)) {
			foreach ($widgets as $widget) {
				if (file_exists(DIR_DRIVERS . 'Form/widgets/' . $widget . '.php')) {
					require_once(DIR_DRIVERS . 'Form/widgets/' . $widget . '.php');
				}
			}
		} elseif (file_exists(DIR_DRIVERS . 'Form/widgets/' . $widgets . '.php')) {
			require_once(DIR_DRIVERS . 'Form/widgets/' . $widgets . '.php');
		}
		return $this;
	}
	
	
	public function loadDecorators($decorators)
	{
		if (is_array($decorators)) {
			foreach ($decorators as $decorator) {
				if (file_exists(DIR_DRIVERS . 'Form/decorators/' . $decorator . '.php')) {
					require_once(DIR_DRIVERS . 'Form/decorators/' . $decorator . '.php');
				}
			}
		} elseif (file_exists(DIR_DRIVERS . 'Form/decorators/' . $decorators . '.php')) {
			require_once(DIR_DRIVERS . 'Form/decorators/' . $decorators . '.php');
		}
		return $this;
	}
	
	
	public function populateFormWidgets(array $triggers)
	{
		if (sizeof($this->widgets) > 0) {
			foreach ($this->widgets as $widget) {
				$widget->setTriggers($triggers[$widget->getFieldName()]);
			}
		}
		return $this;
	}
	
	
	public function addWidget(IFormWidget $element)
	{
		$this->widgets[$element->getFieldName()] = $element;
		return $this;
	}
	
	
	public function getWidgets()
	{
		return $this->widgets;
	}
	
	
	public function getWidget($field)
	{
		if (isset($this->widgets[$field])) {
			return $this->widgets[$field];
		}
		return null;
	}
	
	
	public function setAttribute($name, $value)
	{
		$this->attributes[$name] = $value;
		return $this;
	}
	
	
	public function setAttributes(array $attributes)
	{
		if (sizeof($attributes) > 0) {
			foreach ($attributes as $name => $value) {
				$this->attributes[$name] = $value;
			}
		}
		return $this;
	}
	
	
	public function getAttribute($name)
	{
		if (array_key_exists($name, $this->attributes)) {
			return $this->attributes[$name];
		}
		return null;
	}
	
	
	public function getAttributes($merge = false)
	{
		$attr = array(
			'action' => $this->getAction(),
			'method' => $this->method,
			'enctype' => $this->enctype
		);
		return ($merge === false) ? $this->attributes : array_merge($this->attributes, $attr);
	}
	
	
	public function setFormName($name)
	{
		$this->formName = $name;
		return $this;
	}
	
	
	public function getFormName()
	{
		return $this->formName;
	}
	
	
	public function fill($data)
	{
		$this->validation->add_data($data);
		return $this;
	}
	
	
	public function loadValidator($validator)
	{
		$this->validation->load($validator);
		return $this;
	}
	
	
	public function loadValidators(array $validators)
	{
		foreach ($validators as $validator) {
			$this->validation->load($validator);
		}
		return $this;
	}
	
	
	public function addRule($field, IValidation $validator)
	{
		$this->validation->add_rule($field, $validator);
		return $this;
	}
	
	
	public function isValid()
	{
		$this->validation->validation();
		if (sizeof($this->validation->get_errors()) == 0) {
			if ($this->useCaptcha) {
				if($this->captcha->checkCaptcha($this->request->post($this->captchaField))) {
					return true;
				} else {
					$this->errors = Vf_Translate_Helper::__('Niepoprawmy kod captch-y'); // zmienic na exception Vf_Form_Security_Exception
					return false;
				}
			}
			if ($this->csrf) {
				if ($this->security->csrf_check_token($this->request->post($this->csrfField))) {
					return true;
				} else {
					$this->errors = Vf_Translate_Helper::__('Niepoprawny token');
					return false;
				}
			}
			return true;
		} else {
			$this->errors = $this->validation->get_errors();
			return false;
		}
		return true;
	}
	
	
	public function getErrors()
	{
		return $this->errors;
	}
	
	
	public function hasErrors()
	{
		return (sizeof($this->errors) > 0) ? true : false;
	}
	
	
	public function __call($name, $arguments)
	{
		$call = explode('is', $name);
		$checkType = end($call);
		$className = get_class($arguments[0]);
		$classType = explode('_', $className);
		return ($classType[2] == $checkType) ? true : false;
	}
	
	
	public function display()
	{	
		if ($this->useTemplate)
		{
			$formView = new Vf_View(DIR_VIEW_FORMS . $this->template);
			$formView->form = $this;
			return $formView->render();
		} else {
			$form['open'] = Vf_Html_Helper::tag('form', true, $this->getAttributes(true));
			foreach ($this->widgets as $element) {
				$element->setForm($this); //set this for retrieve errors in element class and can attach it to element widget
				$form[$element->getFieldName()] = $element->display();
			}
			$form['close'] = Vf_Html_Helper::close('form');
			return $form;
		}
	}
	
	
	public function __toString()
	{
		return $this->display();
	}
	
	
	public function setEnctype($enctype)
	{
		$this->enctype = $enctype;
		return $this;
	}
	
	
	public function getEnctype()
	{
		return $this->enctype;
	}
	
	
	public function setMethod($method)
	{
		$this->method = $method;
		return $this;
	}
	
	
	public function getMethod()
	{
		return $this->method;
	}
	
	
	public function setAction($action = self::SELFACTION)
	{
		if($action) {
			$this->action = $_SERVER['PHP_SELF'];
		} else {
			$this->action = $action;
		}
		return $this;
	}
	
	
	public function getAction()
	{
		return (empty($this->action)) ? $_SERVER['PHP_SELF'] : $this->action;
	}
	
	
	public function useTemplate()
	{
		$this->useTemplate = true;
		return $this;
	}
	
	
	public function setTemplate($template)
	{
		$this->template = $template;
		return $this;
	}
	
	
	protected function setCaptchaWidget()
	{
		if($this->useCaptcha) {
			$this->loadWidgets('TextBox');
			$this->loadDecorators('Label');
			if ($this->captchaView == 'form-vertical') {
				$this->addWidget(
					Vf_Form_TextBox_Widget::create()
						->setAttribute('class', 'bs_ form-control')
						->setSetting('label', $this->captcha->getCaptcha())
						->setAttribute('name', $this->captchaField)
						->setAttributes($this->captchaAttributes)
				);
			} elseif ($this->captchaView == 'form-horizontal') {
				//for form-horizontal view
			} elseif ($this->captchaView == 'form-inline') {
				//for form-inline view
			} else {
				$this->addWidget(
					Vf_Form_TextBox_Widget::create()
						->addDecorator(Vf_Form_Label_Decorator::create()->setOption('title', $this->captcha->getCaptcha()))
						->setAttribute('name', $this->captchaField)
						->setAttributes($this->captchaAttributes)
				);
			}
		}
		return null;
	}
	
	
	public function enableCaptcha($view = 'default', array $attributes = array())
	{
		$this->captchaAttributes = $attributes;
		$this->captchaView = $view;
		$this->useCaptcha = true;
		$this->setCaptchaWidget();
		return $this;
	}
	
	
	protected function setCsrfWidget()
	{
		if($this->csrf) {
			$this->loadWidgets('Hidden');
			$this->addWidget(
				Vf_Form_Hidden_Widget::create()
					->setAttribute('name', $this->csrfField)
					->setAttribute('value', '{@csrf_token@}')
			);
		}
		return null;
	}
	
	
	public function enableCsrf()
	{
		$this->csrf = true;
		$this->setCsrfWidget();
		return $this;
	}
}
?>
