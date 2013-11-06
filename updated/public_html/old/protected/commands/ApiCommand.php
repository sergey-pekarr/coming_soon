<?php
class ApiCommand extends CConsoleCommand
{
    
    private $timeStart; 
    private $timeMax=55;
    private $usleepDelay;
    
    public function init() 
    { 
        if (SITE_UNDER_UPDATE) 
            Yii::app()->end();
        
		$this->usleepDelay = (LIVE) ? 50 : 1000;
            
        $this->timeStart = time();    
    }

    
    
    
    //  /home/pinkmeets/pinkmeets.com/public_html/protected/console api Out
    //  /home/onl/domains/onl.lo/public_html/protected/console api Out 
    
    public function actionOut() 
    {
return;//email 2013-08-27	Please no longer send our pinkmeets customers information to cupid, they are ripping me off.    	
    	if (!LIVE) return;//!!!
    	
        $logId = Yii::app()->helperCron->onStartCron('api','Out');        
		
        $modelApiOut = new ApiOut();
		$res = $modelApiOut->ApiPost();//$res = $modelApiOut->ApiPostPON();		
        
        Yii::app()->helperCron->onEndCron($logId, $res);
        
        
        
        
        /*
        //fix for skipped users
        $logId = Yii::app()->helperCron->onStartCron('api','Out_fix');
        
        $modelApiOut = new ApiOut();
        $res = $modelApiOut->ApiPost(true);
        
        Yii::app()->helperCron->onEndCron($logId, $res);*/        
    }
    
}
