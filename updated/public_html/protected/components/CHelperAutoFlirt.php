<?php

class CHelperAutoFlirt
{	
	static public function microtime_float()
	{
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}
	
	static private $_onlineConfiguration = null;
	static public function getOnlineConfiguration_old(){
		if(self::$_onlineConfiguration) return self::$_onlineConfiguration;	
		$qry = "select * from autoflirt_config_online order by id";
		$items = Yii::app()->db->createCommand($qry)->queryAll();
		if(!$items) $items = array();
		$role = 'free';
		$from = 0;		
		foreach($items as &$item){
			if($item['type'] == $role){
				$item['from'] = $from;
				$from += $item['duration'];
				$item['to'] = $from;
			}
		}
		$role = 'gold';
		$from = 0;		
		foreach($items as &$item){
			if($item['type'] == $role){
				$item['from'] = $from;
				$from += $item['duration'];
				$item['to'] = $from;
			}
		}
		self::$_onlineConfiguration = $items;
		return $items;
	}
	
	static public function findOnlineConfigItem_old($configArr, $lastLogin, $now, $role='free'){
		$login = $now - strtotime($lastLogin);
		foreach($configArr as $config){
			if($config['from']<= $login && $login<=$config['to'] && $config['type'] == $role){
				return $config;
			}
		}
		return $configArr[count($configArr)-1];
	}
	
	static private function findInactive_old($items){
		foreach($items as $item){
			if($item['stop_inactive']){
				return $item['day'] + 1;
				break;
			}
		}
		return 5;
	}
	
	static private $_configuration = null;
	static public function getPlanConfiguration_old($role = 'free'){
		if(self::$_configuration) return self::$_configuration;	
		$qry = "select * from autoflirt_config where `type` = '$role' order by day";
		$items = Yii::app()->db->createCommand($qry)->queryAll();
		if(!$items) $items = array();
		self::$_configuration = $items;
		return $items;
		
		//return array(
		//	array('messages'=>2, 'winks'=>6, 'photorequest'=>3, 'view'=> 10),
		//	array('messages'=>2, 'winks'=>2, 'photorequest'=>2, 'view'=> 7),
		//	array('messages'=>2, 'winks'=>4, 'photorequest'=>0, 'view'=> 8),
		//	array('messages'=>1, 'winks'=>3, 'photorequest'=>2, 'view'=> 6),
		//	array('messages'=>0, 'winks'=>2, 'photorequest'=>1, 'view'=> 7),
		//	array('messages'=>2, 'winks'=>1, 'photorequest'=>1, 'view'=> 8),
		//	array('messages'=>0, 'winks'=>0, 'photorequest'=>2, 'view'=> 2),
		//	array('messages'=>0, 'winks'=>3, 'photorequest'=>3, 'view'=> 7),
		//	array('messages'=>1, 'winks'=>0, 'photorequest'=>1, 'view'=> 5),
		//	array('messages'=>1, 'winks'=>1, 'photorequest'=>0, 'view'=> 3),
		//	array('messages'=>1, 'winks'=>2, 'photorequest'=>2, 'view'=> 6),);
	}
	
	static private $_onlineConfig = null;
	static public function getOnlineConfigItem($lastLogin, $now, $role='free'){
		if(self::$_onlineConfig == null){
			$qry = "select `type`, data from autoflirt_config where `type` in ('onlinefree', 'onlinegold')";
			$rows = Yii::app()->db->createCommand($qry)->queryAll();
			
			$free = array(array('from'=>0, 'to'=>600, 'messages'=>0, 'winks'=>0, 'photorequest'=>0, 'view'=> 0));
			$gold = array(array('from'=>0, 'to'=>600, 'messages'=>0, 'winks'=>0, 'photorequest'=>0, 'view'=> 0));
			
			if($rows){
				foreach($rows as $row){
					$data = $row['data'];
					$obj = get_object_vars(json_decode(stripslashes($data)));
					if(!isset($obj['items'])) continue;
					$items = array();
					$to = 0;
					foreach($obj['items'] as $item){
						$item = get_object_vars($item);
						$item['from'] = $to;
						$to += $item['duration'];
						$item['to'] = $to;
						$items[] = $item;
					}				
					if($row['type'] == 'onlinefree'){
						$free = $items;
					}
					else{
						$gold = $items;
					}
				}
			}
			self::$_onlineConfig = array('free' => $free, 'gold'=>$gold);
		}
		$configArr = self::$_onlineConfig[$role];		
		$login = $now - strtotime($lastLogin);
		foreach($configArr as $config){
			if($config['from']<= $login && $login<=$config['to']){
				return $config;
			}
		}
		return $configArr[count($configArr)-1];
	}
	
