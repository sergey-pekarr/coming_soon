<?php

/**
 * Is used in test phase only. Autoflirt features are run by AutoFlirtCommand!
 *
 */
class AutoFlirtController extends Controller
{
	private $timeStart; 
	private $timeMax=55;
	private $usleepDelay;
	private $period = 60; //Cron period to run OnlineFlirt
	private $maxSearchPosition = 3000;
	private $onlineValid = 120; //seconds: Only check user has last activity in $onlineValid
	
	//second: Only consider Plan Item which designed to send in $autoFlirtValid -> now()
	//Reason: When the sytem down for an hour or a day. When system run again, system will not be over loaded
	private $autoFlirtValid = 700; //600-700 is ok, select 700 so we have 100 reserve for late task
	
	public function init() 
	{ 
		if (SITE_UNDER_UPDATE) 
			Yii::app()->end();
		
		$this->usleepDelay = (LIVE) ? 50 : 1000;
		
		$this->timeStart = time(); 
	}
	
	private function checkFakeUserOnline($onlineIds){
		foreach($onlineIds as $onlineId){
			$profileKey = "profile_".$onlineId;
			$cache = Yii::app()->cache->get($profileKey);
			/*
				Note: Less than 10% users are online.
				 + If we create Profile for every id -> N get cache and 0.9NM queries
				 + If we check cache exist before create Profile -> 1,1N get cache
				=> must faster than
			*/
			if($cache != null){
				$profile = new Profile($onlineId);
				$profile->setActivity();
			}
		}
	}
	
	private function getColumn($table, $field){
		$result = array();
		foreach($table as $record){
			$result[] = $record[$field];
		}
		return $result;
	}
	
	private function checkFanGirl(&$users){
		$ids = array();
		foreach($users as &$user){
			if($user['gender'] == 'F'){
				$ids[] = $user['user_id'];
			}
			$user['fangirl'] = false;
		}
		if(count($ids)>0){
			$qry = "select * from users_fan_settings where `status` in ('pending', 'approved') and user_id in ("
				.implode(',', $ids).")";
			$fangirls = Yii::app()->db->createCommand($qry)->queryAll();
			foreach($users as &$user){
				if($user['gender'] == 'F'){
					$fangirl = $this->getRecord($fangirls,'user_id', $user['user_id']);
					if($fangirl != null){
						$user['fangirl'] = true;						
					}
				}
			}
		}
	}
	
