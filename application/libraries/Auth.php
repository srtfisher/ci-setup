<?php
/**
 * A Reusable Auth System for users
 * Handling all the auth items and the user information.
 *
 * @since      1.0
 * @package    Core
**/
class Auth
{
	/**
	 * The current user row
	 * Default is NULL.
	 *
	 * @global object
	**/
	private static $user_row =        NULL;
	
	/**
	 * The user cookie name
	 *
	 * @global string
	**/
	private static $cookie_name =     'srt_';
	
	/**
	 * The CI Object
	 *
	 * @global object
     **/
     private static $CI =               NULL;
     
	/**
	 * The init function to setup everything
	 *
	 * @access public
	 * @return void
	**/
	public static function init()
	{
		self::$CI = get_instance();
		
		// Set the cookie name to something unique
		$explode = explode('.', $_SERVER['HTTP_HOST']);
		
		$hash = md5(
		   $explode[count($explode)-2].'.'.$explode[count($explode)-1]
		);
		
		self::$cookie_name = strtolower(ENVIRONMENT).'_'.$hash;
		
		// Setup the user session
		self::_setup_user();
	}
	
	/**
	 * Is the user a moderator?
	 *
	 * @access public
	 * @return bool
	**/
	public static function is_mod()
	{
		return (in_array(self::uid(), array(1, 49))) ? TRUE : FALSE;
	}
	
	/**
	 * Set the current user internally in the object
	 *
	 * @access    public
	 * @param     int
	 * @return    void
     **/
     public static function set_current_user($user)
     {
          self::$user_row = self::get_row($user);
     }
	
	/**
	 * Generate the cookie value for a user.
	 *
	 * The cookie data will be this:
	 * uid::hash-of-uid-and-then-the-sites-encryption-key
	 *
	 * Example:
	 * <code>
	 * 11::bncwhfib4wiyfbw4yibwiubfwfwfwfwefwef
	 * // "11" is the user ID
	 * // "bncwhfib4wiyfbw4yibwiubfwfwfwfwefwef" is the md5 hash of 
	 * 11.encryptionkeu
	 * </code>
	 * 
	 * @access public
	 * @param int The user's ID
	 * @return string|bool
	**/
	public static function get_cookie_value($uid)
	{
		return $uid.'::'.md5($uid. self::$CI->config->item('encryption_key'));
	}
	
	/**
	 * Setup the user
	 * If they have a session set in the session userdata, load that. 
	 * We are also going to check if they have a remember me cookie set.
	 * From that, we can load the session.
	 *
	 * @access private
	**/
	private static function _setup_user()
	{
		//	Check the session userdata
		$userdata = self::$CI->session->userdata('auth_session');
		
		//	They don't have a userdata set. Check the remember me.
		if (! $userdata)
		{
			//	If the session doesn't exist, check for a remember me cookie.
			if (! isset($_COOKIE[self::$cookie_name]))
				return FALSE;
			
			$cookie = $_COOKIE[self::$cookie_name];
			$decode = self::$CI->encrypt->decode($cookie);
			
			//	It's an invalid cookie, stop there.
			if ( ! $decode OR ! strpos($decode, '::'))
				return FALSE;
			
			list($uid, $hash) = explode('::', $decode, 2);
			
			//	The standard value for the cookie for the user
			$standard_value = self::get_cookie_value($uid);
			
			//	Invalid cookie data (they could have also have changed the encryption key, too)
			if ($decode !== $standard_value)
			{
				delete_cookie(self::$cookie_name);
				return FALSE;
			}			
			
			$obj = new stdClass;
			$obj->uid = $uid;
			$obj->ses_hash = $hash;
			
			self::$CI->session->set_userdata('auth_session', serialize($obj));
			$row = $obj;
		}
		else
		{
			$row = maybe_unserialize($userdata);
		}
          
          if (is_null($row->uid))
          {
               // Something went bad
               return false;
          }
          
          self::$user_row = self::get_row($row->uid);
	}
	
