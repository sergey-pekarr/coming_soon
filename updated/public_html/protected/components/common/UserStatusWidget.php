<?php
class UserStatusWidget extends CWidget
{
	public $profile;
	public $sidebar;
	
	//private $userid;
	//private $unverified;
	//private $cssClass;
	//private $moodstatus;	
	
	/*
	profileid = userid
		- sidebar
			- myunverifiedside
			- edit moodstatus
		- myprofile
			- myunverified
			- edit moodstatus 
	profileid != userid
		- profile
			- unverified
			- verified (verified and mood = 0)
			- moodstatus (verified & mood <> 0)
	*/
	
	public function init()
	{		
	}
	
	public function isVerified($profile){
		return ($profile->getSettingsValue('email_activated') 
			|| $profile->getSettingsValue('email_activated_at') != '0000-00-00 00:00:00');
	}
	
	public function run(){
		$userid = Yii::app()->user->getId();
		$profileId = $this->profile->getId();
		
		//$userid == $profileId -> only current user can edit status
		
		if($this->sidebar && $userid == $profileId){
			
			$this->profile = Yii::app()->user->Profile;
			$activated = $this->isVerified($this->profile);
			if(!$activated){
				$this->render('verify', array('verifiedclass'=> 'myunverifiedside', 'link' => '/profile/verifyemail'
							, 'text' => 'Your Profile is Unverified!'));
			}
			else {
				$mood = $this->profile->getSettingsValue('mood');
				$mText = $this->profile->getSettingsValue('moodstatus');
				$mclass = '';
				if(!$mood || $mood ==0){
					$mIcon = '/images/img/blank.gif';
					$mTitle = 'How are you feeling?';
					$mText = 'Select A Status!';
					$mclass = 'no-status';
				}
				else {
					$mIcon = "/images/img/mood/{$mood}.png";
					$mTitle = 'I\'m feeling...';
				}
				$this->render('userstatusMeetsi', array('selection' => true, 'icon' => $mIcon, 'title' => $mTitle, 
					'text' => $mText, 'class'=>$mclass));
			}			
		}
		else if($userid == $profileId){
			$this->profile = Yii::app()->user->Profile;
			$activated = $this->isVerified($this->profile);
			if(!$activated){
				$this->render('verify', array('verifiedclass'=> 'myunverified', 'link' => '/profile/verifyemail', 
					'text' => 'Your Profile is Unverified!'));
			}
			else {
				$mood = $this->profile->getSettingsValue('mood');
				$mText = $this->profile->getSettingsValue('moodstatus');
				$mclass = '';
				if(!$mood || $mood ==0){
					$mIcon = '/images/img/blank.gif';
					$mTitle = 'How are you feeling?';
					$mText = 'Select A Status!';
					$mclass = 'no-status';
				}
				else {
					$mIcon = "/images/img/mood/{$mood}.png";
					$mTitle = 'I\'m feeling...';
				}
				$this->render('userstatusMeetsi', array('selection' => true, 'icon' => $mIcon, 'title' => $mTitle, 
							'text' => $mText, 'class'=>$mclass));
			}	
		}
		else {
			$activated = $this->isVerified($this->profile);
			if(!$activated){
				$this->render('verify', array('verifiedclass'=> 'unverified', 'link' => '#', 'text' => 'Profile is Unverified!'));
			}
			else{
				$mood = $this->profile->getSettingsValue('mood');
				$mText = $this->profile->getSettingsValue('moodstatus');
				$mclass = '';
				if(!$mood || $mood == '0'){
					$this->render('verify', array('verifiedclass'=> 'verified', 'link' => '#', 'text' => 'Profile is Verified!'));
				}
				else{
					$mIcon = "/images/img/mood/{$mood}.png";
					$mTitle = 'Is feeling...';
					$this->render('userstatusMeetsi', array('selection' => false, 'icon' => $mIcon, 'title' => $mTitle, 
								'text' => $mText, 'class'=>$mclass));					
				}
			}
		}
	}
}
?>