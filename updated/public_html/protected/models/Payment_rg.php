<?php

//http://pm.lo/paymentapi/rg_approved?flag=13d7e73b676&id=18&invoiceID=18&mp=1&udf01=594835&hash=i8vBpUC%2F%2F7BnGsKc3oBmsKDcRlI%3D
//http://pinkmeets.com/paymentapi/rg_approved?flag=13d7e7be98e&id=11168&invoiceID=11168&mp=1&udf01=601465&hash=7sNWFlZH%2BEJ7qbiL3hYLsOxgVj0%3D

//4012888888881881


class Payment_rg extends Payment
{
	private $pmName = 'rg'; 
	
	private $z_id = 0;//zom internal transaction id
	
	public $availablePriceIds = array(1, /* skype 2013-06-17	2*/ /*, 		100*/);
	public $defaultPriceId = 1;
	
	private $allin1Mode = true;//using for /allin1 or /payment
	
	private $rg_MerchantID;
	private $rg_HashSecret;
	private $rg_url;
	
	private $rg_Password="xxx";//uses for tests in test mode

	private $testMode=false;
	
	public function __construct($id=0, $allin1Mode=true)
	{
		parent::__construct($id, $this->pmName);
		
		if (DEBUG_IP) $this->testMode=true;
		
		if ($this->testMode)
		{
			$this->rg_MerchantID="xxx";
			$this->rg_HashSecret="xxx";
			$this->rg_url="https://dev-secure.rocketgate.com/hostedpage/servlet/HostedPagePurchase?";
		}
		else
		{
			$this->rg_MerchantID="xxx";
			$this->rg_HashSecret="xxx";
			$this->rg_url="https://secure.rocketgate.com/hostedpage/servlet/HostedPagePurchase?";		
		}
		
		$this->allin1Mode = $allin1Mode;
	} 

	
	public function startTransaction($user_id, $price_id)
	{
		parent::startTransaction($user_id, $price_id, 0, true);
		
		
		if ($this->errors)
			return false;
		
		$option = CHelperPayment::getOptionRow($price_id);
		$amount = ($option['price_trial']!=0) ? $option['price_trial'] : $option['price'];
		$amount_rebill = $option['price'];
		$term_trial = ($option['term_trial']!=0) ? $option['term_trial'] : $option['term'];
		$term = $option['term'];
		
		$userData = Yii::app()->user->Profile->getData();
		
		$sql = "INSERT INTO `pm_rg_trn` (
			`trn_id`,
			`user_id`,
			`price_id`,
			`added`			
		) VALUES (
			:trn_id,
			:user_id,
			:price_id,
			NOW()
		)";
		
		Yii::app()->db->createCommand($sql)
	    	->bindValue(":trn_id", 		$this->id, 			PDO::PARAM_INT)
	    	->bindValue(":user_id", 	$this->user_id, 	PDO::PARAM_INT)
	    	->bindValue(":price_id", 	$this->price_id, 	PDO::PARAM_INT)
			->execute();		
		
		//$this->z_id = Yii::app()->db->lastInsertId;		
		
			
		if ($this->allin1Mode)
			$return_url_decline = SITE_URL."/allin1/step4";
		else
			$return_url_decline = SITE_URL."/payment/declined";			
			
			
		$data = array(
			'id'=>$this->id,//trn_id
			'merch'=>$this->rg_MerchantID,
			'amount'=>$amount,
			//'method'=>"CC",
			'purchase'=>"ON",
			'avs'=> ($this->testMode) ? "NO" : "YES",
			'scrub'=> ($this->testMode) ? "NO" : "YES",
			'email'=>$userData['email'],//urlencode($userData['email']),
			'invoice'=>$this->id,
			//''=>,
			'rebill-amount'=>$amount_rebill,
			
			'udf01'=>$userData['id'],
			/////'rebill-count'=>120,//12m*10years
			'rebill-start'=>$term_trial,
			'rebill-freq'=>$term,
			'mp'=>$userData['affid'],
			'username'=>$userData['username'],//urlencode($userData['username']),
			'pw'=>CSecur::decryptByLikeNC( $userData['passwd'] ),//urlencode( CSecur::decryptByLikeNC( $userData['passwd'] ) ),
			'prodid'=>$price_id,
			'success'=>SITE_URL."/paymentapi/rg_approved",//urlencode( SITE_URL."/payment/approved" ),
			'fail'=>$return_url_decline,//urlencode( SITE_URL."/payment/declined" ),
		);	
		
		$hashArgs = urldecode(http_build_query($data))."&secret=".$this->rg_HashSecret;
		
