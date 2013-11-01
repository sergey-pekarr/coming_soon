<?php

class ActionController extends Controller
{
	//public function init()
	//{
	//	parent::init();
	//	$this->layout='//layouts/json';
	//}
	
	/* Move to CHelperAction -> can be shared 
	private function renderSidebarView($id){
		$profile = new Profile($id);
		$username = $profile->getDataValue('username');
		$encid = Yii::app()->secur->encryptID($profile->getId());
		return array('username'=>$username, 
			'userid' => $encid, 
			'url' => "<a href='/profile?id={$encid}'>{$username}</a>", 
			'thumnail' => $profile->getImgPrimary(), 
			'location'=> $profile->getLocationValue('city'), 
			'age' => $profile->getDataValue('age'), 
			'online' => $profile->getDataValue('online')
		);		
	}
	*/
	
	/* Move to CHelperAction -> can be shared 
	public function actionIm(){
		$activity = Activity::createActivity();
		$im = $activity->getSidebarData();
		
		$youviewed = array();
		foreach($im['youviewed'] as $item){
			if(count($youviewed)<5){
				$youviewed[] = $this->renderSidebarView($item['id_to']);
			}
		}
		if(count($youviewed) ==0) unset($im['youviewed']);
		else $im['youviewed'] = $youviewed;
		
		$im['viewing'] = null;
		$viewed = array();
		foreach($im['viewed'] as $item){
			$added = $item['added'];
			if(!$im['viewing'] && (now() - strtotime($item['added'])<30)){
				$im['viewing'] = $this->renderSidebarView($item['id_from']);
			}
			else {
				$viewed[] = $this->renderSidebarView($item['id_from']);
			}
		}
		if(count($viewed) ==0) unset($im['viewed']);
		else $im['viewed'] = $viewed;
		
		if(!$im['viewing']) unset($im['viewing']);
	}
	*/
	