	/**
	 * A user's ID
	 *
	 * @return bool|int
	**/
	public static function uid()
	{
		if (! self::logged_in() )
			return FALSE;
		else
			return (int) self::$user_row->user_id;
	}
	
	/**
	 * Is the user logged in?
	 *
	 * @return bool
	**/
	public static function logged_in()
	{
		return (is_null(self::$user_row)) ? FALSE : TRUE;
	}
	
	/**
	 * Is the user logged in?
	 *
	 * @deprecated
	 * @return bool
	**/
	public static function is_logged_in()
	{
		return (is_null(self::$user_row)) ? FALSE : TRUE;
	}
	
		
	/** 
	 * Get user data from the user's table.
	 *
	 * @param string The data column to get
	 * @param int $ID The user ID (optional)
	 * @param bool $no_cache Don't use the cache? (default is false)
	**/
	public static function get($data, $ID = FALSE, $no_cache = FALSE)
	{
		$data = (string) $data;
		$no_cache = (bool) $no_cache;
		
		//	Format the data
		$data = strtolower( $data );
		
		//	Fixing transision issues.
		switch($data)
		{
			case('id');
				$data = 'user_id';
			break;
			
			case('user_full');
			case('name');
				$data = 'user_name';
			break;
			
			case('slug');
			case('user_login');
				$data = 'user_slug';
			break;
		}
		
		$row = self::get_row($ID, $no_cache);
		if (! $row)
			return FALSE;
		
		return ($data == 'user_id') ? intval($row->$data) : $row->$data;
	}
	
	/**
	 * A simple function to get a user's name
	 * We save them in two separate fields. This function
	 * just combines them.
	 *
	 * @access public
	 * @param int
      * @return bool|string
     **/
     public static function get_name($user = false)
     {
          if (! self::logged_in() )
               return FALSE;
          
          return self::get('user_first_name', $user) . ' ' . self::get('user_last_name', $user);
     }
     
	/**
	 * Get an entires user's row
	 * We cache this so it won't be that much of a hit on the server.
	 * We no longer support unserializing the data. Use the user meta for that.
	 *
	 * @access    public
	 * @param     int
	 * @param     bool
	 * @return    object|NULL
	**/
	public static function get_row($ID = FALSE, $no_cache = FALSE)
	{
		//	If they didn't pass an ID.
		if (! $ID )
		{
			if (! self::logged_in())
				return FALSE;
			$ID = self::uid();
		}
		
		//	Check the cache.
		if ( ! $no_cache AND ! defined('NO_CACHE')) :
			if ( $cache = self::$CI->cache->get('user_data_'. $ID, 'userdata' ) )
				return $cache;
		endif;
		
		//	We need to get cache this result.
		self::$CI->db->where('user_id', $ID);
		self::$CI->db->where('isDeleted', 0);
		$lookup = self::$CI->db->get('users', 1, 0);
		
		//	Did we find anything?
		if ( $lookup->num_rows() == 0 )
			return NULL;
		
		//	Cache the result.
		$row = $lookup->row();
		self::$CI->cache->save('user_data_' . $ID, $row, 600);
		
		//	Return; We're done!
		return $row;
	}
	
	/** 
	 * User meta is how we are semantically storing snippets of data about a user.
	 *
	 * @param     string $key The meta KEY
	 * @param     int $ID The user ID (optional)
	 * @param     bool $no_cache Should we not use the cache? (Defaults to false)
	 * @return    string|mixed
	**/
	public static function get_meta($key, $ID = FALSE, $no_cache = FALSE)
	{	
		//	If they didn't pass an ID.
		if (! $ID )
		{
			if (! self::logged_in())
				return FALSE;
			$ID = self::uid();
		}
		
		//	Check the cache!
		if ( ! $no_cache AND ! defined('NO_CACHE') ) :
			if ( $cache = self::$CI->cache->get('user_' . $key . '_' . $ID, 'usermeta' ) )
				return maybe_unserialize( $cache );
		endif;
		
		//	Query.
		self::$CI->db->where('meta_user', $ID);
		self::$CI->db->where('meta_key', $key);
		$lookup = self::$CI->db->get('usermeta');
		
		//	Did we find anything?
		if ($lookup->num_rows() == 0)
			return NULL;
		
		//	Return one column.
		
		if ( $key == 'twitter_oauth_tokens' && $lookup->num_rows() > 0 ) {
			$mysql_result = $lookup->result();
			
			$result = FALSE;
			foreach( $mysql_result as $row )
			{
				if ( $row->meta_value == '' )
					self::delete_meta_id( $row->meta_id, $row->meta_key );
				else
					$result = $row->meta_value;
			}
		} else {
			$row = $lookup->row();
			$result = $row->meta_value;
		}
		
		self::$CI->cache->save('user_' . $key . '_' . $ID, $result, 600);
		return maybe_unserialize( $row->meta_value );
	}
	
