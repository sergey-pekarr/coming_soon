<?php

class MsgController extends Controller
{
	private $userid;
	public $action;
	
	public function init(){
		parent::init();
		$this->layout = '//layouts/member' ;

		$this->userid = Yii::app()->user->id;
		//$this->action = $this->getAction()->getId(); //error?
	}
	
	public function actionIndex(){
		$this->action = 'inbox';
		$this->actionInbox();
	}
	
	public function actionInbox(){
		$this->action = $this->getAction()->getId();
		$act = Activity::createActivity();
		//$items = $act->getMessages(0,50, 'added desc ', " archive_to = '0' and hided_to = '0' and `parent` is null");
		$items = $act->getMessages(0,50, 'added desc ', " archive_to = '0' and hided_to = '0' and `last` = '1'");
		$this->cutText($items);
		$this->render('index', array('items' => $items, 'dir'=> 'from'));
	}
	
	public function actionSent(){
		$this->action = $this->getAction()->getId();
		$act = Activity::createActivity();
		$items = $act->getSentMessages(0,50, 'added desc ',  " `last` = '1' and `parent` is null");
		$this->cutText($items);
		$this->render('sent', array('items' => $items, 'dir'=> 'to'));
	}
	
	public function actionArchive(){
		$this->action = $this->getAction()->getId();
		$act = Activity::createActivity();
		$items = $act->getMessages(0,50, 'added desc ', " archive_to = '1' and hided_to = '0' and `last` = '1'");
		$this->cutText($items);
		$this->render('archive', array('items' => $items, 'dir'=> 'from'));
	}
	
	public function actionArchiveThread(){
		if(!isset($_GET['id']) || !isset($_GET['msgid'])){
			throw new exception('invalid operation');
			Yii::app()->end();
		}
		$encid = $_GET['id'];
		$id = Yii::app()->secur->decryptID($encid);
		$msgid = $_GET['msgid'];
		
		//Simple => process now to save development time. Might improve later
		if(!$id || !$msgid) {
			throw new exception('invalid operation');
			Yii::app()->end();
		}
		$msgid = intval($msgid);
		$id = intval($id);
		//All ids must be matched, protect again hacker or accident
		$qry = "update profile_messages set archive_to = '1' where id_from = $id and id_to = {$this->userid} and id = $msgid";
		$res = Yii::app()->db
			->createCommand($qry)
			->execute();
		echo 'success';
		Yii::app()->end();
	}
	
	public function actionDeleteThread(){
		if(!isset($_GET['id']) || !isset($_GET['msgid'])){
			throw new exception('invalid operation');
			Yii::app()->end();
		}
		$encid = $_GET['id'];
		$id = Yii::app()->secur->decryptID($encid);
		$msgid = $_GET['msgid'];
		
		//Simple => process now to save development time. Might improve later
		if(!$id || !$msgid) {
			throw new exception('invalid operation');
			Yii::app()->end();
		}
		$msgid = intval($msgid);
		$id = intval($id);
		//All ids must be matched, protect again hacker or accident
		$qry = "update profile_messages set hided_to = '1' where id_from = $id and id_to = {$this->userid} and id = $msgid";
		$res = Yii::app()->db
			->createCommand($qry)
			->execute();
		echo 'success';
		Yii::app()->end();
	}
	/*
	public function init()
	   {
	       parent::init();
	       
	       $this->layout = '//layouts/dashboard' ;
	       
	       if (!Yii::app()->user->checkAccess('limited'))
	           $this->redirect(Yii::app()->homeUrl);             
	   }

	   public function actionIndex()
	   {
	       $this->render('index');
	   }
	   
	   public function actionInboxAll()
	   {
	       $this->render('inboxAll');
	   }
	   
	   public function actionAll()
	   {
	       $perPage = 5;
	       
	       $id_to = Yii::app()->secur->decryptID($_GET['id']);
	     
	       if ($id_to)
	       {
	           $page = intval($_REQUEST['page']);
	           $page = ($page) ? $page : 1;
	           $page--;
	           
	           $messages = Messages::getPrivateMessages($id_to, Yii::app()->user->id, $page, $perPage);
	           
	           if ($messages['list'])
	               foreach($messages['list'] as $m)
	                   if ($m['readed']==0)
	                       $markAsReaded[] = $m['id'];
	           
	           if ($markAsReaded)
	               Messages::markAsReaded($markAsReaded);
	           
	           
	           $pages=new CPagination($messages['count']);
	           $pages->pageSize = $perPage;
	           $pages->setCurrentPage($page);
	          
	           $this->render('all', array('messages'=>$messages, 'id_to'=>$id_to, 'pages'=>$pages));
	       }
	   }
	   

	   public function actionVideoMessageCreate()
	   {
		$id = Yii::app()->secur->decryptID($_GET['id']);
	       
	       if ($id)
	       {
	           $profile = new Profile($id);
	           
	           $cameraHD = ($_GET['camera']=='HD') ? true : false;
	           
	           $this->layout='//layouts/profile980';
	           $this->render('videoMessageCreate', array('profile'=>$profile, 'cameraHD'=>$cameraHD));
	       }
	       else
	           $this->redirect(Yii::app()->createUrl('site/index')); 
	   }    
	   */
	
	private function cutText(&$items){
		foreach($items as &$item){
			if(isset($item['text']) && $item['text'] != ''){
				$text = $item['text'];
				
				$r = 0;
				$i = strlen($text);
				for($i=1;$i< strlen($text); $i++){
					if($text[$i] == ' ' && $text[$i-1] != ' '){
						$r++;
						if($r>1 || $i > 20) break;
					}
				}
				$item['text'] = substr($text, 0, $i).($i<strlen($text)?"...":"");	
			}
		}
	}
}