<?php
class MY_Loader extends CI_Loader
{
     /**
      * The constructor
      *
      * @access public
     **/
     public function __construct()
     {
          parent::__construct();
     }
     
     /**
      * Static Library Loader
      *
      * Method to load a library as a static class
      * You can have a library file like normally but we won't be adding it to the CI Object
      *
      * @param      string The library/libraries to load
     **/
     public function static_lib($library)
     {
          // You can call this with an array for simplicity!
          if (is_array($library))
		{
			foreach ($library as $class)
			{
				$this->static($class, $params);
			}

			return;
		}
		
		$library = ucfirst($library);
		
		// It's already loaded
		if (class_exists($library))
		{
               log_message('debug', 'We have already loaded the '.$library.' library.');
          }
          
          $file = dirname(dirname(__FILE__)).DS.'libraries'.DS.$library.'.php';
          
          if ( ! file_exists($file))
          {
               show_error('We can\'t find a library file '.$library);
          }
          
          include_once($file);
          
          return true;
     }
}

/* End file MY_Loader.php */