	/**
	 * Delete a user meta based upon ID
	 *
	 * @param int
	 * @return object
	**/
	private static function delete_meta_id( $ID, $key )
	{
		self::$CI->cache->delete('user_' . $key . '_' . $ID, 'usermeta');
		self::$CI->db->where('meta_id', $ID);
		return self::$CI->db->delete('usermeta');
	}
	
	/**
	 * Function to search user meta.
	 *
	 * @param string $key The key to search.
	 * @param string|mixed	$value The value to search for.
	 * @return int|bool The user ID
	**/
	public static function search_meta($key = FALSE, $value = FALSE)
	{
		//	Did they pass everything?
		if ( ! $key OR ! $value )
			return FALSE;
		
		//	Query
		self::$CI->db->where('meta_key', $key);
		self::$CI->db->where('meta_value', $value);
		$lookup = self::$CI->db->get('usermeta');
		
		//	Did we find anything?
		if ( $lookup->num_rows() == 0 )
			return FALSE;
		
		//	We did!
		$row = $lookup->row();
		return $row->meta_user;
	}
	
	/**
	 * Function to update a user
	 *
	 * @return bool
	**/
	public static function update($key, $value, $ID = FALSE)
	{
		//	If they didn't pass an ID.
		if (! $ID )
		{
			if (! self::logged_in())
				return FALSE;
			$ID = self::uid();
		}
		
		//	All the key's are lowercase
		//	@fixed We used to lowercase the values, we don't do that!
		if ( is_string ( $key ) )
			$key = strtolower( $key );
		
		$value = maybe_serialize($value);
		
		//	Cache
		self::$CI->cache->delete('user_data_'.$ID, 'userdata');
		
		self::$CI->db->set($key, $value);
		self::$CI->db->where('user_id', $ID);
		return self::$CI->db->update('users');
	}
	
	/**
	 * Function to remove user meta
	 *
	 * @access public
	 * @param string The key
	**/
	public static function remove_meta($key, $ID = FALSE)
	{
		//	If they didn't pass an ID.
		if (! $ID )
		{
			if (! self::logged_in())
				return FALSE;
			$ID = self::uid();
		}
		
		//	Kill the cache entry.
		self::$CI->cache->delete('user_' . $key . '_' . $ID, 'usermeta');
		
		self::$CI->db->where('meta_key', $key);
		self::$CI->db->where('meta_user', $ID);
		return self::$CI->db->delete('usermeta');
		
	}
	