	private function checkSendingUser(&$sending, &$sendingIds){
		$sendingIds = array();
		$sending = array();
		
		$lastTime = date('Y-m-d H:i:s', time() - $this->onlineValid); //test 3600, live 60 
		//Only consider users have activity in the last minute
		//Only consider normal signup users
		$qry = "select user_id, loginLast, u.role, u.gender
				from users_activity as a
				inner join users as u on u.id = a.user_id and u.promo = '0' and u.form = '1'
				where activityLast > '$lastTime' and u.gender = 'M'";
		//Last changed on Oct 1st (***): Only autoflirt to Men!
		$onlineAct = Yii::app()->db->createCommand($qry)->queryAll(); 
		//Need improve when we have more than 1000 real users online. 
		//For e.g where activityLast > $lastTime and rand() < 0.5
		
		//Oct 1st: 
		//$this->checkFanGirl($onlineAct); //No need because of last changed (***)
		
		if(!$onlineAct) return array();
		$sendingPhotoIds = array();
		$now = time();
		//$configArr = CHelperAutoFlirt::getOnlineConfiguration();
		foreach($onlineAct as $item){
			$role = $item['role'];
			if($role == 'justjoined') $role = 'free';	
			if($role == 'deleted') continue;	
			if($role != 'free' && $role != 'gold') continue;
			
			//$config = CHelperAutoFlirt::findOnlineConfigItem($configArr, $item['loginLast'], $now, $role);
			$config = CHelperAutoFlirt::getOnlineConfigItem($item['loginLast'], $now, $role);
			
			//n Oct 1st: do not sent to fangirl
			//if($item['fangirl'] === true){ //No need because of last changed (***)
			//	$config['messages'] = 0;
			//}
			
			$total = $config['view']+$config['photorequest']+$config['winks']+$config['messages'];
			
			//Note: ($config['to'] - $config['from'])/$total mean everate seconds per a flirt, assume > 50;
			$willSend = (rand(0, ($config['to'] - $config['from'])/$total) <= $this->period);
			if($willSend){
				$x = rand(0, $total*1000)/1000; //randome value from 0->$total
				$type='view';
				$accumulate = $total;
				if($x<$accumulate){
					$type='view';
				}
				$accumulate -= $config['view'];
				if($x<$accumulate){
					$type='winks';
				}
				$accumulate -= $config['winks'];
				if($x<$accumulate){
					$type='messages';
				}
				$accumulate -= $config['messages'];
				if($x<$accumulate){
					$type='photorequest';
					$sendingPhotoIds[] = $item['user_id'];
				}
				
				//Oct 1st: Dont sent message to fangirl
				//if($item['fangirl'] === true && $type == 'message'){  //No need because of last changed (***)
				//	continue;
				//}
				
				$sendingIds[] = $item['user_id'];
				$sending[$item['user_id']] = array('user_id' => $item['user_id'], 'type' => $type, 'photo'=>0, 'fakeid' => null, 'check' => true);
			}
		}
		
		if(count($sendingPhotoIds)>0){	
			$textids = implode(',',$sendingPhotoIds);		
			$photoqry = "select id, pics
					from users as u
					where id in ($textids)";
			$photos = Yii::app()->db->createCommand($photoqry)->queryAll();
			if($photos && count($photos)>0){
				foreach($photos as $photo){
					$sending[$photo['id']]['photo'] = $photo['pics'];
					//if($photo['pics'] == 0){
					//	$sending[$photo['id']]['check'] = false;
					//}
				}
			}
		}
	}
	
	private function processFakeUsers($textids, &$sending){		
		$sql = "select user_id, fakeids, idposition from users_fakeplan_online where user_id IN ($textids)";
		$fakes = Yii::app()->db->createCommand($sql)->queryAll();
		
		if(!$fakes) return;			
		foreach($fakes as $item){
			$userid = $item['user_id'];
			
			$fakeids = $item['fakeids'];
			$fakeArr = explode(',',$fakeids);
			
			if(count($fakeArr) == 0){
				$sending[$userid]['fakeid'] = false;
				continue;
			}			
			if($item['idposition']>=count($fakeArr) && count($fakeArr)<50){
				$sending[$userid]['fakeid'] = false;
				continue;
			}
			
			if($item['idposition']>=count($fakeArr)){
				$sending[$userid]['fakeid'] = null;
				continue; //Cause rebuild fakelist
			}
			
			$sending[$userid]['fakeid'] = $fakeArr[$item['idposition']%count($fakeArr)];			
		}
		//$sending[$userid]['fakeid'] === null -> record dont exist -> need to build fake list
		$sql = "update users_fakeplan_online set idposition = idposition +1 where user_id IN ($textids)";
		Yii::app()->db->createCommand($sql)->execute();
	}
	
	private function checkEmptyFakelistOnline($sending){
		$emtylist = array();
		foreach($sending as $user_id => $item){
			if($item['fakeid'] === null){
				$emtylist[] = $user_id;
			}
		}
		if(count($emtylist)){
			$inforQry = "select u.id as user_id, u.gender, u.looking_for_gender, l.country, l.latitude, l.longitude
					from users as u
					inner join users_location as l on l.user_id = u.id
					where u.id in (".implode(',', $emtylist).")";
			$inforItems = Yii::app()->db->createCommand($inforQry)->queryAll();
			if($inforItems && count($inforItems)>0){
				foreach($inforItems as $infor){
					CHelperAutoFlirt::buildFakelistOnline($infor['user_id'],$infor['gender'],$infor['looking_for_gender'],
						$infor['country'],$infor['latitude'],$infor['longitude']);
				}
			}
		}
	}
	
