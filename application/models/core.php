<?php
/**
 * The core non-dependent model
 * We don't rely on other things with this model, but other models use this model.
 *
 * @package    Core
 * @author     srtfisher
 * @version    1.0
**/
class Core extends CI_Model
{
	/**
	 * The page's title
	 *
	 * @global string
	**/
	public $title = 'Application';
	
	/**
	 * The constructor
	 *
	 * @access public
	**/
	public function __construct()
	{
		parent::__construct();
		
		// Form validation span delim
		$this->form_validation->set_error_delimiters('<span>', '</span>');
		
		// Asset management Config
		$carabiner_config = array(
			'script_dir' => 'assets/js/', 
			'style_dir'  => 'assets/css/',
			'cache_dir'  => 'assets/cache/',
			'base_uri'   => base_url(),
			'combine'    => FALSE,
			'dev'        => FALSE
		);
		
		//	Enviorment setup
		switch(ENVIRONMENT)
		{
			case('staging');
			case('production');
				$carabiner_config['dev']       = FALSE;
				$carabiner_config['combine']   = FALSE;
				$carabiner_config['minify_css'] = FALSE;
				$carabiner_config['minify_js'] = FALSE;
				
				$cache_adapt = array('adapter' => 'memcached', 'backup' => 'apc');
			break;
			
			case('testing');
			case('development');
			case('development-local');
				$carabiner_config['dev'] = TRUE;
				$carabiner_config['combine'] = FALSE;
				$carabiner_config['minify_css'] = FALSE;
				$carabiner_config['minify_js'] = FALSE;
				
                    if (ENVIRONMENT == 'development-local')
                         $cache_adapt = array('adapter' => 'apc', 'backup' => 'file');
                    else
                         $cache_adapt = array('adapter' => 'memcached', 'backup' => 'apc');
			break;	
		}
		
		// Load the cache driver (We have APC setup in production with file on development servers
		$this->load->driver('cache', $cache_adapt);
		
		// Load Carabiner (The asset manager)
		$this->load->library('carabiner');		
		
		// Set the config
		$carabiner_config['base_uri'] = str_replace('http://', '//', base_url());
		$this->carabiner->config($carabiner_config);
		
		// Load the jQuery globally - it's a small app
		$this->carabiner->js( 'https://ajax.googleapis.com/ajax/libs/jquery/'.$this->config->item('jquery_version').'/jquery.min.js', '', FALSE);
		
		// Cache folder
		$this->setup_cache_folder();
		
		// Load the user library (we can override it!)
		$this->load->static_lib('Auth');
		Auth::init();
		
		// Permissions
		$this->load->static_lib('Perm');
		Perm::init();
		
		// Features
		$this->load->static_lib('Feature');
		Feature::init();
		Feature::set_user();
		
		// Less
		$this->load->static_lib('Less');
		
		$this->load->static_lib('Formr');
	}
	
	/**
	 * Compile and add a Less file to Carabiner
	 *
	 * @param     string
	 * @return    bool
     **/
     public function add_less_file($name)
     {
          // Only compile in development
          if (ENVIRONMENT == 'production')
               return $this->carabiner->css('compiled/'.$name.'.css');
          
          // Load LESS
          Less::init();
          Less::compile($name);
          
          $this->carabiner->css('compiled/'.$name.'.css');
          
          return TRUE;
     }
     
	/**
	 * Load the standard Javascript and CSS files
	 * Not called by default.
	 * These are all combinied and minified during production, so we can load multiple files.
	 * The production site loads assets compressed using app/do_prod.
	 *
	 * @access    public
	 * @return    void
	**/
	public function load_default_assets()
	{
		// Load the two mini files
		if (ENVIRONMENT == 'production')
		{
               $this->carabiner->js('combo.js');
               $this->carabiner->css('compiled/combo.css');
               
               return;
          }
          
          // Javascript
		$this->carabiner->js('tipsy.js');
		$this->carabiner->js('facebox.js');
		$this->carabiner->js('tablesort.js');
		$this->carabiner->js('bootstrap.js');
		$this->carabiner->js('app.js');
		
		// ---------------------------------------------------------------
		// How we have it setup:
		// We have LESS setup for the non-production environments.
		// We will keep a compiled version of CSS under SC.
		// Production will serve the complied version so we don't have to deal with LESS at all during this part.
		// We won't even load the Less Library to save on memory.
		// This REQUIRES you to commit both the LESSified file and the compiled CSS file upon push.
		// ---------------------------------------------------------------
		$css_files = array(
               //'gs',
			//'bootstrap-1.3.0',
			'facebox',
			//'tipsy',
			//'fancybox',
			'main',
		);
		
		$this->carabiner->css('compiled/bootstrap.css');
		
		// Load LESS
		Less::init();
		
          foreach($css_files as $file) :
               Less::compile($file);
               $this->carabiner->css('compiled/'.$file.'.css');
          endforeach;
		
	}
	 