	/**
	 * Return data for sidebar: Count of unread flirts, viewing, viewed, yourviewed, and alert recent activity
	 *
	 * @return mixed This is the return value description
	 *
	 */	
	public function actionIm(){
		
		Yii::app()->user->Profile->setActivity();
		
		$this->layout=false; 
		header('Content-type: application/json'); 
		$im = CHelperAction::buidImData();
		echo CJavaScript::jsonEncode($im); 
		Yii::app()->end();
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
	
	private function requireGold($action = '', $targetid = 0){
		if(!Yii::app()->user->checkAccess('gold')){
			$this->layout=false; 
			$this->render('requiregold', array('targetid' => $targetid, 
				'action' => $action));
			Yii::app()->end();
		}
	}
	
	private function requireTargetId(){
		$id = $this->getTargetId();
		if(!$id){
			$this->render('failed');
			Yii::app()->end();
		}
		return $id;
	}
	
	private function checkBlockResult($result){
		if($result && $result['type'] == 'wasblock'){
			$this->layout=false; 
			echo CJavaScript::jsonEncode(CHelperAction::renderAlertSentFlirt($result['id_from'], 'wasblock', $result['blockaction']));
			Yii::app()->end();
		}
	}
	
	public function actionWinks(){
		
		$id = $this->requireTargetId();
		
		$act = Activity::createActivity();
		$result = $act->sendWinks($id);
		
		$this->checkBlockResult($result);
		
		if($result){
			$this->layout=false; 
			echo CJavaScript::jsonEncode(CHelperAction::renderAlertSentFlirt($result['id_to'], 'winks'));
			Yii::app()->end();
		}
		
		if(!$result) {
			$this->render('failed');
			Yii::app()->end();
		}		
	}
	
	public function actionFavourite(){
		
		$id = $this->requireTargetId();
		
		$this->requireGold('sendfavourite', $id);
		
		$act = Activity::createActivity();
		$result = $act->sendFavourite($id);
		
		$this->checkBlockResult($result);
		
		if($result){
			$this->layout=false; 
			echo CJavaScript::jsonEncode(CHelperAction::renderAlertSentFlirt($result['id_to'], 'favourite'));
			Yii::app()->end();
		}
		
		if(!$result) {
			$this->render('failed');
			Yii::app()->end();
		}	
	}
	
	public function actionLike(){
		
		$id = $this->getTargetId($picid);
		//$this->requireGold();
		
		if(!$id){
			$this->render('failed');
			Yii::app()->end();
		}
		
		$profile = new Profile($id);
		$imgIndx = $profile->imgGetIndx($picid);
		
		$act = Activity::createActivity();
		$result = $act->sendLike($id, $picid);
		
		$this->checkBlockResult($result);
		
		//Note: Like picId because it will not be changed
		//Return $imgIndx because imgUrl require index!
		
		if($result){
			$this->layout=false; 
			echo CJavaScript::jsonEncode(CHelperAction::renderAlertSentFlirt($result['id_to'], 'like', $imgIndx));
			Yii::app()->end();
		}
		
		if(!$result) {
			$this->render('failed');
			Yii::app()->end();
		}	
	}
	
	public function actionBlock(){
		
		$id = $this->requireTargetId();
		
		$act = Activity::createActivity();
		$result = $act->block($id);
		
		if($result){
			$this->layout=false; 
			echo CJavaScript::jsonEncode(CHelperAction::renderAlertSentFlirt($result['id_to'], 'block'));
			Yii::app()->end();
		}
		
		if(!$result) {
			$this->render('failed');
			Yii::app()->end();
		}		
	}
	
	public function actionUnBlock(){
		
		$id = $this->requireTargetId();
		
		$act = Activity::createActivity();
		$result = $act->unBlock($id);
		
		if($result){
			$this->layout=false; 
			echo CJavaScript::jsonEncode(CHelperAction::renderAlertSentFlirt($result['id_to'], 'unblock'));
			Yii::app()->end();
		}
		
		if(!$result) {
			$this->render('failed');
			Yii::app()->end();
		}		
	}
	
	public function actionPhotoRequest(){
		
		$id = $this->requireTargetId();
		
		$act = Activity::createActivity();
		$result = $act->sendPhotoRequest($id);
		
		$this->checkBlockResult($result);
		
		if($result){
			
			if ($result['count']==1)//send email on the first photo request only
			{
				//Send email here
				$profile = new Profile($id);
				$to = $profile->getDataValue('email');
				
				Yii::app()->mail->prepareMailHtml(
					$to,
					'', 
					'photorequest', 
					array(
							'from_user_id' => Yii::app()->user->id,
							'user_id'	=> $id,//email to this user
							)
						);				
			}
			
			$this->layout=false; 
			$actionResult = CHelperAction::renderAlertSentFlirt($result['id_to'], 'photorequest');
			//$actionResult['detailAction'] = $result;
			echo CJavaScript::jsonEncode($actionResult);
			Yii::app()->end();
		}
		
		if(!$result) {
			$this->render('failed');
			Yii::app()->end();
		}		
	}
	
	public function actionSendGift(){
		
		$id = $this->getTargetId($picid);
		
		if(!$id){
			$this->render('failed');
			Yii::app()->end();
		}
		
		$this->requireGold('sendgift', $id);
		
		$define = array('heart','present','chocheart','rose','champagne','knickers','kiss','cake','candycane','pumpkin');
		if(in_array($picid, $define)){
			
			$act = Activity::createActivity();
			$result = $act->sendGift($id, $picid);
			
			$this->checkBlockResult($result);
			
			if($result){
				$this->layout=false; 
				echo CJavaScript::jsonEncode(CHelperAction::renderAlertSentFlirt($result['id_to'], 'gift', $picid));
				Yii::app()->end();
			}
		}
		
		if(!$result) {
			$this->render('failed');
			Yii::app()->end();
		}		
	}
}