	/**
	 * This method will modify $sending to added subject and content to email item!
	 *
	 * @param mixed $sending This is a description
	 * @return mixed This is the return value description
	 *
	 */	
	private function sendOnlineFlirt(&$sending){
		
		//This is fake flirt, so no need to insert into profile_viewed
		
		$onlineIds = array();
		$viewItems = array();
		$messageItems = array();
		$photoItems = array();
		$winkItems = array();
		$newIds = array();
		$now = date('Y-m-d H:i:s');
		$autoMsg = CHelperAutoFlirt::getAutoMsg();
		foreach($sending as $user_id => &$item){
			if($item['fakeid']){
				$onlineIds[] = $item['fakeid'];
				$viewItems[] = "({$item['user_id']}, {$item['fakeid']}, '$now', '0', 1)";
				if($item['type'] == 'winks'){
					$winkItems[] = "({$item['user_id']}, {$item['fakeid']}, '$now', '0', 1)";
				}
				if($item['type'] == 'photorequest' && $item['photo'] > 0){
					$photoItems[] = "({$item['user_id']}, {$item['fakeid']}, '$now', '0', 1)";
				}
				if($item['type'] == 'messages'){
					if(count($autoMsg) > 0){
						$msg = $autoMsg[rand(0, count($autoMsg) -1)];
						$subject = addslashes($msg['subject']);
						$content = addslashes($msg['message']);
						$item['subject'] = $subject;
						$item['content'] = $content;
						$messageItems[] = "({$item['user_id']}, {$item['fakeid']}, '$now', '0', '$subject', '$content')";
					}
				}
			}			
		}
		
		if(count($onlineIds)>0){
			$onlineQry = "update users_activity set activityLast = '$now' where user_id in (".implode(',', $onlineIds).")";
			$ok = Yii::app()->db->createCommand($onlineQry)->execute();
			
			//			foreach($onlineIds as $onlineId){
			//				$profileKey = "profile_".$onlineId;
			//				$cache = Yii::app()->cache->get($profileKey);
			//				/*
			//					Note: Less than 10% users are online.
			//					 + If we create Profile for every id -> N get cache and 0.9NM queries
			//					 + If we check cache exist before create Profile -> 1,1N get cache
			//					=> must faster than
			//				*/
			//				if($cache != null){
			//					$profile = new Profile($onlineId);
			//					$profile->setActivity();
			//				}
			//			}
			$this->checkFakeUserOnline($onlineIds);
		}
		
		if(count($viewItems)>0){
			$viewQry = "insert into profile_view(id_to, id_from, added, `read`, `count`)
						values ".implode(',', $viewItems).
				" on duplicate key update count = count+1, added = '$now'";
			$ok = Yii::app()->db->createCommand($viewQry)->execute();
		}
		if(count($winkItems)>0){
			$winkQry = "insert into profile_winks(id_to, id_from, added, `read`, `count`)
						values ".implode(',', $winkItems).
				" on duplicate key update count = count+1, added = '$now'";
			$ok = Yii::app()->db->createCommand($winkQry)->execute();
		}
		if(count($photoItems)>0){
			$photoQry = "insert into profile_photorequest(id_to, id_from, added, `read`, `count`)
						values ".implode(',', $photoItems).
				" on duplicate key update count = count+1, added = '$now'";
			$ok = Yii::app()->db->createCommand($photoQry)->execute();
		}
		if(count($messageItems)>0){
			$messageQry = "insert into profile_messages(id_to, id_from, added, `read`, `subject`, `text`)
						values ".implode(',', $messageItems);
			$ok = Yii::app()->db->createCommand($messageQry)->execute();
			
			$id = Yii::app()->db->getLastInsertId('profile_messages');
			//Correct id
			$startid = $id-count($messageItems) + 1;			
			foreach($sending as $user_id => &$item){
				if($item['type'] == 'messages'){
					$item['id'] = $startid;
					$startid++;
				}
			}
		}
	}
	
