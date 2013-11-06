<?php
class PromoCommand extends CConsoleCommand
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


    public function actionActivity() 
    {
    	
        $logId = Yii::app()->helperCron->onStartCron('promo','activity');
        
        
        //LAST ACTIVITY
        $ids = (LOCAL) ? HelperPromo::getPromosRandom(1, 2) : HelperPromo::getPromosRandom(1500,2000);        
        $sql = "UPDATE LOW_PRIORITY users_activity SET activityLast=NOW() WHERE user_id IN (".implode(',',$ids).") LIMIT ".count($ids);
        Yii::app()->db->createCommand($sql)->execute();            
	        
//		sleep(1);
	        
        //LATEST VERIFIED
        //or verified again... - simulating of email changed :-)
        $ids = (LOCAL) ? HelperPromo::getPromosRandom(1, 2) : HelperPromo::getPromosRandom(10, 20);
        $sql = "UPDATE LOW_PRIORITY users_settings SET email_activated_at=NOW() WHERE user_id IN (".implode(',',$ids).") LIMIT ".count($ids);
        Yii::app()->db->createCommand($sql)->execute();            
	        
//		sleep(1);
			
        //profile_viewed
        $ids = (LOCAL) ? HelperPromo::getPromosRandom(1, 2) : HelperPromo::getPromosRandom(100, 2000);       
	        
        $sql = "";
        foreach ($ids as $id)
        {
        	$sql .= "INSERT INTO profile_viewed (user_id, count) VALUES ({$id}, 1)
  					 ON DUPLICATE KEY UPDATE count=count+1;";
        }
        Yii::app()->db->createCommand($sql)->execute();         
        
        
        
/* slow...
        //LAST ACTIVITY
        $rand = (LOCAL_OK) ? rand(1,2) : rand(1500,2000);
	        
        $sql = "SELECT id FROM users WHERE promo='1' ORDER BY RAND() LIMIT ".$rand;
        $ids = Yii::app()->db->createCommand($sql)->queryColumn();        
        
        if ($ids)
        {
	        $sql = "UPDATE users_activity SET activityLast=NOW() WHERE user_id IN (".implode(',',$ids).")";
		            
	        Yii::app()->db->createCommand($sql)->execute();
	        
	        sleep(5);
        }	        
	        
        //LATEST VERIFIED
        //or verified again... - simulating of email changed :-)
        $rand = (LOCAL_OK) ? rand(1,2) : rand(10,20);
	        
        $sql = "SELECT id FROM users WHERE promo='1' ORDER BY RAND() LIMIT ".$rand;
        $ids = Yii::app()->db->createCommand($sql)->queryColumn();        
        
        if ($ids)
        {
	        $sql = "UPDATE users_settings SET email_activated_at=NOW() WHERE user_id IN (".implode(',',$ids).")";
		            
	        Yii::app()->db->createCommand($sql)->execute();            
		        
			sleep(5);
        }	
        //profile_viewed
        $rand = (LOCAL_OK) ? rand(1,2) : rand(100,2000);
	        
        $sql = "SELECT id FROM users WHERE promo='1' ORDER BY RAND() LIMIT ".$rand;
        $ids = Yii::app()->db->createCommand($sql)->queryColumn();        

        if ($ids)
        {
	        $sql = "";
	        foreach ($ids as $id)
	        {
	        	$sql .= "INSERT INTO profile_viewed (user_id, count) VALUES ({$id}, 1)
	  						ON DUPLICATE KEY UPDATE count=count+1;";
	        }
	        Yii::app()->db->createCommand($sql)->execute();          	
        }
*/

        
        Yii::app()->helperCron->onEndCron($logId, '');
    }
    
}
