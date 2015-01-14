<?php namespace controllers;
use core\view,
	helpers\nocsrf as NoCSRF;

class Login extends \core\controller{

	public function __construct(){

		parent::__construct();

		$this->language->load('login');

	}

	public function index() {

		$data = [
			'title'     => 'Login',
			'formToken' => NoCSRF::generate('token')];
		
		View::rendertemplate('header', $data);
		View::render('login/index', $data);
		View::rendertemplate('footer', $data);

	}

}
