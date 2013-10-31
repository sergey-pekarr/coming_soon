<?php

class ActivityController extends Controller
{
	private $page = 1;
	private $userid;
	private $data;
	private $count;
	private $totalPage;
	private $perpage =20;
	private $start = 0;
	
	public function init()
	{
		parent::init();
		$this->layout = '//layouts/member' ;
		
		$this->userid = Yii::app()->user->id;
		
		if(isset($_GET['page'])) $this->page = intval($_GET['page']);
		$this->start = $this->perpage * ($this->page - 1);
		if($this->start<0) $this->start = 0;
	}
	
	private function preRender(){
		$this->totalPage = $this->count/$this->perpage;
		if($this->totalPage - floor($this->totalPage)>0) $this->totalPage = floor($this->totalPage) +1;	
		else if($this->totalPage - floor($this->totalPage)<0) $this->totalPage = floor($this->totalPage) -1;
	}
	
	private function getkActivity($tblName, $dir){
		//$qry = "select * from $tblName where id_$dir = {$this->userid} limit $start, {$this->perpage}";
		//$countQry = "select count(*) from $tblName where id_$dir = {$this->userid}";
		//
		//$this->data = Yii::app()->db
		//	->createCommand($qry)
		//	->queryAll();
		//$this->count = Yii::app()->db
		//	->createCommand($countQry)
		//	->queryScalar();
		//
		//$this->totalPage = $this->count/$this->perpage;
		//if($this->totalPage - floor($this->totalPage)>0) $this->totalPage = floor($this->totalPage) +1;	
		//else if($this->totalPage - floor($this->totalPage)<0) $this->totalPage = floor($this->totalPage) -1;		
	}
	
	public function actionWinks(){
		//$data = $this->getkActivity("profile_winks", 'to');
		//$this->render('winks', array('title' => 'Winks', 'items' => $this->data, 'page' => $this->page, 
		//	'total' => $this->totalPage, 'panelsrc'=>'winks?'));
		
		$act = Activity::createActivity();
		$this->count = $act->countAllWinks();
		$this->data = $act->getWinks($this->start, $this->perpage);
		$act->setReadWinks();
		
		$this->preRender();
		
		$this->render('winks', array('title' => 'Winks', 'items' => $this->data, 'page' => $this->page, 
			'total' => $this->totalPage, 'panelsrc'=>'winks?', 'dataid' => 'id_from'));
	}
	
	public function actionView(){
		
		$act = Activity::createActivity();
		$this->count = $act->countAllView();
		$this->data = $act->getView($this->start, $this->perpage);
		$act->setReadView();
		
		$this->preRender();
		
		$this->render('view', array('title' => 'View', 'items' => $this->data, 'page' => $this->page, 
			'total' => $this->totalPage, 'panelsrc'=>'view?', 'dataid' => 'id_from'));
	}
	
	public function actionFavourite(){
		
		if(!Yii::app()->user->checkAccess('gold')){
			$this->redirect('/payment/viewfavourite');
		}
		
		$act = Activity::createActivity();
		$this->count = $act->countAllFavourite();
		$this->data = $act->getFavourite($this->start, $this->perpage);
		$act->setReadFavourite();
		
		$this->preRender();
		
		$this->render('favourite', array('title' => 'Favourite', 'items' => $this->data, 'page' => $this->page, 
			'total' => $this->totalPage, 'panelsrc'=>'favourite?', 'dataid' => 'id_from'));
	}
	
	public function actionPhotoRequest(){
		
		$act = Activity::createActivity();
		$this->count = $act->countAllPhotoRequest();
		$this->data = $act->getPhotoRequest($this->start, $this->perpage);
		$act->setReadPhotoRequest();
		
		$this->preRender();
		
		$this->render('photorequest', array('title' => 'Photo Request', 'items' => $this->data, 'page' => $this->page, 
			'total' => $this->totalPage, 'panelsrc'=>'photorequest?', 'dataid' => 'id_from'));
	}
	
	public function actionLike(){
		
		$act = Activity::createActivity();
		$this->count = $act->countAllLike();
		$this->data = $act->getLike($this->start, $this->perpage);
		$act->setReadLike();
		
		$this->preRender();
		
		$this->render('like', array('title' => 'Like', 'items' => $this->data, 'page' => $this->page, 
			'total' => $this->totalPage, 'panelsrc'=>'like?', 'dataid' => 'id_from'));
	}
	
	public function actionWinksSent(){
		$act = Activity::createActivity();
		$this->count = $act->countSentWinks();
		$this->data = $act->getSentWinks($this->start, $this->perpage);
		
		$this->preRender();
		
		$this->render('winkssent', array('title' => 'Winks Sent', 'items' => $this->data, 'page' => $this->page, 
			'total' => $this->totalPage, 'panelsrc'=>'winks/sent?', 'dataid' => 'id_to'));
	}
	
	public function actionViewSent(){
		$act = Activity::createActivity();
		$this->count = $act->countSentView();
		$this->data = $act->getSentView($this->start, $this->perpage);
		
		$this->preRender();
		
		$this->render('viewsent', array('title' => 'View Sent', 'items' => $this->data, 'page' => $this->page, 
			'total' => $this->totalPage, 'panelsrc'=>'view/sent?', 'dataid' => 'id_to'));
	}
	
	public function actionMyFavourite(){
		$act = Activity::createActivity();
		$this->count = $act->countMyFavourite();
		$this->data = $act->getMyFavourite($this->start, $this->perpage);
		
		$this->preRender();
		
		$this->render('myfavourite', array('title' => 'My Favourite', 'items' => $this->data, 'page' => $this->page, 
			'total' => $this->totalPage, 'panelsrc'=>'myfavourite?', 'dataid' => 'id_to'));
	}
	
	public function actionBlacklisted(){
		
		$act = Activity::createActivity();
		$this->count = $act->countBlock();
		$this->data = $act->getBlock($this->start, $this->perpage);
		
		$this->preRender();
		
		$this->render('block', array('title' => 'Block Users', 'items' => $this->data, 'page' => $this->page, 
			'total' => $this->totalPage, 'panelsrc'=>'blacklisted?', 'dataid' => 'id_to'));
	}
}
