<?php

class DashboardController extends Controller
{
    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) 
        {
			if ( !Yii::app()->user->checkAccess('free') && $action->id!='index' ) 
				$this->redirect('/');
				
			return true;
        }
        
        return false;
	}
	
	
	public function init()
    {
        $this->layout = '//layouts/dashboard' ;
        parent::init();
    }
    
    public function actionIndex()
	{
        if ( !Yii::app()->user->checkAccess('free') )
        {
        	if (
        			Yii::app()->user->id 
        			&& 
        			Yii::app()->user->Profile->getDataValue('role')=='justjoined'
        			&&
        			Yii::app()->user->Profile->getDataValue('ext', 'facebook')
        	)
        	{
        		$this->redirect('/site/registrationStep2');
        	}        		
        	else
        	{
	        	$this->layout='//layouts/guest-home';
	        	$this->render('home-guest');
        	}
        }        	
        else
        {
        	$this->render('index');        
        } 

        
        /*$model=new HelpForm;
        
        if(isset($_POST['HelpForm']))
        {
            $model->attributes=$_POST['HelpForm'];
        
            if($model->validate() && $model->save())
            {
                $success = "Message sent!";
                Yii::app()->user->setFlash('HelpSuccess',$success); 
            }            
        }*/
        
        //$this->render('index'/*, array('model'=>$model)*/);
	}
	
    public function actionMatches()
    {
        $this->render('matches'/*, array('model'=>$model)*/);
    }
    
    public function actionInbox()
    {
        $this->render('inbox'/*, array('model'=>$model)*/);
    }
    
    public function actionInboxAll()
    {
        $this->render('inboxAll'/*, array('model'=>$model)*/);
    }
    
    public function actionSent()
    {
        $this->render('sent'/*, array('model'=>$model)*/);
    } 
    
    public function actionHotList()
    {
        $this->render('hotlist'/*, array('model'=>$model)*/);
    }     
}