	/**
	 * Function to update a meta (or create it).
	 * You can only have one mmeta value.
	 *
	 * @access public
	 * @param string $key The meta key
	 * @param mixed $value The meta value (accepts arrays and objects)
	 * @param int $ID The user ID (optional)
	**/
	public static function update_meta($key, $value, $ID = FALSE)
	{
		//	If they didn't pass an ID.
		if (! $ID )
		{
			if (! self::logged_in())
				return FALSE;
			$ID = self::uid();
		}
		
		//	Convert an array/object/mixed to a string (Serialize it!)
		$value = maybe_serialize($value);
		
		//	Checking to see if it exists.
		//	We pass a $no_cache argument as TRUE to get a fresh value.
		$meta = self::get_meta($key, $ID, TRUE);
		
		if ( $value == '' ) :
			//	Kill the cache entry.
			self::$CI->cache->delete('user_' . $key . '_' . $ID, 'usermeta');
		else :
			//	Cache it AFTER we check if it exists.
			self::$CI->cache->save('user_' . $key . '_' . $ID, $value, 'usermeta', '600');
		endif;
		
		
		//	Non-existant meta value. (Create it!)
		if (is_null($meta)) :
			self::$CI->db->set('meta_user', $ID);
			self::$CI->db->set('meta_key', $key);
			self::$CI->db->set('meta_value', $value);
			return self::$CI->db->insert('usermeta');
		else :
			//	Update it.
			self::$CI->db->where('meta_user', $ID);
			self::$CI->db->where('meta_key', $key);
			self::$CI->db->set('meta_value', $value);
			return self::$CI->db->update('usermeta');
		endif;
	}
	
	/**
	 * Function to setup the current active user.
	 *
	 * @access private
	 * @todo Update this
	**/
	private static function _setLoggedInUser($ID = NULL) {
		if (is_null($ID))
			return FALSE;
		
		if ( $ID !== FALSE && $ID !== FALSE && !is_null( $ID ) )
			self::$is_logged_in = TRUE;
		
		self::$current_user = $ID;
		return TRUE;
	}
	
	/** 
	 * Function to log a user out, correctly.
	 *
	 * @param string The URL to redirect to (leave blank to redirect them to the homepage or pass FALSE if you don't want to redirect)
	 * @return void
	 * @access private
	**/
	public static function logout($redirect = '')
	{
		//	Delete the remember me cookie
		delete_cookie(self::$cookie_name);
		
		//	Kill the session
		self::$CI->session->sess_destroy();
		self::$user_row = NULL;
		
		//	Redirect
		if (! $redirect )
			return TRUE;
		
		if (! empty($redirect) AND valid_url($redirect))
		{
			redirect($redirect);
			return TRUE;
		}
		else
		{
			redirect(base_url());
			return TRUE;
		}
	}
	
	/**
	 * Function to login with either the username or email
	 * and password.
	 *
	 * @param string $key The username/email
	 * @param string $password The user's password
	 * @param mixed $redirect The URL to redirect to (leave blank to redirect them to the homepage or pass FALSE if you don't want to redirect)
	**/
	public static function auth_with_check($key = NULL, $pass = NULL, $redirect = FALSE)
	{
          // Instead of reinventing the wheel, we pass to self::check_login()
          $check = self::check_login($key, $pass);
          
          // It didn't pass
          if (is_simple_error($check) OR ! $check OR ! is_object($check))
               return $check;
          
		// Now, we know that it's correct.
		// Let's log them in.
		$UID = $check->user_id;
          self::do_auth($UID);
		
		// Set the current user
		self::set_current_user($UID);
		
          // We're done here
		return $check;
	}
	
	/**
	 * Match a username and password to a user
	 * Will return FALSE or the user object
	 *
	 * @return    bool|object
	 * @param     string
	 * @param     string
	**/
	public static function check_login($key, $pass)
	{
		// We need to see if the key is a username or a email.
		if ( valid_email( $key ) )
			$key_type = 'email';
		else
			$key_type = 'username';
          
		// Lowering it, always
		$key = trim(strtolower($key));
		
		// Default var to check if we found a user.
		$found = FALSE;
		
		// Is the key a username or an email
		switch($key_type)
		{
			case('email');
				$findEmail = self::findUserbyEmail($key);
				if (! empty($findEmail)) :
					$found = TRUE;
					$dataSet = $findEmail;
				endif;
			break;
			
			case('username');
				$findLogin = self::findUserbyLogin($key);
				if (! empty($findLogin)) :
					$found = TRUE;
					$dataSet = $findLogin;
				endif;
			break;
			
			default;
                    return simple_error('Unknown error.');
               break;
		}
		
		//	Did we find anybody?
		if (! $found )
		{
			// If it still wasn't found, it doesn't exist.
			return simple_error('We can\'t find a user with that login or email.');
		}
		
		// Now we're checking against the password.
		// We can use MD5 because it is pretty easy to update from phpMyAdmin
		if (self::is_md5($dataSet->user_pass))
		{
			if ( self::compareMD5($pass, $dataSet->user_pass) )
			{
				
				// We're updating the user to have the PHPass hashing.
				$new_pass = self::phpassDoHash($pass);
				self::update('user_pass', $new_pass, $dataSet->user_id);
			}
			else
			{
				return simple_error( 'That password is incorrect.');
			}
		} else {
			//	It's PHPass
			
			$check = self::phpassHashCheck($pass, $dataSet->user_pass);
			if (! $check)
				return simple_error( 'That password is incorrect.'); 
		}
          
		return $dataSet;
	}
	
