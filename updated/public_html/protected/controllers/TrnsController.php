<?php

class TrnsController extends Controller
{

	public function init()
    {
		/*$allowedIPs = array(
			'192.168.15.1',	
			'93.175.196.6',
			'37.19.209.155',
			
			'69.55.50.94',//PM
		);
		
		if ( !(DEBUG_IP || in_array(CHelperLocation::getIPReal(), $allowedIPs)) )
		{
			header("HTTP/1.0 403 You have not permission");
			echo '<h1>You have not permission</h1>';
			Yii::app()->end();    	
		}*/
		
        parent::init();
        $this->layout='//layouts/guest1';
    }
    
    
    public function actionIndex()
	{
		echo CHelperSite::curl_request('http://overnightlover.com/trns/');
	}	
	
	
	
	
		
}