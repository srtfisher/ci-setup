<?php
/**
 * Simple ways to handle time
 *
 * Time can be confusing in PHP, but we like making things simple.
 * We save all data in either a UNIX timestamp or a DATETIME timestamp, relative
 * to GMT.
 *
 * From there, we can convert it over to local time or whatever timezone you would like
 *
 * @package Connect
 * @author sean
 * @since 0.1
**/

/**
 * Convert a CI timezone to PHP
 *
 * @return string
 * @param string The CI Timezone
**/
function ci_zone_to_php_zone($ci_zone)
{
	$offset = timezones($ci_zone);
	return timezone_name_from_abbr('', $offset * 3600, false);
}

/**
 * Fixing the CI date helper
 *
 * Converts the GMT Unix timestamp to the local timezone based upon the $timezone param
 *
 * @return int
 * @param int $time The GMT Timestamp
 * @param string $timezone The CI Timezone
 * @param bool $dst Should we use DST.
**/
function gmt_to_local($time = '', $timezone = 'UTC', $dst = FALSE)
{                       
	if ($time == '')
	{
		return now();
	}
	$time += timezones($timezone) * 3600;
	
	// thx http://www.toosweettobesour.com/2009/03/10/calculating-daylight-savings-time-boundary-in-php/
	$dst_begin = strtotime('Second Sunday March 0');  
	$dst_end   = strtotime('First Sunday November 0');
	
	$dst = false;
	if ($time >= $dst_begin AND $time < $dst_end) $dst = true;
	
	if ($dst == TRUE)
	{
		$time += 3600;
	}
	
	return $time;
}

/**
 * Function will get the date format for the current user.
 *
 * @access public
 * @return date() format.
**/
function GetDateFormat( )
{
	// Just save it as a static variable because it's easier
	static $date_format = FALSE;
	
	//	Return the static variable.
	if ( $date_format !== FALSE )
		return $date_format;
	
	//	Get the Meta
	if ( !logged_in() )
		$format = get_instance()->Users->get_meta('date_format');
	else
		$format = FALSE;
	
	//	No user custom format.
	if (! $format )
		$format = "m.d.y";
	
	$date_format = $format;
	return $date_format;
}

/**
 * Function will get the time format for the current user.
 *
 * @access public
 * @return The current time format
**/
function GetTimeFormat( ) {
	static $time_format = FALSE;
	
	if ( $time_format !== FALSE )
		return $time_format;
	
	//	Logged in?
	if (! logged_in() )
		$format = FALSE;
	else
		$format = get_instance()->Users->get_meta('time_format');
	
	if (! $format )
		$format = "g:i a";
	
	$time_format = $format;
	return $time_format;
}

/**
 *	Get the current MySQL date time
 *	It will be in GMT!
 *
 *	@return string
 *	@since 1.0
 *	@category Time
**/
function current_datetime()
{
	return gmdate('Y-m-d H:i:s' );
}

function to_datetime($t)
{
     return date('Y-m-d H:i:s', $t);
}

function current_datettime() {return current_datetime(); }
function currentdatetime() { return current_datetime(); }
/**
 * The the current timezone
 *
 * @since 3.6
**/
function get_current_timezone()
{
	if (! logged_in() )
		return 'UM5';
	
	$get = get_instance()->Users->get_meta('timezone');
	if ( ! $get OR $get === '' )
		return 'UM5';
	else
		return $get;
}

/**
 * Get the current timestamp for a GMT DATETIME
 *
 * We save all time references to MySQL in a GMT DATETIME
 * We want to display it in a local timezone and we want
 * the unix timestamp!
 *
 * @return int
 * @param string $date_time The DATETIME that should be made into a local UNIX TS
 * @param string|void $timezone The CI timezone to user (defaults to UM8) and it looks for the user's timezone!
 * @since 1.0
 * @category Time
**/
function get_local_unix( $date_time, $timezone = FALSE )
{
	//	Convert the DATETIME into a UNIX timestamp.
	//	This is an INT
	$unix_gmt = strtotime( $date_time );
	
	//	What timezone?
	if ( logged_in() AND ! $timezone )
	{
		$user_timezone = get_current_timezone();
		if ( $user_timezone !== '' AND $user_timezone !== NULL )
			$timezone = $user_timezone;
	}
	
	return gmt_to_local( $unix_gmt, $timezone );	
}

/* End of file time_helper.php */