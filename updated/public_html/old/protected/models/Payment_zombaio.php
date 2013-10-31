<?php
class Payment_zombaio extends Payment
{
	private $pmName = 'zombaio'; 
	
	private $z_id = 0;//zom internal transaction id
	
	public $availablePriceIds = array(1);
	public $defaultPriceId = 1;
	
	private $allin1Mode = true;//using for /allin1 or /payment
	
	private $zom_SiteId = "xxx";
	private $zom_GWPass = "xxx";
	
	
	public function __construct($id=0, $allin1Mode=true)
	{
		parent::__construct($id, $this->pmName);
		
		$this->allin1Mode = $allin1Mode;
	} 

	
	public function startTransaction($user_id, $price_id)
	{
		parent::startTransaction($user_id, $price_id, 0, true);
		
		
		if ($this->errors)
			return false;
		
		$sql = "INSERT INTO `pm_zombaio_trn` (
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
		

		$return_url = SITE_URL;
		if ($this->allin1Mode)
			$return_url_decline = urlencode( SITE_URL."/allin1/step4" );
		else
			$return_url_decline = urlencode( SITE_URL );
		$return_url_approve = urlencode( SITE_URL."/payment/approved" );
		
		$extra = "int_trn={$this->id};user_id={$this->user_id}";
		
		$profile = new Profile($this->user_id);
		
		
		$FIRSTNAME = "";//urlencode($USER['firstname']);
		$LASTNAME = "";//urlencode($USER['lastname']);
		$ADDRESS = "";//urlencode($USER['address_line1']);
		$POSTAL = "";//urlencode($USER['zip']);
		$CITY = "";//urlencode($USER['city']);
		$USERNAME = urlencode( $profile->getDataValue('username') );
		$EMAIL = urlencode( $profile->getDataValue('email') );
		$PASSWORD = urlencode( CSecur::decryptByLikeNC( $profile->getDataValue('passwd') ) );
		
		$options = CHelperPayment::getOptionRow($this->price_id);//$options = $this->getOptionRow($this->price_id);
		
		$affid = $profile->getDataValue('affid');
		
		$url = "https://secure.zombaio.com/get_proxy.asp?SiteID={$this->zom_SiteId}&PricingID={$options['zom_pricing_id']}&return_url={$return_url}&return_url_decline={$return_url_decline}&return_url_approve={$return_url_approve}&processor_id={$this->id}&webmaster={$affid}&FirstName={$FIRSTNAME}&LastName={$LASTNAME}&Address={$ADDRESS}&Postal={$POSTAL}&City={$CITY}&Email={$EMAIL}&Username={$USERNAME}&Password={$PASSWORD}&extra={$extra}";
		if ($affid>99)
			$url .= "&AffiliateID={$affid}";
		CHelperLog::logFile('pm_zombaio_submit.log', $url);		
		
		return $url;
	}	
	
	
	
