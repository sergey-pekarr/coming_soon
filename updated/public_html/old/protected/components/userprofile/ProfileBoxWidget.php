<?php
class ProfileBoxWidget extends CWidget
{
	public $id;
	public $imgSize='medium';
	public $infoType=11;
	public $forcePayment=true;
	public $showIcons = true;
	public $showLocation;
	public $showOnlineStatus;
	public $custom;
	public $showBlockIcon;
	
	public $class="";
	
	public function init()
	{
		if ($this->id){
			$profile = new Profile($this->id);
			$this->render('ProfileBox', array('profile'=>$profile, 'infoType'=>$this->infoType, 'forcePayment'=>$this->forcePayment,
				/*'showLocation' => $this->showLocation, 'showOnlineStatus' => $this->showOnlineStatus, 'custom' => $this->custom*/));
		}
		else{
			$profile = false;    
		}   
		
	}
}
?>