	/**
	 * Log the User into the System with a Session and a Cookie
	 * Always use check_login() in combination with this function
	 *
	 * @param     int The user ID
	 * @param     bool Deprecated
	 * @return    string
	**/
	public static function do_auth( $ID, $remember_me = TRUE )
	{
		// This is the cookie value (it also has the hash we need for the session)
		$user_cookie = self::get_cookie_value($ID);
		$hash = end(explode('::', $user_cookie, 2));
		
		$obj = new stdClass;
		$obj->uid = $ID;
		$obj->ses_hash = $hash;
		
		self::$CI->session->set_userdata('auth_session', serialize($obj));
		
		//	Remember me?
		if ($remember_me)
		{
			$crypt = self::$CI->encrypt->encode($user_cookie);
			set_cookie(array(
				'name'     => self::$cookie_name,
				'value'    => $crypt,
				'prefix'   => '',
				'expire'   => 7889231,	//	3 Months
				'path'     => '/'
			));
		}
		return $hash;
	}
	
	/**
	 * Find a user by their User Slug
	 *
	 * @param $login string The user_login column
	 * @deprecated
	**/
	public static function findUserbyLogin($login)
	{
		return self::find_by('user_slug', $login);
	}
	
	/**
	 * Find a User By a Column to match
	 *
	 * @param     array|string What to search for
	 * @param     string What to match $what to (only if $what is a string)
	 * @return    object|null
     **/
     public static function find_by($what = array(), $match = '')
     {
          // We want an array passed
          if (is_string($what))
               $what = array($what => $match);
          
          $what = (array) $what;
          
          // Nothing passed
          if (count($what) == 0 ) return simple_error('Nothing passed to find.');
          
          // Arguments
          foreach($what as $key => $val) :
               self::$CI->db->where($key, $val);
          endforeach;
          
          // Finish the query
          self::$CI->db->from('users');
          $get = self::$CI->db->get();
          
          // Nothing found
          if ($get->num_rows() == 0)
               return NULL;
          else
               return $get->row();
     }
	
	/**
	 * Function to find a user by a user ID.
	 *
	 * @param     $ID int|string The user ID
	 * @deprecated
	**/
	public static function findUserbyID($ID)
	{
		return self::find_by('user_id', $ID);
	}
	
	/**
	 * Looking for a user by email.
	 *
	 * @param     $email string The user's string.
	 * @deprecated
	**/
	public static function findUserbyEmail($email = '')
	{
		return self::find_by('user_email', $email);
	}
	
	/**
	 * Function to see if a tring is MD5 encoded.
	 * Note: This only uses string length to determine this,
	 * not actually checking it.
	 *
	 * @since 2.0
	**/
	public static function is_md5($string)
	{
		if ( strlen($string) >= 33 )
			return FALSE;
		
		return TRUE;
	}
	
	/**
	 * Function to see if a string and a MD5 string are equal.
	 *
	 * @since 2.0
	 * @param $string The NON MD5 encoded string
	 * @param $hash The MD5 Encoded string
	**/
	public static function compareMD5($string, $hash)
	{
		if ( md5( $string ) == $hash )
			return TRUE;
		else
			return FALSE;
	}
	