	public function postback($g)
	{
		if ( !isset($g["ZombaioGWPass"]) || $g["ZombaioGWPass"] != $this->zom_GWPass)
		{
		    header("HTTP/1.0 401 Unauthorized");
		    echo "<h1>Zombaio Gateway 1.1</h1><h3>Authentication failed.</h3>";
		    Yii::app()->end();
		}		

		
		//LOGS
		//check IP
		$remoteIP = CHelperLocation::getIPReal();
		$availableIP = array('82.99.3.1','82.99.3.2','82.99.3.3','82.99.3.4','82.99.3.5','82.99.3.6','82.99.3.11','82.99.3.12','82.99.3.13','82.99.3.14','82.99.3.15','82.99.3.30','213.132.102.1','213.132.102.2','213.132.102.3','213.132.102.4','213.132.102.5','213.132.102.6','213.132.102.7','213.132.102.8','213.132.102.9','213.132.102.10','213.132.102.11','213.132.102.12','213.67.144.191','109.74.14.194','109.74.14.195','109.74.14.196','109.74.14.197','109.74.14.198','109.74.14.199','109.74.14.200','109.74.14.201','109.74.14.202','109.74.14.203','109.74.14.204','109.74.14.205','109.74.14.206','109.74.14.207','109.74.14.208','109.74.14.209','109.74.14.210','109.74.14.211','109.74.14.212','109.74.14.213','109.74.14.214','109.74.14.215','109.74.14.216','109.74.14.217','109.74.14.218','109.74.14.219','109.74.14.220','109.74.14.221','109.74.14.222');
		$s = "";
		$badIP = false;
		if ( !in_array($remoteIP, $availableIP) && !DEBUG_IP )
		{
			$badIP = true;
			$s = "BAD IP - ";
		}		
		$s.= $_SERVER['REQUEST_URI'] . " --- " . date("r") . " remoteIP: " .$remoteIP;
		CHelperLog::logFile('pm_zombaio_postback.log', $s);	

		if ($badIP)
		{
		    header("HTTP/1.0 401 Unauthorized");
		    echo "<h1>Zombaio Gateway 1.1</h1><h3>Authentication failed...</h3>";
		    Yii::app()->end();
		}

		
		$this->id = 		(isset($g["processor_id"])) ? intval(trim($g["processor_id"])) : 0;
				
		// + update customer info
		$this->logPostbackTransaction($g);
		
		//log request uri
		$s = serialize($g);
		$sql = "INSERT INTO pm_zombaio_log (`log`, `type`) VALUES (:s, 'postback')";
		Yii::app()->db->createCommand($sql)
			->bindValue(":s", $s, PDO::PARAM_STR)
			->execute();		
		
		
		//PROCCESS
//before!!!		$this->id = 		(isset($g["processor_id"])) ? intval(trim($g["processor_id"])) : 0;
		$trn = 				(isset($g["Action"])) ? trim($g["Action"]) : "";
		$zom_id = 			(isset($g['TRANSACTION_ID'])) ? trim($g['TRANSACTION_ID']) : 0;
		$amount		= 		(isset($g['Amount'])) ? trim($g['Amount']) : 0;
		$username = 		(isset($g['username'])) ? trim($g['username']) : "";
		$password = 		(isset($g['password'])) ? trim($g['password']) : "";
		$visitorIP = 		(isset($g['VISITOR_IP'])) ? trim($g['VISITOR_IP']) : "";
		$successRebill = 	(isset($g['Success'])) ? intval(trim($g['Success'])) : 0;//https://secure.zombaio.com/zoa/PDF/Zombaio_PWMGM_20070926.pdf
		$email = 			(isset($g['EMAIL'])) ? trim(urldecode($g['EMAIL'])) : "";
		$SUBSCRIPTION_ID =  (isset($g['SUBSCRIPTION_ID'])) ? intval(trim($g['SUBSCRIPTION_ID'])) : 0;
		
		
		if (substr($username, 0, 4) == "Test")
		{
		    return "OK";
		}
			
		if (!$this->id && $SUBSCRIPTION_ID)
		{
			$sql = "SELECT trn_id FROM pm_zombaio_trn WHERE `sub_id`={$SUBSCRIPTION_ID} AND `action`='user.add' LIMIT 1";
		    $this->id = Yii::app()->db->createCommand($sql)->queryScalar();
		    
		    if (!$this->id)
		    {
				$sql = "SELECT trn_id FROM pm_zombaio_postback WHERE `sub_id`={$SUBSCRIPTION_ID} AND `action`='user.add' LIMIT 1";
			    $this->id = Yii::app()->db->createCommand($sql)->queryScalar();	    
		    }
		    
		}		

		//try to get from extra field
		if ( !$this->id && isset($g['extra']) )
		{
			$extra = 	$g['extra'];
	    	$tmp = 		@explode(";", $extra);
	    	$extra = 	(isset($tmp[0])) ? $tmp[0] : "=";
	    	$tmp = 		@explode("=", $extra);
	    	if (isset($tmp[0]) && isset($tmp[1]) && $tmp[0]=='int_trn')
	    		$this->id = intval($tmp[1]);
		}		
/*if (DEBUG_IP && isset($g['EMAIL']))
CHelperSite::vd($g['EMAIL'], 0);*/	

		
		//if zombaio receive without transaction_id...
		//try to find by email (3 wrong postbacks ar 2012-07-29)
		if ( !$this->id && isset($g['EMAIL']) && $g['EMAIL'] )
		{
			/*$sql = "SELECT id FROM users WHERE `email`=:email LIMIT 1";
		    $user_id = Yii::app()->db->createCommand($sql)
		    	->bindValue(":email", $g['EMAIL'], PDO::PARAM_STR)
		    	->queryScalar();*/
			$user_id = Profile::emailExist($g['EMAIL']);
		    
		    if ($user_id)
		    {
				$sql = "SELECT trn_id FROM `pm_zombaio_trn` WHERE `user_id`=:user_id AND `action`='user.add' ORDER BY id DESC LIMIT 1";
			    $this->id = Yii::app()->db->createCommand($sql)	
			    	->bindValue(":user_id", $user_id, PDO::PARAM_INT)
			    	->queryScalar();		    
		    }
		}
		//by IP, can be SLOW, no index
		if ( !$this->id && isset($g['VISITOR_IP']) && $g['VISITOR_IP'] )
		{
			$sql = "SELECT id FROM `pm_transactions` WHERE paymod='zombaio' AND `ip`=:ip ORDER BY id DESC LIMIT 1";//`status`='started' AND 
			$this->id = Yii::app()->db->createCommand($sql)	
				->bindValue(":ip", $g['VISITOR_IP'], PDO::PARAM_STR)
			    ->queryScalar();		    
		}
		
		
/*if (DEBUG_IP)
CHelperSite::vd($this->id, 0);	*/	
		switch ($trn)
		{
			
			case "user.add":
		        if ($this->userAdd($zom_id, $SUBSCRIPTION_ID))
		        	return "OK";
		        else
		            return "ERROR|Add error";

			case "rebill":
		        if ($successRebill!=1)
		            return "OK";
		        
		        if ($this->Rebill($zom_id))
		            return "OK";
		        else
		            return "ERROR|Rebill error";
			
		    case 'chargeback':
		        //> -put in todays gold users for zombaio only users if the sale was cardholder verfied or not. I asked zombaio if they have this nad they replied with the message below.
		        if (isset($_GET['LiabilityCode']) && $username)
		        {
		            $zom_LiabilityCode = intval($_GET['LiabilityCode']);
		            $sql = "UPDATE `pm_today_gold` SET zom_LiabilityCode=:zom_LiabilityCode WHERE username=:username AND paymod='zombaio' LIMIT 1";
					Yii::app()->db->createCommand($sql)	
						->bindValue(":zom_LiabilityCode", $zom_LiabilityCode, PDO::PARAM_INT)
						->bindValue(":username", $username, PDO::PARAM_STR)
					    ->execute();
		        }

		        $this->cancelRecurring();
		        
		        return "OK";		            
			
		    //will done later...
			case "user.delete":
				$this->cancelRecurring();
				return "OK";
		        
		    default:
		        return "OK"; //"UNKNOW_ACTION|UNKNOW_ACTION";
		}
		
		
		
		
	}

	
	/*
	 * first pay
	 */
	public function userAdd($zom_id, $SUBSCRIPTION_ID)
	{
		if (!$this->id)
			return false;
		
        $trn = self::getTransactionInfo($this->id);
        $this->user_id = $trn['user_id'];
        
        //sometimes zombaio sent user.add more than 1 time ...
        $profile = new Profile($this->user_id);
        $paymentInfo = $profile->getPayment();
        if ( isset($paymentInfo['firstpay']) && $paymentInfo['firstpay']!='0000-00-00 00:00:00' && $paymentInfo['paymod']=='zombaio')
       		return true;

       		
        // update zom transaction id and SUBSCRIPTION_ID
        $sql = "UPDATE LOW_PRIORITY `pm_zombaio_trn` SET zom_id=:zom_id, sub_id=:sub_id WHERE trn_id=:trn_id LIMIT 1";
	    Yii::app()->db->createCommand($sql)
	    	->bindValue(":trn_id", $this->id, PDO::PARAM_INT)
			->bindValue(":zom_id", $zom_id, PDO::PARAM_INT)
			->bindValue(":sub_id", $SUBSCRIPTION_ID, PDO::PARAM_INT)
			->execute();       		
       		
		
        //COMPLETE transaction
        $this->CompleteTransaction();
        
		//UPGRADE USER TO GOLD
		$profile->Upgrade($trn['id']);
        
		return true;
	}

	
	/*
	 * rebill
	 */
	public function Rebill($zom_id)
	{
		if (!$this->id)//	initial trn_id
			return false;
			
		$res = parent::Rebill();
		
		return $res;
	}

