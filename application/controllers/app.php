<?php
/**
 * The Main Application Controller
 *
 * @access     public
 * @author     srtfisher
 * @package    Core
**/
class App extends MY_Controller
{
	/**
	 * The silly constructor
	 *
	 * @access    public
     **/
     public function __construct()
     {
		parent::__construct();
		
		// Something went wrong
		if (! isset($this->Core)) :
               redirect(site_url());
               exit;
          endif;
          
		// Load the default assets
		$this->Core->load_default_assets();
	}
	
	/**
	 * The homepage - simple landing page
	 * We load different home pages for different people
	 *
	 * @access public
	**/
	public function index()
	{
          // If they aren't logged in
          if (! Auth::logged_in() AND 1 == 2) :
               $this->load->view('shared/header');
     		// $this->load->view('homepage/public');
     		$this->load->view('shared/footer');
     		
     		return;
          endif;
          
          show_error('Welcome!', 200);
	}
	
	
	/**
	 * This the the page that comes from the /signup page
	 * The form to fill out the information about the account, and everything.
	 *
	 * @access    public
	 * @param     string The account type
     **/
     public function signup(/* $acct_type = 'standard' */)
     {
          // They can't be logged in!
		if ( Auth::logged_in() )
          {
               set_error('You can\'t be logged in!');
               redirect(site_url());
               return;
          }
          
          if (isset($_POST['first-name'])) :
               
               $this->form_validation->set_rules('first-name', 'First Name', 'required|alpha');
               $this->form_validation->set_rules('last-name', 'Last Name', 'required|alpha');
               $this->form_validation->set_rules('your-email', 'Email', 'required|valid_email');
               
               // Login Details
               $this->form_validation->set_rules('login-name', 'Login Name', 'required');
               $this->form_validation->set_rules('your-password', 'Password', 'required');
               
               // About School
               $this->form_validation->set_rules('district-name', 'District Name', 'required');
               $this->form_validation->set_rules('school-name', 'School Name', 'required');
               $this->form_validation->set_rules('daily_student_funding', 'Daily Student Funding', 'required');
               $this->form_validation->set_rules('initial_truancy_rate', 'Initial Truancy Rate', 'required|numeric');
               $this->form_validation->set_rules('timezones', 'Timezone', 'required');
               
               // Do the Validating
               if ($this->form_validation->run() == FALSE)
               {
                    set_error(validation_errors());
               }
               else
               {
                    // Register the user
                    $user_id = Auth::create_user_automagically(array(
                         'user_slug'           =>	strtolower($_POST['login-name']),
                         'user_email'          =>	strtolower($_POST['your-email']),
                         'user_pass'           =>	$_POST['your-password'],
                         'user_first_name'     =>	$_POST['first-name'],
                         'user_last_name'      =>	$_POST['last-name'],
                         'role'                => 'district_admin',
                         'create_time'         =>	current_datetime(),
                         'update_user'         => 0,
                         'district_id'         => 2, // This is the internal demo district
                         'isDeleted'           => 0,
                         'school_id'           => 1, // This is an internal demo school
                         'student_id'          => 0,
                    ));
                    
                    // In error?
                    if (is_simple_error($user_id)) :
                         set_error($user_id);
                         redirect('signup');
                         return;
                    endif;
                    
                    // Create the District
                    $this->db->set('district_name', $_POST['district-name']);
                    $this->db->set('timezone', $_POST['timezones']);
                    $this->db->set('create_time', current_datetime());
                    $this->db->set('isDeleted', 0);
                    $this->db->set('status', 'pending');
                    $this->db->insert('district');
                    
                    $district_id = $this->db->insert_id();
                    
                    // Create the school
                    $this->db->set('district_id', $district_id);
                    $this->db->set('school_name', $_POST['school-name']);
                    $this->db->set('daily_student_funding', $_POST['daily_student_funding']);
                    $this->db->set('initial_truancy_rate', $_POST['initial_truancy_rate']);
                    $this->db->set('current_truancy_rate', 0);
                    $this->db->set('create_time', current_datetime());
                    $this->db->set('isDeleted', 0);
                    $this->db->insert('school');
                    
                    $school_id = $this->db->insert_id();
                    
                    // Update the user
                    Auth::update('district_id', $district_id, $user_id);
                    Auth::update('school_id', $school_id, $user_id);
                    
                    // Log them in
                    Auth::do_auth($user_id);
                    
                    set_good('Your account has been created.');
                    
                    redirect(site_url());
                    return;
		}
               
          endif; // On Post
          $this->Core->set_title('Register!');
          
          $this->load->view('shared/header');
		$this->load->view('signup/register');
		$this->load->view('shared/footer');

     }
     
