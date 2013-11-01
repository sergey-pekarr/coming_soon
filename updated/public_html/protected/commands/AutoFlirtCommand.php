<?php
class AutoFlirtCommand extends CConsoleCommand
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
		
		//echo "\r\nTime: ".$time;
		
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
			
			$subject = $data['subject'];
			$body = $data['body'];
			
			unset($data['subject']);
			unset($data['body']);
			
			$dataSerialise = addslashes(serialize($data));
			$mailItems[] = "({$userid}, '{$user['email']}', '', '{$item['type']}', '1', '$dataSerialise', '{$subject}', '{$body}', now())";
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
		
		//echo "\r\nTime: ".$time;
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
}

/*

Configuration
	AutoFlirt_Online_Config
	AutoFlirt_Config
	
Prolem with profile n: viewed user are all offline??? DateTime problem? or cache problem??

Prolem with cache: winks flirt come -> reset cache view and winks

Cleanup 50 items

*/

// */1 * * * * php public_html/protected/console.php AutoFlirt OnlineFlirt
// */1 * * * * php public_html/protected/console.php AutoFlirt AutoFlirt


