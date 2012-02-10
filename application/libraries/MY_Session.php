<?php
/**
 * This moves the Session class to use PHP Sessions
 * Put in during development for a speed boost - will be removed for production.
 *
 * We need to sync sessions across servers, not an issue right now though.
 *
 * @access public
 * @see Session
 * @package Connect
 * @author sean
**/
class MY_Session extends CI_Session
{
	public $userdata = NULL;
	
	/**
	 * Constructor
	 *
	**/
	public function __construct()
	{
		//parent::__construct();
		
		// Start PHP Session
		session_start();
		
		// Load the userdata
		$this->userdata = (isset($_SESSION['userdata'])) ? $_SESSION['userdata'] : array();
	}
	
	/**
	 * Fetch a specific item from the session array
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	function userdata($item)
	{
		return ( ! isset($this->userdata[$item])) ? FALSE : $this->userdata[$item];
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch all session data
	 *
	 * @access	public
	 * @return	array
	 */
	function all_userdata()
	{
		return $this->userdata;
	}

	// --------------------------------------------------------------------

	/**
	 * Add or change data in the "userdata" array
	 *
	 * @access	public
	 * @param	mixed
	 * @param	string
	 * @return	void
	 */
	function set_userdata($newdata = array(), $newval = '')
	{
		if (is_string($newdata))
		{
			$newdata = array($newdata => $newval);
		}

		if (count($newdata) > 0)
		{
			foreach ($newdata as $key => $val)
			{
				$this->userdata[$key] = $val;
			}
		}

		$this->sess_write();
	}
	
	// --------------------------------------------------------------------

	/**
	 * Delete a session variable from the "userdata" array
	 *
	 * @access	array
	 * @return	void
	 */
	function unset_userdata($newdata = array())
	{
		if (is_string($newdata))
		{
			$newdata = array($newdata => '');
		}

		if (count($newdata) > 0)
		{
			foreach ($newdata as $key => $val)
			{
				unset($this->userdata[$key]);
			}
		}

		$this->sess_write();
	}
	
	/**
	 * Write the current session data to the PHP Session
	 *
	 * @access public
	**/
	public function sess_write()
	{
		$_SESSION['userdata'] = $this->userdata;
		
	}
	
	public function sess_destroy()
	{
		session_destroy();
	}
}
/* End of file MY_Session.php */