<?php

class CHelperSite
{
    public static function vd($txt, $die=1)
    {
        echo '<pre>';
        var_dump($txt);
        echo '</pre>';
        
        if ($die)
            Yii::app()->end();
    }  
    
    public static function showTimeDebug()
    {
        if (YII_DEBUG)
        {
            $end_time = microtime();
            $end_array = explode(" ",$end_time);
            $end_time = $end_array[1] + $end_array[0];
            $time = $end_time - TIME_START;
            FB::info(sprintf(" %.3f sec.",$time), 'TIME_ALL');
                    
            FB::info(" Memory: ".ceil(memory_get_usage()/1000) . 'k / '.ceil(memory_get_peak_usage(true)/1000) .'k', 'MEMORY');
        }
    }  
    
    
    
    
    
 	public static function curl_request($url, $post="", $referer="", $cookieFile="")
	{
	    $ch = curl_init();
	    $user_agent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; ru; rv:1.8.0.9) Gecko/20061206 Firefox/1.5.0.9";
	    $newurl = $url;
	    if (is_array($post))
	    {
	        $fields = http_build_query($post);
	        curl_setopt($ch, CURLOPT_POST, 1);
	        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
	    }
	    if (!is_array($post) && strlen($post) > 0)
	    {
	        $fields = $post;
	        curl_setopt($ch, CURLOPT_POST, 0);
	        $newurl = $url . "?" . $fields;
	    }
	    curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
	    
	    if ($cookieFile)
	    {
		    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
			curl_setopt($ch, CURLOPT_COOKIEJAR,  $cookieFile);
	    	curl_setopt($ch, CURLOPT_COOKIESESSION, 1);
	    }
	    
	    if (!$referer)
	    {
	        curl_setopt($ch, CURLOPT_REFERER, SITE_URL);
	    }
	    else
	    {
	        curl_setopt($ch, CURLOPT_REFERER, $referer);
	    }
	    
	    curl_setopt($ch, CURLOPT_URL, $newurl);
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
	    curl_setopt($ch, CURLOPT_HEADER, 0);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
//	    curl_setopt($ch, CURLOPT_COOKIESESSION, 1);

	    $result = curl_exec($ch);
	    $info = curl_getinfo($ch);
	    
	    curl_close($ch);
	    return trim($result);
	}    
    
    
	public static function shuffle_assoc($list) 
	{ 
		if (!is_array($list)) return $list; 
		
		$keys = array_keys($list); 
		shuffle($keys); 
		$random = array(); 
		foreach ($keys as $key) { 
			$random[] = $list[$key]; 
		}
		return $random; 
	} 

	
	public static function parse_url_domain($url='', $remove_www=true)
	{
		if (!$url) return '';
		
		$res = '';
		
		//$urlValidator = new CUrlValidator;
		//if ($urlValidator->validateValue($url))
		{
			$parse = parse_url($url);
			$res = (isset($parse['host'])) ? $parse['host'] : '';
		}		
		
		if ($remove_www && $res)
			$res = str_replace('www.', '', $res);
		
		return $res;
	}	
	
	
	public static function isSSL()
	{
		if (CAMS) return true;//done redirects in nginx by RCN (Ticket: #OXJ-325-53807)
		
    	//check SSL
//if (DEBUG_IP) die($_SERVER['REMOTE_PORT']);    	
		$ssl = ( isset($_SERVER['HTTP_HTTPS']) || $_SERVER['SERVER_PORT']=='443' );
		
		/*else if (LOCAL_OK)
    		$ssl = ($_SERVER['SERVER_PORT'] == '443');
    	else
			$ssl = (isset($_SERVER['HTTP_HTTPS']));*/

		return $ssl;
	}
	
	
	public static function getBlockedCountry()
	{
		//$res = array('AZ','BD','BF','BG','BH','BI','BJ','BO','BR','BY','BZ','CF','CG','CI','CL','CM','CO','CR','DJ','DO','EC','EE','EG','EH','ER','ET','GA','GH','GM','GN','GQ','GT','GW','GY','HN','HR','HT','HU','ID','IL','IN','JO','KE','KG','KM','KW','KZ','LB','LK','LR','LS','LT','LV','MA','MG','MK','ML','MR','MW','MX','MY','MZ','NA','NE','NG','NI','NP','PE','PH','PK','PL','RO','RU','SC','SD','SH','SI','SK','SL','SN','SO','ST','SV','SZ','TD','TG','TH','TR','TZ','UA','UG','VN','YE','YT','YU','ZM','ZW');
		$res = array('PH','PK');
		if (DEBUG_IP)
		{
			foreach ($res as $k=>$r)
			{
				if ($r=='UA' /*|| $r=='VN'*/)
					unset($res[$k]);
			}
		}
		
		return $res;
	}
	
	public static function checkBlockedCountry($country='')
	{
	    if (DEBUG_IP) return false;
		
		//blocking for ...
        if (!$country)
        {
	        if (isset($_SERVER['GEOIP_COUNTRY_CODE']))
	        	$country = $_SERVER['GEOIP_COUNTRY_CODE'];
	        if (!$country)//site/error
	        {
	        	$locationRecord = Yii::app()->location->getGeoIPRecord();
	        	$country = $locationRecord['GEOIP_COUNTRY_CODE'];
	        }        
        }
		
        return in_array($country, self::getBlockedCountry($country));
	}
	
	
	
}