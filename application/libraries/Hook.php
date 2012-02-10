<?php
/**
 * The Web Hook System
 *
 * We use this to send a request to a district about an event that is occuring
 * This system must also detect if a web hook is set for the district.
 *
 * @package    Core
 * @author     srtfisher
 * @since      0.5
**/
class Hook
{
     /**
      * Codeigniter Object
      *
      * @access     private
     **/
     private static $CI;
     
     /**
      * Setup the CI Object
      * We might have to do this a few times
      *
      * @access     public
     **/
     public static function setup_ci()
     {
          self::$CI = get_instance();
     }
     
     /**
      * The 'constructor'
      *
      * @access     public
      * @param      void
      * @return     void
     **/
     public static function init()
     {
          // Setup the Object
          self::setup_ci();
     }
     
     /**
      * Fire a Web Hook
      *
      * @param      string The URL to Fire
      * @param      string The event type
      * @param      array The data for the event
      * @return     bool
     **/
     public static function fire($url, $type, $data = array())
     {
          // Invalid URL
          if (! valid_url($url)) return simple_error('That isn\'t a valid URL.');
          
          $args = array();
          $args['event'] = $type;
          $args['data'] = (array) $data;
          
          // Call it
          return self::$CI->curl->simple_post($url, array('json' => json_encode($args)));
     }
     
     /**
      * Get a Web Hook for a district
      *
      * This is cached
      *
      * @param      int The district ID
      * @return     string
     **/
     public static function get_hook_url($d = 0)
     {
          if ($d < 1) return FALSE;
          
          if ($cached = self::$CI->cache->get('d-'.$d.'-hook-url'))
               return $cached;
          
          $get = self::$CI->db
          ->where('id', $d);
          ->where('isDeleted', 0)
          ->get();
          
          if ($get->num_rows() == 0) return FALSE;
          
          self::$CI->cache->save('d-'.$d.'-hook-url', $get->row()->hook_url, 3600);
          
          return $get->row()->hook_url;
     }
}
/* End of file Hook.php */