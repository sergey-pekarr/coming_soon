<?php

//test card 4012888888881881

include_once DIR_ROOT.'/protected/vendors/pmRocketGate/GatewayService.php';

class Payment_rg2 extends Payment
{
	private $pmName = 'rg2';

	public $availablePriceIds = array(1,2);//ready for monthly, quarterly, annually rebills only
	public $defaultPriceId = 1;
	public $availablePriceRebillsIds = array(1,2);//must include all current and old prices used for sales
	
	//private $zsignupMode = false;
	
	public $testMode = false;//TEST / LIVE
	

	private $rg_MerchantID;
	private $rg_Password;
	

	
	public function __construct($id=0, $zsignupMode=false)
	{
		parent::__construct($id, $this->pmName);

		if ( defined('DEBUG_IP') && DEBUG_IP) $this->testMode = true;

		/*if ($this->testMode)
		{
			$this->rg_MerchantID="xxx";
			$this->rg_Password="xxx";
		}
		else*/
		{
			$this->rg_MerchantID="xxx";
			$this->rg_Password="xxx";
		}	
	} 
	
	public function getMerchantID()
	{
		return $this->rg_MerchantID;
	}
	
	public function startTransaction($user_id, $price_id, $post, $cams_type='sale'/*auth or sale*/, $basedOnONL=array())
	{
//return false;
		
		if ( !in_array($cams_type, array('sale'/*,'auth'*/)) ) die ('cams_type error');
		
		/*if ($cams_type=='auth')
		{
			if (isset($basedOnONL) && is_array($basedOnONL) && !empty($basedOnONL))
				$amountFinal = 0;
			else
				$amountFinal = 2;// $0 not works on RG!!! (tried in test mode only)
		
			parent::startTransaction($user_id, $price_id,	$amountFinal);	
		}
		else*/
		{
			//email 2013-10-14: Prevent mastercard signups
			if ( !CONSOLE && CHelperPayment::detectCardType($post['ccnum'])=='mastercard')
			{
				Yii::app()->user->setFlash('errorCustom',"Sorry - we do not accept Mastercard, please try a different card instead");
				return false;
			}			
			
			
			parent::startTransaction($user_id, $price_id, 0, true);	
		}

			
		
		$url = "";
		
		if ($this->errors)
		{
			FB::error($this->errors);
			return false;
		}
//FB::error($post, '$post');			
		
		$optionRow = CHelperPayment::getOptionRow($price_id);//$optionRow = $this->getOptionRow($price_id);
		/*if ($cams_type=='auth')
			$price_trial = $amountFinal;
		else*/
			$price_trial = ($optionRow['price_trial']!=0) ? $optionRow['price_trial'] : $optionRow['price'];//$optionRow['price_trial'];
		$price = $optionRow['price'];//$optionRow['price_trial'];
		$term_trial = ($optionRow['term_trial']!=0) ? $optionRow['term_trial'] : $optionRow['term'];//$optionRow['term_trial'];
		$term = $optionRow['term'];//$optionRow['term_trial'];
		
		switch ( ceil($term/30) )
		{
			//case 1: $REBILL_FREQUENCY = "MONTHLY"; break;
			case 3: $REBILL_FREQUENCY = "QUARTERLY"; break;
			case 6: $REBILL_FREQUENCY = "SEMI-ANNUALLY"; break;
			case 12: $REBILL_FREQUENCY = "ANNUALLY"; break;
			default: $REBILL_FREQUENCY = "MONTHLY"; break;
		}
		
		$visitor_ip = CHelperLocation::getIPReal();
		
		$profile = new Profile($user_id);
		$affid = $profile->getDataValue('affid');
		
		$cardhash = "";//MD5('card_number'.$post['ccnum']);			
		
		$sql = "INSERT INTO pm_rg2_trn
				(
					`trn_id`,
					`user_id`,
					`ts`,
					`date`,
					`type`,
					`amount`,
					`price_id`,
					`cardhash`,
					`visitor_ip`,
					`aff_id`
				
				) VALUES (
					{$this->id},
					{$user_id},
					UNIX_TIMESTAMP(), 
					CURDATE(), 
					'initial', 
					'{$price_trial}',
					{$price_id},
					'{$cardhash}',
					'{$visitor_ip}',
					{$affid}
				)";
        Yii::app()->db->createCommand($sql)->execute();
        
	    $trnId = Yii::app()->db->lastInsertId;//nb_2_id   			
			
        if (!$trnId)
        	return false;//die('DB error');			
		
		
		//
		//	Allocate the objects we need for the test.
		//
		$request = new GatewayRequest();
		$response = new GatewayResponse();
		$service = new GatewayService();


		$request->Set(GatewayRequest::MERCHANT_ID(), $this->rg_MerchantID);
		$request->Set(GatewayRequest::MERCHANT_PASSWORD(), $this->rg_Password);
		
		// For example/testing, we set the order id and customer as the unix timestamp as a convienent sequencing value
		// appending a test name to the order id to facilitate some clarity when reviewing the tests
		$request->Set(GatewayRequest::MERCHANT_CUSTOMER_ID(), $this->id/*$user_id*/);
		$request->Set(GatewayRequest::MERCHANT_INVOICE_ID(), $this->id);
		
		$request->Set(GatewayRequest::CURRENCY(), "USD");
		$request->Set(GatewayRequest::AMOUNT(), $price_trial);    // bill x.xx now
		
		$request->Set(GatewayRequest::CARDNO(), $post['ccnum']);
		$request->Set(GatewayRequest::EXPIRE_MONTH(), $post['ccmon']);
		$request->Set(GatewayRequest::EXPIRE_YEAR(), $post['ccyear']);
		$request->Set(GatewayRequest::CVV2(), $post['ccvv']);
		
		$request->Set(GatewayRequest::CUSTOMER_FIRSTNAME(), $post['firstname']);
		$request->Set(GatewayRequest::CUSTOMER_LASTNAME(), $post['lastname']);
		$request->Set(GatewayRequest::EMAIL(), $post['email']);
		$request->Set(GatewayRequest::IPADDRESS(), $visitor_ip);
		
		$request->Set(GatewayRequest::BILLING_ADDRESS(), $post['address']);
		$request->Set(GatewayRequest::BILLING_CITY(), $post['city']);
		$request->Set(GatewayRequest::BILLING_STATE(), (($post['country']=='US') ? $post['state'] : '') );
		$request->Set(GatewayRequest::BILLING_ZIPCODE(), $post['zip']);
		$request->Set(GatewayRequest::BILLING_COUNTRY(), $post['country']);
		
		// Risk/Scrub Request Setting
		$request->Set(GatewayRequest::SCRUB(), (($this->testMode)?"IGNORE":"YES") );
		$request->Set(GatewayRequest::CVV2_CHECK(), (($this->testMode)?"IGNORE":"YES") );
		$request->Set(GatewayRequest::AVS_CHECK(), (($this->testMode)?"IGNORE":"YES") );
		
		
		if ($cams_type=='sale')
		{
			//recurring
			$request->Set(GatewayRequest::REBILL_AMOUNT(), $price);
			/////$request->Set(GatewayRequest::REBILL_COUNT(), 120);//12m*10years
			//$request->Set(GatewayRequest::REBILL_END_DATE(), '2023-04-12');
			$request->Set(GatewayRequest::REBILL_FREQUENCY(), $REBILL_FREQUENCY);
			$request->Set(GatewayRequest::REBILL_START(), $term_trial);
		}
		//FB::error($request);
		
		
		//
		//	Setup test parameters in the service and
		//	request.
		//
		if ($this->testMode) $service->SetTestMode(TRUE);
		
		$rg_response = array();
//CARD_HASH
//
		//SALE		
		if ($cams_type=='sale')
		{
			//
			//	Perform the Purchase transaction.
			//
			if ($service->PerformPurchase($request, $response)) 
			{
				$success = true;				
				$status = 'approved';
			
				$rg_response["Response Code"] = $response->Get(GatewayResponse::RESPONSE_CODE());
				$rg_response["Reasone Code"] = $response->Get(GatewayResponse::REASON_CODE());
				$rg_response["Auth No"] = $response->Get(GatewayResponse::AUTH_NO());
				$rg_response["AVS"] = $response->Get(GatewayResponse::AVS_RESPONSE());
				$rg_response["CVV2"] = $response->Get(GatewayResponse::CVV2_CODE());
				$rg_response["GUID"] = $response->Get(GatewayResponse::TRANSACT_ID());
				$rg_response["Account"] = $response->Get(GatewayResponse::MERCHANT_ACCOUNT());
				$rg_response["Scrub"] = $response->Get(GatewayResponse::SCRUB_RESULTS());
				
				$rg_response["CARD_HASH"] = $response->Get(GatewayResponse::CARD_HASH());
				$cardhash=$rg_response["CARD_HASH"];
				
			} else {
				
				$success = false;				
				$status='declined';
				
				$rg_response["GUID"] = $response->Get(GatewayResponse::TRANSACT_ID());
				$rg_response["Response Code"] = $response->Get(GatewayResponse::RESPONSE_CODE());
				$rg_response["Reasone Code"] = $response->Get(GatewayResponse::REASON_CODE());
				$rg_response["Exception"] = $response->Get(GatewayResponse::EXCEPTION());
				$rg_response["Scrub"] = $response->Get(GatewayResponse::SCRUB_RESULTS());
			}
		}
		//AUTH		
		/*else if ($cams_type=='auth')
		{
			if (isset($basedOnONL) && is_array($basedOnONL) && !empty($basedOnONL))
			{
				$success = true;
				$status = 'approved';
				
				
				//find UniqueID from sale trn
				$sql = "SELECT * FROM pm_rg_cams_trn WHERE user_id={$user_id} AND `status`='approved' AND `type`='initial' AND cams_type='sale' ORDER BY id DESC LIMIT 1";
				$rowSale = Yii::app()->db->createCommand($sql)->queryRow();
				if (!$rowSale) return false;

				//$rg_response = unserialize($rowSale['response']);
				$cardhash=$rowSale["cardhash"];
			}
			else
			{
				//
				//	Perform the Auth-Only transaction.
				//
				if ($service->PerformAuthOnly($request, $response)) 
				{
					$success = true;
					$status = 'approved';
					$rg_response["Response Code"] = $response->Get(GatewayResponse::RESPONSE_CODE());
					$rg_response["Reasone Code"] = $response->Get(GatewayResponse::REASON_CODE());
					$rg_response["Auth No"] = $response->Get(GatewayResponse::AUTH_NO());
					$rg_response["AVS"] = $response->Get(GatewayResponse::AVS_RESPONSE());
					$rg_response["CVV2"] = $response->Get(GatewayResponse::CVV2_CODE());
					$rg_response["GUID"] = $response->Get(GatewayResponse::TRANSACT_ID());
					$rg_response["Account"] = $response->Get(GatewayResponse::MERCHANT_ACCOUNT());
					$rg_response["Scrub"] = $response->Get(GatewayResponse::SCRUB_RESULTS());
					
					$rg_response["CARD_HASH"] = $response->Get(GatewayResponse::CARD_HASH());
					$cardhash=$rg_response["CARD_HASH"];
					
				} else {
				  
					$success = false;
					$status = 'declined';				
					$rg_response["GUID"] = $response->Get(GatewayResponse::TRANSACT_ID());
					$rg_response["Response Code"] = $response->Get(GatewayResponse::RESPONSE_CODE());
					$rg_response["Reasone Code"] = $response->Get(GatewayResponse::REASON_CODE());
					$rg_response["Exception"] = $response->Get(GatewayResponse::EXCEPTION());
					$rg_response["Scrub"] = $response->Get(GatewayResponse::SCRUB_RESULTS());
				}			
			}
		}*/
		else
		{
			die('error...');
		}
		
//FB::warn($rg_response,'s');


		$sql = "UPDATE `pm_rg2_trn`
				SET response=:response, status=:status, cardhash=:cardhash
				WHERE id = {$trnId} LIMIT 1 ";
		Yii::app()->db->createCommand($sql)
				->bindValue(":response", serialize($rg_response), 	PDO::PARAM_STR)
				->bindValue(":status", 	 $status, 					PDO::PARAM_STR)
				->bindValue(":cardhash", $cardhash, 				PDO::PARAM_STR)
				->execute();


		//$card_number = preg_replace("/^[0-9]{12}/is", "************", $post['ccnum']);
		$this->updateCustomerInfo($post['ccname'], $post['ccnum']/*$card_number*/, $post['firstname'], $post['lastname'], $post['address'], $post['country'], $post['state'], $post['city'], $post['zip'], $post['email'], $visitor_ip, ""/*rg cardhash 64  $cardhash*/);

		//auth or sale was successful!
		$res = false;//res of trn
		if ($success)
		{
			//AUTH
			/*if ($cams_type=='auth')
			{
//FB::warn('1','s');
		        //COMPLETE transaction
		        $this->CompleteTransaction('authed', true);
//FB::warn('2','s');		        
				//reg user in whitelabel
				$modelCams = new Cams();
				if ($modelCams->regWhiteLabel($this->id))
				{
					//UPGRADE USER for stats
					$profile->settingsUpdate('cams_joined', '1');
					$profile->UpgradeCams($this->id, 0);				
					
					$res = true;//$modelCams->getWhiteLabel_1_click_link($this->user_id);//SITE_URL.'/payment/approved';
				}
				else
					$res = false;
//FB::warn($res,'s');
			}
			//SALE
			else if ($cams_type=='sale')*/
			{
				//COMPLETE transaction
			    $this->CompleteTransaction();
			        
				//UPGRADE USER TO GOLD
				$profile->Upgrade($this->id);
				
				//send welcome email!!!
				/*Yii::app()->mail->prepareMailHtml(
					$profile->getDataValue('email'),
					'',
					'welcome',
					array(
						'user_id'	=> $profile->getDataValue('id'),
					)
				);*/
				
				$res = true;
			}
		}			

			
		return $res;//true or false!!!
	}	

	
	
