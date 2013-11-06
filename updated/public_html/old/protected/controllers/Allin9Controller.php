<?php

class Allin9Controller extends Controller
{
	
	public function init()
    {
        parent::init();
        $this->layout='//layouts/allin9';
    }	
	
  
    
    /**
     * STEP 1
     */
    public function actionIndex()
    {
//2013-02-26: Oleg, can you please redirect link type 2 to the normal $39 link and retain the affiliate id please, my chatters agents continue to send to it, and i'm no longer using it
///HelperAff::registerAffId();
///$this->redirect('/allin1/');
/*2013-03-19
[18:04:18] Heath: Ahh
[18:04:22] Heath: I see
[18:04:29] Heath: Bring back the $9 link
[18:04:33] Heath: for the affiliates
[18:04:39] Heath: without redirect*/
    	
    	
    	
//FB::warn($_POST, 'actionIndex');
    	
		if (Yii::app()->user->id)
		{
			$userData = Yii::app()->user->Profile->getData();
			if (
				($userData['role']=='free' && $userData['form']=='932')
			)
				$this->redirect('/allin9/step2');
			else
				$this->redirect('/');
		} 
		
		//check if IP registered for last 6 mount:
		if (!Yii::app()->user->id && CHelperProfile::checkExistedRegIP())
		{
			$this->redirect("/site/login");
		}		

		$model = new Allin9Step1Form;
		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='Allin9Step1Form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['Allin9Step1Form']))
		{
            $model->attributes=$_POST['Allin9Step1Form'];
			// validate user input and redirect to the previous page if valid
			/*if($model->validate() && $model->login())
				$this->redirect(Yii::app()->user->returnUrl);*/
            if($model->validate())
            {
                $reg = $model->doAllin9Registration();
				Yii::app()->end();            	
            }
		}

        //$location_id = Yii::app()->location->findLocationIdByIP();        
        //$location = Yii::app()->location->getLocation( $location_id );		
FB::error($model->attributes);		
		$this->render('allin9Step1', array('model'=>$model/*, 'location_id'=>$location_id, 'location'=>$location*/));
    }

    
    /**
     * STEP 2 
     */
    public function actionStep2()
    {
    	if (!Yii::app()->user->id)
    		$this->redirect(SITE_URL.'/allin9');
    	elseif ( Yii::app()->user->Profile->getDataValue('role')=='gold' )
    		$this->redirect(SITE_URL);
    	else
    	{
    		if ( Yii::app()->user->Profile->getDataValue('form')!='932' )
    		{
				Yii::app()->user->logout();
				$this->redirect( SITE_URL.'/allin9' );    			
    		}
    	}
	
    		
    		
    	if ( !isset(Yii::app()->session['allin9_paymod_next']) /*|| DEBUG_IP*/ )
    		Yii::app()->session['allin9_paymod_next'] = CHelperPayment::getAllinNextBilling('932');
    	
    		
    	FB::error(Yii::app()->session['allin9_paymod_next'], 'CURRENT PAYMENT');
    	$modelPayment = CHelperPayment::getModelPayment(Yii::app()->session['allin9_paymod_next']) ;//$this->getModelPayment(Yii::app()->session['allin9_paymod_next']);
		
		if (!$modelPayment)
		{
    		unset( Yii::app()->session['allin9_paymod_next'] );    			
    		echo 'Error step2 ...';
    		Yii::app()->end();		
		}
		
    	if ($_POST)
    	{
    		$price_id = $_POST['term'];    		
    		
			//for some payments (zombaio, ...): transaction started on setPriceId and needs redirect to payment page
			if ( !$modelPayment->errors )
				switch(Yii::app()->session['allin9_paymod_next'])
				{
					case 'zombaio':
					case 'zombaio2':
						
						$redirectUrl = $modelPayment->startTransaction( Yii::app()->user->id, $price_id );
						
						if ( $redirectUrl )
						{
							$this->redirect( $redirectUrl );
						}
						else 
			    			$this->redirect( '/allin9/step4' );//echo $modelPayment->errors[0];
			    		
			    		Yii::app()->end();
						break;
						
						
					default: //prepare step 3
						
						$modelPayment->checkPriceId($price_id);
						
						break;
				}
			
			
				
			
				
			//step3
			if ( !$modelPayment->errors )
			{
				Yii::app()->session['allin9_price_id'] = $price_id;
				$this->redirect(SITE_URL_SSL."/allin9/step3");//filling card, etc
			}
			else 
			{
    			echo $modelPayment->errors[0];
    			Yii::app()->end();
			}
    	}
    	
    	$this->render(
    		'allin9Step2', 
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
    		$this->redirect(SITE_URL.'/allin9');
    	elseif ( Yii::app()->user->Profile->getDataValue('role')=='gold' )
    		$this->redirect(SITE_URL);
    	else
    	{
    		if ( Yii::app()->user->Profile->getDataValue('form')!='932' && Yii::app()->user->Profile->getDataValue('form')!='88' )
    		{
				Yii::app()->user->logout();
				$this->redirect( SITE_URL.'/allin9' );    			
    		}
    	}  	
    	
    	
        if ( !isset(Yii::app()->session['allin9_paymod_next']) || !isset(Yii::app()->session['allin9_price_id']) )
    		$this->redirect(SITE_URL.'/allin9/step2');
    	
    		
    	//check SSL
		if (LOCAL_OK)
    		$ssl = ($_SERVER['SERVER_PORT'] == '443');
    	else
			$ssl = (isset($_SERVER['HTTP_HTTPS']));    		
		if ( !$ssl /*$_SERVER['SERVER_PORT'] != '443'*/ )
			$this->redirect( SITE_URL_SSL."/allin9/step3" );    	
    		
			
    	$price_id = Yii::app()->session['allin9_price_id'];
		
    	$modelPayment = CHelperPayment::getModelPayment(Yii::app()->session['allin9_paymod_next']); //$this->getModelPayment(Yii::app()->session['allin9_paymod_next']);
		if (!$modelPayment)
		{
			unset( Yii::app()->session['allin9_paymod_next'] );    			
		    echo 'Error step2 ...';
		    Yii::app()->end();
		} 

//FB::warn($_POST, 'actionStep3');
		
		
		$model = new Allin9Step3Form;
		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='Allin9Step3Form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['Allin9Step3Form']))
		{
            $model->attributes=$_POST['Allin9Step3Form'];
            if($model->validate())
            {
            	$res = $model->doAllin9Step3($modelPayment, $price_id);//Yii::app()->end();
            	echo $res;
            	Yii::app()->end();

            }
		}
		
		$optionRow = CHelperPayment::getOptionRow($price_id);//$optionRow = $modelPayment->getOptionRow($price_id);
		$this->render('allin9Step3', array('model'=>$model, 'price'=>$optionRow['price'], 'paymod'=>Yii::app()->session['allin9_paymod_next']));
    }
    
    

    
    
    
    
    /**
     * STEP 4
     */
    public function actionStep4()    
    {
    	Yii::app()->session['allin9_paymod_next'] = ZSIGNUP2_BACKUP_BILLER;
    	
    	$this->render('allin9Step4');
    }
    
    
    
    
    
    
    
    
    
    
    
    
	public function actionNormal(){
		Yii::app()->session['device-type'] = 'pc';
		$this->redirect('/allin9');
	}
    
}