	static private $_offlineConfig = array();
	static public function getPlanConfiguration($role = 'free'){
		if(!isset(self::$_offlineConfig[$role])){
			$qry = "select data from autoflirt_config where `type` = '$role'";
			$data = Yii::app()->db->createCommand($qry)->queryScalar();
			$obj = array();
			try{
				$obj = get_object_vars(json_decode(stripslashes($data)));
			}
			catch(exception $ex){
			}
			self::$_offlineConfig[$role]['inactive'] = 5;
			if(isset($obj['inactive'])) self::$_offlineConfig[$role]['inactive'] = $obj['inactive'];
			self::$_offlineConfig[$role]['items'] = array();
			if(isset($obj['items'])){
				foreach($obj['items'] as $item){
					$item = get_object_vars($item);
					foreach($item['subItems'] as &$subItem){
						$subItem = get_object_vars($subItem);
					}
					self::$_offlineConfig[$role]['items'][] = $item;
				}
			}
		}
		return self::$_offlineConfig[$role];	
	}
	
	static public function getAutoMsg(){
		$qry = "select * from automsg";
		return Yii::app()->db->createCommand($qry)->queryAll();
	}
	
	static public function getFakeUsers($userid, $gender, $lookingGender, $country, $lat, $lon, $start = 0, $count = 200){
		$lookingGender = str_split($lookingGender);
		if(count($lookingGender) ==1) $lookingtext = "u.gender = '{$lookingGender[0]}'";
		else $lookingtext = "u.gender in ('".implode("','",$lookingGender)."')";
		
		$qry = "select u.id as user_id
				from users as u
				inner join users_location as l on l.user_id = u.id and u.promo = '1'
				where l.country = '$country' and $lookingtext and ".CHelperProfile::whereLocation($lat, $lon, 100, 'l')
			."limit $start, $count";
		$items = Yii::app()->db->createCommand($qry)->queryColumn();
		return $items;
	}
	
	//Will be called for the first time user login loginCount = 1 ??: < 0.1 seconds/users
	static public function buildFakelistOnline($userid, $gender, $lookingGender, $country, $lat, $lon){
		
		$time_start = self::microtime_float();
		$start = 0;
		$count = 300;
		$items = self::getFakeUsers($userid, $gender, $lookingGender, $country, $lat, $lon, $start, $count);
		if(!$items) $items = array();
		shuffle($items);
		$items = array_slice($items,0,140);
		$textids = implode(',', $items);
		$qry = "insert into users_fakeplan_online(user_id, fakeids, idposition) value($userid, '$textids', 0)
				on duplicate key update fakeids = '$textids', idposition=0;";
		Yii::app()->db->createCommand($qry)->execute();
		
		$time_end = self::microtime_float();
		
		$time = $time_end - $time_start;
		
		echo "\r\nTime: ".$time;
	}
	
	/**
	 * This is method buildTimePlanForFakePlan
	 *
	 * @param mixed $joined This is a description
	 * @param mixed $role This is a description
	 * @return mixed This is the return value description
	 *
	 */	
	static public function buildTimePlanForFakePlan($joined, $role = 'free', $objConfig = null){
		
		if($objConfig == null){
			$objConfig = self::getPlanConfiguration($role);
		}
		$inactive = $objConfig['inactive'];
		$configs = $objConfig['items'];
		
		if($inactive < 3) $inactive = 3;
		if($inactive>100) $inactive = 100;
		
		$temps = array();
		$now = time();
		for($day = 0;$day < count($configs); $day ++){			
			$configDay = $configs[$day];
			$fromTime = 0;
			foreach($configDay['subItems'] as $config){
				if(!$config['duration']) continue;
				$toTime = $fromTime + $config['duration'];
				foreach($config as $type=>$count){
					if($type == 'messages' || $type == 'winks' || $type=='photorequest' || $type == 'view'){
						for($tempi =0;$tempi<$count;$tempi++){
							$timetosend = $joined + $day * 24*3600 + rand($fromTime, $toTime);
							//dont create item older than this current time
							if($timetosend>$now){
								$temps[] = array('type'=>$type, 'timetosend' => $timetosend);
							}
						}
					}
				}
				$fromTime = $toTime;
			}
		}
		
		//Sort
		$times = array();
		foreach($temps as $temp){
			$times[] = $temp['timetosend'];
		}
		array_multisort($times, SORT_ASC, $temps);
		return $temps;
	}
	
