<?php

class Allin1Controller extends Controller
{
	
	public function init()
    {
        parent::init();
        $this->layout='//layouts/allin1';
    }	
	
  
    
    /**
     * STEP 1
     */
    public function actionIndex()
    {
FB::warn($_POST, 'actionIndex');
    	
		if (Yii::app()->user->id)
		{
			$userData = Yii::app()->user->Profile->getData();
			if (
				($userData['role']=='free' && $userData['form']=='93')//$userData['role']=='free93'
				||
				($userData['role']=='free' && $userData['form']=='88')
			)
				$this->redirect('/allin1/step2');
			else
				$this->redirect('/');
		} 
		
		//check if IP registered for last 6 mount:
		if (!Yii::app()->user->id && CHelperProfile::checkExistedRegIP())
		{
			$this->redirect("/site/login");
		}		

		$model = new Allin1Step1Form;
		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='Allin1Step1Form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['Allin1Step1Form']))
		{
            $model->attributes=$_POST['Allin1Step1Form'];
			// validate user input and redirect to the previous page if valid
			/*if($model->validate() && $model->login())
				$this->redirect(Yii::app()->user->returnUrl);*/
            if($model->validate())
            {
                $reg = $model->doAllin1Registration();
				Yii::app()->end();            	
            }
		}

        //$location_id = Yii::app()->location->findLocationIdByIP();        
        //$location = Yii::app()->location->getLocation( $location_id );		
FB::error($model->attributes);		
		$this->render('allin1Step1', array('model'=>$model/*, 'location_id'=>$location_id, 'location'=>$location*/));
    }

    
    /**
     * STEP 2 
     */
    public function actionStep2()
    {
    	if (!Yii::app()->user->id)
    		$this->redirect(SITE_URL.'/allin1');
    	elseif ( Yii::app()->user->Profile->getDataValue('role')=='gold' )
    		$this->redirect(SITE_URL);
    	else
    	{
    		if ( Yii::app()->user->Profile->getDataValue('form')!='93' && Yii::app()->user->Profile->getDataValue('form')!='88' )
    		{
				Yii::app()->user->logout();
				$this->redirect( SITE_URL.'/allin1' );    			
    		}
    	}
	
		//2013-03-07, email: The step 2 page is unnecessary. please send them straight to the zombaio ticket page after step1..
		//2013-05-30	- not for RG 
		$price_id=1;
		
		if ( !isset(Yii::app()->session['allin1_paymod_next']) )
			Yii::app()->session['allin1_paymod_next'] = CHelperPayment::getAllinNextBilling();
		
		$modelPayment = CHelperPayment::getModelPayment(Yii::app()->session['allin1_paymod_next']) ;//$this->getModelPayment(Yii::app()->session['allin1_paymod_next']);	

		if (!$modelPayment)
		{
			unset( Yii::app()->session['allin1_paymod_next'] );    			
		    echo 'Error step2 ...';
		    Yii::app()->end();		
		}
		
		if (Yii::app()->session['allin1_paymod_next']!='rg')
		{
			$redirectUrl = $modelPayment->startTransaction( Yii::app()->user->id, $price_id );
			/*if (DEBUG_IP)
				CHelperSite::vd($redirectUrl);
			else
				$this->redirect( $redirectUrl );
			exit();		
			*/
			if ( $redirectUrl )
				$this->redirect( $redirectUrl );
			else 
			    $this->redirect( '/allin1/step4' );//   		
		}	
		
    	if ($_POST)
    	{
    		$price_id = $_POST['term'];    		
    		
			//for some payments (zombaio, ...): transaction started on setPriceId and needs redirect to payment page
			if ( !$modelPayment->errors )
				switch(Yii::app()->session['allin1_paymod_next'])
				{
					case 'zombaio':
					case 'rg':
						
						$redirectUrl = $modelPayment->startTransaction( Yii::app()->user->id, $price_id );
						
						if ( $redirectUrl )
						{
							$this->redirect( $redirectUrl );
						}
						else 
			    			$this->redirect( '/allin1/step4' );//echo $modelPayment->errors[0];
			    		
			    		Yii::app()->end();
						break;
						
						
					default: //prepare step 3
						
						$modelPayment->checkPriceId($price_id);
						
						break;
				}
			
			
				
			
				
			//step3
			if ( !$modelPayment->errors )
			{
				Yii::app()->session['allin1_price_id'] = $price_id;
				$this->redirect(SITE_URL_SSL."/allin1/step3");//filling card, etc
			}
			else 
			{
    			echo $modelPayment->errors[0];
    			Yii::app()->end();
			}
    	}
    	
    	$this->render(
    		'allin1Step2', 
    		array(
    			//'modelPayment'		=> $modelPayment,
    			'payment_options' 	=> $modelPayment->getOptions(),
    			'defaultPriceId'  	=> $modelPayment->defaultPriceId,
    		)
    	);
    }
    

    
    /**
     * STEP 3
     */
    public function actionStep3()
    {
        if (!Yii::app()->user->id)
    		$this->redirect(SITE_URL.'/allin1');
    	elseif ( Yii::app()->user->Profile->getDataValue('role')=='gold' )
    		$this->redirect(SITE_URL);
    	else
    	{
    		if ( Yii::app()->user->Profile->getDataValue('form')!='93' && Yii::app()->user->Profile->getDataValue('form')!='88' )
    		{
				Yii::app()->user->logout();
				$this->redirect( SITE_URL.'/allin1' );    			
    		}
    	}  	
    	
    	
        if ( !isset(Yii::app()->session['allin1_paymod_next']) || !isset(Yii::app()->session['allin1_price_id']) )
    		$this->redirect(SITE_URL.'/allin1/step2');
    	
    		
    	//check SSL
		if (LOCAL_OK)
    		$ssl = ($_SERVER['SERVER_PORT'] == '443');
    	else
			$ssl = (isset($_SERVER['HTTP_HTTPS']));    		
		if ( !$ssl /*$_SERVER['SERVER_PORT'] != '443'*/ )
			$this->redirect( SITE_URL_SSL."/allin1/step3" );    	
    		
			
    	$price_id = Yii::app()->session['allin1_price_id'];
		
    	$modelPayment = CHelperPayment::getModelPayment(Yii::app()->session['allin1_paymod_next']); //$this->getModelPayment(Yii::app()->session['allin1_paymod_next']);
		if (!$modelPayment)
		{
			unset( Yii::app()->session['allin1_paymod_next'] );    			
		    echo 'Error step2 ...';
		    Yii::app()->end();
		} 

//FB::warn($_POST, 'actionStep3');
		
		
		$model = new Allin1Step3Form;
		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='Allin1Step3Form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['Allin1Step3Form']))
		{
            $model->attributes=$_POST['Allin1Step3Form'];
            if($model->validate())
            {
            	$res = $model->doAllin1Step3($modelPayment, $price_id);//Yii::app()->end();
            	echo $res;
            	Yii::app()->end();

            }
		}
		
		$optionRow = CHelperPayment::getOptionRow($price_id);//$optionRow = $modelPayment->getOptionRow($price_id);
		$this->render('allin1Step3', array('model'=>$model, 'price'=>$optionRow['price'], 'paymod'=>Yii::app()->session['allin1_paymod_next']));
    }
    
    

    
    
    
    
    /**
     * STEP 4
     */
    public function actionStep4()    
    {
    	Yii::app()->session['allin1_paymod_next'] = ZSIGNUP_BACKUP_BILLER;
    	
    	$this->render('allin1Step4');
    }
    
    
    
    
    
    
    
    
    
    
    
    
	public function actionNormal(){
		Yii::app()->session['device-type'] = 'pc';
		$this->redirect('/allin1');
	}
    
}









