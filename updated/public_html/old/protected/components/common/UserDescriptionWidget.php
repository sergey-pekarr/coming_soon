<?php

/**
 * force new uers on PC to make an introduction thread. When a new user signs up he must introduce himself and start a thread doing that or he will never get approved.
 *
 */
class UserDescriptionWidget extends CWidget
{	
	public $userprofile;
	
	public function init()
	{
	}
	
	public function run(){
		/*
		//ALTER TABLE `users_settings` ADD COLUMN `status` ENUM('justjoined','approved') NOT NULL DEFAULT 'justjoined'  AFTER `moodstatus` ;		//Update users_settings set `status` = 'approved';		$status = $this->userprofile->getSettingsValue('status');		if($status == 'justjoined' || $status == '' || $status == null){
			$text = $this->userprofile->getPersonalValue('character');
			if(CHelperProfile::countWords($text)<20){
				$this->render('userdescriptionpopup', array('userprofile' => $this->userprofile));
			}
			else{
				$this->userprofile->settingsUpdate('status', 'approved');
			}
		}
		//Test only: $this->userprofile->settingsUpdate('status', 'justjoined');
		*/
		
		$text = $this->userprofile->getPersonalValue('character');
		$joined = $this->userprofile->getActivityValue('joined');
		if(CHelperProfile::countWords($text)<20 && $joined > '2012-09-12 12:00:00'){
			$this->render('userdescriptionpopup', array('userprofile' => $this->userprofile));
		}
		
	}
}