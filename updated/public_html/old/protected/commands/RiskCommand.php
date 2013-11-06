<?php
class RiskCommand extends CConsoleCommand
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

	
	/**
	 * Synchronize with pm_today_gold
	 *
	 */	
	public function actionDetectRisk(){
		
		$logId = Yii::app()->helperCron->onStartCron('risk','DetectRisk');
		
		/*
		$db = Yii::app()->db;
		$qry1 = "select max(id) from pm_today_risk";
		$maxRiskId = $db->createCommand($qry1)->queryScalar();
		if(!$maxRiskId) $maxRiskId = 0;
		
		$qry = "select id, user_id from pm_today_gold where id > $maxRiskId";
		$items = $db->createCommand($qry)->queryAll();
		if(!$items) $items = array();
		
		foreach($items as $item){
			ProfileClientInfo::updatePaymentInfo($item['user_id']);
			$qry = "insert ignore into pm_today_risk(id, user_id, reg_date) 
					values({$item['id']},{$item['user_id']}, now())";
			$ok = $db->createCommand($qry)->execute();;
			RiskDetection::detectRisk_Email($item['id'], $item['user_id']);
			RiskDetection::detectRisk_Location($item['id'], $item['user_id']);
			RiskDetection::detectRisk_FigurePrint($item['id'], $item['user_id']);
		}*/
		
		RiskDetection::detectRisk();
		
		Yii::app()->helperCron->onEndCron($logId, count($items));
	}	
	
	/**
	 * Note: This method is called just one time manually to build user_info_ex from old data!
	 *
	 */	
	public function actionConvertOldData()
	{
		$logId = Yii::app()->helperCron->onStartCron('risk','ConvertOldData');
		
		$N = 200;
		$read = $N;
		$start = 0;
		$startTime = time();
		
		while($read == $N){
			sleep(1);//!!!			
			$qry = "select user_id from users_activity where loginCount > 0 limit $start, $N";
			$items = Yii::app()->db->createCommand($qry)->queryColumn();			
			$read = count($items);
			
			ProfileClientInfo::buildUserInfoEx($items);
			$start += $read;
			
			$elapse = date('H:i:s', time() - $startTime);
			echo " - ".number_format($start).": ok in $elapse \r\n";
		}
		
		Yii::app()->helperCron->onEndCron($logId, 'ok');
	}
	
	/**
	* Note: This method is called just one time manually to build pm_today_risk from old data!
	*
	*/	
	public function actionDetectRiskForOldData()
	{
		$db = Yii::app()->db;
		$qry1 = "select max(id) from pm_today_risk";
		$maxRiskId = $db->createCommand($qry1)->queryScalar();
		if(!$maxRiskId) $maxRiskId = 0;
		
		$qry = "select id, user_id, manager_id, date from pm_today_gold where id > $maxRiskId order by id";
		$items = $db->createCommand($qry)->queryAll();
		if(!$items) $items = array();
		
		
		$startTime = time();
		$i = 0;				
		foreach($items as $item){
			ProfileClientInfo::updatePaymentInfo($item['user_id']);
			$qry = "insert ignore into pm_today_risk(id, user_id, reg_date) 
					values({$item['id']},{$item['user_id']}, now())";
			$ok = $db->createCommand($qry)->execute();;
			//RiskDetection::detectRisk_Email($item['id'], $item['user_id']);
			//RiskDetection::detectRisk_Location($item['id'], $item['user_id']);
			RiskDetection::detectRisk_FigurePrint2($item['id'], $item['user_id'], $item['manager_id'], $item['date']);
			$i++;
			
			if($i>0 && $i %100 == 0){
				$elapse = date('H:i:s', time() - $startTime);
				echo " - ".number_format($i).": ok in $elapse \r\n";
			}
		}
	}
	
	public function actionSendRiskReport(){
		RiskDetection::checkAndSendMail();
	}
}