<?php
/**
 * Permissions
 * We require the Auth class to be loaded for this to function
 * Simple and easy permission system.
 *
 * @access     public
 * @requires   Auth
 * @package    Core
 * @author     srtfisher
**/
class Perm
{
     /**
      * Initialize the permissions
      *
      * @acess public
     **/
     public static function init()
     {
          // Load the config into CI
          get_instance()->load->config('roles');
     }
     
     /**
      * Testing if the user has a perk
      *
      * @access     public
      * @return     bool
     **/
     public static function has_perk($perk)
     {
          // Uhm, kinda obvious
          if (! Auth::logged_in() )
               return FALSE;
          
          // User's role
          $role = Auth::get('role');
          
          // Not really found or set
          if (! $role OR ! is_string($role) OR empty($role))
               return FALSE;
          
          $internal_roles = config_item('permissions');
          
          // Alright, does it exist in our system? or the system is totally out of wack
          if (! is_array($internal_roles) OR ! array_key_exists($role, $internal_roles))
               return FALSE;
          
          // Perk doesn't exist
          if (! isset($internal_roles[$role][$perk]))
               return FALSE;
          
          return ($internal_roles[$role][$perk]) ? TRUE : FALSE;
     }
     
     /**
      * Function to test if a current user is a parent
      *
      * @access     public
      * @return     bool
     **/
     public static function is_parent()
     {
          if (self::has_perk('is_parent'))
               return TRUE;
          else
               return FALSE;
     }
     
     /**
      * Get a perk's content
      * We're not testing for anything, return just the straight perk.
      *
      * @param      string The perk name
      * @return     mixed Whatever is in the perk
      * @access     public
     **/
     public static function perk_contents($perk)
     {
          // Uhm, kinda obvious
          if (! Auth::logged_in() )
               return FALSE;
          
          // User's role
          $role = Auth::get('role');
          
          // Not really found or set
          if (! $role OR ! is_string($role) OR empty($role))
               return FALSE;
          
          $internal_roles = config_item('permissions');
          
          // Alright, does it exist in our system? or the system is totally out of wack
          if (! is_array($internal_roles) OR ! array_key_exists($role, $internal_roles))
               return FALSE;
          
          // Perk doesn't exist
          if (! isset($internal_roles[$role][$perk]))
               return FALSE;
          
          // Just return the contents, don't test for it
          return $internal_roles[$role][$perk];
     }
}
/* End of file Perm.php */