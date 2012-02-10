<?php
/**
 * Essential Functions that will actually help us a lot!
 *
 * @package    Core
 * @since      0.1
**/

/**
 * Serialize data, if needed.
 *
 * @since 2.0.5
 *
 * @param mixed $data Data that might be serialized.
 * @return mixed A scalar data
 */
function maybe_serialize($data) {
	if (is_array($data) OR is_object($data))
		return serialize($data);

	if (is_serialized($data))
		return serialize($data);

	return $data;
}

/**
 * Unserialize value only if it was serialized.
 *
 * @since 2.0.0
 *
 * @param string $original Maybe unserialized original, if is needed.
 * @return mixed Unserialized data can be any type.
 */
function maybe_unserialize($original)
{
	if (is_serialized($original)) // don't attempt to unserialize data that wasn't serialized going in
		return @unserialize($original);
	return $original;
}

/**
 * Check value to find if it was serialized.
 *
 * If $data is not an string, then returned value will always be false.
 * Serialized data is always a string.
 *
 *
 * @param mixed $data Value to check to see if was serialized.
 * @return bool False if not serialized and true if it was.
 */
function is_serialized($data)
{
	// if it isn't a string, it isn't serialized
	if (!is_string($data))
		return false;
	$data = trim($data);
	if ('N;' == $data)
		return true;
	if (!preg_match('/^([adObis]):/', $data, $badions))
		return false;
	switch ($badions[1]) {
		case 'a' :
		case 'O' :
		case 's' :
			if (preg_match("/^{$badions[1]}:[0-9]+:.*[;}]\$/s", $data))
				return true;
			break;
		case 'b' :
		case 'i' :
		case 'd' :
			if (preg_match("/^{$badions[1]}:[0-9.E-]+;\$/", $data))
				return true;
			break;
	}
	return false;
}

/**
 * Check whether serialized data is of string type.
 *
 * @since 2.0.5
 *
 * @param mixed $data Serialized data
 * @return bool False if not a serialized string, true if it is.
 */
function is_serialized_string($data)
{
	// if it isn't a string, it isn't a serialized string
	if (!is_string($data))
		return false;
	$data = trim($data);
	if (preg_match('/^s:[0-9]+:.*;$/s', $data)) // this should fetch all serialized strings
		return true;
	return false;
}

/**
 * Function to get the domain from a URL
 *
 * @param string
 * @return string
**/
function getDomain($url)
{
	$parsed = parse_url($url); 
	return str_replace('www.', '', $parsed['host']); 
	return $hostname; 
}

/**
 * See if a string is in a valid URL format
 *
 * @param string
 * @return bool
**/
function valid_url($url)
{
	$pattern = '|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i';
	if (!preg_match($pattern, $url))
		return FALSE;
	else
		return TRUE;
}

/*
function write_file($file, $what) {
	if (!$fp = fopen($file, 'w'))
		return false;
		
		
	fwrite($fp, $what);
	fclose($fp);
}
*/

/**
 * Is the user currently logged in
 *
 * @access public
 * @return bool
 * @see Users::is_logged_in()
**/
function logged_in()
{
	return Auth::is_logged_in();
}

/**
 * Function to get a var from a DB result.
 * It'll return whatever is in the first column of a DB result.
 *
 * @param object The CI Database query object
 * @return mixed The first column of the first row
**/
function get_var($result)
{
	if ($result->num_rows() < 1)
		return false;
	foreach($result->row() as $row_item)
		return stripslashes($row_item);
}

/**
 * Function to get the var after executing a query
 *
 * We execute a query and get the var - simple stuff.
 *
 * @param string The DB Query
 * @return mixed
**/
function get_var_query($query)
{
	$CI = get_instance();
	$do = $CI->db->query($query);
	return get_var($do);
}

/***
 * Geneate a random string
 *
 * Usefull for hashes and passwords.
 * @return string 7 Chars long
**/
function gen_hash() {
	$chars = "abcdefghijkmnopqrstuvwxyz023456789";
    srand((double)microtime()*1000000);
    $i = 0;
    $pass = '' ;
		while ($i <= 7) {
   	    $num = rand() % 33;
    	   $tmp = substr($chars, $num, 1);
    	   $pass = $pass . $tmp;
    	   $i++;
	}
 	  
	return $pass;
}

