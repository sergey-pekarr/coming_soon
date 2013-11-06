<?php

class CHelperAction
{	
	static private function renderSidebarView($id){
		$profile = new Profile($id);
		$username = $profile->getDataValue('username');
		$encid = Yii::app()->secur->encryptID($profile->getId());
		return array('username'=>$username, 
			'userid' => $encid, 
			'url' => "/profile/{$encid}", 
			'thumnail' => $profile->imgUrl('small'), 
			'location'=> $profile->getLocationValue('city'), 
			'age' => $profile->getDataValue('age'), 
			'online' => $profile->getDataValue('isOnline')
			);		
	}
	
	static public function buidImData(){
		$activity = Activity::createActivity();
		$im = $activity->getSidebarData();
		
		$youviewed = array();
		foreach($im['youviewed'] as $item){
			if(count($youviewed)<5){
				$youviewed[] = self::renderSidebarView($item['id_to']);
			}
		}
		if(count($youviewed) ==0) unset($im['youviewed']);
		else $im['youviewed'] = $youviewed;
		
		$viewed = array();
		$viewing = array();
		foreach($im['viewed'] as $item){
			$added = $item['added'];
			if(count($viewing)<5 && !$im['viewing'] && (time() - strtotime($item['added'])<60)){
				$viewing[] = self::renderSidebarView($item['id_from']);
			}
			else if(count($viewed)<5){
				$viewed[] = self::renderSidebarView($item['id_from']);
			}
		}
		if(count($viewed) ==0) {
			//unset($im['viewed']);
			$im['viewed'] = array();
		}
		else $im['viewed'] = $viewed;
		
		if(count($viewing) ==0){
			//unset($im['viewing']); //undefine -> cause unchange
			$im['viewing'] = array();
		}
		else $im['viewing'] = $viewing;
		
		if($im['alert'] 
			&& isset($im['alert']['added']) && (time() - strtotime($im['alert']['added'])<60)
		){
			$msgid = null;
			if($im['alert']['type'] == 'messages' && isset($im['alert']['id'])){
				if(isset($im['alert']['parent'])){
					$msgid = $im['alert']['parent'];
				}
				else {
					$msgid = $im['alert']['id'];
				}
			}
			$im['alert'] = self::renderAlertReceiveFlirt($im['alert']['id_from'], $im['alert']['type'], $msgid);
		} else{
			unset($im['alert']);
		}
		
		return $im;
	}
	
	static public function buidImData_old(){
		//Return sample data to test
		//Will complete this method later
		$imdata = array(
			'activity' => array('Email'=>0, 'Wink'=>5, 'View' => 10, 'Favorite' => 0, 'PhotoRequest'=>0, 'Like' => '51+'),
			'viewing' => array(array('username'=>rand(1000,1000000), 'userid' => 'encid', 'url' => '/profile/viewprofile', 'thumnail' => '/images/design/nophoto_male_small.jpg', 'location'=>'Brooklyn', 'age' => '22', 'online' => true),),
			'viewed' => array(array('username'=>rand(1000,1000000), 'userid' => 'encid', 'url' => '/profile/viewprofile', 'thumnail' => '/images/design/nophoto_male_small.jpg', 'location'=>'Brooklyn', 'age' => '22', 'online' => rand(0,1)),
					array('username'=>rand(1000,1000000), 'userid' => 'encid', 'url' => '/profile/viewprofile', 'thumnail' => '/images/design/nophoto_male_small.jpg', 'location'=>'Brooklyn', 'age' => '22', 'online' => rand(0,1)),
					array('username'=>rand(1000,1000000), 'userid' => 'encid', 'url' => '/profile/viewprofile', 'thumnail' => '/images/design/nophoto_male_small.jpg', 'location'=>'Brooklyn', 'age' => '22', 'online' => rand(0,1)),
					array('username'=>rand(1000,1000000), 'userid' => 'encid', 'url' => '/profile/viewprofile', 'thumnail' => '/images/design/nophoto_male_small.jpg', 'location'=>'Brooklyn', 'age' => '22', 'online' => rand(0,1)),
					array('username'=>rand(1000,1000000), 'userid' => 'encid', 'url' => '/profile/viewprofile', 'thumnail' => '/images/design/nophoto_male_small.jpg', 'location'=>'Brooklyn', 'age' => '22', 'online' => rand(0,1)),
					),
				'youviewed' => array(array('username'=>rand(1000,1000000), 'userid' => 'encid', 'url' => '/profile/viewprofile', 'thumnail' => '/images/design/nophoto_male_small.jpg', 'location'=>'Brooklyn', 'age' => '22', 'online' => rand(0,1)),
					array('username'=>rand(1000,1000000), 'userid' => 'encid', 'url' => '/profile/viewprofile', 'thumnail' => '/images/design/nophoto_male_small.jpg', 'location'=>'Brooklyn', 'age' => '22', 'online' => rand(0,1)),
					array('username'=>rand(1000,1000000), 'userid' => 'encid', 'url' => '/profile/viewprofile', 'thumnail' => '/images/design/nophoto_male_small.jpg', 'location'=>'Brooklyn', 'age' => '22', 'online' => rand(0,1)),
					array('username'=>rand(1000,1000000), 'userid' => 'encid', 'url' => '/profile/viewprofile', 'thumnail' => '/images/design/nophoto_male_small.jpg', 'location'=>'Brooklyn', 'age' => '22', 'online' => rand(0,1)),
					array('username'=>rand(1000,1000000), 'userid' => 'encid', 'url' => '/profile/viewprofile', 'thumnail' => '/images/design/nophoto_male_small.jpg', 'location'=>'Brooklyn', 'age' => '22', 'online' => rand(0,1)),
					),
				'alert' => array('title'=>'Winked', 'desc' => 'Somebody has just winked you', 'content'=>'html here '.rand(10000,100000000) ),
				);
		
		
		foreach($imdata['activity'] as &$item){
			if(rand(0,10)<5){
				$item = rand(0,60);
			}
		}
		
		if(rand(0,10)<8){
			unset($imdata['alert']);
		}
		//return array();
		return $imdata;
	}
	
