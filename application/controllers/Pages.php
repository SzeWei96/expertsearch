<?php 
	class Pages extends CI_Controller{
		function __construct(){
			parent::__construct();
			$this->load->helper('url');
		}  

		public function view($page = 'home'){
			if (!file_exists(APPPATH.'views/pages/'.$page.'.php')) {
				echo "Sorry, file does not exist";
			}
			$data['title'] = ucfirst($page);
			$this->load->view('templates/header');
			$this->load->view('pages/'.$page, $data);
			$this->load->view('templates/footer');
			//$this->load->view('templates/footer');
		}
	}
	