	private function setTargetCache($sending){
		foreach($sending as $user_id => $item){
			if($item['fakeid']){
				//Simple method -> fast but has problem. Notification does not happen!
				//$mkey = 'activity_'.$user_id;				
				//Yii::app()->cache->delete($mkey);
				
				//Advance method: change cache value
				$act = Activity::createActivity($user_id);
				$type = $item['type'];
				if($type == 'messages'){
					$obj = array('type' => 'messages', 'id' => isset($item['id'])?$item['id']:null,
						'id_from' => $item['fakeid'], 'id_to' => $user_id, 
						'added' => date('Y-m-d H:i:s'), 'read' => 0, 
						'subject' => $item['subject'], 'text' => $item['content'], 
						'parent' => null  );
				}
				else {
					$obj = array('type'=>$type, 'id_from' => $item['fakeid'], 'id_to' => $user_id, 
						'added' => date('Y-m-d H:i:s'), 'read' => 0, 'count' => 1   );
				}
				
				if($type != 'view'){
					$viewObj = array('type'=>'view', 'id_from' => $item['fakeid'], 'id_to' => $user_id, 
						'added' => date('Y-m-d H:i:s'), 'read' => 0, 'count' => 1   );
					
					$act->updateActivityCache('view', $viewObj);
				}
				
				$act->updateActivityCache($type, $obj);
			}
		}
	}
	
	public function actionTest1(){
		//CHelperAutoFlirt::buildFakelistOnline(8,'M','F','US',41.439720, -81.735558);
		CHelperAutoFlirt::buildFakePlan(8,'M','F','US',41.439720, -81.735558, '2012-07-24 16:00:00');
	}
	
	/*
	When the site has 1000 real users online. Each minute we might have to send ~ 200 flirts > 600 query.
	Test: On dev, set all 31 real users online, configure to send every user a flirt
	Result: 0.1->0.2 seconds with simple cache method, and without send email
	Advance cache will be improve later
	*/
	
	/**
	* Run every 1 minutes
		*
	* @return mixed This is the return value description
		*
	*/	
	
	public function actionOnlineFlirt(){
		
		$logId = Yii::app()->helperCron->onStartCron('AutoFlirt','OnlineFlirt');
		
		$time_start = CHelperAutoFlirt::microtime_float();
		
		
		//Yii::app()->helperCron->onEndCron($logId, 'checkSendingUser');
		$this->checkSendingUser($sending, $sendingIds);
		
		if(count($sendingIds) ==0){
			Yii::app()->helperCron->onEndCron($logId, count($sending));
			return;
		}
		
		$textids = implode(',',$sendingIds);		
		
		//Yii::app()->helperCron->onEndCron($logId, 'processFakeUsers');
		$this->processFakeUsers($textids, $sending);
		
		//Yii::app()->end();
		
		//Note: This method will modify $sending to added subject and content to email item!
		//Yii::app()->helperCron->onEndCron($logId, 'sendOnlineFlirt');
		$this->sendOnlineFlirt($sending);
		
		//Yii::app()->helperCron->onEndCron($logId, 'setTargetCache');
		$this->setTargetCache($sending);
		
		//fake_online has not created for user -> create here. 
		//Yii::app()->helperCron->onEndCron($logId, 'checkEmptyFakelistOnline');
		$this->checkEmptyFakelistOnline($sending);
		
		//Discussion: We are sending flirt to online user, that mean he will see in bottom right notification
		// => dont need to send email. We will consider to send email for offline message, or real message
		//$this->sendEmail($sending);	
		
		$time_end = CHelperAutoFlirt::microtime_float();
		
		$time = $time_end - $time_start;
		
		echo "\r\nTime: ".$time;
		
		Yii::app()->helperCron->onEndCron($logId, count($sending));
	}
	