	/*
	 * cancel recurring
	 */
	public function cancelRecurring()
	{
		if (!$this->id) return;
		
		$trnInfo = parent::getTransactionInfo($this->id);
		
		if (isset($trnInfo['user_id']) && $trnInfo['user_id'])
			parent::cancelRecurring($trnInfo['user_id']);
	}	
	
	/*
	 * log postback
	 */
	public function logPostbackTransaction($g)
	{
	    $action = 	(isset($g['Action'])) ? 		$g['Action'] : "";
	    $username = (isset($g['username'])) ? 		$g['username'] : "";
	    $sub_id = 	(isset($g['SUBSCRIPTION_ID'])) ? $g['SUBSCRIPTION_ID'] : 0;
	    $zom_id = 	(isset($g['TRANSACTION_ID'])) ? $g['TRANSACTION_ID'] : 0;
$firstname =(isset($g['FIRSTNAME'])) ? 		$g['FIRSTNAME'] : "";
$lastname = (isset($g['LASTNAME'])) ? 		$g['LASTNAME'] : "";
$ccname	=	(isset($g['NAME_ON_CARD'])) ? 	$g['NAME_ON_CARD'] : "";
$address = 	(isset($g['ADDRESS'])) ? 		$g['ADDRESS'] : "";
$zip = 		(isset($g['POSTAL'])) ? 		$g['POSTAL'] : "";
$state = 	(isset($g['REGION'])) ? 		$g['REGION'] : "";
$city = 	(isset($g['CITY'])) ? 			$g['CITY'] : "";
$country = 	(isset($g['COUNTRY'])) ? 		$g['COUNTRY'] : "";
$email = 	(isset($g['EMAIL'])) ? 			$g['EMAIL'] : "";
	    $amount = 	(isset($g['Amount'])) ? 		$g['Amount'] : "0";
	    $currency = (isset($g['Amount_Currency'])) ? $g['Amount_Currency'] : "";
	    $ip = 		(isset($g['VISITOR_IP'])) ? 	$g['VISITOR_IP'] : "";
$ccnum_hash=(isset($g['CardHash'])) ? 		$g['CardHash'] : "";
	    $affid = 	(isset($g['webmaster'])) ? 		$g['webmaster'] : "";
	    $trans_id = (isset($g['processor_id'])) ? 	$g['processor_id'] : 0;
	    $extra = 	(isset($g['extra'])) ? 			$g['extra'] : "";
	    $tmp = 		@explode(";", $extra);
	    $extra = 	(isset($tmp[1])) ? $tmp[1] : "=";
	    $tmp = 		@explode("=", $extra);
	    $user_id = 	(isset($tmp[1])) ? $tmp[1] : 0;
	    $reasoncode=(isset($g['ReasonCode'])) ? 	$g['ReasonCode'] : "";
	    /*if (!$username)
	    {
	        $sql = "SELECT username FROM ". DB_NAME. ".osdate_user WHERE id='{$user_id}' OR email='{$email}' LIMIT 1";
	        $row = $db->getRow($sql);
	        $username = $row['username'];
	    }*/
	    $sql = "INSERT INTO pm_zombaio_postback (
	    	`trn_id`, 
	    	`action`, 
	    	`zom_id`, 
`firstname`, 
`lastname`, 
`nameoncard`, 
`address`, 
`postal`, 
`region`, 
`city`, 
`country`, 
`email`, 
	    	`amount`, 
	    	`currency`, 
	    	`visitor_ip`, 
`cardhash`, 
	    	`affid`, 
	    	`user_id`, 
	    	`ts`, 
	    	`sub_id`, 
	    	`username`, 
	    	`reasoncode`
	    ) VALUES (
	    	:trn_id, 
	    	:action, 
	    	:zom_id, 
:firstname, 
:lastname, 
:nameoncard, 
:address, 
:postal, 
:region, 
:city, 
:country, 
:email, 
	    	:amount, 
	    	:currency, 
	    	:visitor_ip, 
:cardhash, 
	    	:affid, 
	    	:user_id, 
	    	NOW(), 
	    	:sub_id, 
	    	:username, 
	    	:reasoncode
	    )";
		