	/**
	 * Function to check if a password is equal to the save hash.
	 * 
	 * @param $string The NON Hashed String
	 * @param $hash The save Hash
	 * @since 2.0
	**/
	public static function phpassHashCheck($string, $hash)
	{
		//if ( !class_exists('Hash') )
		//	return FALSE;
		
		$check = Hash::CheckPassword($string, $hash);
		
		if (!$check) {
			return FALSE;
		} else {
			return TRUE;
		}
	}
	
	/**
	 * We're gonna has a string using phpass
	 *
	 * @return string The hashed version of $string.
	**/
	public static function phpassDoHash($string)
	{
		return Hash::HashPassword($string);
	}
	
	
	/**
	 * Function to format a username.
	 * e.g, remove characters, spaces, switch to lower.
	 *
	 * @param string
	 * @return string
	**/
	public static function cleanUsername($string)
	{
		return sanitize_title_with_dashes( $string );
	}
	
	/**
	 * Model to hold the user registrations
	 * It does all the processing here, no redirects.
	 *
	 * @param array $data The data argument
	 * @return object|int A simple_error on error or an int which is the user ID
	**/
	public static function user_register( $data = array() )
	{
		$defaults = array(
			'user_slug'		=>	'',
			'user_email'	=>	'',
			'user_pass'		=>	'',
			'user_name'		=>	'',
			'user_registration'	=>	'',
			'user_suspended	'	=>	0,
			'user_timezone'		=>	'UM8',
		);
		
		//	We have a default set of values
		$data = wp_parse_args($data, $defaults);
		
		// Something went wrong
		if ( ! is_array( $data ) )
			return simple_error('Internal error.');
		
		//	Validate the data
		if (! valid_email($data['user_email']) OR empty($data['user_email']))
			return simple_error("That email is invalid.");
		
		if ( empty($data['user_slug']))
			return simple_error('That username is empty!');
		
		if (empty($data['user_pass']))
			return simple_error('The password is empty!');
		
		//	We don't force a user's name
		
		//	Sanitize
		$data['user_slug'] = sanitize_title_with_dashes($data['user_slug']);
		$data['user_registration'] = current_datetime();
		
		//	Now checking if they are already taken!
		$lookup_username = self::findUserbyLogin($data['user_slug']);
		
		if ($lookup_username !== FALSE)
			return simple_error('That username is already taken.');
		
		//	Email registered?
		$lookup_email = self::findUserbyEmail($data['user_email']);
		
		if ($lookup_email !== FALSE)
			return simple_error("Someone is already registered with that email.");
		
		// Done. Let's get to it.
		$hash = gen_hash();
		
		$sql = array();
		$sql['user_pass'] = md5( $data['user_pass'] );
		$sql['user_name'] = $data['user_name'];
		$sql['user_slug'] = $data['user_slug'];
		$sql['user_email'] = $data['user_email'];
		
		self::$CI->db->insert('users', $sql);
		
		//	UID
		$UID = self::$CI->db->insert_id();
		
		// They have to be activated, so we're gonna store their registration in another table.
		// When they are activated, we delete the row
		self::$CI->db->set('time', current_datetime());
		self::$CI->db->set('hash', md5($hash));		
		self::$CI->db->set('user', $UID);
		self::$CI->db->insert('acct_registrations');
		
		// Setting up the welcome email
		// We're gonna use a nice HTML template, but we still need to support non-HTML email users!
		$link = base_url() . 'users/activate/'.md5($hash);
		
		$content = "We just need to activate your account and you will be all set.  Click on the link below or copy and paste it into your browser.<br />";
		$content .= "<br />";
		$content .= "<a href='".$link."'><font color='black'>".$link."</font></a><br />";
		
		self::$CI->email->subject('Welcome to TruantToday!');
		self::$CI->email->message($content);	
		self::$CI->email->to($data['user_email'], $data['user_name']);
		
		self::$CI->email->send();
		return TRUE;
	}
	
