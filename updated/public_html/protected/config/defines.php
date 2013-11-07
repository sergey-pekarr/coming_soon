<?php

ini_set('default_charset', 'UTF-8');
mb_internal_encoding("UTF-8"); //!!!

//ini_set('date.timezone', 'UTC');
//ini_set('date.timezone', 'America/Los_Angeles');
ini_set('date.timezone', 'America/New_York');

if ( stristr(dirname(__FILE__), "/ms.lo/") )
{
	define('LOCAL_OK', true); 
} 
else 
{
    define('LOCAL_OK', false);
}

if ( LOCAL_OK ) define('LOCAL', true); else define('LOCAL', false);


if (!defined('CONSOLE')) define ('CONSOLE', false);

if (!CONSOLE)
{
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
    {
        $ips = @explode(', ', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $ip = trim($ips[0]);
    }
    if ( !isset($ip) || !$ip)
    {
        $ip = (isset($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
    }
}
else
    $ip='';

/*if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
{
    $ips = @explode(', ', $_SERVER['HTTP_X_FORWARDED_FOR']);
    $ip = trim($ips[0]);
}
if (!isset($ip))
{
    $ip=$_SERVER['REMOTE_ADDR'];
}*/
if (in_array($ip, array('192.168.15.1', '10.10.10.144', '127.0.0.1')))
    define('DEBUG_IP', true);
else
    define('DEBUG_IP', false);



//define('DIR_ROOT', dirname(__FILE__).'/../..');
//n: The line above cause problem when go out for 1 level dirname(DIR_ROOT) -> .../protected/config/..
define('DIR_ROOT', dirname(dirname(dirname(__FILE__))));

/*if ( isset($_SERVER['REQUEST_URI']) && (stristr($_SERVER['REQUEST_URI'], '/admin') 
	|| (isset($_SERVER['HTTP_REFERER']) && stristr($_SERVER['HTTP_REFERER'], '/admin'))
) )//if ( stristr($_SERVER['REMOTE_ADDR'], '/admin') || stristr($_SERVER['HTTP_REFERER'], '/admin') )
    define ('ADMIN', true);
else
    define ('ADMIN', false);
*/
if ( isset($_SERVER['REQUEST_URI']) && stristr($_SERVER['REQUEST_URI'], '/admin') )//if ( stristr($_SERVER['REMOTE_ADDR'], '/admin') || stristr($_SERVER['HTTP_REFERER'], '/admin') )
    define ('ADMIN_AREA', true);
else
    define ('ADMIN_AREA', false);
    
/*if ( ADMIN_AREA 
	|| 
	(isset($_SERVER['HTTP_REFERER']) && stristr($_SERVER['HTTP_REFERER'], '/admin'))
)
    define ('ADMIN', true);
else
    define ('ADMIN', false);*/    


$HTTP_HOST = (isset($_SERVER['HTTP_HOST'])) ? strtolower($_SERVER['HTTP_HOST']) : '';
//CAMS alias detect
if ( preg_match("/^join\./", $HTTP_HOST ) ) 
	define("CAMS", true);
else
	define("CAMS", false);

define("CAM_DUMMY", false);//not uses on PM...
	
	
define('SITE', 'meetsi.com');
if (CAMS || CAM_DUMMY)
{
	define('SITE_NAME', '...Cams - Live Sex Chat, Sex Shows and Webcam Sex - Amateur Cams and Pornstars');//title!!!
	define('SITE_NAME_FULL', SITE_NAME);
}
else
{
	define('SITE_NAME', 'meetsi.com - Online Dating');//title!!!
	define('SITE_NAME_FULL', 'meetsi.com - Online Dating');// best dating online for free
}
    



if (DEBUG_IP)
{
    // remove the following lines when in production mode
    defined('YII_DEBUG') or define('YII_DEBUG',true);
    // specify how many levels of call stack should be shown in each log message
    defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);        
}

define('FACEBOOK', false);/////////////////////////////////////////////////////
define('LAYOUT', '//layouts/main'); 

if (LOCAL)
{
    define('SITE_UNDER_UPDATE', false);

    define('LIVE', false);
    define('DEMO', false);
    
    if (LOCAL_OK)
    {
	    if (CAMS || CAM_DUMMY)
	    {
	    	define('DOMAIN', $HTTP_HOST);
	    	define("DOMAIN_COOKIE", $HTTP_HOST);
	    }
	    else
	    { 
	    	define('DOMAIN', 'ms.lo');
	    	define("DOMAIN_COOKIE", str_replace(array('www.') , "", $HTTP_HOST));
	    }
	    // not works for cams.NLC on live server: define("DOMAIN_COOKIE", str_replace(array('cams.','www.') , "", $HTTP_HOST));	
    	
	    
	    define('SITE_URL', 'http://'.DOMAIN);
	    define('SITE_URL_SSL', 'https://'.DOMAIN);   
	    define('TICKET_URL', 'http://ticket.'.DOMAIN);
	    define('SITE_MAIN_URL', 'http://ms.lo');
	    
	    define("CACHE_KEY", "ms.lo");
	    
	    
	    //DB
	    define('DB_NAME', 'ms');
	    define('DB_USER', 'root');
	    define('DB_PASS', 'NiTqUHyhWY4gacYkDlLD');
	    
	    //GEO DB LIB
	    define('DB_NAME_GEO', 'ms_geo');//static db for all sites
	    define('DB_USER_GEO', 'root');
	    define('DB_PASS_GEO', 'NiTqUHyhWY4gacYkDlLD');

	    //DB_STATS
	    define('DB_NAME_STATS', 'ms_stats');
	    define('DB_USER_STATS', 'root');
	    define('DB_PASS_STATS', 'NiTqUHyhWY4gacYkDlLD');
	    
		//DB_QUIZZ
	    define('DB_NAME_QUIZZ', 'quizz');
	    define('DB_USER_QUIZZ', 'root');
	    define('DB_PASS_QUIZZ', 'ok1605');	
		
	    //externals
	    //FACEBOOK
	    define('FB_APPID', '');
	    define('FB_SECRET', '');
	    define('FB_COOKIE', true);
	    //define('FB_APP_URL', 'http://'.DOMAIN);
	
	    
	    
	    
	    define('DIR_IMG', dirname(__FILE__).'/../../../media');//define('DIR_IMG', dirname(__FILE__).'/../../../pictures');
	    define('DIR_IMG_PROMO', '/home/meetsi/domains/_MEDIA_PROMO');
	    //define('URL_MEDIA', SITE_URL.'/media');

	    define('IMG_USE_CACHE_DIR', true);//use cache for previews small and medium images - good for nginx server
	    define('IMG_DIR_CACHE', '/home/meetsi/domains/ms.lo/public_html/cache');
	    define('IMG_URL_CACHE', /*SITE_URL.*/'/cache');	   
		
		define('QUIZZ_DIR_IMG',  dirname(__FILE__).'/../../../media/quizz');
	    
    } else {

    }

    
    define('METRICS_ID', '');
}
else
{
    
    /*if (!DEBUG_IP)
        define("SITE_UNDER_UPDATE", true);
    else*/
        define("SITE_UNDER_UPDATE", false);
    
    
    
    define('LIVE', true);
    define('DEMO', false);
    
    if (CAMS || CAM_DUMMY)
    {
    	define('DOMAIN', $HTTP_HOST);
    	define("DOMAIN_COOKIE", $HTTP_HOST);
    }
    else 
    {
    	define('DOMAIN', 'meetsi.com');
    	define("DOMAIN_COOKIE", str_replace(array('www.') , "", $HTTP_HOST));
    }	
    // not works for cams.NLC on live server: define("DOMAIN_COOKIE", str_replace(array('cams.','www.') , "", $HTTP_HOST));
    	
    if ( $HTTP_HOST )
		define('SITE_URL', 'http://'.$HTTP_HOST);
	else
		define('SITE_URL', 'http://'.DOMAIN);
    
	if ( $HTTP_HOST )
        define('SITE_URL_SSL', 'https://'.$HTTP_HOST);
	else
		define('SITE_URL_SSL', 'https://'.DOMAIN);
		
	define('TICKET_URL', 'http://ticket.meetsi.com');
	define('SITE_MAIN_URL', 'http://meetsi.com');
	
	define("CACHE_KEY", "meetsi.com");
	
	
    //DB
    define('DB_NAME', 'ms');
    define('DB_USER', 'meetsi');
    define('DB_PASS', 'klfIL69rHo');
    
	//GEO DB LIB
	define('DB_NAME_GEO', 'ms_geo');//define('DB_NAME_GEO', 'geo');
	define('DB_USER_GEO', 'meetsi');
	define('DB_PASS_GEO', 'klfIL69rHo');
   

    //DB_STATS
	define('DB_NAME_STATS', 'ms_stats');
	define('DB_USER_STATS', 'meetsi');
	define('DB_PASS_STATS', 'klfIL69rHo');
    
	//DB_QUIZZ
	define('DB_NAME_QUIZZ', 'quizz');
	define('DB_USER_QUIZZ', 'meetsi');
	define('DB_PASS_QUIZZ', '6Gj5exvob8rK1uCjxSb');
    
    //externals
    //FACEBOOK
	define('FB_APPID', '...');
	define('FB_SECRET', '...');
	define('FB_COOKIE', true);
    //define('FB_APP_URL', 'http://apps.facebook.com/...');
    
 
    define('DIR_IMG', dirname(__FILE__).'/../../../media');//define('DIR_IMG', dirname(__FILE__).'/../../../pictures');
    define('DIR_IMG_PROMO', '/home/meetsi/_MEDIA_PROMO');
    //define('URL_MEDIA', SITE_URL.'/media');

	define('IMG_USE_CACHE_DIR', true);//use cache for previews small and medium images - good for nginx server
	define('IMG_DIR_CACHE', '/home/meetsi/meetsi.com/public_html/cache');
	define('IMG_URL_CACHE', /*SITE_URL.*/'/cache');	    
		
	define('QUIZZ_DIR_IMG',  dirname(__FILE__).'/../../../media/quizz'); 
    
	define('METRICS_ID', '');
}




//define('DIR_IMG', dirname(__FILE__).'/../../media');//define('DIR_IMG', dirname(__FILE__).'/../../../pictures');

define('IMG_ANIMATION_USE', false);

define('IMG_PROFILE_MAX', 100);//max count of images for user's profile

define('IMG_SIZE_MIN', 1);
define('IMG_SIZE_MAX', 5*1024*1024);
//define('DIR_IMG', dirname(__FILE__).'/../../../pictures');
//define('DIR_IMG_TMP', DIR_IMG);//
define('DIR_IMG_TMP', DIR_ROOT . '/tmp');//define('DIR_IMG_TMP', dirname(__FILE__).'/../../../tmp');


define("SALT", 'ok1605');//do not change!!!
define("SECURE_KEY", 'ok1605');//do not change!!!


define("PROFILE_USERNAME_LEN_MIN", 3);
define("PROFILE_USERNAME_LEN_MAX", 32);

define("PROFILE_USERNAME_PATTERN", '/^[0-9a-zA-Z_\.\-]{'.PROFILE_USERNAME_LEN_MIN.','.PROFILE_USERNAME_LEN_MAX.'}$/');
define("ZIP_PATTERN", '/^[0-9a-zA-Z\s-]{3,13}$/');








//PAYMENTS
/*if (DEBUG_IP)
	define("MAIN_BILLER", 'segpay');
else
	define("MAIN_BILLER", 'zombaio');*/
define("MAIN_BILLER", 'rg2');

define("ZSIGNUP_BACKUP_BILLER", 'zombaio');
define("ZSIGNUP2_BACKUP_BILLER", 'zombaio2');


//CAMS domains
define("CAMS_DOMAIN_NLC", true);
define("CAMS_DOMAIN_ONL", false);

define("REVERSALS_SITE", "MC");
define("CAM_SITE", "PM");
define("CAM_SITE_URL", "http://xxx.com");


//CAMS GATEWAY (not paymod)
define("CAMS_BILLER_WAY", 'rg');

//http://stackoverflow.com/questions/72768/how-do-you-detect-credit-card-type-based-on-number
define("CCARD_PATTERN", "/^(?:4[0-9]{12}(?:[0-9]{3})?|5[1-5][0-9]{14}|6(?:011|5[0-9][0-9])[0-9]{12}|3[47][0-9]{13}|3(?:0[0-5]|[68][0-9])[0-9]{11}|(?:2131|1800|35\d{3})\d{11})$/");//define("CCARD_PATTERN", '/^[0-9]{16}$/');
define("CVV_PATTERN", '/^[0-9]{3,4}$/');


//PANAMUS
define("PANAMUS_PUBLIC", "xxx");
define("PANAMUS_PRIVATE", "xxx");
define("PANAMUS_USE", false);//stopped 2013-05-17


//FB
/*if (FACEBOOK)
{
    include_once dirname(__FILE__)."/fb_init.php";
}*/

//promos id: 110790-501202