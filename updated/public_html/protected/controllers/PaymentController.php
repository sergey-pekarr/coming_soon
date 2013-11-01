<?php

class PaymentController extends Controller
{
	
	public function init()
	{
		parent::init();
		$this->layout='//layouts/payment';
	}	
	
	public function actionIndex()
	{
		if ( 
				!Yii::app()->user->id
				||
				!Yii::app()->user->checkAccess('free') || Yii::app()->user->checkAccess('gold') 
				/*2013-02-18
				|| 
				(
					Yii::app()->user->Profile->getDataValue('form')!='1'
					&&
					Yii::app()->user->Profile->getDataValue('form')!='88'
				)*/
		)
		{
			$this->redirect('/');
		}	
		else
		{
			if (HelperDuplicates::checkExistedPaidIP())
				$this->redirect( "/site/errors" );
			
			
			if (MAIN_BILLER=='rg2')
			{
		    	//check SSL
		    	if (LOCAL)
		    		$ssl = ($_SERVER['SERVER_PORT'] == '443');
		    	else
					$ssl = true;//done in nginx	(isset($_SERVER['HTTP_HTTPS']));    		
				if ( !$ssl )
					$this->redirect( SITE_URL_SSL."/payment" );
			}
			
			$params = $this->preProcessLink();
			
			/*if (Yii::app()->user->id && Yii::app()->user->Profile->getDataValue('username')=='tester')
				$params['paymod'] = 'segpay';
			else*/
				$params['paymod'] = MAIN_BILLER; 
			
			$this->render('index', $params);
		} 
	}    
	
	/*
	 * use for Payment forms only (netbilling_2, etc)
	 */
	public function actionProccess()
	{
		//$price_id = 1;
		//$price_id = (isset($_REQUEST['p'])) ? intval($_REQUEST['p']) : 1;
		
		switch (MAIN_BILLER)
		{
			case 'rg2': 
				$modelPayment = new Payment_rg2(0, false);
				break;
			default: 
				echo 'error...';
				die();
		}
		
		$model = new PaymentForm;
		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='PaymentForm')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['PaymentForm']))
		{
            $model->attributes=$_POST['PaymentForm'];
            if($model->validate())
            {
            	$res = $model->doProccess($modelPayment);//Yii::app()->end();
            	echo $res;
            	Yii::app()->end();

            }
		}	
	}

    
	/*
	 * use without payment form - zombaio, etc
	 */
	public function actionRedirection()
	{
		//$paymod = MAIN_BILLER;//$_REQUEST['m'];
		$price_id = (isset($_REQUEST['p'])) ? intval($_REQUEST['p']) : 1;
		
		if (MAIN_BILLER=='zombaio')
		{
			if ($price_id==2)
				$paymod = 'zombaio2';
			else 
				$paymod = 'zombaio';		
		}
		else
			$paymod=MAIN_BILLER;
		
		/*if (Yii::app()->user->Profile->getDataValue('username')=='tester')
			$paymod='segpay';*/
		
		$redirectUrl = SITE_URL;
		
        if ($paymod == 'zombaio')
        {
        	$modelPayment = new Payment_zombaio( 0, false );
        	$redirectUrl = $modelPayment->startTransaction( Yii::app()->user->id, $price_id );        	
        }
	    if ($paymod == 'zombaio2')
        {
        	$modelPayment = new Payment_zombaio2( 0, false );        	
        	$redirectUrl = $modelPayment->startTransaction( Yii::app()->user->id, $price_id );        	
        }
	    if ($paymod == 'segpay')
        {
        	$modelPayment = new Payment_segpay( 0, false );
        	
        	$redirectUrl = $modelPayment->startTransaction( Yii::app()->user->id, $price_id );        	
        	
        	//$this->render('contentSegpay', array('redirectUrl'=>$redirectUrl));
        }        
	    if ($paymod == 'rg')
        {
        	$modelPayment = new Payment_rg(0, false );
        	
        	$redirectUrl = $modelPayment->startTransaction( Yii::app()->user->id, $price_id );        	
        	
        	//$this->render('contentSegpay', array('redirectUrl'=>$redirectUrl));
        }         
        
        if (!$redirectUrl)
			$redirectUrl="/site/errors";        
        
        $this->redirect($redirectUrl);
	}
	
	
	private function preProcessLink(){
		if(isset($_GET['id'])){
			$id = $_GET['id'];
			$idlen = strlen($id);
					
			if($idlen == 37){
				$encProfileId = substr($id, 5,32);
				$profileId = Yii::app()->secur->decryptID($encProfileId);
				$id = substr($id, 0, 5);
				if(!$id){
					return array();
				}
				$profile = new Profile($profileId);
				$targetName = $profile->getDataValue('username');
				$defines = array(
					'73ff4' => array('sendmessage', "You Must Upgrade To Send <strong>$targetName</strong> A Private Message","Your Message Has NOT been Sent!"),
					'70134' => array('sendfavourite', "You Must Upgrade To add <strong>$targetName</strong> as a favourite","You Must Upgrade To Favourite"),
					'00384' => array('sendgift', "You Must Upgrade To Send <strong>$targetName</strong> as a gift","Your Gift Has NOT been Sent!"),
					);
				if(isset($defines[$id])){
					$item = $defines[$id];
					return array('action' => $item[0], 'profileid' => $profileId, 'title' => $item[1], 'desc' => $item[2]);
				}
				else {
					return array();
				}
			}
			else if($idlen ==5){
				$defines = array(
					'5de8g' => array('viewfavourite', 'You Must Upgrade To see Who Favours You','See who is interested in your profile'),
					'356f5' => array('viewemail', 'To read more messages you must upgrade','To upgrade your account, please complete the details below to subscribe now!'),
					'acfcb' => array('viewlargephoto', 'You Must Upgrade To See Large Photos','As a free member you can only view thumbnail photos. To see full size photos you must upgrade to our Premium Plan'),
					'2d97e' => array('searchmore', 'You Must Upgrade To View More Search Results','To view the rest of the profiles in your search results you need to upgrade your account, please complete the details below to subscribe now!'),
					'5f580' => array('onlinemore', 'You Must Upgrade To View More Online Members','To view the rest of the online profiles available you need to upgrade your account, please complete the details below to subscribe now!'),
					);
				if(isset($defines[$id])){
					$item = $defines[$id];
					return array('action' => $item[0], 'title' => $item[1], 'desc' => $item[2]);
				}
			}
			else {
				return array();
			}
		}
	}
	
	
	
	
	
	
	public function actionApproved()
	{
		$this->layout = '//layouts/static2' ;
		$this->render('approved');
	}
	
	public function actionDeclined()
	{
		if (Yii::app()->user->id)
		{
			$form = Yii::app()->user->Profile->getDataValue('form');
			if ($form == '93')
				$this->redirect(SITE_URL."/allin1/step4");
			if ($form == '932')
				$this->redirect(SITE_URL."/allin9/step4");
		}
		
		
		$this->layout = '//layouts/static2' ;
		$this->render('declined');
	}	
	
	
	
	
	
	
	
	
	
	
	//helper for /zsignup (hides real http_referer)
	public function actionRedirectToPaymentPage()
	{
		if (isset(Yii::app()->session['RedirectToPaymentPage']))
		{
			$url = Yii::app()->session['RedirectToPaymentPage'];
			unset(Yii::app()->session['RedirectToPaymentPage']);
			
			$this->redirect($url);
		}
		else 
			$this->redirect('/');
		
	}
	
	
	
}