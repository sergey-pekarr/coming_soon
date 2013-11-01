<?php
class ProfileCommand extends CConsoleCommand
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
	
	private function getColumn($table, $field){
		$result = array();
		foreach($table as $record){
			$result[] = $record[$field];
		}
		return $result;
	}	
	
		private function createUsersPlan(){
		$db = Yii::app()->db;
		$lastIdQry = "select max(user_id) from users_plan";
		$lastIdQry = $db->createCommand($lastIdQry)->queryScalar();
		if(!$lastIdQry) $lastIdQry = 0;
		$insQry = "insert ignore into users_plan(user_id, form) 
					select id as user_id, form 
					from users 
					where promo = '0' and id > $lastIdQry";
		$db->createCommand($insQry)->execute();
	}
	
	private function buildFakePlan(){
		$db = Yii::app()->db;
		$newQry = "select p.user_id, u.gender, u.looking_for_gender, l.country, l.latitude, l.longitude, a.joined, u.role
					from users_plan as p
					inner join users as u on u.id = p.user_id
					inner join users_location as l on l.user_id = u.id
					inner join users_activity as a on a.user_id = u.id
					where p.plan = '0' and p.form = '1' and u.gender = 'M'";
		//Last changed on Oct 1st: Only autoflirt to Men!
		$newUsers = $db->createCommand($newQry)->queryAll();
		
		//n Oct
		//$this->checkFanGirl($newUsers); //No need
		
		if(count($newUsers)>0){
			foreach($newUsers as $user){
				
				//if($user['gender'] == 'F' && isset($user['fangirl']) && $user['fangirl'] === true){
				//	//Oct 1st: Dont build fakeplan for fangirl users
				//	continue;
				//}
				
				CHelperAutoFlirt::buildFakePlan($user['user_id'],$user['gender'],$user['looking_for_gender'],
					$user['country'], $user['latitude'],$user['longitude'],
					$user['joined'],$user['role'], 0, 500);//, $user['fangirl']
				usleep($this->usleepDelay);
			}
		}
		
		//Note: buildFakePlan only process for found items. We need to update remains items
		$updateQry = "update users_plan set plan = '1' where plan = '0'";
		$ok =  $db->createCommand($updateQry)->execute();
		
		return $newQry != null? count($newUsers) : 0;
	}
	
	//Note 2012-07-30: Has change users_plan to contain plan of plan(all_plan_time, and next_plan_time)
	//													-> when should check Inactive users
	//Estimation: 20K users a month, 10K new users in last 14 days, everage a user need to check every 1.5 day (5 photo + email ~ 1.5 days)
	// -> 7K users need to check.
	// -> If process run every 5 minutes, 260 times a day -> 30 users might need to update fake plan, 1/2 login every day -> 15 real update
	public function actionCheckInactive(){
		$logId = Yii::app()->helperCron->onStartCron('Profile','CheckInactive');
		$db = Yii::app()->db;		
		$now = date('Y-m-d H:i:s');
		//This process is called by cron for every 10 minutes. 
		$limit = 1000;
		$qry = "select * from users_plan where plan = '1' and next_plan_time < '$now' and next_plan_time <> '0000-00-00 00:00:00' limit 0, $limit";
		$items = $db->createCommand($qry)->queryAll();
		if(!$items) $items = array();
		
		$checkUsers = $this->getColumn($items, 'user_id');
		
		$loginUsers = array();
		if(count($checkUsers)>0){
			$checkLogins = "select user_id, loginLast, activityLast 
						from users_activity 
						where user_id in (".implode(",", $checkUsers).")";		
			$loginUsers = $db->createCommand($checkLogins)->queryAll();
		}
		if(!$loginUsers) $loginUsers = array();
		
		$updatedCount = 0;
		foreach($loginUsers	as $loginUser){
			$user = null;
			foreach($items as $item){
				if($item['user_id'] == $loginUser['user_id'] && $item['pre_plan_time'] < $loginUser['loginLast']){
					//$next_plan_time = $item['next_plan_time'];
					$all_plan_time = $item['all_plan_time'];
					$pos = strpos($all_plan_time,',');
					if($pos>1){
						$next_plan_time = substr($all_plan_time, 0, $pos - 1);
						try{
							//Correct error if happen. Normally it will not happen
							$next_plan_time = strtotime($next_plan_time);
							$next_plan_time = date('Y-m-d H:i:s', $next_plan_time);
						} catch(exception $ex){
							break;
						}
						$udtFakePlanQry = "Update users_fakeplan set `enable` = 1 
											where user_id = {$item['user_id']} and timetosend < '$next_plan_time'";
						$ok = $db->createCommand($udtFakePlanQry)->execute();
						$updatedCount++;
						usleep($this->usleepDelay);
					}
					break;
				}
			}			
		}
		/*Impvement later
			1. for each to scan all users is not the best way (currently, maximum 1000 users, 0.5Mil loop to check is not a big prolem)
			2. To avoid multiple single update $udtFakePlanQry, we can create temporary table, use innner join to this table to update
				As measurement in head of method. 15 update queries is not a problem. We can accept < 100!
		*/
		
		//Note: Run hundred of single updates cause slow respone.
		//		use substring and instr might be better. Need test!
		$udtQry = "Update users_plan
				   set pre_plan_time = next_plan_time,
					   next_plan_time = case when instr(all_plan_time, ',') > 0 
                                        then substr(all_plan_time, 1, instr(all_plan_time, ',') - 1)
                                   when all_plan_time <> '' then all_plan_time
                                   else '0000-00-00 00:00:00' end,
					   all_plan_time = case when instr(all_plan_time, ',') > 0
                                        then substr(all_plan_time from instr(all_plan_time, ',') + 1)
                                  else '' end
				   where  plan = '1' and next_plan_time < '$now' and next_plan_time <> '0000-00-00 00:00:00' limit $limit";
		$ok = $db->createCommand($udtQry)->execute();
		
		/*test the update query above
		select user_id, substr(all_plan_time, 1, instr(all_plan_time, ',') - 1) as next_time, 
		          substr(all_plan_time from instr(all_plan_time, ',') + 1) as all_time, 
		          instr(all_plan_time, ',')
		from users_plan;
		-> Has tested: bad all_plan_time ('', ',', ',,', 'fasdf') do not cause error!, might cause warning
		*/
		
		/*
		How to test now ??????????
			1. Change ChelperAutFlirt.buildFakePlan on local to build plan eventhought for old day 
				(should delete users_plan and users_fakeplan before test)
			2. Manual edit users_plan to have data to test
			3. Disable update and insert in CheckInactive, run and debug to see how it work CheckInactive
			3. enable update and insert and run CheckInactive
			
		select user_id, max(timetosend) 
		from users_fakeplan 
		where `enable` = 1
		group by user_id;
		*/
		$data = json_encode(array('checkUser' => count($items), 'updatedCount' => $updatedCount));
		Yii::app()->helperCron->onEndCron($logId, $data);		
	}
	
	/**
	* Run every 7 minutes => use prime number to avoid over multiple cron run at the same time
	*
	* @return mixed This is the return value description
	*
	*/	
	public function actionAfterCreateUser(){
		$logId = Yii::app()->helperCron->onStartCron('Profile','AfterCreateUser');
		$this->createUsersPlan();
		
		$count = $this->buildFakePlan();
		$data = json_encode(array('builtUsers' => $count));
		Yii::app()->helperCron->onEndCron($logId, $data);
	}

// Run every 7 minutes => use prime number to avoid over multiple cron run at the same time
// */7 * * * * php public_html/protected/console.php Profile AfterCreateUser
// */7 * * * * php /usr/bin/php /home/pinkmeets/pinkmeets.com/public_html/protected/console.php Profile AfterCreateUser
// */5 * * * * php /usr/bin/php /home/pinkmeets/pinkmeets.com/public_html/protected/console.php Profile CheckInactive
}