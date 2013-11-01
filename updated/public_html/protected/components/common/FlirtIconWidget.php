<?php
class FlirtIconWidget extends CWidget
{
	//arry(type=>'', action=>'')
	//Email
	//Wink, Favourite, Block, Report
	//Full Actions: //'Wink','Email','Favourite','Block','Report', 'Request-Photos', 'Gift', 'View', 'Like', 'EmailSent'
	public $types;
	public $profileid;
	private $icons;
	private $encryptid;
	
	public function init()
	{
		$this->icons = array();
		$this->encryptid = Yii::app()->secur->encryptID($this->profileid);
		if($this->types){
			foreach($this->types as $type){
				if(is_array($type)){
					if(isset($type['type'])){
						$action = isset($type['action'])?$type['action']:null;
						$this->addIcons($type['type'], $action);
					}
				} else {
					$this->addIcons($type);
				}
			}
		}		
	}
	
	public function run(){
		$this->render('icons', array('icons'=> $this->icons, 'profileid' => $this->encryptid));
	}
	
	public function getIcons(){
		return $icons;
	}
	
	public function addIcons($type, $action=null){
		if(in_array($type, array('Email')) && $action == null){
			throw new Exception('Email\'s action must not be empty');		
		}
		if(!in_array($type, array('Wink','Email','Favourite','Block','Report'))){
			throw new Exception('Unsupport icon\'s type ' + $type);
		}
		//$isfree = Yii::app()->user->checkAccess('free');
		//if($isfree && in_array($type, array('Email', 'Favourite'))){
		//	$action="doRequestMember('$type', '{$this->encryptid}')";
		//}
		$this->icons[] = array('type'=>$type, 'action'=>$action);
		return $this;
	}
}
?>