	/**
	 * Set the current page's title.
	 *
	 * @access    public
	 * @param     string
	 * @return    void
	**/
	public function set_title($str)
	{
		$this->title = (string) $str .' - Application';
	}
	
	/**
	 * Get the current page's title.
	 * You can also set it by passing a param
	 *
	 * @access    public
	 * @param     string
	 * @return    string
	**/
	public function title($str = '')
	{
          if (! empty($str)) $this->set_title($str);
          
          return trim($this->title);
	}
	
	/**
	 * Set the current good message
	 *
	 * @access    public
	 * @param     mixed Either string or simple_error
	**/
	public function set_good($argument)
	{
		if (is_simple_error($argument))
			$argument = get_simple_error($argument);
		
		$argument = (string) $argument;
		$this->session->set_userdata('current_good', $argument);
	}
	
	/**
	 * Set the current error message
	 *
	 * @access public
	 * @param mixed Either string or simple_error
	**/
	public function set_error($argument)
	{
		if (is_simple_error($argument))
			$argument = get_simple_error($argument);
		
		$argument = (string) $argument;
		$this->session->set_userdata('current_error', $argument);
	}
	
	/**
	 * Get the current error
	 *
	 * @return null|string
	**/
	public function get_current_error()
	{
		$userdata = $this->session->userdata('current_error');
		if (! $userdata)
			return NULL;
		else
			return $current_good;
	}
	
	/**
	 * Get the current good message
	 *
	 * @access public
	 * @return bool|string
	**/
	public function get_current_good()
	{
		$userdata = $this->session->userdata('current_good');
		if (! $userdata)
			return NULL;
		else
			return $current_good;
	}
		
	/**
	 * Setup the application's cache folder
	 * We want to make sure that there is an index.html file there.
	 *
	 * @access private
	 * @return void
	**/
	public function setup_cache_folder( $board = FALSE )
	{
		//	The file cache folder
		$cache_folder = dirname(dirname(__FILE__)).'/cache/';
		
		if ( ! file_exists($cache_folder))
		{
			mkdir($cache_folder);
			chmod($cache_folder, 755);
		}
		
		//	Asset cache folder
		$cache_folder = dirname(FCPATH.DS.'cache'.DS);
		
		if ( ! file_exists($cache_folder))
		{
			mkdir($cache_folder);
			chmod($cache_folder, 755);
		}
	}
	
	/**
      * Simple Benchmarking
      *
      * @access     public
      * @param      functions to run
      * @return     void
     **/
     public function bench()
     {
          if (func_num_args() == 0) show_error('No arguments passed.');
          
          $list = func_get_args();
          $i = 0;
          
          foreach($list as $arg) :
               $i++;
               if (! is_callable($arg))
                    continue;
               
               $this->benchmark->mark('argument-'.$i.'-start');
               
               // Call the argument
               $arg();
               
               $this->benchmark->mark('argument-'.$i.'-end');
               
               echo PHP_EOL."Benchmarking result for Argument ".$i.": ". $this->benchmark->elapsed_time('argument-'.$i.'-start', 'argument-'.$i.'-end').PHP_EOL;
          endforeach;
     }
     
          /**
      * Upload a File to S3
      *
      * @access     public
      * @param      string The Full Path File to upload
      * @param      string The Folder to Upload to (defaults to the year/month format)
      * @return     string
     **/
     public function upload_to_s3($what, $to = '', $bucket = 'truanttoday-uploads')
     {
          if (empty($to))
               $to = date('Y').DS.date('m');
          
          if (! is_file($what))
               throw new InvalidArgumentException('First argument must be a file.');
          
          $this->load->library('s3');
          $this->load->config('s3');
          $this->s3->setAuth($this->config->item('access_key'), $this->config->item('secret_key'));
          
          // Path Info
          $info = pathinfo($what);
          $extension = (isset($info['extension'])) ? $info['extension'] : 'txt';
          
          // Get the Content Type
          include(dirname(dirname(__FILE__)).DS.'config/mimes.php');
          $this_one = $mimes[$extension];
          
          // Get the first if it's an array
          if (is_array($this_one))
               $this_one = reset($this_one);
          
          $body = file_get_contents($what);
          
          // Upload it 
          $do = $this->s3->putObject($body, $bucket, $to.DS.$info['basename'], 'public-read', array(), array('Content-Type' => $this_one));
          
          if (! $do)
               return FALSE;
          else
               return 'https://'.$bucket.'.s3.amazonaws.com/'.$to.'/'.$info['basename'];
     }
}

