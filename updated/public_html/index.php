<?php

//Black country list
/*$blocksCountry = array('CN', 'IN');
if (isset($_SERVER['GEOIP_COUNTRY_CODE']) && in_array($_SERVER['GEOIP_COUNTRY_CODE'], $blocksCountry))
{
    header("HTTP/1.1 403 Forbidden");
    header("Location: /error_403.html");
    exit();
}
*/







$start_time = microtime();// ��������� ������� �����
$start_array = explode(" ",$start_time);// ��������� ������� � ������������ (���������� ���������� ��������� ������ �������-������)
$start_time = $start_array[1] + $start_array[0];// ��� � ���� ��������� �����
define('TIME_START', $start_time);

// change the following paths if necessary
$yii=dirname(__FILE__).'/../framework/yii.php';

include_once dirname(__FILE__).'/protected/config/defines.php';


if (SITE_UNDER_UPDATE)
{
    header("Location: /underupdate.php");
    exit();
}



if (isset($_GET['service']) && !stristr($_SERVER["REQUEST_URI"], 'site/login'))
{
    header("Location: /site/login?service=".$_GET['service']);
    exit();
}




if (SITE_UNDER_UPDATE)
{
    header("Location: /underupdate.html");
    exit();
}














if ( LOCAL ){
	if (LOCAL_OK) {
        $config=dirname(__FILE__).'/protected/config/lo.php';
    }
}
else{
	$config=dirname(__FILE__).'/protected/config/live.php';
}



require_once($yii);
Yii::createWebApplication($config)->run();
