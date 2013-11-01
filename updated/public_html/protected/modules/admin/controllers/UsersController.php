<?php

class UsersController extends Controller
{
	public function actionIndex()
	{
		$model = new UsersForm;
		$model->attributes = (isset($_REQUEST['UsersForm'])) ? $_REQUEST['UsersForm'] : '';
		
		$users = array('count'=>0, 'list'=>array(), 'affs' => array());

		$page = (isset($_REQUEST['page'])) ? intval($_REQUEST['page']) : 0;
		$page = ($page) ? $page : 1;
		$page--;        
		
		if ($model->validate())
			$users = Users::getUsers($model->attributes, $page);

		$pages=new CPagination($users['count']);
		$pages->pageSize = $model->perPage;
		$pages->setCurrentPage($page);
		
		$affs = $users['affs'];;
		
		$this->render('index', array('model'=>$model, 'users'=>$users, 'pages'=>$pages, 'affs' => $affs));
	}
	
	public function actionApproveImage()
	{
		$perPage = 20;
		
		$images = array('count'=>0, 'list'=>array());

		$page = (isset($_REQUEST['page'])) ? intval($_REQUEST['page']) : 0;
		$page = ($page) ? $page : 1;
		$page--;        
		
		$images = Users::getImagesNotApproved(array('perPage'=>$perPage), $page);

		$pages=new CPagination($images['count']);
		$pages->pageSize = $perPage;
		$pages->setCurrentPage($page);
		
		$this->render('approveImage', array('images'=>$images, 'pages'=>$pages));	
	}
	
	
	public function actionXrateImage()
	{
		$type = (isset($_REQUEST['type'])) ? $_REQUEST['type'] : '';
		
		$perPage = (isset($_REQUEST['ppp'])) ? intval($_REQUEST['ppp']) : 100;
		
		$images = array('count'=>0, 'list'=>array());

		$page = (isset($_REQUEST['page'])) ? intval($_REQUEST['page']) : 0;
		$page = ($page) ? $page : 1;
		$page--;        
		
		
		if (isset($_POST['user_id']) && is_array($_POST['user_id']))
		{
			//CHelperSite::vd($_POST, 0);
			foreach($_POST['user_id'] as $k=>$user_id)
			{
				$n = intval($_POST['n'][$k]);
				$value = ($_POST['rated'][$k]=='naked') ? 'naked' : 'clothed';
				
				$profile = new Profile($user_id);
				$profile->imageUpdate($n, 'xrated', $value);        	
			}
		}
		
		
		$images = Users::getImagesXrate(array('type'=>$type, 'perPage'=>$perPage), $page);

		$pages=new CPagination($images['count']);
		$pages->pageSize = $perPage;
		$pages->setCurrentPage($page);
		
		$this->render('xrateImage', array('images'=>$images, 'pages'=>$pages, 'type'=>$type, 'ppp'=>$perPage));	
	}
	
	public function actionFind()
	{
		$model = new UsersFindForm;
		
		$model->attributes = (isset($_REQUEST['UsersFindForm'])) ? $_REQUEST['UsersFindForm'] : '';
		//if (isset($model->albumIdStr)) $model->albumIdStr = trim($model->albumIdStr);
		
		if (isset($_REQUEST['UsersFindForm']) && $model->validate())
		{
			$res = Users::findUsers($model->attributes);


			$this->render('find', array('model'=>$model, 'res'=>$res));
		}
		else 	
			$this->render('find', array('model'=>$model));
		
		
	}
	
	public function actionEdit()
	{
		$id = intval($_GET['id']);
		
		if ($id)
		{
			$profile = new Profile($id);
			
			$sql = "SELECT COUNT(id) FROM  `pm_transactions` WHERE user_id=:user_id";
			$trnExists = Yii::app()->db->createCommand($sql)
				->bindValue(":user_id", $id, 	PDO::PARAM_INT)
				->queryScalar();
			
			$this->render('edit', array('profile'=>$profile, 'trnExists'=>$trnExists));
		}
	}
	
	/*public function actionReportedAbuse()
	{
		$model = new Users;
	       $this->render('reportedAbuse', array('reports'=>$model->gerReportedAbuse()));
	} 
	*/
	
	
	
	
	
