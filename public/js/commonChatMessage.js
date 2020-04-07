/***********************************************************************
 *=========================== NAMESPACE ================================
 ***********************************************************************
 * Declare commonChatMessage namespace
 */
if( typeof Plex != 'undefined' ) Plex.common = {};
/***********************************************************************/

/***********************************************************************
 *========================= HAS SCROLL BAR =============================
 ***********************************************************************
 * Check if a jQuery object has scroll bars
 */
(function($) {
    $.fn.hasScrollBar = function() {
        return this.get(0).scrollHeight > this.height();
    }
})(jQuery);
/***********************************************************************/

/***********************************************************************
 *======================== GET DATE SEPARATOR ==========================
 ***********************************************************************
 * Gets the date separators for chat: 'Today', 'Yesterday', 'Wednesday',
 * '3/17/2015' given a laravel date
 * @param		string		laravel_date		laravel date, given in UTC
 * @return		string							'Yesterday', 'Today', [weeekday]
 * 												[calendar date]
 */
Plex.common.get_date_separator = function( laravel_date ){
	// instantiate date object
	timestamp = this.make_date( laravel_date );
	now = new Date();

	// get unix time
	var unix_timestamp = timestamp.getTime();
	var unix_now = now.getTime();

	// do math for 'today', or 'yesterday' strings
	var diff = unix_now - unix_timestamp;
	var ms_offset = now.getHours() * 3600000
		+ now.getMinutes() * 60000
		+ now.getSeconds() * 1000
		+ now.getMilliseconds();
	var prefix = '';
	// if less than a week
	if( diff < ( 604800000 - ms_offset ) ){
		prefix = this.get_weekday( timestamp );
		// if less than 2 days
		if( diff < ( 172800000 - ms_offset ) ){
			prefix = 'Yesterday';
			// if less than 1 day old
			if( diff < ms_offset ){
				timestamp 
				prefix = 'Today';
			}
		}
		return prefix;
	}

	// else get calendar ( 3/17/2015 ) date
	return timestamp.toLocaleDateString();
}
/***********************************************************************/

/***********************************************************************
 *============================ GET WEEKDAY =============================
 ***********************************************************************
 * Given a laravel timestamp date. Gets the weekday: e.g.: "Monday".
 * @param		date object		d		the laravel date timestamp
 * @return		string					the day of the week
 */
Plex.common.get_weekday = function( d ){
	// instantiate date object
	var weekday_index = d.getDay();
	var weekday_string = '';
	var weekdays = [
		'Sunday',
		'Monday',
		'Tuesday',
		'Wednesday',
		'Thursday',
		'Friday',
		'Saturday'
	];
	return weekdays[ weekday_index ];
}
/***********************************************************************/

/***********************************************************************
 *============================= MAKE DATE ==============================
 ***********************************************************************
 * Makes a date object given a UTC laravel timestamp
 * @param		string		laravel_date		laravel timestmap. UTC
 * @return		object		timestamp_date		a js Date object
 */
Plex.common.make_date = function( laravel_date ){
	// break down raw laravel date
	var laravel_date_arr = laravel_date.split( ' ' );
	var full_date = laravel_date_arr[0];
	var full_time = laravel_date_arr[1];

	// break down date
	var full_date_arr = full_date.split( '-' );
		// get date pieces
		var year = full_date_arr[0];
		var month = full_date_arr[1];
			// month is zero offset
			month = parseInt( month );
			month--;
		var day = full_date_arr[2];

	// break down time
	var full_time_arr = full_time.split( ':' );
		// get time pieces
		var hour = full_time_arr[0];
		var minute = full_time_arr[1];
		var second = full_time_arr[2];

	// instantiate date object as UTC
	var timestamp_date = new Date( Date.UTC( year, month, day, hour, minute, second ) );
	//var timestamp_date = new Date( year, month, day, hour, minute, second );

	return timestamp_date;
}
/***********************************************************************/

/***********************************************************************
 *======================== GET TIMESTAMP TIME ==========================
 ***********************************************************************
 * Gets a time given a laravel timestamp
 * @param		string		timestamp			laravel timestamp. UTC
 * @return		string		timestamp_time		timestamp's time. Formatted an
 * 												converted to user's local time
 */
Plex.common.get_timestamp_time = function( timestamp ){
	// instantiate date object
	timestamp_date = this.make_date( timestamp );
	// get raw time, which includes milliseconds
	var raw_time = timestamp_date.toLocaleTimeString();
	formatted_time_arr = raw_time.split( ':', 2 );
	formatted_time_suffix_arr = raw_time.split( ' ' );
	// build new timestamp
	timestamp_time = formatted_time_arr[0]
		+ ':'
		+ formatted_time_arr[1]
		+ ' '
		+ formatted_time_suffix_arr[1];

	return timestamp_time;
}
/***********************************************************************/

// -- mobile chat/messages slide effect
//slide and hide out of view
Plex.common.slideHide = function( hide_elem, show_elem, dir, speed ){
    
    var oppositeDirection = 'left';

    if( dir === 'left' ){
        oppositeDirection = 'right';
    }

    $(hide_elem).hide('slide', {direction: dir}, speed, function(){
        Plex.common.slideShow( show_elem, oppositeDirection, speed );
    });
}

//show and slide into view 
Plex.common.slideShow = function( elem, dir, speed ){
    $(elem).show('slide', {direction: dir}, speed);
}