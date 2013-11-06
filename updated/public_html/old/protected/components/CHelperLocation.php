<?php

class CHelperLocation
{
    public static function getIPReal()
    {
        $ip = '';
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
        {
          $ips = @explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
          $ip = trim($ips[0]);
        }
        
        if (!$ip)
        {
          $ip = (isset($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
        }
		
		$ip = long2ip(ip2long($ip));//to prevent possible server's problem

        if (!$ip)
        {
        	$ip = '0.0.0.0';
        }
		
        return $ip;
    }

    
}
