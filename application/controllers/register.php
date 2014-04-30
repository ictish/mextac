<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Register extends CI_Controller {
	public function _remap($method, $args) {
		if (method_exists($this, $method)){
			// Call before action
			$this->before();
			return call_user_func_array(array($this, $method), $args);
		}
		show_404();
	}
	
	private function before() {
		if($this->session->userdata('logged_in')){
			redirect('room','refresh');
		}
	}
	
	public function index(){
		$data['message'] = "NOT_SET";
		$this->load->view('registerView',$data);	
	}
	
	public function verifyRegister(){
		$this->load->helper('form');
		
		$this->load->library('form_validation','url');
		
		$this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean');
		$this->form_validation->set_rules('email', 'Email', 'trim|xss_clean|required|valid_email');
		$this->form_validation->set_rules('password', 'Password', 'trim|xss_clean|required');
			
		if ($this->form_validation->run() == FALSE){    // if form validation fails, get form errors and set messages, redirect to register
			$data['message'] = "VALIDATION_ERRORS";
			$this->load->view('registerView',$data);      //if validation fails load the registrationForm
		
		}
		else{                                           //if form validation passes, get data,check duplicate,do redirects 
			$username = htmlspecialchars($_POST['username']);
			$email = htmlspecialchars($_POST['email']);
			$password =sha1(htmlspecialchars($_POST['password']));
			
			$this->load->model('DBFunctions');
			
			$isUserExists = $this->DBFunctions->userExists($email);  //check if user exists
			
			if($isUserExists){                                      // if exists ask for new username and send to register
				$data['message'] = USER_EXISTS;
				$this->load->view('registerView',$data);
			}
			else{			// if doesnot exis, create this user
				$isEnteredIntoDatabase = $this->DBFunctions->register($username,$email,$password);   // entering data into database
			
				if($isEnteredIntoDatabase){
					$data['message']=REGISTRATION_SUCCESSFUL;
					$this->load->view('loginView',$data);          //if registration successful load the loginForm  
				}
				else{
					$data['message']=REGISTRATION_FAILED;
					$this->load->view('registerView',$data);          //if registration failed load the registerForm 	
				}
			}	
		}	
	}	
}