/**
 * The class to create a modal popup
 *
 * @package    Core
 * @author     srtfisher
**/
class Modal
{
     private $title = '';
     private $content = '';
     
     private $primary = array();
     private $secondary = array();
     private $link = array();
     
     private $render = '';
     
     public $options = array();
     public $id = '';
     
     /**
      * The Constructor
      *
      * @access     public
     **/
     public function __construct()
     {
          $this->id = gen_hash();
     }
     
     /**
      * Set the title of the model
      *
      * @access     public
      * @param      string
     **/
     public function title($str = '') { $this->title = $str; }
     
     /**
      * Set the content of the model
      *
      * @access     public
      * @param      string
     **/
     public function content($str = '') { $this->content = $str; }
     
     /**
      * Set the Primary Link
      *
      * @access     public
      * @param      string
      * @return     void
     **/
     public function primary($content, $href, $class = 'btn primary', $id = '', $attr = '')
     {
          $this->primary = array(
               'content'           => $content,
               'href'              => $href,
               'class'             => $class,
               'id'                => $id,
               'attr'              => $attr,
          );
     }
     
     /**
      * Set the Secondary Link
      *
      * @access     public
      * @param      string
     **/
     public function secondary($content, $href, $class = 'btn secondary', $id = '', $attr = '')
     {
          $this->secondary = array(
               'content'           => $content,
               'href'              => $href,
               'class'             => $class,
               'id'                => $id,
               'attr'              => $attr,
          );
     }
     
     /**
      * Set the link to link to the model
      *
      * @access     public
      * @return     void
     **/
     public function link($content, $class = '', $id = '', $attr = '')
     {
          $this->link = array(
               'content'      => $content,
               'class'        => $class,
               'id'           => $id,
               'attr'         => $attr,
          );
     }
     
     /**
      * Render the Data
      * This will setup all the HTML to display
      *
      * @access     public
      * @return     void
      * @param      void
     **/
     public function render()
     {
          $str = '';
          
          // The link to the modal
          if (count($this->link) == 0)
               return;
          
          if (is_callable($this->link['content']))
               $content = call_user_func($this->link['content']);
          else
               $content = $this->link['content'];
          
          $str = '<a href="javascript:void(o);" data-controls-modal="modal-'.$this->id.'" data-backdrop="'.((isset($this->options['backdrop'])) ? $this->options['backdrop'] : 'true' ).'" data-keyboard="'.((isset($this->options['keyboard'])) ? $this->options['keyboard'] : 'true' ).'" class="'.$this->link['class'].'" '.$this->link['attr'].' id="'.$this->link['id'].'">'.$content.'</a>';
          
          // The actual modal
          $str .= '<div id="modal-'.$this->id.'" class="modal hide fade">';
          $str .= '<div class="modal-header">';
          $str .= '<a href="#" class="close">&times;</a>';
          
          $str .= '<h3>'.$this->title.'</h3></div>';
          
          // Content
          if (is_callable($this->content))
               $content = call_user_func($this->content);
          else
               $content = '<p>'.$this->content.'</p>';
          
          $str .= '<div class="modal-body">'.$content .'</div>';
          
          // Links
          $str .= '<div class="modal-footer">';
          
          if (count($this->primary) > 0)
               $str .= '<a href="'.$this->primary['href'].'" class="'.$this->primary['class'].'" id="'.$this->primary['id'].'" '.$this->primary['attr'].'>'.$this->primary['content'].'</a>';
          
          if (count($this->secondary) > 0)
               $str .= '<a href="'.$this->secondary['href'].'" class="'.$this->secondary['class'].'" id="'.$this->secondary['id'].'" '.$this->secondary['attr'].'>'.$this->secondary['content'].'</a>';
          else
               $str .= '<a href="javascript:void(o);" onclick="$(\'#modal-'.$this->id.'\').modal(\'hide\');" class="btn secondary">Close</a>';
          
          $str .= '</div></div>';
          
          // We're done here!
          $this->render = $str;
     }
     
