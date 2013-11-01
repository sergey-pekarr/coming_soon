<?php

class SearchController extends Controller
{
	public function init()
	{
		parent::init();

		$this->layout = '//layouts/member' ;
	}	
	
	/**
	 * search users
	 */
	public function actionIndex()
	{
		$profile = new Profile(Yii::app()->user->id);
		$condition = new SearchCondition();
		$this->render('index', array('profile' => $profile, 'condition' => $condition->listCondition()));
		
	}
	
	/*
	- Add search form to handle post
	- decode searchcondition
	- check page
	- build query
	- render result
		- UserBoxWidget
		- UserPanelWidget
		- Top paginate
		- Bottom paginate
	- search box
	*/
	public function actionResult(){
		
		if($_POST){
			$model = new SearchForm($_POST, 16);
		}
		else{			
			$model = new SearchForm($_GET, 16);
		}
		//$model->queryData();
		$model->queryData2();
		
		$profile = new Profile(Yii::app()->user->id);
		
		$total = $model->count/$model->perpage;
		if($total - floor($total)>0) $total = floor($total) +1;	
		else if($total - floor($total)<0) $total = floor($total) -1;	
		
		if(!Yii::app()->user->checkAccess('gold') && $model->page>5){
			
			CHelperProfile::getPaymentLinkWithAction('searchmore', '', $link, $nav);	
			$this->redirect("/payment/$nav");
		}
		
		$options = $model->combineOptions();
		foreach($options as $key => $value){
			if(!$value || $value == '' || $value == '0'){
				unset($options[$key]);
			}
		}		
		unset($options['page']);
		
		$options = http_build_query($options);
		
		$filter = array();
		if($model->age) $filter['age'] = $model->age;
		if($model->maxage) $filter['maxage'] = $model->maxage;
		if($model->username && $model->username != '') $filter['username'] = $model->username;
		if($model->radius) $filter['radius'] = $model->radius;
		$filter['has_photo'] = $model->has_photo;
		
		$this->render('result', array('profile' => $profile, 'users' => $model->data, 
			'total'=> $total, 'page'=>$model->page, 'options'=>$options, 'filter' => $filter));
	}
	
	
	/**
	 * Save user's search condition
	 *
	 * @return mixed This is the return value description
	 *
	 */	
	public function actionSave(){
		$post = $_POST;
		$condition = new SearchCondition();
		$condition->save($post);		
	}
	
	
	/**
	 * Load user's search condition
	 *
	 * @return mixed This is the return value description
	 *
	 */	
	public function actionRemove(){
		$post = $_POST;
		$condition = new SearchCondition();
		$condition->remove($post);		
	}
	
	
	public function actionOnline(){
		
		$profile = new Profile(Yii::app()->user->id);
		
		$pageSize = 20;
		$pagesMax = 50;
		$model = new Profiles;		
		
		$page = 1;
		if(isset($_GET['page'])){
			try{
				$page = intval($_GET['page']);
			} 
			catch(exception $ex){
			}
		}
		
		$res = $model->findOnlineNow($page, $pageSize, $pagesMax);
		
		$total = $res['count']/$pageSize;
		if($total - floor($total)>0) $total = floor($total) +1;	
		else if($total - floor($total)<0) $total = floor($total) -1;	
		
		$this->render('online', array('profile' => $profile, 'users' => $res['ids'], 
			'total'=> $total, 'page'=>$page));
	}
	
	
	public function actionIndexOld()
	{
		//FB::error($_REQUEST);
		if (!Yii::app()->user->id)
			$this->redirect( Yii::app()->getHomeUrl() );

		$model = new SearchForm;
		
		if(isset($_REQUEST['SearchForm']))
		{
			$model->attributes=$_REQUEST['SearchForm'];
			$model->validate();
		}
		
		if (!$model->hasErrors())
		{
			$modelProfiles = new Profiles;            
			
			$perPage = 20;
			
			$page = (isset($_REQUEST['page'])) ? intval($_REQUEST['page']) : 1;
			$page = ($page) ? $page : 1;
			$page--;
			
			$profiles = $modelProfiles->Search($model->attributes, $page, $perPage);

			$pages=new CPagination($profiles['count']);
			$pages->pageSize = $perPage;
			$pages->setCurrentPage($page);
		}
		
		$this->render(
			'index', 
			array(
					'model'=>$model,
					'profiles'=>$profiles['list'],                
					'pages'=>$pages,
					'perPage'=>$perPage
					)
				);
	}
}