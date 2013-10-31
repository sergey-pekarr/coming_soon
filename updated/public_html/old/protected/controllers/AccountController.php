<?php

class AccountController extends Controller
{
	private $userid;
	
	public function init(){
		parent::init();
		
		$this->layout = '//layouts/member' ;
		
		$this->userid = Yii::app()->user->id;
	}
	
	public function actionIndex(){
		$this->render('index', array('profile' => Yii::app()->user->Profile));
	}
	
	private function getTargetId(&$picid = null){
		$uri = Yii::app()->request->requestUri;
		$controlname = $this->getId();
		$actionname = $this->getAction()->getId();
		$id = "/$actionname/$controlname/";
		$querystring = substr($uri, strlen($id), 1000);
		
		$queries = explode('/', $querystring);
		if($queries && count($queries)>0 && $queries[0] != ''){
			if(count($queries)>1) $picid = $queries[1];
			return Yii::app()->secur->decryptID($queries[0]);
		}
		return null;
	}
	
	public function actionChangeNotification(){
		
		$btnState =  $this->getTargetId();
		$profile = Yii::app()->user->Profile;
		
		$curentValue = $profile->getSettingsValue('hided_notify');
		$newValue = '0';
		if($curentValue == '1'){
			$newValue = '0';
		}
		else {
			$newValue = '1';
		}
		$profile->settingsUpdate('hided_notify', $newValue);
		//subscribe = !hided_notify
		echo CJavaScript::jsonEncode(array('success'=>true, 'subscribe' => intval($curentValue)));
		Yii::app()->end();
	}
	
	private function endAction($res){
		echo CJavaScript::jsonEncode($res);
		Yii::app()->end();
	}
	
	public function actionEditUsername(){
		$res = array('success' => false, 'message' => '');
		$profile = Yii::app()->user->Profile;
		if(!$_POST){
			$res['message'] = 'Please type new username.';
			$this->endAction($res);
		}
		
		$newusername = $_POST['newusername'];
		$confirmnewusername = $_POST['confirmnewusername'];
		
		if($newusername == ''){
			$res['message'] = 'Please type new username.';
			$this->endAction($res);
		}
		
		if($newusername != $confirmnewusername){
			$res['message'] = 'You must confirm your username.';
			$this->endAction($res);
		}
		
		if($profile->getDataValue('username') == $newusername){
			$res['message'] = 'You username has not been changed';
			$this->endAction($res);
		}
		
		$model = new UserNameForm();
		$model->username = $newusername;
		if($model->validate()){
			$model->usernameCheck();
		}
			
		if($model->hasErrors()){
			$res['message'] = $model->getErrors('username');
			$this->endAction($res);
		}
		else{
			$res['success'] = true;
			$res['message'] = '';
			$this->endAction($res);
		}
	}
		
	public function actionEditPassword(){
		$res = array('success' => false, 'message' => '');
		$profile = Yii::app()->user->Profile;
		if(!$_POST){
			$res['message'] = 'Please type your password.';
			$this->endAction($res);
		}
		
		$newpass = $_POST['newpass'];
		$confirmnewpass = $_POST['confirmnewpass'];
		
		if($newpass == ''){
			$res['message'] = 'Please type your password.';
			$this->endAction($res);
		}
		
		if($newpass != $confirmnewpass){
			$res['message'] = 'You must confirm your password.';
			$this->endAction($res);
		}
		
		$count = strlen($newpass);
		if( $count<6 || $count>12){
			$res['message'] = 'Password must has 6-12 characters';
			$this->endAction($res);
		}
		
		$profile->Update('password', $newpass);
		
		$res['success'] = true;
		$res['message'] = '';
		$this->endAction($res);		
	}
	
	public function actionEditBirthday(){
		$res = array('success' => false, 'message' => '');
		$profile = Yii::app()->user->Profile;
		if(!$_POST){
			$res['message'] = 'Please correct your date of birth.';
			$this->endAction($res);
		}
		
		if(!CHelperProfile::checkBirthdayAge18($_POST)){
			$res['message'] = 'Invalid date of birth.';
			$this->endAction($res);
		}
		else{
			$profile->Update('birthday', "{$_POST['year']}-{$_POST['month']}-{$_POST['day']}");
			
			$res['success'] = true;
			$res['message'] = '';
			$res['age'] = $profile->getDataValue('age');
			$this->endAction($res);		
		}
		
	}
	
	public function actionDeleteAccount(){
		
		//Profile::deleteUser(Yii::app()->user->id);		
		//$this->redirect(Yii::app()->homeUrl);
		
		//There is a problem with cache. We will consider to delete account later
		/*
			Propose model:
			1. Mark the account to be delete. User can not access to the account
			2. Wait a period time so every reference in cache to this accout were removed
				- Flirt count
				- Recent activities
			3. Delete
		*/
		
		Profile::deleteUser(Yii::app()->user->id);//oleg, 2012-07-17, I added role 'deleted'
		
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}
	
}