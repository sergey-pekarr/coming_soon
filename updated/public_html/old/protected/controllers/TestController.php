<?php

class TestController extends Controller
{

	public function init()
    {
		if (!DEBUG_IP)
		    Yii::app()->end();    	
    	
        parent::init();
        $this->layout='//layouts/guest1';
    }

    
    
    public function actionIndex()
	{

	}
    
    
    
    
    /**
     * create pass
     * USE: http://DOMAIN/test/pass/?pass=PASSWORD
     */
    public function actionPass()
    {
        if (LOCAL)
            echo MD5(SALT.$_GET['pass']);
        Yii::app()->end();
    }
    
    public function actionIp2long()
    {
        echo ip2long($_GET['ip']);
        Yii::app()->end();
    }    
    
    
  

    
    public function actionIp()
    {
        //if (!DEBUG_IP) return;
//echo Yii::app()->user->id . ' - ';
//return;        
        
        
        $ip = $_GET['ip'];
CHelperSite::vd("IP: ".$ip, 0);        
        $ip = ($ip) ? $ip : Yii::app()->location->getIPReal();
        
        CHelperSite::vd("IP: ".$ip, 0);   
        
        $location_id = Yii::app()->location->findLocationIdByIP($ip);
        $location = Yii::app()->location->getLocation( $location_id );
        CHelperSite::vd($location, 0);
        
    } 

	
    
    
    
    
    
    
    
}