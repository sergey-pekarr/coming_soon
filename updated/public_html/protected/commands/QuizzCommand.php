<?php
class QuizzCommand extends CConsoleCommand
{
	private $timeStart; 
	private $timeMax=55;
	private $usleepDelay;
	
	public function init() 
	{ 
//		if (SITE_UNDER_UPDATE) 
			Yii::app()->end();
		
		$this->usleepDelay = 50;
		
		$this->timeStart = time(); 
	}
	
	public function actionCalculateRank(){
return;//oleg 0213-01-16		
		sleep(30); //There are many tasks run at *:*:01. This task run at *:*:31 -> avoid overhead
		
		$db = Yii::app()->dbquizz;
		
		$logId = Yii::app()->helperCron->onStartCron('Quizz','CalculateRank');
		
		//Yii::app()->helperCron->onEndCron($logId, '1');	
		
		$trans = $db->beginTransaction();
		
		
		//Yii::app()->helperCron->onEndCron($logId, '2');	
		
		$now = date("Y-m-d H:i:s");
		$yesterday = date("Y-m-d H:i:s", time() - 24*3600);
		$twodaybefore = date("Y-m-d H:i:s", time() - 2*24*3600);
		try
		{
			$ok = false;

			$qry1 = "update test as t
				set t.last_date_taken = 0, t.two_date_before_taken = 0
				where status = 'completed'";
			$ok = $db->createCommand($qry1)->execute();
			
			//Yii::app()->helperCron->onEndCron($logId, '3');	
			//if($ok){
				$ok = false; //When exception happen -> $ok = false
				$qry2 = "update test as t
					inner join (select test_id, count(*) as count
								from taken
								where regdate >= '$yesterday'
								group by test_id) as v on v.test_id = t.id
					set t.last_date_taken = v.count";
				$ok = $db->createCommand($qry2)->execute();
				
			//Yii::app()->helperCron->onEndCron($logId, '4');	
			//}
			
			//if($ok){
				$ok = false;
				$qry3 = "update test as t
					inner join (select test_id, count(*) as count
								from taken
								where '$twodaybefore' <= regdate and regdate < '$yesterday'
								group by test_id) as v on v.test_id = t.id
					set t.two_date_before_taken = v.count";
				$ok = $db->createCommand($qry3)->execute();
				
			//Yii::app()->helperCron->onEndCron($logId, '5');	
			//}
			$trans->commit();
		}
		catch(Exception $e) // an exception is raised if a query fails
		{
			$trans->rollback();
		}
		
		//$trans->commit();		
		
		$data = json_encode(array('ok' => $ok));
		Yii::app()->helperCron->onEndCron($logId, $data);	
	}
// */1 * * * *  /usr/bin/php /home/pinkmeets/pinkmeets.com/public_html/protected/console.php Quizz CalculateRank
}
