<?php
/**
 * The Less Container
 *
 * We simply compile files here.
 *
 * @access     public
 * @package    Core
**/
class Less
{
     public static $less_folder;
     public static $compile_folder;
     
     /**
      * Setup 
      *
      * @access     public
     **/
     public static function init()
     {
          require_once(dirname(__FILE__).DS.'less_lib'.DS.'lessc.inc.php');
          
          self::$less_folder = ABSPATH.DS.'assets'.DS.'css'.DS.'less'.DS;
          self::$compile_folder = ABSPATH.DS.'assets'.DS.'css'.DS.'compiled'.DS;
     }
     
     /**
      * Compile a CSS File
      *
      * @access     public
     **/
     public static function compile($name)
     {
          try
          {
               lessc::ccompile(self::$less_folder.$name.'.css', self::$compile_folder.$name.'.css');
          }
          catch (exception $ex)
          {
               exit('lessc fatal error:<br />'.$ex->getMessage());
          }
     }
}
/* End of file Less.php */