	/*
	 * ************************************************************************
	 * CAMS SALES
	 * ************************************************************************
	 */
	/*public function CamSales()	
	{
		$way='rg';

		if (!LIVE) return;
		$this->testMode = false;	
		
		if ( intval(date("H"))<=1 )
			$DELTA = 1;//days (==1 - yesterday and today)
		else
			$DELTA = 0;//today only
		
		$updated=0;
		$log  = "";//$log  = "------------------------------------------------------- \n";
		//$log .= date("r") . " started \n";

		do
		{
			$date= date("Y-m-d", strtotime("-{$DELTA} DAY"));

			$cams = new Cams();
			$result = $cams->XMLFeedOnCharge($date, $way);
			
			$log .= $date . ", NumUsersReturned: {$result['NumUsersReturned']} \n";
			
			if ($result['trns'])
			{
				foreach($result['trns'] as $user_id=>$trns)
				{

					
					
					if (!$trns) continue;
					
					$profile = NULL;
					$initialTrn=NULL;
					
					foreach ($trns as $t)
					{
						//trn isset in db?
						$sql = "SELECT id FROM `pm_rg_cams_trn` WHERE user_id=:user_id AND cams_streamate_trn_id=:cams_streamate_trn_id LIMIT 1";
						$exists = Yii::app()->db->createCommand($sql)
								->bindValue(":user_id", 				$user_id, 		PDO::PARAM_INT)
								->bindValue(":cams_streamate_trn_id", 	$t['TransID'], 	PDO::PARAM_INT)
								->queryScalar();

						if ($exists) continue;
						
						if ($profile==NULL)
							$profile = new Profile($user_id);
							
						//find initial AUTH transaction
						if ($initialTrn==NULL)
						{
							$sql = "SELECT * FROM pm_rg_cams_trn WHERE user_id='{$user_id}' AND cams_type='auth' AND `type`='initial' AND `status`='approved' ORDER BY id DESC LIMIT 1";
							$initialTrn = Yii::app()->db->createCommand($sql)->queryRow();
							if (!$initialTrn || !$initialTrn['trn_id'])
							{
								$log.= "\n ERROR: INITIAL TRANSACTION NOT FOUND. USER_ID: {$user_id} \n";
								break;// continue;
							}
						}								
			        	$price_id = $initialTrn['price_id'];
			        	$amount = $t['SuccessAmt']/100;						
							
						parent::startTransaction($user_id, $price_id, $amount);
		        	
			        	if ($this->errors)
			        	{
			        		$log .= "ERROR:  ".$user_id." \n";
			        		continue;
			        	}			        	
			        	
						$affid = $profile->getDataValue('affid');
						
						$sql = "INSERT INTO pm_rg_cams_trn
								(			
									`trn_id`,
									`user_id`,
									`cams_type`,
									`ts`,
									`date`,
									`type`,
									`amount`,
									`price_id`,
									`aff_id`
								
								) VALUES (
									{$this->id},
									{$user_id},
									'sale',
									UNIX_TIMESTAMP(), 
									CURDATE(), 
									'cam_sale', 
									'{$amount}',
									{$price_id},
									{$affid}
								)";
				        Yii::app()->db->createCommand($sql)->execute();
				        
			        	//COMPLETE transaction
			        	$this->CompleteTransaction('completed', true);		        	
	
			        	//UPGRADE USER for stats
			        	$profile->UpgradeCams($this->id, $amount);
			        	
			        	$updated ++;		        	
					}
				}
			}
			
//CHelperSite::vd($result, 0);
		}
		while ($DELTA--);
		
		$log.= "updated: ".$updated." \n";
		
		return $log;
	}*/
	
	
	