/**
 * Generate a small string
 *
 * @return string 4 Chars long
**/
function generate_small_str()
{
	$chars = "abcdefghijkmnopqrstuvwxyz";
    srand((double)microtime()*1000000);
    $i = 0;
    $pass = '' ;
		while ($i <= 4) {
   	    $num = rand() % 33;
    	   $tmp = substr($chars, $num, 1);
    	   $pass = $pass . $tmp;
    	   $i++;
	}
 	  
	return $pass;
}

/**
 * Generate a random string with a custom length
 *
 * @return string Length based upon argument
**/
function generate_custom_str($length = 6)
{
	$length = (int) $length;
	
	$chars = "abcdefghijkmnopqrstuvwxyz";
    srand((double)microtime()*1000000);
    $i = 0;
    $pass = '' ;
		while ($i <= $length) {
   	    $num = rand() % 33;
    	   $tmp = substr($chars, $num, 1);
    	   $pass = $pass . $tmp;
    	   $i++;
	}
 	  
	return $pass;
}

/**
 * Password generator
 *
 * @return string
**/
function rand_pw($length=15,$moreCharacters=array())
{
        if (!is_array($moreCharacters))
                if (!is_object($moreCharacters))
                        $moreCharacters = array($moreCharacters);
                        
        $chars = array_merge(
                        range('a','z'),
                        range('A','Z'),
                        range(1,9),
                        array('~','!','@','#','$','%','^','&','*','(',')','_','+','|','{','}','"',':','?','>','<','`','-','=','\\','[',']','\'',';','/','.',','),
                        $moreCharacters
              );
                
        shuffle($chars);
        array_splice($chars,intval($length));
        return implode('',$chars);
}

/**
 * Generic function to see if an array contains a specific value.
 *
 * @since 2.0
**/
function does_array_have_value($array, $value) {
	foreach($array as $key => $ar) {
		if ($ar == $value)
			return $key;
	}
	return false;
}

/**
 * Find the geographical center between two points.
 *
 * It's just averages them and returns the average.
 *
 * @return string.
 * @param string
**/
function FindCenter($lat1, $lon1, $lat2, $lon2)
{
	//	Useful for Google Maps
	$lat_avg = $lat1+$lat2;
	$lat_avg = $lat_avg/2;
	
	$lon_avg = $lon1+$lon2;
	$lon_avg = $lon_avg/2;
	
	return $lat_avg . ',' . $lon_avg;
}

/**
 * Turn all @ links (@username here) into links relative to the site url
 * 
 * A text with @username would turn into domain.com/username, with domain.com
 * being the site_url()
 * 
 * @return string
 * @param string $text
 */
function linkify($text)
{
    $text= preg_replace("/@(\w+)/", '<a href="'.base_url().'/$1">@$1</a>', $text);
   // $text= preg_replace("/\#(\w+)/", '<a href="http://search.twitter.com/search?q=$1" target="_blank">#$1</a>',$text);
    return $text;
}

/**
 * Bold a string
 * 
 * @return string
 * @param string
 */
function bold($text = '')
{
     wrap_str('strong', $text, '', TRUE);
}

/**
 * Testing if we are running Internet Explorer
 * 
 * @return bool
 * @param void
**/
function is_ie()
{
    if (isset($_SERVER['HTTP_USER_AGENT']) AND 
    (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false))
        return TRUE;
    else
        return FALSE;
}

/**
 * Remove all the GET values from a string
 *
 * @return string
 * @param string
 * @category Sys_core
**/
function strip_get_from_string($string)
{
	list($uri, $get) = explode('?', $string, 2);
	return $get;
}

/**
 * Remove all the GET values from an array of segments
 *
 * @return array
 * @param array
 * @deprecated
**/
function strip_get_segments($array)
{
	$string = implode('/', $array);
	return explode('/', strip_get_from_string($string));
}

/**
 * Load a view file
 * 
 * @category Sys_core
 * @return void
 * @param string $what What view the load
 * @param array|void $second The parameters to pass.
**/
function load_view($what, $second = FALSE)
{
	get_instance()->load->view($what, $second = FALSE);
}

/**
 * Expand all shortend URLs
 *
 * @param string The URL to expand
 * @return string The expanded URL or the original URL if already expanded
**/
function expandShortUrl($url)
{
    $url = prep_url($url);
	
	$headers = get_headers($url, 1);
    if (isset($headers['Location']))
		return $headers['Location'];
	else
		return $url;
}


