<?php

class FanCommand extends CConsoleCommand
{
	private $timeStart; 
	private $timeMax=55;
	private $usleepDelay;
	
	public function init() 
	{ 
		if (SITE_UNDER_UPDATE) 
			Yii::app()->end();
		
		$this->usleepDelay = 50;
		
		$this->timeStart = time(); 
	}
	
	public function actionPayout(){
return;//oleg 2013-01-16		
		sleep(25);
		$logId = Yii::app()->helperCron->onStartCron('Fan','Payout');
		CHelperFanProfile::checkWeeklyPayout();
		Yii::app()->helperCron->onEndCron($logId, '');	
	}
	
	public function actionCheckNewMessage(){
return;//oleg 2013-01-16		
		sleep(15);
		$logId = Yii::app()->helperCron->onStartCron('Fan','CheckNewMessage');
		CHelperFanProfile::checkNewFanMessages();
		//Yii::app()->helperCron->onEndCron($logId, '1');	
		CHelperFanProfile::checkDailyStatistic();
		Yii::app()->helperCron->onEndCron($logId, 'ok');	
		
		sleep(5);
		$logId = Yii::app()->helperCron->onStartCron('Fan','Payout');
		CHelperFanProfile::checkWeeklyPayout();
		Yii::app()->helperCron->onEndCron($logId, '');	
	}
}