	/**
	 * Forgot Password Page
	 *
	 * @param     string The hash if they are coming from an email
	**/
	public function action_forgot_pass($hash = '')
	{
		if (logged_in())
		{
			set_error('You cannot be logged in to get a new password, silly.');
			redirect('home');
		}
		
		// On callback from an email link
		$hash = trim($hash);
		if (! empty($hash))
		{
			$process = Auth::process_forgot_pass($hash);
			if ( is_simple_error($process))
			{
				set_error($process);
				redirect();
				return;
			}
			elseif (! $process)
			{
				set_error('Something went wrong - try again!');
				redirect();
				return;
			}
			
			set_good('Password activated!');
			redirect();
		}
		
		
		// On submit
		if (isset($_POST['email']))
		{
			if (! valid_email($_POST['email']))
			{
				set_error('That isn\'t a valid email.');
				redirect('forgot-password');
				return;
			}
			
			// Find the user
			$email = $_POST['email'];
			$find = Auth::findUserbyEmail($email);
			if (! $find )
			{
				set_error('We couldn\'t find a user with that email.');
				redirect('forgot-password');
				return;
			}
			
			// Found! :D
			$generate = Auth::generate_forgot_pass($find->user_id);
			if (! $generate )
			{
				set_error('There was an error - try again!');
				redirect('forgot-password');
				return;
			}
			
			$this->email->to($find->user_email);
			$this->email->from('info@truanttoday.com', 'TruantToday');
			$this->email->subject('Forgotten Password at TruantToday');
			$this->email->message('Hi,<br />
			It seems that you forgot your password! Luckily we can get you a new one really quickly. Just click below and your new password below will be activated.<br /><br />
			
			<b>New Password:</b> '.$generate->pass.'<br />
			<b>Activate it</b> - <a href="'.site_url('forgot/'.$generate->hash).'"><font color="black">'.site_url('forgot/'.$generate->hash).'</font></a>.');
			
			$this->email->send();
			
			set_good('You have a new password in your inbox.');
			redirect();
			return;
		}
		
		$this->load->view('shared/header');
		$this->load->view('forgot_pass');
		$this->load->view('shared/footer');
	}
	
	/**
	 * The 404 Handler
	 *
	 * @access    public
     **/
     public function action_four_oh_four()
     {
          $this->Core->set_title('404: File not Found');
          $this->output->set_status_header('404');
          
          $this->load->view('shared/header');
		$this->load->view('404');
		$this->load->view('shared/footer');

     }
     
	/**
	 * Manage a user's login
	 *
	 * @access public
	**/
	public function action_login_post()
	{
		// Must be logged out
		if (Auth::logged_in())
		{
			set_error('You must be logged out to login!');
			redirect(site_url());
			return;
		}
		
		// No post or invalid post
		if (! isset($_POST['username']) OR ! isset($_POST['password']))
		{
			set_error("You didn't submit a username/password.");
			redirect(site_url());
			return;
		}
		
		// Do the auth method
		$do_auth = Auth::auth_with_check($_POST['username'], $_POST['password']);
		
		// We're in error
		if (is_simple_error($do_auth))
		{
			set_error($do_auth);
			redirect(site_url());
		}
		
		//	Auth passed!
		set_good('Welcome!');
		$login_redirect = $this->session->userdata('login_redirect');
		
		if (! $login_redirect) :
               redirect(base_url());
		else :
			redirect($login_redirect);
          endif;
	}
	
	/**
	 * Log a user out
	 *
	 * @access    public
	 * @param     void
     **/
     public function action_logout_get()
	{
		Auth::logout();
		
		// Send them away!
		set_good('Goodbye!');
		redirect();
	}
	
	/**
	 * A task to run when you want to deploy new, updates assets to the production servers
	 *
	 * @access    public
	 * @param     int
     **/
     public function action_compile_get()
     {
          $css_combi = ABSPATH . DS.'assets'.DS.'css'.DS.'compiled'.DS;
          $js_combi = ABSPATH . DS.'assets'.DS.'js'.DS;
          
          $js_files = array(
               'tipsy.js',
               'facebox.js',
               'tablesort.js',
               'bootstrap.js',
               'app.js',
          );
		
		$css_files = array(
			'bootstrap.css',
			'facebox.css',
			'main.css'
		);
		
		$css_combined = '';
		foreach($css_files as $file)
		   $css_combined .= file_get_contents($css_combi.$file);
          
          if (file_exists($css_combi.'combo.css'))
               unlink($css_combi.'combo.css');
          
          // Minify CSS
          require_once(dirname(dirname(__FILE__)).DS.'libraries'.DS.'cssmin'.EXT);
          
          $min = new Minify_CSS();
          $css_combined = $min->minify($css_combined, array('preserveComments'=> FALSE, 'prependRelativePath' => NULL));
          
          // Removing Lines
          $css_combined = str_replace(PHP_EOL, '',$css_combined);
          
          $open = fopen($css_combi.'combo.css', 'w');
          fwrite($open, $css_combined);
          fclose($open);
          
          // Let's do it for the JS now!
          $js_combined = '';
		foreach($js_files as $file) :
		   $js_combined .= file_get_contents($js_combi.$file);
          endforeach;
          
          
          // Minify them
          $this->load->library('curl');
          
          $js_combined = $this->curl->simple_post('http://marijnhaverbeke.nl/uglifyjs', array('js_code' => $js_combined), array());
          
          if (file_exists($js_combi.'combo.js'))
               unlink($js_combi.'combo.js');
          
          $open = fopen($js_combi.'combo.js', 'w');
          fwrite($open, $js_combined);
          fclose($open);
          
          echo "CSS and JS files compilied".PHP_EOL;
     }
     
     /**
      * Migrate to the latest migration
      *
      * @access     private
     **/
     public function action_up_migration_get()
     {
          $this->load->library('migration');
          $this->migration->current();
     }
}

/* End of file app.php */