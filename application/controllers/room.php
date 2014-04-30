<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

session_start();

class Room extends CI_Controller {
	public function _remap($method, $args) {
		if (method_exists($this, $method)){
			// Call before action
			$this->before();
			return call_user_func_array(array($this, $method), $args);
		}
		show_404();        //if the user enters any other function which is not in this class
	}
	
	private function before() {
		if($this->session->userdata('logged_in')){
			return true;
		}
		else{
			redirect('login','refresh');
		}	
	}	


	public function index(){
		$posts = $this->showPosts("1");
		if($posts){                             //if result exists send the result to room for display
			$this->load->view('roomView',$posts);
		}
		else{                                    
			$data['message'] = "NO_POSTS_YET";
			$this->load->view('roomView',$data);
		}
	}
	
	public function showPosts($parameter){
		$page = (int) $parameter;  //type casting the parameter to integer will cause page to be 0 is it contains alphabets
		$session_data = $this->session->userdata('logged_in');
		
		$this->load->model('DBFunctions');
		$postsFormDB = $this->DBFunctions->getPosts($page);
		
		if($postsFormDB){
			if(!isset($postsFormDB['message'])){
				$postsFormDB['message'] = "NOT_SET";
			}
			$n = count($postsFormDB);
			array_unshift($postsFormDB,$session_data['username'],$n + 2,$page);
			//setting the data to be sent to the view
			for($i = 0; $i < $n + 2; $i++){
				$di = 'di'.$i;
				$data[$di] = $postsFormDB[$i];
			}
			$data['message'] = $postsFormDB['message'];
			return $data;
		}
		else{
			return false;
		}		
	} 
	
	public function ajax($param){
		$posts = $this->showPosts($param);
		if($posts){
			print_r($posts);                         //returns the array to ajax responseText
		}
		else{
			echo("NO!");
		}
	}
	
	public function ajaxPost(){
		if(isset($_POST['username'])&&isset($_POST['status'])){
		 	$username = $_POST['username'];
			$posts = $_POST['status'];
			$date = date("Y-m-d H:i:s");
			
			$this->load->model('DBFunctions');
			$insertPost = $this->DBFunctions->post($username,$posts,$date);
		
			if($insertPost){
				echo($date);
			}
			else{
				echo("false");
			}
		}
		else{
			echo("VARIABLES_NOT_SET");
		}
	}
	
	public function logout(){
		$this->session->unset_userdata('logged_in');
		session_destroy();
		redirect('login', 'refresh');
	}
}

?>