	private function checkUserHasNoPhoto($candidates){
		$userids = array();
		foreach($candidates as $candidate){
			$userids[] = $candidate['user_id'];
		}
		if(count($userids)>0){
			$textids = implode(',',$userids);		
			$photoqry = "select id, pics
					from users as u
					where id in ($textids) and pics=0";
			$userids = Yii::app()->db->createCommand($photoqry)->queryColumn();
			if($userids && count($userids)>0){
				return implode(',',$userids);
			}
		}
		return null;
	}
	
	private function getRecord($table, $filterField, $filterValue){
		foreach($table as $record){
			if($record[$filterField] == $filterValue){
				return $record;
			}
		}
		return null;
	}
	
	private function getEmailOrPhotoColumn($candidates, $field){
		$result = array();
		foreach($candidates as $record){
			if($record['type'] == 'photorequest' || $record['type'] == 'messages'){ 
				$result[] = $record[$field];
			}
		}
		return $result;
	}
	
	private function prepareSubjectAndContent($template, &$data, &$res = null){
		//$data['body'] = '';
		//$data['subject'] = '';
		//if($res != null) $res['viewPath'] = dirname(dirname(__FILE__))."/views/mail/html/$template.php";
		//return;
		$controller = new CController('YiiMail');
		if($template == 'messages') $template = 'email';
		//Body Html   
		$viewPath = dirname(dirname(__FILE__))."/views/mail/html/$template.php";		
		$data['body'] = addslashes($controller->renderInternal($viewPath, array('data'=>$data), true));
		//if($res != null) $res['viewPath1'] = $viewPath;
		
		//Subject
		$viewPath = dirname(dirname(__FILE__))."/views/mail/subject/$template.php";
		$data['subject'] = addslashes($controller->renderInternal($viewPath, array('data'=>$data), true));
		//if($res != null) $res['viewPath2'] = $viewPath;
	}
	
