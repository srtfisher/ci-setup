<?php
/**
 * We extend CI Email because it makes life simple
 * Also, we could eventually use Postmark.
 *
 * @access public
 * @since 0.6
 * @package Core
**/
class MY_Email extends CI_Email {
	
	private $email_title = '';
	private $email_content = '';
	private $email_tpl = 'standard';
	
	/**
	 * Constructor
	 *
	 * @access public
	**/
	public function __construct()
	{
		parent::__construct();
		
		// Default from email
		
		/*
		parent::initialize(array(
            'protocol' => 'smtp',
            'smtp_host' => '',
            'smtp_user' => '',
            'smtp_pass' => '',
            'smtp_port' => 465,
            'crlf' => "\r\n",
            'newline' => "\r\n"
          ));
          */
          
          // Default sender
          parent::from("info@truanttoday.com", "TruantToday");
          
          // Load SES
          // get_instance()->load->library('Amazon_ses');
	}
	
	public function clear()
	{
		parent::clear();
		parent::from("info@truanttoday.com", "TruantToday");
	}
	
	/**
	 * Set the email title
	 *
	 * @access public
	 * @param string
	**/
	public function subject($str = '')
	{
		$this->email_title = $str;
	}
	
	/**
	 * Set the email content with HTML tags
	 *
	 * @access public
	 * @param string
	**/
	public function message($str = '')
	{
		$this->email_content = $str;
	}
	
	/**
	 * Set the email template to be used
	 * Default is 'standard'.
	 *
	 * @access public
	 * @param string
	**/
	public function template($tpl = 'standard')
	{
		$this->email_tpl = $tpl;
	}
	
	/**
	 * Send the email
	 * Overrides the parent class function
	 *
	 * @access public
	**/
	public function send()
	{
		// Email TPL (We call the 3rd arg as TRUE because that will return the HTML)
		$email_template = get_instance()->load->view('email/'.$this->email_tpl, array(), TRUE);
		
		//	Invalid template
		if (! $email_template OR ! is_string($email_template) )
			show_error('Invalid email template');
		
		$content_html = '';
		$content_html = str_replace('{content}', $this->email_content, $email_template);
		$content_html = str_replace('{title}', $this->email_title, $content_html);
		
		// Set the message
		parent::set_alt_message(strip_tags($this->email_content));
		parent::message($content_html);
		parent::subject($this->email_title);
		
		// Set mailtype to HTML
		parent::set_mailtype('html');
		
		// Send
		parent::send();
		
		// You can use the below to override parent::send()
		return;
		
		// ------------
		// Parent
		// ------------
		
		if ($this->_replyto_flag == FALSE)
		{
			$this->reply_to($this->_headers['From']);
		}

		if (( ! isset($this->_recipients) AND ! isset($this->_headers['To']))  AND
			( ! isset($this->_bcc_array) AND ! isset($this->_headers['Bcc'])) AND
			( ! isset($this->_headers['Cc'])))
		{
			$this->_set_error_message('lang:email_no_recipients');
			return FALSE;
		}

		parent::_build_headers();

		if ($this->bcc_batch_mode  AND  count($this->_bcc_array) > 0)
		{
			if (count($this->_bcc_array) > $this->bcc_batch_size)
				return $this->batch_bcc_send();
		}

		parent::_build_message();

		if ( ! $this->_spool_email())
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
	
	/**
	 * We're taking the method they use to send emails
	 *
	 * This is internally used to send the email
	 *
	 * @access    private
     **/
     protected function _spool_email()
     {
          $this->_unwrap_specials();
          
          // Subject
          get_instance()->amazon_ses->subject($this->_subject);
          
          // Content
          get_instance()->amazon_ses->message_alt($this->alt_message);
          get_instance()->amazon_ses->message($this->_body);
          
          // To
          get_instance()->amazon_ses->to($this->_recipients);
          
          get_instance()->amazon_ses->send(TRUE);
     }
	 
}
/* End of file MY_Email.php */