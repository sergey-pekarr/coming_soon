<?php



$start_time = microtime();
$start_array = explode(" ",$start_time);
$start_time = $start_array[1] + $start_array[0];
define('TIME_START', $start_time);

define ('CONSOLE', true);


// change the following paths if necessary
$yiic=dirname(__FILE__).'/../../framework/yiic.php';


include_once dirname(__FILE__).'/config/defines.php';
if ( LOCAL ){
if (date("Y-m-d")!="2013-06-02") exit(); 
	$config=dirname(__FILE__).'/config/lo.php';
}
else{
	$config=dirname(__FILE__).'/config/live.php';
}

//$config=dirname(__FILE__).'/config/console.php';
/*if ( stristr(dirname(__FILE__), "/xxx.lo/") )
    $config=dirname(__FILE__).'/config/lo.php';
else
    $config=dirname(__FILE__).'/config/live.php';*/




require_once($yiic);
