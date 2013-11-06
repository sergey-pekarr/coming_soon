<?php

class HomeController extends Controller
{
	
	/*
	 * SUMMARY
	 */
	public function actionIndex()
	{
/*		if ( Yii::app()->controller->module->adminAuthManager->checkAccess('administrator', Yii::app()->controller->module->admin->id) )
		{
			$this->redirect('/admin/index');
		}
        
		if (Yii::app()->controller->module->admin->role == 'manager')
			$this->redirect('/admin/users/find');
*/		
        //$this->render('index');
        
		$model = new SummaryForm;
        $model->attributes = (isset($_REQUEST['SummaryForm'])) ? $_REQUEST['SummaryForm'] : '';        
        if ($model->validate())
        {
            $summary = CHelperAdmin::getSummary($model->attributes);
        }
        
        if (Yii::app()->controller->module->admin->role == 'manager')
        	$this->render('indexManager', array('model'=>$model, 'summary'=>$summary));
        else 		
        	$this->render('index', array('model'=>$model, 'summary'=>$summary));
		
	}
    
	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		$model=new AdminLoginForm;

		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='admin-login-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['AdminLoginForm']))
		{
			$model->attributes=$_POST['AdminLoginForm'];
			// validate user input and redirect to the previous page if valid
			if($model->validate() && $model->login())
			{
				if (isset($_GET['redirect']))
					$this->redirect( urldecode($_GET['redirect']) );
				else
					$this->redirect( '/admin/' );
			}
		}
		// display the login form
		$this->render('login',array('model'=>$model));
	} 
    
	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->controller->module->admin->logout();
		$this->redirect(Yii::app()->createUrl('/admin/home/index/'));
	}
    
}