	    Yii::app()->db->createCommand($sql)
	    	->bindValue(":trn_id", 		$trans_id, 					PDO::PARAM_INT)
	    	->bindValue(":action", 		$action, 					PDO::PARAM_STR)
	    	->bindValue(":zom_id", 		$zom_id, 					PDO::PARAM_INT)
->bindValue(":firstname", 	$firstname, 				PDO::PARAM_STR)
->bindValue(":lastname", 	$lastname, 					PDO::PARAM_STR)
->bindValue(":nameoncard", 	$ccname, 					PDO::PARAM_STR)
->bindValue(":address", 	$address, 					PDO::PARAM_STR)
->bindValue(":postal", 		$zip, 						PDO::PARAM_STR)
->bindValue(":region", 		$state, 					PDO::PARAM_STR)
->bindValue(":city", 		$city, 						PDO::PARAM_STR)
->bindValue(":country", 	$country, 					PDO::PARAM_STR)
->bindValue(":email", 		$email, 					PDO::PARAM_STR)
	    	->bindValue(":amount", 		$amount, 					PDO::PARAM_STR)
	    	->bindValue(":currency", 	$currency, 					PDO::PARAM_STR)
	    	->bindValue(":visitor_ip", 	$ip, 						PDO::PARAM_STR)
->bindValue(":cardhash", 	$ccnum_hash, 					PDO::PARAM_STR)
	    	->bindValue(":affid", 		$affid, 					PDO::PARAM_INT)
	    	->bindValue(":user_id", 	$user_id, 					PDO::PARAM_INT)
	    	->bindValue(":sub_id", 		$sub_id, 					PDO::PARAM_INT)
	    	->bindValue(":username", 	$username, 					PDO::PARAM_STR)
	    	->bindValue(":reasoncode", 	$reasoncode, 				PDO::PARAM_STR)
			->execute();

			
		//update customer info in main trn
		if ($action=='user.add')
		{
			$ccnum = "";
			$this->updateCustomerInfo($ccname, $ccnum, $firstname, $lastname, $address, $country, $state, $city, $zip, $email, $ip, $ccnum_hash);
		}
			
			
	    return true;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
}