/**
 * Remove numbers from a string
 *
 * @param string
 * @return string
**/
function remove_numbers($str = '')
{
	return str_replace(array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9'), '', $str);
}

/**
 * Merge user defined arguments into defaults array.
 *
 * This function is used throughout WordPress to allow for both string or array
 * to be merged into another array.
 *
 *
 * @param string|array $args Value to merge with $defaults
 * @param array $defaults Array that serves as the defaults.
 * @return array Merged user defined values with defaults.
 * @category WordPress
 */
function wp_parse_args($args, $defaults = '') {
	if (is_object($args))
		$r = get_object_vars($args);
	elseif (is_array($args))
		$r = $args;
	else
		wp_parse_str($args, $r);

	if (is_array($defaults))
		return array_merge($defaults, $r);
	return $r;
}

/**
 * So we don't have to repeat ourselves dozens of times
 * 
 * @return string
 * @param string The URL to redirect them back to after they are logged in
 * @access public
 * @category Sys_core
**/
function login_url($redirect = '')
{
    $url = site_url('login');
    
    //  Quick and easier than having a two function stack, just throw 'current' as the
    //  redirect url and it will set it to the current url.
    if ($redirect === 'current')
        $redirect = current_url();
    
    
    if ($redirect === '')
        return $url;
    
    //  We need to setup a redirect
    return $url . '?redirect=' . urlencode($redirect);
}

/**
 * Set the current good message
 *
 * @access public
 * @param string
**/
function set_good($what)
{
	return get_instance()->Core->set_good($what);
}

/**
 * Set the current error message
 *
 * @param string
 * @access public
**/
function set_error($what)
{
	return get_instance()->Core->set_error($what);
}

/**
 * Get the current user ID
 *
 * @return bool|int
**/
function current_uid()
{
	if (! logged_in())
		return FALSE;
	
	return Auth::uid();
}


/**
 * A simple error handler
 *
 * @access public
 * @return object
**/
function simple_error($error)
{
	$obj = new stdClass();
	$obj->simple_error = $error;
	return $obj;
}

/**
 * Detect if a variable is an simple_error object
 *
 * @return bool
**/
function is_simple_error($obj)
{
	if (! is_object($obj))
		return FALSE;
	
	if (! property_exists($obj, 'simple_error'))
		return FALSE;
	
	return TRUE;
}

/**
 * Get the error from a `simple_error`
 *
 * @return     string|bool
 * @param      object
**/
function get_simple_error($obj)
{
	if (! is_simple_error($obj))
		return FALSE;
	
	return $obj->simple_error;
}

/**
 * Generate a bunch of li's
 * Simple way to add all the items you want and we will validate it in-function.
 *
 * @access     public
 * @param      array
**/
function generate_menu_list_items($data)
{
     if (! is_array($data))
          return false;
     
     $base = site_url();
     $current = reset(explode('/', str_replace($base, '', current_url()), 2));
     
     // Loop though each
     foreach($data as $row) :
          // Find out if this one is the current base
          $this_one = str_replace($base, '', $row['a_href']);
          if ($this_one == $current)
          {
               if (! isset($row['li_class']))
                    $row['li_class'] = 'active';
               else
                    $row['li_class'] = $row['li_class'] . ' active';
          }
          
          if (isset($row['checkFor']))
          {
               // Skip this one since it's invalid
               if (! $row['checkFor'] AND ! is_null($row['checkFor']) )
                    continue;
          }     
          
          ?><li <?php
               if (isset($row['li_class'])) {
                    echo 'class="'.$row['li_class'].'"';
               } ?> <?php
               if (isset($row['li_id'])) {
                    echo 'id="'.$row['li_id'].'"';
               } ?>>
          <a href="<?php echo $row['a_href']; ?>" class="<?php
          if (isset($row['a_class'])) {
               echo $row['a_class'];
          } ?>" id="<?php
          if (isset($row['a_id'])) {
               echo $row['a_id'];
          } ?>">
          <span><?php echo $row['a_content']; ?></span>
          </a></li><?php
     
     endforeach;
}

/**
 * Simple function to wrap something in a HTML element
 *
 * @access     public
 * @return     string
 * @param      string The element name (div, block, article, etc)
 * @param      mixed The string to wrap or a callback function
 * @param      string Attributes to add to the HTML element (title, style, etc)
 * @param      bool Should we return or echo it out (defaults to return)
**/
function wrap_str($element, $string, $attr = '', $echo = FALSE)
{
     $element = (string) $element;
     $string = (string) $string;
     $attr = (string) $attr;
     $echo = (bool) $echo;
     
     // Creating the string
     $return = '<'.$element.' '.$attr.'>'.(is_callable($string) ? $string() : $string).'</'.$element.'>';
     
     // Echo it if they want, we'll still return it regardless
     if ($echo)
          echo $return;
     
     return $return;
}

/**
 * Get the jQuery format to execute code on page load
 * This does not add script tags
 *
 * @return     string
 * @param      string The string to execute
**/
function on_jquery_load($str)
{
     ?>$(document).ready(function () {<?php
     if (is_callable($str))
          $str();
     else
          echo $str;
     ?>});<?php
}

/**
 * Force SSL on the Current URL
 * We will redirect them and exit them after this function.
 *
 * @return     void
 * @access     public
**/
function force_ssl() {
     if (! isset($_SERVER['HTTPS']) OR $_SERVER['HTTPS'] !== "on") {
          $url = "https://". $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
          redirect($url);
          exit;
     }
}

/**
 * Build URL query based on an associative and, or indexed array.
 *
 * This is a convenient function for easily building url queries. It sets the
 * separator to '&' and uses _http_build_query() function.
 *
 * @see _http_build_query() Used to build the query
 * @link http://us2.php.net/manual/en/function.http-build-query.php more on what
 *		http_build_query() does.
 *
 * @since 2.3.0
 *
 * @param array $data URL-encode key/value pairs.
 * @return string URL encoded string
 */
function build_query( $data ) {
	return _http_build_query( $data, null, '&', '', false );
}

// from php.net (modified by Mark Jaquith to behave like the native PHP5 function)
function _http_build_query($data, $prefix=null, $sep=null, $key='', $urlencode=true) {
	$ret = array();

	foreach ( (array) $data as $k => $v ) {
		if ( $urlencode)
			$k = urlencode($k);
		if ( is_int($k) && $prefix != null )
			$k = $prefix.$k;
		if ( !empty($key) )
			$k = $key . '%5B' . $k . '%5D';
		if ( $v === null )
			continue;
		elseif ( $v === FALSE )
			$v = '0';

		if ( is_array($v) || is_object($v) )
			array_push($ret,_http_build_query($v, '', $sep, $k, $urlencode));
		elseif ( $urlencode )
			array_push($ret, $k.'='.urlencode($v));
		else
			array_push($ret, $k.'='.$v);
	}

	if ( null === $sep )
		$sep = ini_get('arg_separator.output');

	return implode($sep, $ret);
}

/**
 * Retrieve a modified URL query string.
 *
 * You can rebuild the URL and append a new query variable to the URL query by
 * using this function. You can also retrieve the full URL with query data.
 *
 * Adding a single key & value or an associative array. Setting a key value to
 * an empty string removes the key. Omitting oldquery_or_uri uses the $_SERVER
 * value. Additional values provided are expected to be encoded appropriately
 * with urlencode() or rawurlencode().
 *
 * @since 1.5.0
 *
 * @param mixed $param1 Either newkey or an associative_array
 * @param mixed $param2 Either newvalue or oldquery or uri
 * @param mixed $param3 Optional. Old query or uri
 * @return string New URL query string.
 */
function add_query_arg() {
	$ret = '';
	if ( is_array( func_get_arg(0) ) ) {
		if ( @func_num_args() < 2 || false === @func_get_arg( 1 ) )
			$uri = $_SERVER['REQUEST_URI'];
		else
			$uri = @func_get_arg( 1 );
	} else {
		if ( @func_num_args() < 3 || false === @func_get_arg( 2 ) )
			$uri = $_SERVER['REQUEST_URI'];
		else
			$uri = @func_get_arg( 2 );
	}

	if ( $frag = strstr( $uri, '#' ) )
		$uri = substr( $uri, 0, -strlen( $frag ) );
	else
		$frag = '';

	if ( preg_match( '|^https?://|i', $uri, $matches ) ) {
		$protocol = $matches[0];
		$uri = substr( $uri, strlen( $protocol ) );
	} else {
		$protocol = '';
	}

	if ( strpos( $uri, '?' ) !== false ) {
		$parts = explode( '?', $uri, 2 );
		if ( 1 == count( $parts ) ) {
			$base = '?';
			$query = $parts[0];
		} else {
			$base = $parts[0] . '?';
			$query = $parts[1];
		}
	} elseif ( !empty( $protocol ) || strpos( $uri, '=' ) === false ) {
		$base = $uri . '?';
		$query = '';
	} else {
		$base = '';
		$query = $uri;
	}

	wp_parse_str( $query, $qs );
	$qs = urlencode_deep( $qs ); // this re-URL-encodes things that were already in the query string
	if ( is_array( func_get_arg( 0 ) ) ) {
		$kayvees = func_get_arg( 0 );
		$qs = array_merge( $qs, $kayvees );
	} else {
		$qs[func_get_arg( 0 )] = func_get_arg( 1 );
	}

	foreach ( (array) $qs as $k => $v ) {
		if ( $v === false )
			unset( $qs[$k] );
	}

	$ret = build_query( $qs );
	$ret = trim( $ret, '?' );
	$ret = preg_replace( '#=(&|$)#', '$1', $ret );
	$ret = $protocol . $base . $ret . $frag;
	$ret = rtrim( $ret, '?' );
	return $ret;
}

/**
 * Removes an item or list from the query string.
 *
 * @since 1.5.0
 *
 * @param string|array $key Query key or keys to remove.
 * @param bool $query When false uses the $_SERVER value.
 * @return string New URL query string.
 */
function remove_query_arg( $key, $query=false ) {
	if ( is_array( $key ) ) { // removing multiple keys
		foreach ( $key as $k )
			$query = add_query_arg( $k, false, $query );
		return $query;
	}
	return add_query_arg( $key, false, $query );
}

/**
 * Simple Money Formatter
 *
 * @param      mixed
 * @return     string
**/
function money($what)
{
     return '$'.money_format('%i', $what);
}

/**
 * Covert cents to dollars format
 *
 * Takes 12345 and turns it into 123.45.
 *
 * @access     public
 * @param      int
 * @param      bool
 * @param      bool
 * @return     string
**/
function cents_to_dollars($cents = 0, $remove_dots = TRUE)
{
     // Remove the dots before hand.
     if ($remove_dots) $cents = str_replace('.', '', $cents);
     
     $cents = (int) $cents;
     
     // We don't accept negative values
     if ($cents < 0) $cents = 0;
     
     // If it's less that 100 cents
     if ($cents <= 9)
          return '0.0'.$cents;
     elseif($cents <= 99)
          return '0.'.$cents;
          
     $length = strlen($cents);
     
     // substr(string string, int start [, int length])
     $begin = substr($cents, 0, $length-2);
     $ending = substr($cents, $length-2, 2);
     
     return $begin.'.'.$ending;
}

/**
 * Converts a Dollar Amount to Cents
 *
 * @access     public
 * @param      string
 * @return     int
**/
function dollars_to_cents($dollars = '')
{
     $dollars = (string) $dollars;
     
     // Nada
     if (empty($dollars)) return 0;
     
     $dollars = str_replace('$', '', $dollars);
     
     return $dollars*100;
}

/**
 * Sanitize to Cents format for Stripe
 *
 * @param      mixed
 * @return     int
**/
function sanitize_amount($a)
{
     $a = str_replace('.', '', $a);
     $a = str_replace('$', '', $a);
     
     return (int) $a;
}

/**
 * Assets URL
 * Right now, it's an alias for site_url() until we move
 *
 * @access     public
 * @param      string
**/
function assets($what = '')
{
     $what = 'assets/'.$what;
     
     return site_url($what);
}

/**
 * Generate a Sparkline
 *
 * @access     public
 * @return     string
**/
function generate_sparkline($data = array(), $w = 100, $h = 30)
{
     // http://chart.apis.google.com/chart?cht=lc&chs=100x30&chd=t:5.3,26.5,15.9,31.7,42.3,21.2,26.5,42.3,47.6,95.2,18.5,26.5,21.2,37.0,52.9,58.2,47.6,68.8,26.5,31.7,42.3&chco=336699&chls=1,1,0&chm=o,990000,0,20,4&chxt=r,x,y&chxs=0,990000,11,0,_|1,990000,1,0,_|2,990000,1,0,_&chxl=0:|8|1:||2:||&chxp=0,42.3
     
     $out = 'http://chart.apis.google.com/chart?cht=lc&chs=';
     $out .= $w;
     $out .= 'x';
     $out .= $h;
     $out .= '&chd=t:';
     
     // Data
     $started = false;
     foreach($data as $key => $val) :
          
          if ($started)
          {
               $out .= ','.$val;
          }
          else
          {
               $started = true;
               $out .= $val;
          }
     endforeach;
     
     $out .= '&chco=336699&chls=1,1,0&chm=o,990000,0,20,4&chxt=r,x,y&chxs=0,990000,11,0,_|1,990000,1,0,_|2,990000,1,0,_&chxl=0:|';
     $out .= end($data);
     $out .= '|1:||2:||&chxp=';
     
     $out .= end($data);
     
     return $out;
}

/* End of file core_helper.php */