	public function actionEditMainTab()
	{
		FB::error($_POST['UserEditMainTabForm']);
		
		$user_id = (isset($_POST['UserEditMainTabForm']['user_id'])) ? intval($_POST['UserEditMainTabForm']['user_id']) : 0;
		
		
		
		$model = new UserEditMainTabForm($user_id);
		
		if ( isset($_POST['ajax']) )
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
		
		if(isset($_POST['UserEditMainTabForm']))
		{
			$model->attributes = $_POST['UserEditMainTabForm'];
			if($model->validate())
			{
				$model->saveMain();
				Yii::app()->end();
			}
		}    
		
		echo CActiveForm::validate($model);
		Yii::app()->end();              
	}
	/*
		public function actionEditAppearanceTab()
		{

			$model = new UserEditAppearanceTabForm;
	      
	        if ( isset($_POST['ajax']) )
	        {
	            echo CActiveForm::validate($model);
	            Yii::app()->end();
	        }
	                
	        if(isset($_POST['UserEditAppearanceTabForm']))
	        {
	            $model->attributes = $_POST['UserEditAppearanceTabForm'];
	            if($model->validate())
	            {
	                $model->saveAppearance();
	                Yii::app()->end();
	            }
	        }    
	        
	        echo CActiveForm::validate($model);
	        Yii::app()->end();              
		}

		public function actionEditLifestyleTab()
		{

			$model = new UserEditLifestyleTabForm;
	      
	        if ( isset($_POST['ajax']) )
	        {
	            echo CActiveForm::validate($model);
	            Yii::app()->end();
	        }
	                
	        if(isset($_POST['UserEditLifestyleTabForm']))
	        {
	            $model->attributes = $_POST['UserEditLifestyleTabForm'];
	            if($model->validate())
	            {
	                $model->saveLifestyle();
	                Yii::app()->end();
	            }
	        }    
	        
	        echo CActiveForm::validate($model);
	        Yii::app()->end();              
		}


	    public function actionMessageSend()
	    {
	//FB::warn($_POST, 'actionMessageSend');
	        $model = new MessageSendForm;
	        if ( isset($_POST['MessageSendForm']) && isset($_POST['ajax']) )
	        {
	            echo CActiveForm::validate($model);
	            Yii::app()->end();
	        }
	                
	        if(isset($_POST['MessageSendForm']))
	        {
	            $model->attributes = $_POST['MessageSendForm'];
	            if($model->validate())
	            {
	                $model->doSend();
	                //Yii::app()->end();
	            }
	        }        
	        Yii::app()->end();
	    }*/

	public function actionEditLocationTab()
	{
		/*$user_id = (isset($_POST['user_id'])) ? intval($_POST['user_id']) : 0;
		if($user_id && isset($_POST['locationcode'])){			
			//if(!isset($_POST['locationcode'])) $_POST['locationcode'] = Yii::app()->location->defaultLocationId;
			$locationCode = $_POST['locationcode'];
			if($locationCode != null){
				$location = Yii::app()->location->getLocation($locationCode);
				if($location){
					$_POST['country'] = $location['country'];
					$_POST['city'] = $location['city'];
					$_POST['state'] = $location['state'];
					$_POST['latitude'] = $location['latitude'];
					$_POST['longitude'] = $location['longitude'];
					$_POST['stateName'] = $location['stateName'];
					if($location['zip'] != '' && $location['zip'] != null){
						$_POST['zipcode'] = $location['zip'];
					}
				}
			}
			//country, city, state, state as stateName, latitude, longitude, locationcode, zipcode
			
			$country  = $_POST['country'];
			$city  = $_POST['city'];
			$zipcode  = $_POST['zipcode'];
			$state = $_POST['state'];
			$latitude = $_POST['latitude'];
			$longitude = $_POST['longitude'];
			
			$profile = new Profile($user_id);
			$profile->locationUpdate2($country, $state, $city, $latitude, $longitude, $zipcode);
			echo CJavaScript::jsonEncode($_POST);
			exit;
		}
		
		echo '{}';
		exit;*/
		
		FB::warn($_POST, 'AdminUserLocationForm');
		
		$model = new AdminUserLocationForm();
		if ( isset($_POST['AdminUserLocationForm']) && isset($_POST['ajax']) )
	    {
	    	echo CActiveForm::validate($model);
	        Yii::app()->end();
		}
	                
	    if(isset($_POST['AdminUserLocationForm']))
	    {
	    	$model->attributes = $_POST['AdminUserLocationForm'];
	        if($model->validate())
	        {
	        	$model->locationUpdate();
	            Yii::app()->end();
			}
		}        
	    Yii::app()->end();
	}
	
}