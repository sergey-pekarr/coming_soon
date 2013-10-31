<?php
class FanMenuItemWidget extends CWidget
{	
	public $userprofile;
	public $group;
	
	public function init()
	{
		
	}
	
	public function run(){
		if($this->userprofile->getDataValue('gender') != 'F'){
			//Do nothing
		}
		else{
			$fan = FanProfile::createFanProfile();
			$status = $fan->getStatus();
			if(in_array($status, array('initial', 'canceled'))){
				$this->render('fanmenuitem', array());
			}
			else {
				$this->render('fanmenugroup', array());
			}
		}
	}
}