<?php

class ThreadController extends Controller
{
	private $userid;
	public $id;
	public $encid;
	public $parentmsgid;
	
	public function init(){
		parent::init();
		$this->layout = '//layouts/member' ;
		
		$this->userid = Yii::app()->user->id;
		if(isset($_GET['id'])){
			$this->encid = $_GET['id'];
		}
		
		if(isset($_GET['msgid'])){
			$this->parentmsgid = $_GET['msgid'];
		}
		
		if(isset($this->encid)){
			$this->id = Yii::app()->secur->decryptID($this->encid);
		}
	}
	
	public function actionIndex(){
				
		if(!$this->id){
			$this->render('error', array('msg' => 'Mesasage Not Found'));
			Yii::app()->end();
		}
		
		$user = Yii::app()->user->Profile;
		$target = new Profile($this->id);
		if(!$this->parentmsgid){
			$this->render('index', array('user' => $user, 'target' => $target, 'data' => array()));		
			Yii::app()->end();	
		}
		
		$act = Activity::createActivity();
		if(Yii::app()->user->checkAccess('gold')){
			$act->setReadMessagesByThread($this->id, $this->parentmsgid);
		}
		$data = $act->getThreads($this->id, $this->parentmsgid);
		
		$addeds = array();
		foreach($data as $item){
			$addeds[] = $item['added'];
		}
		
		//Order by time
		array_multisort($addeds, SORT_ASC, $data);
		
		$this->render('index', array('user' => $user, 'target' => $target, 'data' => $data));
	}
	
	private function requireGold($action = '', $targetid = 0){
		if(!Yii::app()->user->checkAccess('gold')){
			$this->layout=false; 
			$this->render('requiregold', array('targetid' => $targetid, 
				'action' => $action));
			Yii::app()->end();
		}
	}
	
	private function checkBlockResult($result){
		if($result && $result['type'] == 'wasblock'){
			$this->layout=false; 
			echo CJavaScript::jsonEncode(CHelperAction::renderAlertSentFlirt($result['id_from'], 'wasblock'));
			Yii::app()->end();
		}
	}
	
	public function actionSend(){
		//Simple => handle request immediately
		if(!$_POST){
			Yii::app()->end();
		}
		
		$this->requireGold('sendmessage', $this->id);
		
		$subject = '';
		$content = '';
		$parentmsgid = null;
		if(isset($_POST['subject'])) $subject = $_POST['subject'];
		if(isset($_POST['content'])) $content = $_POST['content'];
		if(isset($_POST['parentmsgid'])){
			$parentmsgid = $_POST['parentmsgid'];
			if(trim($parentmsgid) == '') $parentmsgid = null;
		}
		
		if(!$this->id)
		{
			Yii::app()->end();
		}
		
		$user = new Profile($this->userid);
		$target = new Profile($this->id);
		
		$act = Activity::createActivity();
		$result = $act->sendMessages($this->id, $subject, $content, $parentmsgid);
		
		$this->checkBlockResult($result);
		
		if($result){
			
			$reshtml = array(
				'subject' => $subject,
				'content' => $content,
				'fromName' => $user->getDataValue('username'),
				'toName' => $target->getDataValue('username'),
				'fromLink' => "/profile/".Yii::app()->secur->encryptID($this->userid),
				'toLink' => "/profile/".Yii::app()->secur->encryptID($this->id),
				'fromImgUrl' => $user->imgUrl(),
				'toImgUrl' => $target->imgUrl(),
				'time' => 'less than 5 seconds',
				);
			if(!isset($result['parent']) || $result['parent'] == ''){
				$reshtml['threadlink'] = SITE_URL."/thread/".Yii::app()->secur->encryptID($this->id).'/'.$result['id'];
			} else{
				$reshtml['threadlink'] = SITE_URL."/thread/".Yii::app()->secur->encryptID($this->id).'/'.$result['parent'];
			}
			
			//Note: Do not send reply message.
			if(!isset($result['parent']) && $target->getSettingsValue('hided_notify') == '0'){
				Yii::app()->mail->prepareMailHtml(
					'',
					'', 
					'email', 
					array(
							'from_user_id' => $this->userid,
							'user_id'	=> $this->id,//email to this user
							'messages_Url' => CHelperProfile::getAutoLoginUrl($this->id).'?redirect='. urlencode("/thread/".Yii::app()->secur->encryptID($this->userid).'/'.$result['id']),
							'message_subject' => $subject
							)
						);
			}
			
			echo CJavaScript::jsonEncode($reshtml);
			Yii::app()->end();			
		}
		else {
			Yii::app()->end();
		}
	}
	
	
}