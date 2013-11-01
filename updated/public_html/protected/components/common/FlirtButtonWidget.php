<?php
class FlirtButtonWidget extends CWidget
{
	//arry(type=>'', action=>'')
	//Email
	//'Wink','Email','Favourite','Block','Report', 'Request-Photos', 'Gift'
	//Full Actions: //'Wink','Email','Favourite','Block','Report', 'Request-Photos', 'Gift', 'View', 'Like', 'EmailSent'
	public $profileid;
	public $type;
	public $action;
	public $text;
	public $styles;
	public $attributes;
	private $encryptid;
	
	public function init()
	{	
		$this->encryptid = Yii::app()->secur->encryptID($this->profileid);
		
		if(in_array($this->type, array('Email', 'EmailSent')) && $this->action == null){
			throw new Exception('Email\'s action must not be empty');		
		}	
		if(!in_array($this->type, array('Wink','Email','Favourite','Block','Report', 'Request-Photos', 'Gift','EmailSent', 'Edit', 'Save'))){
			throw new Exception('Unsupport button\'s type ' + $this->type);
		}
		//$isfree = Yii::app()->user->checkAccess('free');
		//if($isfree && in_array($this->type, array('Email', 'Favourite', 'Gift', 'EmailSent'))){
		//	$this->action="doRequestMember('{$this->type}', '{$this->encryptid}')";
		//}
		
		if(!isset($this->action)){
			$this->action = "doAction('{$this->type}','{$this->encryptid}',this); return false;";;
		}
		
		if(!isset($this->text)){
			switch($this->type){
				case 'Email':
				case 'EmailSent':
					$this->text = "Send Mesage";
					break;
				case 'Wink':
					$this->text = "Send A Wink";
					break;
				case 'Gift':
					$this->text = "Send A Flirt";
					break;
				case 'Request-Photos':
					$this->text = "Request Photos";
					break;
				case 'Favourite':
					$this->text = "Add To Favourite";
					break;
				default:
					$this->text = $this->type;
					break;
				
			}
		}
	}
	
	public function buildStyles($styles){
		return $styles;
		//Removed
		$text = '';
		foreach($styles as $key => $value){
			$text.= "$key: $value; ";
		}
		return $text;
	}
	
	public function buildAttributes($atts){
		return $atts;
		//Removed
		$text = '';
		foreach($atts as $key => $value){
			$text.= "$key='$value' ";
		}
		return $text;
	}
	
	public function run(){
		$this->render('button', array('type'=> $this->type, 'profileid' => $this->encryptid, 'action'=>$this->action, 'text' => $this->text,
			'styles' => $this->buildStyles($this->styles), 'attributes' => $this->buildAttributes($this->attributes)));
	}
}
?>