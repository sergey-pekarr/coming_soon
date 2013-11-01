<?php

class LandingcamsController extends Controller
{
	
	private $priceId = 11;
	
	public function init()
    {
        parent::init();
        $this->layout='//layouts/landingcams';
        $this->pageTitle = SITE_NAME;
        
        
        if (CHelperSite::checkBlockedCountry())
        {
        	Yii::app()->user->setFlash('errorCustom','We are sorry, but your country is not supported.');
        	$this->redirect('/site/errors');        
        }        
    }	
	
  
    
    /**
     * STEP 1
     */
    public function actionIndex()
    {
   	
FB::warn($_POST, 'CAMS ALL');
		
		$model = new LandingCamsAllinOneStepForm;//init affid
//if (DEBUG_IP) CHelperSite::vd(Yii::app()->user->id);
		if (Yii::app()->user->id && !isset($_POST['LandingCamsAllinOneStepForm']))
		{
			$userData = Yii::app()->user->Profile->getData();
			
			if ($userData['form']=='cams')
			{
				/*if ($userData['role']=='gold')
					$this->redirect(SITE_MAIN_URL);
				else*/
				{
					if ($userData['settings']['cams_joined']=='1')
					{
						if ($userData['settings']['cams_autologin']!='')
						{
							$camsModel = new Cams();
							$url_1_click = $camsModel->getWhiteLabel_1_click_link( Yii::app()->user->id );
							
if (DEBUG_IP) CHelperSite::vd("DEBUG_IP: ".$url_1_click);
							
							if ($url_1_click) 
								$this->redirect( $url_1_click );
						}
						
						Yii::app()->user->logout();
						$this->redirect('/');//$this->redirect('/join');
					}
///					else
///						$this->redirect('/join/step2');
				}
					

if (!isset(Yii::app()->session['MC_step4']))//if (!isset($_SERVER['HTTP_REFERER']) || (isset($_SERVER['HTTP_REFERER']) && !stristr($_SERVER['HTTP_REFERER'], '/step4')))
{
	unset(Yii::app()->session['MC_step4']);
	
	Yii::app()->user->logout();
	$this->redirect('/');//$this->redirect('/join');
}
			}
			else
			{
				Yii::app()->user->logout();
				//$this->redirect( SITE_URL );
				$this->redirect('/');//$this->redirect('/join');//skype 2013-03-07
				
			}
		} 
	
		
		
		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='LandingCamsAllinOneStepForm')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['LandingCamsAllinOneStepForm']))
		{
            $model->attributes=$_POST['LandingCamsAllinOneStepForm'];
            if($model->validate())
            {
                $res = "";
//FB::error($model->subStep, 'subStep');                
            	switch($model->subStep)
	    		{
	    			case 0: $res = $model->doRegistration(); 
	    					break;
	    			
	    			case 1: 
	    			case 2: //$model->updateProfile();//if user something changed
	    					break;
	    			
	    			case 3: 
	    					if (CAM_DUMMY && !Yii::app()->user->id)
	    						$res = $model->doRegistration();

    						$model->updateProfile();//if user something changed
	    					
	    					$model->ccname = $model->firstname.' '.$model->lastname;//ccname hided on form	 
							$model->email = Yii::app()->user->Profile->getDataValue('email');
							$model->username = Yii::app()->user->Profile->getDataValue('username');
	    					
							if (CHelperPayment::getCamsWay(Yii::app()->user->id)=='nb')
								Yii::app()->session['camsStep2_post'] = $model->attributes;//for NB next steps
//FB::warn($model->attributes);	    					
	    					$res = $this->Step3($model->attributes); 
	    					break;
	    		}                
            }
            else
            	$res = CActiveForm::validate($model);
			
            echo $res;
            Yii::app()->end();
		}
		
		
		
    	//check SSL
		if ( CHelperSite::isSSL()==false )
			$this->redirect( SITE_URL_SSL."/" ); ;//$this->redirect( SITE_URL_SSL."/join/" ); 		
		
		

		//check if IP registered for last 6 mount:
