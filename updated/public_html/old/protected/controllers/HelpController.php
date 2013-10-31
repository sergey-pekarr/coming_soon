<?php

class HelpController extends Controller
{
	public function init()
    {
        parent::init();
        $this->layout = '//layouts/one-column' ;
    }

	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
		);
	}
    
    public function actionIndex()
	{
        $model=new HelpForm;
        
        if(isset($_POST['HelpForm']))
        {
            $model->attributes=$_POST['HelpForm'];
        
            if($model->validate() && $model->save())
            {
                $success = "Message sent! We respond within 24 hours!";
                Yii::app()->user->setFlash('HelpSuccess',$success); 
            }            
        }
        
        $this->render('index', array('model'=>$model));
	}


	// Uncomment the following methods and override them if needed
	/*
	public function filters()
	{
		// return the filter configuration for this controller, e.g.:
		return array(
			'inlineFilterName',
			array(
				'class'=>'path.to.FilterClass',
				'propertyName'=>'propertyValue',
			),
		);
	}

	public function actions()
	{
		// return external action classes, e.g.:
		return array(
			'action1'=>'path.to.ActionClass',
			'action2'=>array(
				'class'=>'path.to.AnotherActionClass',
				'propertyName'=>'propertyValue',
			),
		);
	}
	*/
}