	private function autoEmail($candidates, &$res = null){
		$fakeids = $this->getEmailOrPhotoColumn($candidates, 'fakeid');
		
		if(count($fakeids) == 0) return;
		if($res == null) $res = array();
		
		$fakeidsText = implode(",",$fakeids);
		$fromUsersQry = "select id as user_id, username, birthday, gender, looking_for_gender from users where id in ($fakeidsText)";
		$fromLocationsQry = "select user_id, country, state, city from users_location where user_id in ($fakeidsText)";
		$fromSettingsQry = "select user_id, ageMin, ageMax from users_settings where user_id in ($fakeidsText)";
		$fromImagesQry = "select user_id, n from user_image where `primary` = '1' and user_id in ($fakeidsText)";
		
		$userids = $this->getEmailOrPhotoColumn($candidates, 'user_id');
		$useridsText = implode(",",$userids);
		$usersQry = "select id as user_id, username, email 
					from users as u 
					inner join users_settings as s on s.user_id = u.id and s.hided_notify = '0'
					where id in ($useridsText) and u.role in ('free', 'gold') and u.form = '1' and u.promo = '0'";
		
		$db = Yii::app()->db;
		
		$fromUsers = $db->createCommand($fromUsersQry)->queryAll();
		$fromLocations = $db->createCommand($fromLocationsQry)->queryAll();
		$fromSettings = $db->createCommand($fromSettingsQry)->queryAll();
		$fromImages = $db->createCommand($fromImagesQry)->queryAll();
		
		$users = $db->createCommand($usersQry)->queryAll();
		
		if(count($users) == 0) return;
		
		$mailItems = array();
		$cmail = Yii::app()->mail;
		foreach($candidates as $item){
			if($item['type'] <> 'photorequest' && $item['type'] != 'messages'){
				continue;
			}
			
			
			$fromid = $item['fakeid'];
			$userid = $item['user_id'];
			
			$fromUser = $this->getRecord($fromUsers, 'user_id', $fromid);
			$fromLocation = $this->getRecord($fromLocations, 'user_id', $fromid);
			$fromSetting = $this->getRecord($fromSettings, 'user_id', $fromid);
			$fromImage = $this->getRecord($fromImages, 'user_id', $fromid);
			
			$user = $this->getRecord($users, 'user_id', $userid);
			
			if(!$user || !$fromUser || !$fromLocation || !$fromSetting || !isset($user['email'])) continue;
			
			$data = array();
			//Add code to build array here			
			
			$data['from_profileUrl'] = CHelperProfile::getAutoLoginUrl($userid).'?redirect='. urlencode('/profile/'.Yii::app()->secur->encryptID($userid));
			$img = '';
			if (!$fromImage && $fromUser['gender']=='F')
			{
				$img = SITE_URL . '/images/design/nophoto_female_'.'medium'.'.jpg';
			}
			else if(!$fromImage /*&& $fromUser['gender']=='F'*/)
			{
				$img = SITE_URL . '/images/design/nophoto_male_'.'medium'.'.jpg';
			}
			else {
				$img = CHelperProfile::imageCachePrepare($fromid, $fromImage['n'], 'medium');
			}
			
			
			$data['from_imgUrl'] = $img;
			$data['from_username'] = $fromUser['username'];
			$data['from_age'] = CHelperProfile::getAge($fromUser['birthday']);
			$data['from_gender_text'] = CHelperProfile::textGender($fromUser['gender']);
			$data['from_looking_for_gender_text'] = CHelperProfile::textLookGender($fromUser['looking_for_gender']);
			$loc = '';
			if(isset($fromLocation['city']) && $fromLocation['city'] != '') $loc = $fromLocation['city'];
			if(isset($fromLocation['state']) && $fromLocation['state'] != '') $loc .= ($loc==''?'':', ').$fromLocation['state'];
			if(isset($fromLocation['country']) && $fromLocation['country'] != '') $loc .= ($loc==''?'':', ').$fromLocation['country'];
			$data['from_location'] = $loc;
			$data['from_minage'] = $fromSetting['ageMin'];
			$data['from_maxage'] = $fromSetting['ageMax']; 
			
			
			
			$data['username'] = $user['username'];
			$data['photoUploadUrl'] = CHelperProfile::getAutoLoginUrl($userid).'?redirect='. urlencode( '/account' );
			
			if($item['type'] == 'messages'){
				$data['subject'] = $fromUser['username'].' just sent you a message';
				$data['messages_Url'] = CHelperProfile::getAutoLoginUrl($userid).'?redirect='. urlencode('/thread/'.Yii::app()->secur->encryptID($fromid).'/'.$item['id']);
				$data['message_subject'] = $item['subject'];
			}
			else {				
				$data['subject'] = 'Photo Request';
			}
			
			$data['autoLoginUrl'] = CHelperProfile::getAutoLoginUrl($userid);
			$data['unsubscribeUrl'] = CHelperProfile::getAutoLoginUrl($userid).'?redirect=/account';
			
			$this->prepareSubjectAndContent($item['type'], $data, $res);		
			
			$dataSerialise = addslashes(serialize($data));
			$mailItems[] = "({$userid}, '{$user['email']}', '', '{$item['type']}', '1', '$dataSerialise', '{$data['subject']}', '{$data['body']}', now())";
			//$mailItems[] = "({$userid}, '{$user['email']}', '', '{$item['type']}', '1', '', '', '', now())";
		}
		if(count($mailItems)>0){
			$mailQry = "insert into log_mail(`user_id`, `to`, `from`, `template`, `html`, `data`, `subject`, `body`, `added`)
					values ".implode(",\r\n", $mailItems);
			$ok = $db->createCommand($mailQry)->execute();
		}
	}
	
