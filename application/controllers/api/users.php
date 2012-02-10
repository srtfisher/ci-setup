<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * The User API
 * We interact with our users via our neat API
 *
 * @package		Core
 * @author		srtfisher
*/

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH.'/libraries/REST_Controller.php';

class Users extends REST_Controller
{
     /**
      * The constructor
      *
      * @access     public
      * @return     void
      * @param      void
     **/
     public function __construct()
     {
          parent::__construct();
     }
     
     /**
      * Simple function to get public info about a user
      * The user must be in the same school as the user that the API key was issued to
      *
      * You can find a user based upon a number of variables, 'email', 'user_id' and 'login_name'.
      *
      * @param      void We use GET variables
      * @access     public
     **/
     public function info_get()
     {
          // The variables
          $type = 'null';
          
          if ($this->param('user_id') AND is_numeric($this->param('user_id')) :
               $type = 'user_id';
          elseif ($this->param('email') AND valid_email($this->param('email')) :
               $type = 'email';
          elseif ($this->param('login_name')) :
               $type = 'login_name';
          else :
               $this->response(array('error' => 'No identifier specified.'), 500);
               return;
          endif;
          
          // Now find the user
          $find_user = FALSE;
          switch($type)
          {
               case('user_id');
                    $find_user = Auth::get_row(intval($this->param('user_id')));
               break;
               
               case('login_name');
                    $find_user = Auth::findUserbyLogin($this->param('login_name'));
               break;
               
               case('email');
                    $find_user = Auth::findUserbyEmail($this->param('email'));
               break;
          }
          
          if ( ! $find_user OR is_null($find_user)) :
               $this->response(array('error' => 'No user found.'), 404);
               return;
          endif;
          
          // Now we need to check on the API key and see if it has valid permissions for the user
          $get_api_user = $this->get_api_user();
          
          // Nothing found :(
          if ($get_api_user->num_rows() == 0) :
               $this->response(array('error' => 'Invalid credential - no key found.'), 403);
               return;
          endif;
          
          // More permissions! - If they're not an admin, check it.
          if (Perm::perk_contents('global.level') == 1)
          {
               // Do nothing for the admin role!
          }
          elseif(Perm::perk_contents('global.level') == 2)
          {
               if ($find_user->district_id !== $get_api_user->row()->district_id)
               {
                    $this->response(array('error' => 'Invalid Permissions - API user is not in user\'s district.'), 403);
                    return; 
               }
          } else
          {
               if ($find_user->school_id !== $get_api_user->row()->school_id)
               {
                    $this->response(array('error' => 'Invalid Permissions - API user is not in user\'s school.'), 403);
                    return; 
               }
          }
          
          // We're good, display them
          $to_display = array();
          $to_display['user_id'] = $find_user->user_id;
          $to_display['email'] = $find_user->user_email;
          $to_display['first_name'] = $find_user->user_first_name;
          $to_display['last_name'] = $find_user->user_last_name;
          $to_display['full_name'] = $to_display['first_name'] . ' ' . $to_display['last_name'];
          $to_display['login_name'] = $find_user->user_slug;
          $to_display['role'] = $find_user->role;
          
          $to_display['school_id'] = $find_user->school_id;
          $to_display['district_id'] = $find_user->district_id;
          
          $to_display['create_time'] = $find_user->create_time;
          $to_display['update_time'] = $find_user->update_time;
          $to_display['last_login_time'] = (is_null($find_user->last_login_time) OR empty($find_user->last_login_time)) ? 'false' : $find_user->last_login_time;
          $to_display['student_id'] = (is_null($find_user->student_id) OR $find_user->student_id < 1) ? 'false' : $find_user->student_id;
          
          $this->response($to_display, 200);
     }
     
	function user_get()
    {
        if(!$this->param('id'))
        {
        	$this->response(NULL, 400);
        }

        // $user = $this->some_model->getSomething( $this->param('id') );
    	$users = array(
			1 => array('id' => 1, 'name' => 'Some Guy', 'email' => 'example1@example.com', 'fact' => 'Loves swimming'),
			2 => array('id' => 2, 'name' => 'Person Face', 'email' => 'example2@example.com', 'fact' => 'Has a huge face'),
			3 => array('id' => 3, 'name' => 'Scotty', 'email' => 'example3@example.com', 'fact' => 'Is a Scott!', array('hobbies' => array('fartings', 'bikes'))),
		);
		
    	$user = @$users[$this->param('id')];
    	
        if($user)
        {
            $this->response($user, 200); // 200 being the HTTP response code
        }

        else
        {
            $this->response(array('error' => 'User could not be found'), 404);
        }
    }
    
    function user_post()
    {
        //$this->some_model->updateUser( $this->param('id') );
        $message = array('id' => $this->param('id'), 'name' => $this->post('name'), 'email' => $this->post('email'), 'message' => 'ADDED!');
        
        $this->response($message, 200); // 200 being the HTTP response code
    }
    
    function user_delete()
    {
    	//$this->some_model->deletesomething( $this->param('id') );
        $message = array('id' => $this->param('id'), 'message' => 'DELETED!');
        
        $this->response($message, 200); // 200 being the HTTP response code
    }
    
    function users_get()
    {
        //$users = $this->some_model->getSomething( $this->param('limit') );
        $users = array(
			array('id' => 1, 'name' => 'Some Guy', 'email' => 'example1@example.com'),
			array('id' => 2, 'name' => 'Person Face', 'email' => 'example2@example.com'),
			3 => array('id' => 3, 'name' => 'Scotty', 'email' => 'example3@example.com', 'fact' => array('hobbies' => array('fartings', 'bikes'))),
		);
        
        if($users)
        {
            $this->response($users, 200); // 200 being the HTTP response code
        }

        else
        {
            $this->response(array('error' => 'Couldn\'t find any users!'), 404);
        }
    }


	public function send_post()
	{
		var_dump($this->request->body);
	}


	public function send_put()
	{
		var_dump($this->put('foo'));
	}
}