	/**
	 * A method to create a user's account and email them the details
	 * Used to create a parent account and email them the password.
	 *
	 * @access    public
	 * @param     array 
	 * @return    int|object A Simple error
	**/
	public static function create_user_automagically( $data = array() )
	{
		$defaults = array(
			'user_slug'           =>	'',
			'user_email'          =>	'',
			'user_pass'           =>	'',
			'user_first_name'     =>	'',
			'user_last_name'      =>	'',
			'role'                => 'parent',
			'create_time'         =>	'',
			'update_user'         => 0,
			'district_id'         => 0,
			'isDeleted'           => 0,
			'school_id'           => 0,
			'student_id'          => 0,
		);
		
		//	We have a default set of values
		$data = wp_parse_args($data, $defaults);
		
		//	Something went wrong
		if ( ! is_array( $data ) )
			return simple_error('Internal error.');
		
		//	Validate the data
		if (! valid_email($data['user_email']) OR empty($data['user_email']))
			return simple_error("That email is invalid or empty.");
		
		if ( empty($data['user_slug']))
			return simple_error('That username is empty!');
		
		// Their actual name
		if (empty($data['user_first_name']) OR empty($data['user_last_name']))
               return simple_error('They didn\'t fill out the full first and last names!');
          
          // Sanitize them
          $data['user_first_name'] = sanitize_title($data['user_first_name']);
          $data['user_last_name'] = sanitize_title($data['user_last_name']);
          
		// We create their password for them
		if (empty($data['user_pass']))
			$data['user_pass'] = gen_hash();
		
		//	Sanitize
		$data['user_slug'] = sanitize_title_with_dashes($data['user_slug']);
		$data['create_time']  = current_datetime();
		
		//	Now checking if they are already taken!
		$lookup_username = self::findUserbyLogin($data['user_slug']);
		
		if ($lookup_username !== FALSE)
			return simple_error('That username is already taken.');
		
		//	Email registered?
		$lookup_email = self::findUserbyEmail($data['user_email']);
		
		if ($lookup_email !== FALSE)
			return simple_error("Someone is already registered with that email.");
		
		// Done. Let's get to it.
		$hash = gen_hash();
		
		// Saving the plaintext password
		$password = $data['user_pass'];
		
		$data['user_pass'] = md5($password);
		
		// The query
		self::$CI->db->insert('users', $data);
		
		//	UID
		$UID = self::$CI->db->insert_id();
		
		$link = site_url();
		
		$content = "We have created an account for you at TruantToday. You can login at the link below with the account details listed below. Enjoy!<br />";
		$content .= "<br />";
		$content .= "<a href='".$link."'><font color='black'>".$link."</font></a><br />Your login details will be:<br /><br />Username: ".$data['user_slug']."<br />Password: ".$password;
		
		//$content .= "<br><br>Regards,<br>TruantToday";
		
		self::$CI->email->subject('Welcome to TruantToday!');
		self::$CI->email->message($content);	
		self::$CI->email->to($data['user_email'], $data['user_first_name'] . ' ' .$data['user_last_name']);
		
		self::$CI->email->send();
		return $UID;
	}
	
	/**
	 * Function to see if they have a valid email and/or username
	 *
	 * @return bool
	 * @todo Update this
	**/
	public static function has_email_and_slug( )
	{
		if (! self::logged_in() )
			return FALSE;
		
		$email = (string) self::get('user_email');
		$slug = (string) self::get('user_slug');
		
		if ( empty( $email ) || empty( $slug ) )
			return FALSE;
		else
			return TRUE;
	}
	
	/**
	 * Generate a forgotten password request
	 *
	 * @return array
	**/
	public static function generate_forgot_pass( $ID )
	{
		$isset = self::isset_user_forgot_pass( $ID );
		if ( $isset->num_rows() > 0 )
			self::delete_users_forgot_pass( $ID );
		
		$return = new stdClass();
		$hash = gen_hash();
		
		//	DB insert
		self::$CI->db->set('chg_user', $ID);
		self::$CI->db->set('chg_time', now());
		self::$CI->db->set('chg_hash', md5( $hash ) );
		//self::$CI->db->set('chg_pass_md',  md5( $hash ) );
		
		self::$CI->db->insert('acct_passchg');
		
		$return->hash = md5( $hash );
		$return->pass = $hash;
		
		return $return;
	}
	