		$sha1Hash = hash("sha1", $hashArgs, true);
		$b64 = base64_encode($sha1Hash);
		$hash = urlencode($b64);
		
		$url = $this->rg_url . http_build_query($data) . "&hash=".$hash;
		
		CHelperLog::logFile('pm_rg_submit.log', $url);	
		
if (DEBUG_IP) CHelperSite::vd($url);		
		
		return $url;
	}	
	
	
	
	/*
	 * calls from /payment/approve
	 * first pay
	 */
	public function success()
	{
		require_once DIR_ROOT.'/protected/vendors/pmRocketGate/LinkReader.php';
		
		// It is important to confirm that the link is coming from RocketGate.
		// This is done by checking the hash value in the incoming URL against
		// our internally computed hash value.
		//
		// First, split the incoming URL to obtain everything after the "?".
		//
		list($uri_string, $values_string) = split('\?', $_SERVER['REQUEST_URI']);		
		
		//
		// Create a LinkReader.php class instance to check the hash
		// contained in the URL.
		//
		$link_reader = new LinkReader($this->rg_HashSecret);		
		

		// Confirm that the incoming link is from RocketGate
		if($link_reader->ParseLink($values_string) != 0){
		  //
		  // Either this link was not made by RocketGate, or there is a
		  // problem with the secret key
		  //
		  die("Link contains invalid hash value!!!<br/>\n");
		}		
		
		
		//SUCCESS!!
		//die('SUCCESS!!');
		///id=18&invoiceID=18&mp=1&udf01=594835
		$this->id = intval($_REQUEST['id']);
		if (!$this->id)
		{
			die('Error, wrong trn id');
		}
		
        $trn = self::getTransactionInfo($this->id);
		if (!$trn)
		{
			die('Error, wrong trn');
		}
        
		//check duplicate calls
		if ($trn['status']=='completed')
        	return true;
        
        $this->user_id = $trn['user_id'];

       		
        // update zom transaction id and SUBSCRIPTION_ID
        $sql = "UPDATE LOW_PRIORITY `pm_rg_trn` SET `action`='approved', responce=:responce WHERE trn_id=:trn_id LIMIT 1";
	    Yii::app()->db->createCommand($sql)
	    	->bindValue(":responce", serialize($_REQUEST), PDO::PARAM_STR)
			->bindValue(":trn_id", $this->id, PDO::PARAM_INT)
			->execute();       		
       		
		
        //COMPLETE transaction
        $this->CompleteTransaction();
        
        $profile = new Profile($this->user_id);
        
		//UPGRADE USER TO GOLD
		$profile->Upgrade($this->id);		
		
		
		$log = $_SERVER['REQUEST_URI']."    - ".date("r") . " / " .CHelperLocation::getIPReal()."\n";
		CHelperLog::logFile('pm_rg_approved.log', $log);
		
		return true;
	}

	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	

	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	//Test #6 – Sale of 3 Day Trial Subscription Which Renews to $9.99 Monthly
	public function startTransaction_TEST6($user_id, $price_id)
	{
		
		parent::startTransaction($user_id, $price_id);
		
		
		if ($this->errors)
			return;
		
		$option = CHelperPayment::getOptionRow($price_id);
		$amount = ($option['price_trial']!=0) ? $option['price_trial'] : $option['price'];
		$amount_rebill = $option['price'];
		$term_trial = ($option['term_trial']!=0) ? $option['term_trial'] : $option['term'];
		$term = $option['term'];
		
		$userData = Yii::app()->user->Profile->getData();
		
		$sql = "INSERT INTO `pm_rg_trn` (
			`trn_id`,
			`user_id`,
			`price_id`,
			`added`			
		) VALUES (
			:trn_id,
			:user_id,
			:price_id,
			NOW()
		)";
		
		Yii::app()->db->createCommand($sql)
	    	->bindValue(":trn_id", 		$this->id, 			PDO::PARAM_INT)
	    	->bindValue(":user_id", 	$this->user_id, 	PDO::PARAM_INT)
	    	->bindValue(":price_id", 	$this->price_id, 	PDO::PARAM_INT)
			->execute();		
		
		//$this->z_id = Yii::app()->db->lastInsertId;		
		
			
		$data = array(
			'id'=>$this->id,//trn_id
			'merch'=>$this->rg_MerchantID,
			'amount'=>$amount,
			//'method'=>"CC",
			'purchase'=>"ON",
			'avs'=> "YES",
			'scrub'=> "NO",// "YES",
			'email'=>$userData['email'],//urlencode($userData['email']),
			'invoice'=>$this->id,
			//''=>,
			'rebill-amount'=>$amount_rebill,
			
			'udf01'=>$userData['id'],
			//'rebill-count'=>120,//12m*10years
			'rebill-start'=>$term_trial,
			'rebill-freq'=>$term,
			'mp'=>$userData['affid'],
			'username'=>$userData['username'],//urlencode($userData['username']),
			'pw'=>CSecur::decryptByLikeNC( $userData['passwd'] ),//urlencode( CSecur::decryptByLikeNC( $userData['passwd'] ) ),
			'prodid'=>$price_id,
			'success'=>SITE_URL."/paymentapi/rg_approved",//urlencode( SITE_URL."/payment/approved" ),
			'fail'=>SITE_URL."/payment/declined",//urlencode( SITE_URL."/payment/declined" ),
			
		
		
			'fname'=>' John',
			'lname'=>'Test',
			'address'=>'1234 Main Street',
			'city'=>'Las Vegas',
			'state'=>'NV',
			'zip'=>'89141',
			'country'=>'US',
		);	
		
		$hashArgs = urldecode(http_build_query($data))."&secret=".$this->rg_HashSecret;
		
		$sha1Hash = hash("sha1", $hashArgs, true);
		$b64 = base64_encode($sha1Hash);
		$hash = urlencode($b64);
		
		$url = $this->rg_url . http_build_query($data) . "&hash=".$hash;
		
		//CHelperLog::logFile('pm_rg_submit.log', $url);	
		
