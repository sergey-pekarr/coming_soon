<?php

class UtilsCommand extends CConsoleCommand
{
    
    private $timeStart; 
    private $timeMax=60;
    private $usleepDelay = 100;
    
    public function init() 
    { 
    	if (SITE_UNDER_UPDATE) 
            Yii::app()->end();
        
		$this->usleepDelay = (LIVE) ? 100 : 1000;

        $this->timeStart = time();    
    }

    
	/**
	 * 21 * * * *  ...RIGHT COMMAND AND PATH.../protected/console utils ClearCronLog
	 * 21 * * * * php /usr/bin/php /home/pinkmeets/pinkmeets.com/public_html/protected/console.php utils ClearCronLog
	 *
	 * @return mixed 
	 *
	 */	
    public function actionClearCronLog()
    {
        $logId = Yii::app()->helperCron->onStartCron('Utils','ClearCronLog');        
        
        sleep(5);
        
        $interval =  (LOCAL) ? "7 DAY" : "6 MONTH";
        $sql = "DELETE FROM log_cron WHERE timeStart<(NOW()-INTERVAL {$interval}) LIMIT 10000";
		$res = Yii::app()->db->createCommand($sql)->execute();
        
        Yii::app()->helperCron->onEndCron($logId, $res);
    }  
   
    
}
