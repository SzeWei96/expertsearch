<?php
	class Test extends CI_Controller
	{
        function __construct() { 
            parent::__construct(); 
            $this->load->helper('url'); 
            //$this->load->library('session');
        }

        public function  test(){

            $this->load->view('templates/header');
            $this->load->view('view/test'); 
			$this->load->view('templates/footer');
        }

        
    }
?>