//if (DEBUG_IP) CHelperSite::vd($url);		
		
		return $url;
	}		
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
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
			CHelperLog::logFile('pm_rg_postback_ERR.log', $logErr ." : ". var_export($xml, true));
			return false;			
		}
		
		$sql = "SELECT * FROM `pm_rg_trn` WHERE trn_id={$this->id} AND action='approved' ORDER BY id LIMIT 1";
		$initialTrn = Yii::app()->db->createCommand($sql)->queryRow($sql);
		if (!$initialTrn) 
		{
			$logErr .= "Initial trn not found";
			CHelperLog::logFile('pm_rg_postback_ERR.log', $logErr ." : ". var_export($xml, true));
			return false;			
		}		
		
		$this->user_id = $initialTrn['user_id'];
		if (!$this->user_id) 
		{
			$logErr .= "wrong user {$this->user_id} ";
			CHelperLog::logFile('pm_rg_postback_ERR.log', $logErr ." : ". var_export($xml, true));
			return false;			
		}			
		
		
		//$profile = new Profile($this->user_id);
		$trnId = parent::Rebill();
		
		if (!$trnId) 
		{
			$logErr .= "rebill";
			CHelperLog::logFile('pm_rg_postback_ERR.log', $logErr ." : ". var_export($xml, true));
			return false;			
		}		
		
		$sql = "INSERT INTO `pm_rg_trn` (
			`trn_id`,
			`user_id`,
			`price_id`,
			`action`,
			`added`			
		) VALUES (
			:trn_id,
			:user_id,
			:price_id,
			'rebill',
			NOW()
		)";
		
		Yii::app()->db->createCommand($sql)
	    	->bindValue(":trn_id", 		$trnId, 			PDO::PARAM_INT)
	    	->bindValue(":user_id", 	$this->user_id, 	PDO::PARAM_INT)
	    	->bindValue(":price_id", 	$this->price_id, 	PDO::PARAM_INT)
			->execute();		
		
		return true;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	

	

	
	
	
	
	
    
    
    /*
     * RG tests...
     */
	public function dev_tests()
	{
//passed
//die('PASSED');
		
				
		if (!$this->testMode) die ('Needs test mode');
		
		include_once DIR_ROOT.'/protected/vendors/pmRocketGate/GatewayService.php';
		include_once DIR_ROOT.'/protected/vendors/pmRocketGate/devTests.php';
		
		$rg_user_id=1;
		$rg_this_id=12345678;    	
		
		
//		dev_test_1($this->rg_MerchantID, $this->rg_Password, $rg_user_id, $rg_this_id);
//		dev_test_2($this->rg_MerchantID, $this->rg_Password, $rg_user_id, $rg_this_id);
//		dev_test_3($this->rg_MerchantID, $this->rg_Password, $rg_user_id, $rg_this_id);
//		dev_test_4($this->rg_MerchantID, $this->rg_Password, $rg_user_id, $rg_this_id);
//		dev_test_5($this->rg_MerchantID, $this->rg_Password, $rg_user_id, $rg_this_id);
		dev_test_6($this->rg_MerchantID, $this->rg_Password, $rg_user_id, $rg_this_id);
		
	}	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}