/* skype 2013-03-12: remove this
		if (Yii::app()->user->id==0 && CHelperProfile::checkExistedRegIP())
		{
			$this->redirect(SITE_MAIN_URL."/site/login");
		}*/
    	if (Yii::app()->user->id==0 && HelperDuplicates::checkExistedPaidIP())
		{
			$this->redirect("/site/errors");
		}
					
		//!!!	for init TM (nb)
		if (CHelperPayment::getCamsWay(Yii::app()->user->id)=='nb')
			$modelPayment = CHelperPayment::getCamsPaymod() ;//new Payment_netbilling_2_cams();
		
		if (CAM_DUMMY)
			$this->render('stepAll_dummy', array('model'=>$model));
		else
			$this->render('stepAll', array('model'=>$model));
    }
    
    /**
     * NETBILLING_2
     * STEP 3 after 3D check for nb_2
     */
    public function actionStep2_nb_2_3D_callback()
    {
//http://cams.overnightlover.com/join/Step2_nb_2_3D_callback?reference=566763&result=0&PayerAuthenticationID=3583914
//CHelperLog::logFile('pm_nb_2_cams_3D_postback___TMP.log', $_REQUEST);   
//CHelperSite::vd(Yii::app()->session['landingcams_step2nb_2_post']); 	
    	//!!!
    	if (!isset(Yii::app()->session['landingcams_step2nb_2_post']) || !Yii::app()->user->id)
    	{
    		Yii::app()->user->logout();
    		$this->redirect(SITE_URL.'/');;//$this->redirect(SITE_URL.'/join');
    		//$this->redirect(SITE_URL.'/join/step4');
    	}
    		
    	
    	$modelPayment = new Payment_netbilling_2_cams(0);
    	$success = $modelPayment->postback_3D( $_GET );
    	
		$url = $this->afterInit($success);

    	
    	$this->redirect($url);
    }

    
    public function afterInit($success)
    {
        if ($success!=false && is_array($success) && !empty($success))
    	{
			$url = $this->doNLC_auth(true, $success);
    	}
    	else
    	{
    		$url = SITE_URL.'/join/step4';
    	}
    	return $url;
    }
    
    
    
    /**
     * show if both approved
     */
    /*public function actionStep3()
    {
    
    }*/
    
    public function Step3($camsStep2_post)
    {
//CHelperSite::vd(Yii::app()->session['camsStep2_post']);

    	if (Yii::app()->user->id && Yii::app()->user->Profile->getDataValue('form')=='cams')
    	{
    		$userId = Yii::app()->user->id;
//if (!$userId) { (LOCAL) ? $userId=1 : $userId=593858; }

			$profile = new Profile($userId);
			
			$camsModel = new Cams();
			
			//if allready done step3
			if ($profile->isJoinedTo_NLC())
			{
	    		$url_1_click = $camsModel->getWhiteLabel_1_click_link($userId);
	    		
	    		if ($url_1_click)
	    		{
	    			if (DEBUG_IP) CHelperSite::vd("DEBUG_IP (step3): ".$url_1_click);
					return $url_1_click;//	    			$this->redirect($url_1_click);    			
	    		}				
			}

    		
    		//post data losted...
///    		if (!isset(Yii::app()->session['camsStep2_post']))
///    			$this->redirect('/join/step4');
    		
    		
    		//POST
//    		if (isset($_POST['start']))
    		{
    			
///    			$camsStep2_post = Yii::app()->session['camsStep2_post'];
    			
    			
    			
    			/*//   ***** 1 *****
    			//joinNC
    			if ( !$profile->isJoinedTo_NC() && isset($_POST['joinNC']) && ($_POST['joinNC']=='on' || $_POST['joinNC']=='1') )
    			{
    				$profileData = $profile->getData();
    				$profileData['password'] = CSecur::decryptByLikeNC($profileData['passwd']);

    				$data = array(
    					'profileData'=>$profileData,
    					'joinONL_post'=>$camsStep2_post,
    				);
    				
    				$url = "https://" . ((LOCAL_OK) ? "nc.lo" : "naughtyconnect.com") . "/camsONL/join.php";
    				$res = CHelperSite::curl_request($url, $data);    				
					
    				//log res
    				$sql = "INSERT IGNORE INTO `log_joinNC` (`user_id`, `dt`, `res`) VALUES ({$userId}, NOW(), :res)";
    				Yii::app()->db->createCommand($sql)->bindValue(":res", $res, PDO::PARAM_STR)->execute();
    				
    				$cams_joinNC = ($res=="OK") ? 'yes' : 'no';    				
    			}
    			else
    				$cams_joinNC = 'skipped';
   				
    			$profile->settingsUpdate('cams_joinNC', $cams_joinNC);*/
    				
    				
    				
    				
    			//   ***** 2 *****


					
					
    		    //joinONL gold
				//if ( !CAM_DUMMY && !$profile->isJoinedTo_ONL() )//if ( !$profile->isJoinedTo_ONL() /*2013-02-04	&& isset($_POST['joinONL']) && ($_POST['joinONL']=='on' || $_POST['joinONL']=='1')*/ )
				if ( !CAM_DUMMY && !$profile->isJoinedTo_ONL() && isset($_POST['joinONL']) && ($_POST['joinONL']=='on' || $_POST['joinONL']=='1') )
    			{
    				
    				$priceID = $this->priceId;
    				
    				if (CHelperPayment::getCamsWay($userId)=='nb')
    				{
    					$modelPayment = new Payment_netbilling_2_cams();
	    				
    					$res = $modelPayment->startTransaction(Yii::app()->user->id, $priceID, $camsStep2_post);
						
	    				if (isset($res['url']))
						{
							$redirectUrl = $res['url'];
						}
						elseif (isset($res['success']))
						{
							$redirectUrl = $this->afterInit($res['success']);
						}
						else
						{
							$redirectUrl = SITE_URL.'/join/step4';
						}    					
    				}
    				elseif (CHelperPayment::getCamsWay($userId)=='rg')
    				{

    					$modelPayment = new Payment_rg_cams();
    					
    					$res = $modelPayment->startTransaction(Yii::app()->user->id, $priceID, $camsStep2_post);
						
	    				if ($res)
						{
							$redirectUrl = $this->doNLC_auth(false, array('1'=>'1'), $camsStep2_post);//$this->afterInit($res['success']);
						}
						else
						{
							$redirectUrl = SITE_URL.'/join/step4';
						}     					
    				}
    				elseif (CHelperPayment::getCamsWay($userId)=='payon')
    				{
    					$modelPayment = new Payment_wirecard_payon_cams();
    					
    					$res = $modelPayment->startTransaction(Yii::app()->user->id, $priceID, $camsStep2_post);
						
	    				if ($res)
						{
							$redirectUrl = $this->doNLC_auth(false, array('1'=>'1'), $camsStep2_post);//$this->afterInit($res['success']);
						}
						else
						{
							$redirectUrl = SITE_URL.'/join/step4';
						}       					
    				}
    				else
    				{
    					die('error');
    				}

					return $redirectUrl;
    			}
    			else
    			{ 
    				if (CHelperPayment::getCamsWay($userId)=='nb')
    					return $this->doNLC_auth();
    				else
    					return $this->doNLC_auth(false, array(), $camsStep2_post);
    			}					
    		}
    		
    		
    		
    		
/*    		
			$this->render(
    			'step3', 
    			array(
    				'profile'=>$profile,	
    				'profileData'=>$profile->getData(),
    			)
    		);*/
    	}
    	else
    	{
    		Yii::app()->user->logout();
    		return SITE_MAIN_URL;//$this->redirect(SITE_MAIN_URL);
    	}
    }    
    
    
    public function doNLC_auth($forceEMP=false, $basedOnONL=array(), $rg_post=array())
    {

    	$userId = Yii::app()->user->id;
    	
    	if (!$userId)
    		return '/';//return '/join';//$this->redirect();    	
    	
		$profile = new Profile($userId);
			    	
    	//if allready done step3
		if ($profile->isJoinedTo_NLC())
		{
	    	$url_1_click = $camsModel->getWhiteLabel_1_click_link($userId);
	    		
	    	if ($url_1_click)
	    	{
	    		if (LOCAL_OK) CHelperSite::vd("LOCAL_OK (step3): ".$url_1_click);
	    			
				return $url_1_click;//	    		$this->redirect($url_1_click);    			
	    	}				
		}    	
    	
    	if (CHelperPayment::getCamsWay($userId)=='nb')
    	{
			//post data losted...
	    	if (!isset(Yii::app()->session['camsStep2_post']))
	    	{
	    		return '/join/step4';//$this->redirect('/join/step4');
	    	}
	    	
	    	$camsStep2_post = Yii::app()->session['camsStep2_post'];
	
	    	if ( !DEBUG_IP && isset(Yii::app()->session['camsStep2_post']) )
				unset(Yii::app()->session['camsStep2_post']);      	
    	}
    	else
    	{
    		$camsStep2_post = $rg_post;
    	}
    	
    	
		$camsModel = new Cams();
    		
    	//AUTH NLC
    	$priceID=8;
    	if (CHelperPayment::getCamsWay($userId)=='nb')
    	{
    		$modelPayment_sub = new Payment_netbilling_2_cams_sub();
    		$camJoined = $modelPayment_sub->startTransaction($userId, $priceID, $camsStep2_post, $forceEMP, $basedOnONL);
    	}
    	elseif (CHelperPayment::getCamsWay($userId)=='rg')
    	{
    		$modelPayment_sub = new Payment_rg_cams();
    		$camJoined = $modelPayment_sub->startTransaction($userId, $priceID, $camsStep2_post, 'auth', $basedOnONL);
    	}
    	elseif (CHelperPayment::getCamsWay($userId)=='payon')
    	{
    		$modelPayment_sub = new Payment_wirecard_payon_cams();
    		$camJoined = $modelPayment_sub->startTransaction($userId, $priceID, $camsStep2_post, 'auth', $basedOnONL);    	
    	}
    	else
    	{
    		die('error');
    	}
    	
    	if ($camJoined)
			$url = $camsModel->getWhiteLabel_1_click_link($userId);
		else
			$url = SITE_URL.'/join/step4';
    	
		if (DEBUG_IP)
			CHelperSite::vd("REDIRECT TO: ".$url);
		else
			return $url;//$this->redirect($url);
    }
    
    
    /**
     * STEP 4
     */
    public function actionStep4()    
    {
    	//Yii::app()->session['zsignup_paymod_next'] = ZSIGNUP_BACKUP_BILLER;
    	
    	Yii::app()->session['MC_step4']=true;//$_SERVER['HTTP_REFERER'] not works on ssl page on PM server...
    	
    	$this->render('step4');
    }    
    
    
    
	public function actionNormal(){
		Yii::app()->session['device-type'] = 'pc';
		$this->redirect('/');//$this->redirect('/join/');
	}
	

	
	
    
}









