<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class Index extends CI_Controller {
	
	function __construct(){
		parent::__construct();
		$this->load->library('session');
		$this->load->helper(array('form','url'));
		
		$loginInfo=$this->session->userdata('loginInfo');
		if(empty($loginInfo)){
			redirect('login','index');
		}
	}
	
	
	public function index() {
		$this->load->view ( 'header' );
		$this->load->view ( 'left' );
		$this->load->view ( 'index' );
		$this->load->view ( 'footer' );
	}
}