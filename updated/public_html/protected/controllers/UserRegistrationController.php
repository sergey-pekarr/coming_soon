<?php

class UserRegistrationController extends Controller
{
	public function actionIndex()
	{
		$this->render('index');
	}
    
	public function actionStep1()
	{
        $model = new UserRegistrationForm;
//FB::info($_POST, 'actionStep1');
        if(isset($_POST['UserRegistrationForm']))
        {
            $model->attributes = $_POST['UserRegistrationForm'];            
            if($model->validate())
            {
                $model->doRegistration();
            }
            Yii::app()->end();            
        }
	}

	public function actionStep1Validation()
	{
//FB::info($_POST,'step1Validation');
        $model = new UserRegistrationForm;
        echo CActiveFormSw::validate($model);
        Yii::app()->end(); 
	}
    
	public function actionStep2_FormLoad()
	{
        $this->layout='//layouts/ajax';
        $this->renderPartial('_Step2Form', array(), false, true);		
	}
    
	public function actionStep2()
	{
//FB::info($_POST,'step2');
        $model = new UserRegistrationStep2Form;
      
        if ( isset($_POST['ajax']) && $_POST['ajax']==='reg_step2_form' )
        {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
                
        if(isset($_POST['UserRegistrationStep2Form']))
        {
            $model->attributes = $_POST['UserRegistrationStep2Form'];
            if($model->validate())
            {
                $model->doRegistrationStep2();
				
                Yii::app()->end();
            }
        }        

        echo CActiveForm::validate($model);
        Yii::app()->end();
	}
	
	public function actionStep2Validation()
	{
        $model = new UserRegistrationStep2Form;
        echo CActiveForm::validate($model);
        Yii::app()->end();
	}

    
	public function actionStep3()
	{
        $model = new UserRegistrationStep3Form;
        if (isset($_POST['UserRegistrationStep3Form']))
        {
            $model->attributes=$_POST['UserRegistrationStep3Form'];
            
            if ($model->description)
            {
                Yii::app()->user->Profile->personalUpdate('description', $model->description);                
            }
            
            $model->image=CUploadedFile::getInstance($model,'image');
            if ($model->image)
            {
                Yii::app()->user->Profile->imgAdd(
                    array(
                        'name'=>$model->image->getTempName(),
                        'type'=>$model->image->getType(),
                        'size'=>$model->image->getSize()
                    )                
                );
            }                        
            
            $this->redirect(Yii::app()->createUrl('site/index'));
            
        }

        //echo CActiveForm::validate($model);
        Yii::app()->end();
	}    
    
}