	/**
	 * Does a user have a forgotten password request already?
	 *
	 * @return bool
	**/
	public static function isset_user_forgot_pass( $ID = FALSE )
	{
		if ( ! $ID )
			$ID = self::uid();
		
		self::$CI->db->where('chg_user', $ID );
		return self::$CI->db->get('acct_passchg');
	}
	
	/**
	 * Delete a user forgot password request
	 *
	 * @return bool
	**/
	public static function delete_forgot_pass( $hash )
	{
		self::$CI->db->where('chg_hash', $hash);
		self::$CI->db->delete('acct_passchg');
		return TRUE;
		
	}
	
	/**
	 * Delete all forgotten passwords for a user
	 *
	 * @return    bool
	**/
	public static function delete_users_forgot_pass( $id )
	{
		self::$CI->db->where('chg_user', $id);
		self::$CI->db->delete('acct_passchg');
		return TRUE;
		
	}
	
	/**
	 * Process a forgotten password for a user
	 *
	 * @access public
	 * @param string The hash
	**/
	public static function process_forgot_pass($str = '')
	{
		$str = trim($str);
		if ( empty($str))
			return FALSE;
		
		$get = self::$CI->db->from('acct_passchg')->where('chg_hash', $str)->get();
		if ( $get->num_rows() == 0)
			return simple_error('That\s an invalid URL.');
		
		// It's good!
		self::update('user_pass', $str, $get->row()->chg_user);
		return TRUE;
	}
	
	/**
	 * Get the avatar type
	 *
	 * @param string
	 * @param int The user ID (defaults to current user)
	**/
	public static function get_avatar_type( $UID = FALSE )
	{
		if ( !$UID ) {
			if ( !logged_in() )
				return 'gravatar';
			
			$UID = self::uid();
		}
		
		$meta = self::GetUserMeta('user_avatar', $UID);
		
		if ( !$meta || $meta == '' )
			return 'gravatar';
		
		return strtolower( trim( $meta ) );
	}
	
	/**
	 * Get the users avatar URL
	 *
	 * @return string
	 * @param int $uid The UID
	**/
	public static function get_avatar( $uid = FALSE )
	{
		if ( !$uid ) {
			if ( !logged_in() )
				return '';
			
			$uid = self::uid();
		}
		
		$type = self::get_avatar_type( $uid );
		
		//	Which type?
		switch( $type )
		{
			case('twitter');
				//	We saved it in their User meta
				$cred = self::get_meta('twitter_cred');
				return $cred->profile_image_url;
			break;
			
			case('facebook');
				$picture = facebook_picture();
				//	Unshorten it.
				return expandShortUrl( $picture );
			break;
			
			//	Default is gravatar
			default;
				return gravatar( self::get('user_email', $uid), 'G', 80);
			break;
		}
		
	}
	
	/**
	 * Get the avatar URL
	 *
	 * We call on THIS function because we save the URL to the photo in the usermeta
	 * Facebook is iffy in their way I can access it
	 *
	 * If they haven't selected one yet, it will result a Gravatar img.
	 *
	 * @return string
	**/
	public static function get_avatar_url( $uid = FALSE, $size = FALSE )
	{
		if ( ! $uid )
		{
			if ( !logged_in() )
				return FALSE;
			
			$uid = self::uid();
		}
		
		$meta = self::get_meta('user_avatar_url', $uid );
		if ( !$meta || $meta == '' )
			return gravatar( self::get('user_email', $uid), 'G', 80);
		
		return $meta;
	}
	
	/**
	 * Get the UNIX timestamp for the local time for the registered user
	 *
	 * @return int
	**/
	public static function get_reg_date( $ID = FALSE )
	{
		if ( !$ID )
		{
			if ( !logged_in() )
				return 0;
			else
				$ID = self::uid();
		}
		
		$date = self::get('user_registration', $ID);
		return get_local_unix( $date );
	}
}

/* End file Auth.php */