	static public function renderAlertSentFlirt($id_to, $type, $objid = null){
		$profile = new Profile($id_to);
		
		$username = $profile->getDataValue("username");
		$title = 'Notification';
		$des = '';
		$picid = null;
		$msgid = null;
		
		switch($type){
			case 'winks':
				$des = "You have winked $username";
				break;
			case 'messages';
				$msgid = $objid;
				$des = "You have emailed $username";
				break;
			case 'favourite';
				$des = "You have added $username to your favourites";
				break;
			
			case 'like';
				$des = "You have liked $username's photo";
				$picid = $objid;
				break;
			
			case 'gift';
				$des = "You have sent $username a $objid";
				break;
			
			case 'photorequest';
				$des = "You have requested photos of $username";
				break;
			case 'block':
				$des = "You have blocked $username";
				break;
			case 'unblock':
				$des = "You have unblocked $username";
				break;
			case 'wasblock':
				$gender = $profile->getDataValue('gender') == 'M'?'him':'her';
				$des = "$username blocked you to send $gender any message";
				break;
			default;
				return false;
		}		
		
		$result = array('title'=>$title, 'desc' => $des,
			'content' => self::renderAlertContent($profile, false, $msgid, $picid)
			);
		return $result;
	}
	
	static public function renderAlertReceiveFlirt($id_from, $type, $msgid = null){		
		$profile = new Profile($id_from);
		
		$username = $profile->getDataValue("username");
		$title = '';
		$des = '';
		
		switch($type){
			case 'winks':
				$title = 'Winked';
				$des = "$username have just winked you";
				$msgid = null;
				break;
			case 'messages';
				$title = 'Emailed';
				$des = "$username has just emailed you";
				break;
			case 'photorequest';
				$title = 'Photo-Request';
				$des = "$username have requested your photos";
				break;
			case 'view';
				$title = 'Viewed';
				$des = "$username is currently viewing your profile";
				$msgid = null;
				break;
			default;
				return false;
		}		
		
		$result = array('title'=>$title, 'desc' => $des,
			'content' => self::renderAlertContent($profile, true, $msgid)
			);
		return $result;
	}
	
	static public function renderAlertContent($profile, $showbtn = true, $emailid = null, $picid = null){
		$encid = Yii::app()->secur->encryptID($profile->getId());
		$img = $profile->imgUrl();
		if($picid) $img = $profile->imgUrl('medium', $picid, false);
		$u1 = CHelperProfile::showProfileInfoSimple($profile,13);
		$u2 = CHelperProfile::showProfileInfoSimple($profile,7);
		$btnLink = $emailid?"/thread/$encid/$emailid":"/profile/$encid";
		$btnText = $emailid?"View Message":"View Profile";
		
		//$btnWidth = ($emailid?90:70).'px';
		$btnWidth = 'auto';
		
		//$imgSmall = $profile->imgUrl('small', $picid, false);
		//$imgBig = $profile->imgUrl('big', $picid, false);
		//$imgMediumTrue = $profile->imgUrl('medium', $picid, true);
		//$test = "<!--medium: $img; small: $imgSmall; big: $imgBig; mediumTrue: $imgMediumTrue -->";
		$test = '';
		return
		"<div style='cursor: pointer; margin-top:10px;' class='notify-profile' onclick='window.location=\"/profile/$encid\"'>
			<img src='$img' width='82' style='float:left'> 
			<div style='float:left; padding-top:10px; padding-left:10px; width:105px'>$u1<br>$u2</div>
			<div class='clear'></div>
			$test
		</div>"
			.($showbtn?
				"<a style='width:{$btnWidth}; margin-right:30px;' class='green-continue content_button editbutton' href='$btnLink'>
			$btnText
			<span><img class='iconForward' alt='' src='/images/img/blank.gif'></span>			
		</a>" : "");
	}
}