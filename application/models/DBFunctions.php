<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DBFunctions extends CI_Model {

	function __construct()
    {
        parent::__construct();
    }
		
	public function authenticate($email,$password){
		$this -> db -> select('userID, username,email, password');
		$this -> db -> from('users');
		$this -> db -> where('email', $email);
		$this -> db -> where('password', $password);
		$this -> db -> limit(1);

		$query = $this -> db -> get();
		
		if($query -> num_rows() == 1){
			return $query->result();
		}
		else{
			return false;
		}
	}
	
	public function userExists($email){
		$this->db->select('email');
		$this->db->from('users');
		$this->db->where('email',$email);
		$this->db->limit(1);
		
		$query = $this->db->get();
		
		if($query->num_rows == 1){
			return true;
		}
		else{
			return false;
		}
	}
	
	public function register($username,$email,$password){
		$this->db->set('username',$username);
		$this->db->set('email',$email);
		$this->db->set('password',$password);
		
		$this->db->insert('users');

		if($this->db->affected_rows() == 1){
			return $this->db->affected_rows();
		}else{
			return false;
		}	
	}
	
	public function post($username,$posts,$date){
		$this->db->set('username',$username);
		$this->db->set('posts',$posts);
		$this->db->set('time_of_post',$date);
		
		$this->db->insert('posts');
		
		if($this->db->affected_rows() == 1){
			return true;
		}
		else{
			return false;
		}
	}
	
	public function getPosts($page){
		$this->db->select('username,posts,time_of_post');
		$this->db->from('posts');
		$this->db->order_by('time_of_post','DESC');
		$this->db->limit(LENGTH,LENGTH*($page - 1));	
		
		$query = $this->db->get();
		
		if($query->num_rows()>0){
			if($query->num_rows<LENGTH){
				$data = $query->result_array();
				$data['message'] = "MAX_VIEW_REACHED";
			}
			else{
				$data = $query->result_array();    //when MAX_VIEW_NOT_REACHED message is not set
			}
			return $data;
		}
		else{
			return false;
		}
	}
}
?>