     /**
      * Echo's the data
      * Requires you to render it first
      *
      * @access     public
      * @return     void
     **/
     public function display()
     {
          echo $this->render;
     }
     
     /**
      * Return the data
      * Requires you to render it first
      *
      * @access     public
      * @return     string
      * @param      void
     **/
     public function rtn()
     {
          return $this->render;
     }
}

/**
 * Twitter Bootstrap Popover
 *
 * @access     public
 * @package    Core
 * @author     srtfisher
**/
class Popover
{
     private $render;
     private $data = array();
     private $js_option = array();
     
     public function __construct()
     {
          $this->data['attr'] = '';
          $this->data['placement'] = 'right';
     }
     public function placement($where = 'right') { $this->data['placement'] = $where; }
     
     public function title($title = '') { $this->data['title'] = $title; }
     public function content($content = '') { $this->data['content'] = $content; }
     public function link($str = '', $class = 'btn', $href = '#', $attr = '')
     {
          $this->data['link'] = array(
               'str' => $str,
               'class' => $class,
               'href' => $href,
               'attr' => $attr
          );
     }
     
     public function option($key, $val = '') { $this->js_option[$key] = $val; }
     
     /**
      * Render
      *
      * @access     public
     **/
     public function render()
     {
          $ren = '';
          $ren = $ren .'<a href="'.$this->data['link']['href'].'" class="'.$this->data['link']['class'].'" '.$this->data['link']['attr'].' rel="popover" title="'.$this->data['title'].'" data-content="'.$this->data['content'].'" data-placement="'.$this->data['placement'].'">'.$this->data['link']['str'].'</a>';
          
          if (count($this->js_option) > 0) : foreach($this->js_option as $key => $val) :
               
          endforeach; endif;
          
          $this->render = $ren;
     }
     
     /**
      * Echo's the data
      * Requires you to render it first
      *
      * @access     public
      * @return     void
     **/
     public function display()
     {
          echo $this->render;
     }
     
     /**
      * Return the data
      * Requires you to render it first
      *
      * @access     public
      * @return     string
      * @param      void
     **/
     public function rtn()
     {
          return $this->render;
     }
}

/**
 * Twitter Boostrap Breadcrumbs
 *
 * @access     public
 * @package    Core
**/
class Breadcrumbs
{
     private $segments = array();
     private $render;
     private $ul_attr = array();
     
     /**
      * Add a Segment
      *
      * @param
     **/
     public function add($name, $link = '')
     {
          $name = trim($name);
          $this->segments[] = array(
               'name'    => $name,
               'href'    => $link,
          );
     }
     
     /**
      * Set the List Attributes
      *
      * @access     public
     **/
     public function attr($array)
     {
          $array = (array) $array;
          
          if (count($array) == 0) return;
          
          foreach($array as $key => $val) :
               if ($key == 'class')
                    $val = 'breadcrumb '.$val;
               
               $this->ul_attr[$key] = $val;
          endforeach;
     }
     
     /**
      * Render the List
      *
      * @access     public
      * @return     void
     **/
     public function render()
     {
          $str = '';
          $str .= '<ul ';
          
          if (count($this->ul_attr) == 0)
          {
               $str .= 'class="breadcrumb"';
          }
          else
          {
               foreach($ul_attr as $key => $val)
                    $str .= ' '.$key.'="'.$val.'"';
          }
          
          $str .= '>';
          
          // Loop though the segments
          if (count($this->segments) > 0) :
               $num = count($this->segments);
               
               foreach($this->segments as $key => $seg) :
                    $str .= '<li ';
                    
                    // The anchor
                    if ($key+1 == $num)
                    {
                         // It's active
                         $str .= 'class="active">';
                         $str .= $seg['name'];
                    }
                    else
                    {
                         // It's not - closing the open tag
                         $str .= '>';
                         $str .= anchor($seg['href'], $seg['name']);
                         $str .= ' <span class="divider">/</span>';
                    }
                    
                    $str .= '</li>';
               endforeach;
          endif;
          
          $str .= '</ul>';
          
          // Done!
          $this->render = $str;
     }
     
      /**
      * Echo's the data
      * Requires you to render it first
      *
      * @access     public
      * @return     void
     **/
     public function display()
     {
          echo $this->render;
     }
     
     /**
      * Return the data
      * Requires you to render it first
      *
      * @access     public
      * @return     string
      * @param      void
     **/
     public function rtn()
     {
          return $this->render;
     }
}

/* End of file core.php */