	//called by cron ProfileCommand.actionAfterUserCreate: 0.1 seconds
	static public function buildFakePlan($userid, $gender, $lookingGender, $country, $lat, $lon, $joined, $role = 'free', $start = 0, $count = 500, $fangirl = false){
		
		$time_start = self::microtime_float();
		
		$joined = strtotime($joined);
		
		$items = self::getFakeUsers($userid, $gender, $lookingGender, $country, $lat, $lon, $start, $count);
		if(!$items) $items = array();
		
		if($role == 'justjoined') $role = 'free';
		if($role != 'free' && $role != 'gold') return;
		
		if(count($items) == 0) return;
		
		//When we delete old item, rebuild new list -> different items
		shuffle($items);
		
		$objConfig = self::getPlanConfiguration($role);
		$inactive = $objConfig['inactive'];
		
		$temps = self::buildTimePlanForFakePlan($joined, $role, $objConfig);
		
		
		//Create insert
		//$insArr = array("delete from users_fakeplan where user_id = $userid");
		$insArr = array("delete from users_fakeplan where user_id = $userid;
						insert into users_fakeplan(user_id, fakeid, `type`, timetosend, `subject`, `content`, `enable`)
						values ");
		$i = 0;
		$autoMsg = self::getAutoMsg();
		if(!$autoMsg) $autoMsg = array();
		
		//n: 2012-07-30
		$next_plan_time = '';
		$all_plan_time = '';
		$flirtandphoto = 1; //Start at 1 for welcome
		
		foreach($temps as $temp){
			if($i<count($items)){
				$item = $items[$i];
				$subject = '';
				$content = '';
				$temp['timetosend'] = date('Y-m-d H:i:s', $temp['timetosend']);
				if($temp['type'] == 'messages' && count($autoMsg) > 0){
					$msg = $autoMsg[rand(0, count($autoMsg) -1)];
					$subject = addslashes($msg['subject']);
					$content = addslashes($msg['message']);
				}
				else if(count($autoMsg) ==0){
					break;
				}
				
				if($flirtandphoto >= $inactive){
					$enable = 0;
				}
				else {
					$enable = 1;
				}
				
				$insArr[] = (count($insArr) ==1?'':",\r\n").
					" ($userid, {$item}, '{$temp['type']}', '{$temp['timetosend']}', '$subject', '$content', $enable)";
				$i++;
				if($temp['type'] == 'messages' || $temp['type'] == 'photorequest'){
					
					//n: 2012-07-30	
					if($flirtandphoto % $inactive == 0){
						if($next_plan_time == ''){
							$next_plan_time = $temp['timetosend'];
						}
						else{
							$all_plan_time .= ($all_plan_time == ''?'':',').$temp['timetosend'];
						}
					}
					$flirtandphoto++;
				}
			}
			else{
				break;
			}
		}
		
		if(count($insArr) >1){
			$qry = implode("", $insArr);
			
			//n: 2012-07-30
			$nowString = date('Y-m-d H:i:s');
			if($next_plan_time == '') $next_plan_time = '0000-00-00 00:00:00';
			$qry .= ";
					Update users_plan 
					set plan = '1', pre_plan_time = '$nowString', next_plan_time= '$next_plan_time', all_plan_time = '$all_plan_time'
					where user_id = $userid;";
			
			$ok = Yii::app()->db->createCommand($qry)->execute();	
		}	
		
		$time_end = self::microtime_float();
		
		$time = $time_end - $time_start;
	}
}

/*
n 2012-07-27:
+ admin pages for AutoFlirt config
	- Free, Gold
	- Online
+ We will send user welcome and 4 flirts in (photorequest, email). 
	If user dont login, we will stop to send more flirt to them (inactive)
	If user login again -> we will send more 5 flirt in (photorequest, email)
+ Add cron to check if user inactive or deactive
	- select 1000 users has just login, compare with cache and remove processed items (store 5000 item in cache)
	- select users_fakeplan where enable = true -> missing items need to re-active
+ Add cron to build new plan for gold users
*/