<?php

class CHelperDate extends CApplicationComponent
{
    public static function date_distanceOfTimeInWords($fromTime, $toTime = 0, $showLessThanAMinute = false) 
    {
        $distanceInSeconds = round(abs($toTime - $fromTime ));
        $distanceInMinutes = round($distanceInSeconds / 60);
           
        if ( $distanceInMinutes <= 1 ) {
                if ( !$showLessThanAMinute ) {
                    return ($distanceInMinutes == 0) ? 'less than a minute' : '1 minute';
                } else {
                    if ( $distanceInSeconds < 5 ) {
                        return 'less than 5 seconds';
                    }
                    if ( $distanceInSeconds < 10 ) {
                        return 'less than 10 seconds';
                    }
                    if ( $distanceInSeconds < 20 ) {
                        return 'less than 20 seconds';
                    }
                    if ( $distanceInSeconds < 40 ) {
                        return 'about half a minute';
                    }
                    if ( $distanceInSeconds < 60 ) {
                        return 'less than a minute';
                    }
                   
                    return '1 minute';
                }
        }
        if ( $distanceInMinutes < 45 ) {
                return $distanceInMinutes . ' minutes';
        }
        if ( $distanceInMinutes < 90 ) {
                return 'about 1 hour';
        }
        if ( $distanceInMinutes < 1440 ) {
                return 'about ' . round(floatval($distanceInMinutes) / 60.0) . ' hours';
        }
        if ( $distanceInMinutes < 2880 ) {
                return '1 day';
        }
        if ( $distanceInMinutes < 43200 ) {
                return 'about ' . round(floatval($distanceInMinutes) / 1440) . ' days';
        }
        if ( $distanceInMinutes < 86400 ) {
                return 'about 1 month';
        }
        if ( $distanceInMinutes < 525600 ) {
                return round(floatval($distanceInMinutes) / 43200) . ' months';
        }
        if ( $distanceInMinutes < 1051199 ) {
                return 'about 1 year';
        }
           
        return 'over ' . round(floatval($distanceInMinutes) / 525600) . ' years';
    }
    

    /**
     * 
     */
    public static function date_ToSQLFormat($date)
    {
        return date("Y-m-d H:i:s", $date);
    }    

    /**
     * 
     */
    public static function getBirthday($d)
    {
        $dt = strtotime($d);
        
        $res['day'] = date('d', $dt);
        $res['month'] = date('m', $dt);
        $res['year'] = date('Y', $dt);
        
        return $res;
    }
    
    
    
    public static function secToTime($seconds, $hourHide=true) {
        $hours = floor($seconds / 3600);
        $minutes = floor($seconds % 3600 / 60);
        $seconds = $seconds % 60;
        
        if ($hourHide)
            return sprintf("%d:%02d", $minutes, $seconds);
        else
            return sprintf("%d:%02d:%02d", $hours, $minutes, $seconds);
    }

    
    
    static public function getTimeZoneOffset()
    {
		//time offset
		/*$timezone = new DateTimeZone(date_default_timezone_get()); // Get default system timezone to create a new DateTimeZone object
    	return $timezone->getOffset(new DateTime(date("Y-m-d H:i:s")));  */ 
    	return date('Z'); 
    }

}