	private function checkAutoFlirtCache($candidates){
		foreach($candidates as $candidate){
			//Simple method
			//$mkey = 'activity_'.$candidate['user_id'];				
			//Yii::app()->cache->delete($mkey);
			
			//Advance method: change cache value
			$act = Activity::createActivity($candidate['user_id']);
			$type = $candidate['type'];
			if($type == 'messages'){
				$obj = array('type' => 'messages', 'id' => isset($candidate['id'])?$candidate['id']:null,
					'id_from' => $candidate['fakeid'], 'id_to' => $candidate['user_id'], 
					'added' => date('Y-m-d H:i:s'), 'read' => 0, 
					'subject' => $candidate['subject'], 'text' => $candidate['content'], 
					'parent' => null  );
			}
			else {
				$obj = array('type'=>$type, 'id_from' => $candidate['fakeid'], 'id_to' => $candidate['user_id'], 
					'added' => date('Y-m-d H:i:s'), 'read' => 0, 'count' => 1   );
			}
			
			/*
			Explain: when flirt is sent to offline user, cache data is null -> there is no update back to cache.
			processing cost: Get data from cache
			*/
			
			if($type != 'view'){
				$viewObj = array('type'=>'view', 'id_from' => $candidate['fakeid'], 'id_to' => $candidate['user_id'], 
					'added' => date('Y-m-d H:i:s'), 'read' => 0, 'count' => 1   );
				
				$act->updateActivityCache('view', $viewObj);
			}
			
			$act->updateActivityCache($type, $obj);
		}
	}
	
	/**
	* Run every 1 minutes for dev, 11 minutes for live
	*
	* @return mixed This is the return value description
	*
	*/	
	public function actionAutoFlirt(){
		
		$logId = Yii::app()->helperCron->onStartCron('AutoFlirt','AutoFlirt');
		
		$res = array();
		$time_start = CHelperAutoFlirt::microtime_float();
		
		$now = date('Y-m-d H:i:s');
		$pre10 = date('Y-m-d H:i:s', time()- $this->autoFlirtValid);
		
		try{		
			//Only send items designed to send in the last ten minutes. Delete all the others
			$canQry = "select user_id, fakeid, `type`, `subject`, `content` from users_fakeplan 
						where timetosend <= '$now' and timetosend>='$pre10' and `enable` = 1";
			$candidates = Yii::app()->db->createCommand($canQry)->queryAll($canQry);
			
			$viewQry = "insert into profile_view(id_from, id_to, added, `read`, `count`)
							select fakeid, user_id, '$now', '0', 1 
							from users_fakeplan 
							where timetosend <= '$now' and timetosend>='$pre10' and `enable` = 1 
						on duplicate key update `count` = `count` + 1, added = '$now'";
			$ok = Yii::app()->db->createCommand($viewQry)->execute();
			
			$onlineIds = $this->getColumn($candidates, 'fakeid');
			$this->checkFakeUserOnline($onlineIds);
			
			$winksQry = "insert into profile_winks(id_from, id_to, added, `read`, `count`)
							select fakeid, user_id, '$now', '0', 1 
							from users_fakeplan 
							where `type` = 'winks' and timetosend <= '$now' and timetosend>='$pre10' and `enable` = 1
						on duplicate key update `count` = `count` + 1, added = '$now'";
			$ok = Yii::app()->db->createCommand($winksQry)->execute();
			
			$nophotousers = $this->checkUserHasNoPhoto($candidates);
			if($nophotousers != null && $nophotousers != ''){
				$photoQry = "insert into profile_photorequest(id_from, id_to, added, `read`, `count`)
							select fakeid, user_id, '$now', '0', 1 
							from users_fakeplan 
							where `type` = 'photorequest' and timetosend <= '$now' and timetosend>='$pre10' and `enable` = 1 and user_id in ($nophotousers)
						on duplicate key update `count` = `count` + 1, added = '$now'";
				$ok = Yii::app()->db->createCommand($photoQry)->execute();
			}
			
			$messagesQry = "insert into profile_messages(id_from, id_to, added, `read`, `subject`, `text`)
							select fakeid, user_id, '$now', '0', `subject`, `content` 
							from users_fakeplan 
							where `type` = 'messages' and timetosend <= '$now' and timetosend>='$pre10' and `enable` = 1";
			$ok = Yii::app()->db->createCommand($messagesQry)->execute();
			
			//Correct id
			$msgid = Yii::app()->db->getLastInsertId('profile_messages');
			for($i = count($candidates)-1;$i>=0;$i--){
				$candidate = &$candidates[$i];
				if($candidate['type'] == 'messages'){
					$candidate['id'] = $msgid;
					$msgid--;
				}
			}
			
			$this->checkAutoFlirtCache($candidates);
			$res['count'] = count($candidates);
			
			$this->autoEmail($candidates, $res);
			
		} 
		catch(exception $ex){
			$res['errMessage'] = $ex->getMessage();
			//$res['getTrace'] = $ex->getTrace();
			$res['getFile'] = $ex->getFile();
			$res['getLine'] = $ex->getLine();
		}
		
		//if(!LOCAL_OK){ //Dont delete so we can reuse to test
		$delQry = "delete from users_fakeplan where timetosend <= '$now'";
		$ok = Yii::app()->db->createCommand($delQry)->execute();
		//}
		
		$time_end = CHelperAutoFlirt::microtime_float();
		
		$time = $time_end - $time_start;
		
		echo "\r\nTime: ".$time;
		Yii::app()->helperCron->onEndCron($logId, json_encode($res));
	}
	