	//REBILL like rg postback
	public function Rebill($xml)
	{
		$res = HelperXML::xml2ary($xml);
		//
		$logErr = "ERR: ";
		
		if (!isset($res["RecurringBilling"])) 
		{
			$logErr .= "RecurringBilling";
			// not error, can be cancel..,creditvoid,...  CHelperLog::logFile('pm_rg_postback_ERR.log', $logErr ." : ". var_export($xml, true));
			return true;//return false;			
		}
			
		//initial rg trn id
		$this->id = (isset($res["RecurringBilling"]["_c"]["customerID"]['_v'])) ? intval($res["RecurringBilling"]["_c"]["customerID"]['_v']) : 0;
		if (!$this->id) 
		{
			$logErr .= "customerID";
			CHelperLog::logFile('pm_rg2_postback_ERR.log', $logErr ." : ". var_export($xml, true));
			return false;			
		}
		
		$sql = "SELECT * FROM `pm_rg2_trn` WHERE trn_id={$this->id} AND `status`='approved' ORDER BY id LIMIT 1";
		$initialTrn = Yii::app()->db->createCommand($sql)->queryRow($sql);
		if (!$initialTrn) 
		{
			$logErr .= "Initial trn not found";
			CHelperLog::logFile('pm_rg2_postback_ERR.log', $logErr ." : ". var_export($xml, true));
			return false;			
		}		
		
		$this->user_id = $initialTrn['user_id'];
		if (!$this->user_id) 
		{
			$logErr .= "wrong user {$this->user_id} ";
			CHelperLog::logFile('pm_rg2_postback_ERR.log', $logErr ." : ". var_export($xml, true));
			return false;			
		}			
		
		
		//$profile = new Profile($this->user_id);
		$trnId = parent::Rebill();
		
		if (!$trnId) 
		{
			$logErr .= "rebill";
			CHelperLog::logFile('pm_rg2_postback_ERR.log', $logErr ." : ". var_export($xml, true));
			return false;			
		}		
		
		$sql = "INSERT INTO `pm_rg2_trn` (
			`trn_id`,
			`user_id`,
			`price_id`,
			`status`,
			`type`,
			`ts`,
			`date`			
		) VALUES (
			:trn_id,
			:user_id,
			:price_id,
			'approved',
			'renew',
			UNIX_TIMESTAMP(), 
			CURDATE() 
		)";
		
		Yii::app()->db->createCommand($sql)
	    	->bindValue(":trn_id", 		$trnId, 			PDO::PARAM_INT)
	    	->bindValue(":user_id", 	$this->user_id, 	PDO::PARAM_INT)
	    	->bindValue(":price_id", 	$this->price_id, 	PDO::PARAM_INT)
			->execute();		
		
		return true;
	}
  
}