	public function actionBuildPlanForOld(){
		$qry = "select u.id, u.username, country, u.gender, u.looking_for_gender, u.role, a.joined, latitude, longitude
				from users as u 
				inner join users_location as l on l.user_id = u.id
				inner join users_activity as a on a.user_id = u.id
				where u.promo = '0' and u.form = '1'";
		//Only consider normal signuped users
		
		$items = Yii::app()->db->createCommand($qry)->queryAll();
		if(!$items) $items = array();
		foreach($items as $item){
			$role = $item['role'];
			if($role == 'justjoined') $role = 'free';
			
			CHelperAutoFlirt::buildFakePlan($item['id'],$item['gender'],$item['looking_for_gender'],$item['country'],
				$item['latitude'],$item['longitude'],$item['joined'],$item['role']);
		}
	}

	
	//	/**
	//	 * Run every once a day
	//	 *
	//	 * @return mixed This is the return value description
	//	 *
	//	 */	
	//	public function actionCleanOldView(){
	//		$oldest = date('Y-m-d H:i:s', time() - 3600 * 24 * 7);
	//		$qry = "delete from profile_view where added < '$oldest'";
	//		$ok = Yii::app()->db->createCommand($qry)->execute();
	//	}
	
	
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
					$user['joined'],$user['role'], 0, 500); //, $user['fangirl']
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
	
	
	public function actionDetectRiskForOldData(){
		$db = Yii::app()->db;
		$qry1 = "select max(id) from pm_today_risk";
		$maxRiskId = $db->createCommand($qry1)->queryScalar();
		if(!$maxRiskId) $maxRiskId = 0;
		
		$qry = "select id, user_id, manager_id from pm_today_gold where id > $maxRiskId order by id";
		$items = $db->createCommand($qry)->queryAll();
		if(!$items) $items = array();
		
		
		$startTime = time();
		$i = 0;				
		foreach($items as $item){
			ProfileClientInfo::updatePaymentInfo($item['user_id']);
			$qry = "insert ignore into pm_today_risk(id, user_id, reg_date) 
					values({$item['id']},{$item['user_id']}, now())";
			$ok = $db->createCommand($qry)->execute();
			//RiskDetection::detectRisk_Email($item['id'], $item['user_id']);
			//RiskDetection::detectRisk_Location($item['id'], $item['user_id']);
			RiskDetection::detectRisk_FigurePrint2($item['id'], $item['user_id'], $item['manager_id']);
			$i++;
			
			if($i>0 && $i %100 == 0){
				$elapse = date('H:i:s', time() - $startTime);
				echo " - ".number_format($i).": ok in $elapse \r\n";
			}
		}
	}
	
	public function actionTestDetectRisk(){
		$res = RiskDetection::checkRisk('s 1920x1080', 'a 1920x1040', '', '', 'p Win32', '','', 'sn 1920x1080', 'an 1920x1040', '');
	}
	
	public function actionSendRiskReport(){
		RiskDetection::checkAndSendMail();
	}
}

