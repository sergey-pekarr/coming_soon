<?php
class Payment
{
	public $id=0;
	
	public $user_id;
	public $paymod;

	public $availablePriceIds;//array
	public $defaultPriceId;
	
	public $price_id;
	
	public $attributes;//ccard, etc
	
	public $errors = array();
	
	public $invoice_no="";
	
	public function __construct($id, $paymod)
	{
		$this->id = intval($id);
		$this->paymod = $paymod;
		
		
		$this->attributes = array(			
			'ccname' => '',
			'ccnum' => '',
		    'ccmon' => '',
			'ccyear' => '',
			'ccvv' => '',
			
			'firstname' => '',
			'lastname' => '',
		    'address' => '',
			'country' => '',
			'state' => '',
			'city' => '',
			'zip' => '',
			'email' => ''		
		);
	} 
	

	
	/*
	 * get ALL options for paymod
	 */
	public function getOptions($orderRand = true)
	{
		$mkey = "Payment_getOptions_".$this->paymod;
		$res = Yii::app()->cache->get($mkey);
		
		if ($res===false)
		{
			$sql = "SELECT * FROM pm_pricing";
			
			if ($this->availablePriceIds)
				$sql .= " WHERE id IN (".implode(',', $this->availablePriceIds).")";
			
			$sql .= " ORDER BY pos";
			
			$res = Yii::app()->db->createCommand($sql)->queryAll();
			
			Yii::app()->cache->set($mkey, $res, 300);
		}
		
		if ($orderRand)
			$res = CHelperSite::shuffle_assoc($res);
		
		return $res;
	}
	
	/**
	 * get option ROW
	 */
	/*public function getOptionRow($priceId)
	{
		$priceId = intval($priceId);
		
		if (!$priceId)
			return false;
		
		$options = CHelperPayment::getOptionsAll();//$this->getOptions();
		if ($options)
			foreach($options as $o)
				if ($o['id']==$priceId)
					return $o;

		return false;
	}*/	
	
	public function checkPriceId($price_id)
	{
		if (!in_array($price_id, $this->availablePriceIds))
		{
			$errors[] = "Bad price";
			return false;
		}
		
		return true;
		//$this->price_id = $price_id;
	}

	/*
	 * START 
	 */
	public function startTransaction($user_id, $price_id, $amount=0, $checkDuplicates=false)
	{
		if (!$user_id || !$price_id || !$this->paymod || !$this->checkPriceId($price_id))
		{
			$this->errors[] = 'Wrong transaction params...';
			if ( !CONSOLE && Yii::app()->user->id ) 
				Yii::app()->user->setFlash('errorCustom',"Wrong transaction params...");			
			return false;
		}
		
		if ( !CONSOLE && $checkDuplicates && Yii::app()->user->id )//trn of user (not cams sales, etc)
		{
			if ( HelperDuplicates::checkExistedPaidIP() )				
			{
				$this->errors[] = 'checkExistedPaidIP';
				return false;
			}			
		}
		
		
		
		$this->user_id = $user_id;
		$this->price_id = $price_id;

		$optionRow = CHelperPayment::getOptionRow($this->price_id);//$optionRow = $this->getOptionRow($this->price_id);
		
		if (!$amount)
			$amount = ($optionRow['price_trial']!=0) ? $optionRow['price_trial'] : $optionRow['price'];
//CHelperSite::vd($amount);
				
		$this->invoice_no = $this->user_id.'-'.time();
		
		$sql = "INSERT INTO pm_transactions (
			`invoice_no`,
			`user_id`,
			`date`,
			`paymod`,
			`status`,
			`amount`,
			`price_id`,
			
			`ccname`,
			`ccnum`,
			`firstname`,
			`lastname`,
			`address`,
			`country`,
			`state`,
			`city`,
			`zip`,
			`email`,
			`ip`,
			`ccnum_hash`,
			`added`
		) VALUES (
			:invoice_no,
			:user_id,
			:date,
			:paymod,
			'started',
			:amount,
			:price_id,
			
			:ccname,
			:ccnum,
			:firstname,
			:lastname,
			:address,
			:country,
			:state,
			:city,
			:zip,
			:email,
			:ip,
			:ccnum_hash,
			NOW()
		)";
		
		$ccnumHided = ($this->attributes['ccnum']) ? CHelperPayment::hideCard($this->attributes['ccnum'], 2) : "";
		$ccnumHash	= "";//($this->attributes['ccnum']) ? MD5($this->attributes['ccnum']) : "";
		
		$ip = (!CONSOLE) ? CHelperLocation::getIPReal() : '';
		
		Yii::app()->db->createCommand($sql)
	    	->bindValue(":invoice_no", 	$this->invoice_no, 				PDO::PARAM_STR)
	    	->bindValue(":user_id", 	$this->user_id, 				PDO::PARAM_INT)
	    	->bindValue(":date", 		date('Y-m-d'), 					PDO::PARAM_STR)
	    	->bindValue(":paymod", 		$this->paymod, 					PDO::PARAM_STR)
	    	->bindValue(":amount", 		$amount, 						PDO::PARAM_STR)
	    	->bindValue(":price_id", 	$this->price_id, 				PDO::PARAM_INT) 	
	    	->bindValue(":ccname", 		$this->attributes['ccname'], 	PDO::PARAM_STR)
	    	->bindValue(":ccnum", 		$ccnumHided, 					PDO::PARAM_STR)
	    	->bindValue(":firstname", 	$this->attributes['firstname'], PDO::PARAM_STR)
	    	->bindValue(":lastname", 	$this->attributes['lastname'], 	PDO::PARAM_STR)
	    	->bindValue(":address", 	$this->attributes['address'], 	PDO::PARAM_STR)
	    	->bindValue(":country", 	$this->attributes['country'], 	PDO::PARAM_STR)
	    	->bindValue(":state", 		$this->attributes['state'], 	PDO::PARAM_STR)
	    	->bindValue(":city", 		$this->attributes['city'], 		PDO::PARAM_STR)
	    	->bindValue(":zip", 		$this->attributes['zip'], 		PDO::PARAM_STR)
	    	->bindValue(":email", 		$this->attributes['email'], 	PDO::PARAM_STR)
	    	->bindValue(":ip", 			$ip, 							PDO::PARAM_STR)
	    	->bindValue(":ccnum_hash", 	$ccnumHash,						PDO::PARAM_STR)	    	
			->execute();		
		
		$this->id = Yii::app()->db->lastInsertId;
		
		if (!$this->id)
			$this->errors[] = 'DB error...';
	}

	
	
	/*
	 * get transaction info
	 */
	static function getTransactionInfo($id)
	{
		if (!$id) return false;

		$sql = "SELECT * FROM pm_transactions WHERE id=:id LIMIT 1";
		return Yii::app()->db->createCommand($sql)
			    	->bindValue(":id", $id, PDO::PARAM_INT)
					->queryRow();
	}
	
	private function updateTransactionResult($status)
	{
		if (!$this->id) return;
		
		$sql = "UPDATE pm_transactions SET `status`=:status WHERE id=:id LIMIT 1";
		Yii::app()->db->createCommand($sql)
	    	->bindValue(":status", 		$status, 		PDO::PARAM_STR)
	    	->bindValue(":id", 			$this->id, 		PDO::PARAM_INT)
	    	->execute();		
	}
	
	/*
	 * FIRST PAID
	 * 
	 * or success AUTH transaction for cams ($trnRes='authed', $skipTodayGold=true)
	 */
	public function CompleteTransaction($trnRes='completed', $skipTodayGold=false)
	{
		$this->updateTransactionResult($trnRes);
		
		
		//today gold report 'init'
		if ($this->user_id)
		{
			if ($skipTodayGold==false)
			{
				$sql = "INSERT DELAYED pm_today_gold (user_id, paymod) VALUES (:user_id, :paymod)";
				Yii::app()->db->createCommand($sql)
			    	->bindValue(":user_id", $this->user_id, PDO::PARAM_INT)
			    	->bindValue(":paymod", 	$this->paymod, 	PDO::PARAM_STR)
			    	->execute();
			}
		    	
		    	
		    //clear aff cache in stats
		    $profile = new Profile($this->user_id);	
		    $affid = $profile->getDataValue('affid');
		    if ($affid/* && $affid>=100*/)
		    {
			    $url = "http://aff.meetsi.com/api_out.php?key=zanNbtxgU82YYdBn&action=clear_cache&affid=".$affid;
			    $log = CHelperSite::curl_request($url);
			    //CHelperLog::logFile('stats_clear_cache.log', $url.' - '.$log);
		    }
		}
	}
	
	/*
	 * REBILL
	 * &
	 * STORE DECLINED REBILL
	 * 
	 */
	public function Rebill($success=true)
	{
		// $this->id   - FIRST (initial) TRANSACTION
			
        $trn = self::getTransactionInfo($this->id);
        
        if (!$trn || !$trn['user_id'] || !$trn['price_id'])
        	return false;
        
        $this->user_id = $trn['user_id'];
        $this->price_id = $trn['price_id'];		

		$optionRow = CHelperPayment::getOptionRow($this->price_id);
		$amount = $optionRow['price'];
		
		$this->invoice_no = $this->user_id.'-'.time();
		
		//renewal_number
		$sql = "SELECT COUNT(id) FROM pm_transactions WHERE user_id=:user_id AND `status`='renewal' AND paymod=:paymod AND price_id=:price_id";
		$renewal_number = Yii::app()->db->createCommand($sql)
	    	->bindValue(":user_id", 		$this->user_id, 				PDO::PARAM_INT)
	    	->bindValue(":paymod", 			$this->paymod, 					PDO::PARAM_STR)
	    	->bindValue(":price_id", 		$this->price_id, 				PDO::PARAM_INT) 	
			->queryScalar();		
		if (!$renewal_number) 
			$renewal_number=1;
		else
			$renewal_number++;

		if ($success)
			$status='renewal';
		else
			$status='renewal_declined';
			
		$sql = "INSERT INTO pm_transactions (
			`invoice_no`,
			`user_id`,
			`date`,
			`paymod`,
			`status`,
			`renewal_number`,
			`amount`,
			`price_id`,
			`added`
		) VALUES (
			:invoice_no,
			:user_id,
			:date,
			:paymod,
			:status,
			:renewal_number,
			:amount,
			:price_id,
			NOW()
		)";
		
		Yii::app()->db->createCommand($sql)
	    	->bindValue(":invoice_no", 		$this->invoice_no, 				PDO::PARAM_STR)
	    	->bindValue(":user_id", 		$this->user_id, 				PDO::PARAM_INT)
	    	->bindValue(":date", 			date('Y-m-d'), 					PDO::PARAM_STR)
	    	->bindValue(":status", 			$status, 						PDO::PARAM_STR)
	    	->bindValue(":renewal_number", 	$renewal_number, 				PDO::PARAM_INT)
	    	->bindValue(":paymod", 			$this->paymod, 					PDO::PARAM_STR)
	    	->bindValue(":amount", 			$amount, 						PDO::PARAM_STR)
	    	->bindValue(":price_id", 		$this->price_id, 				PDO::PARAM_INT) 	
			->execute();		

		$trn_id = Yii::app()->db->lastInsertId;
        
		//upgrade user
		if ($success)
		{
			$profile = new Profile($this->user_id);
			$profile->Upgrade($trn_id); //rebill transaction ID!!!				
		}

		
		return $trn_id;
	}	
	
	
	/*
	 * 
	 */
	public function updateCustomerInfo($ccname, $ccnum, $firstname, $lastname, $address, $country, $state, $city, $zip, $email, $ip, $ccnum_hash, $ccnum_type='')
	{
	    if (!$this->id) return;
		
		$sql = "UPDATE pm_transactions 
		SET 
	    	`ccname`=:ccname,
	    	`ccnum`=:ccnum,
	    	`firstname`=:firstname,
	    	`lastname`=:lastname,
	    	`address`=:address,
	    	`country`=:country,
	    	`state`=:state,
	    	`city`=:city,
	    	`zip`=:zip,
	    	`email`=:email,
	    	`ip`=:ip,
	    	`ccnum_hash`=:ccnum_hash,
	    	`ccnum_type`=:ccnum_type		
	    WHERE id=:id LIMIT 1";
		
		
		$ccnum = CHelperPayment::hideCard($ccnum, 2);
		$ccnum_hash="";
		
		Yii::app()->db->createCommand($sql)
	    	->bindValue(":ccname", 		$ccname, 		PDO::PARAM_STR)
	    	->bindValue(":ccnum", 		$ccnum, 		PDO::PARAM_STR)
	    	->bindValue(":firstname", 	$firstname, 	PDO::PARAM_STR)
	    	->bindValue(":lastname", 	$lastname, 		PDO::PARAM_STR)
	    	->bindValue(":address", 	$address, 		PDO::PARAM_STR)
	    	->bindValue(":country", 	$country, 		PDO::PARAM_STR)
	    	->bindValue(":state", 		$state, 		PDO::PARAM_STR)
	    	->bindValue(":city", 		$city, 			PDO::PARAM_STR)
	    	->bindValue(":zip", 		$zip, 			PDO::PARAM_STR)
	    	->bindValue(":email", 		$email, 		PDO::PARAM_STR)
	    	->bindValue(":ip", 			$ip, 			PDO::PARAM_STR)
	    	->bindValue(":ccnum_hash", 	$ccnum_hash, 	PDO::PARAM_STR)
	    	->bindValue(":ccnum_type", 	$ccnum_type, 	PDO::PARAM_STR)
	    	->bindValue(":id", 			$this->id, 		PDO::PARAM_INT)
	    	->execute();		
	}
	
	
	public function findUserIdByUsername($username)
	{
		if (!$username) return 0;
		
		$sql = "SELECT id FROM users WHERE username=:username LIMIT 1";
		$res = Yii::app()->db->createCommand($sql)
	    	->bindValue(":username", $username, PDO::PARAM_STR)
	    	->queryScalar();
	    
	    if (!$res) $res = 0;
	    	
	    return $res;
	}
	
	

	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	public function cancelRecurring($user_id)
	{
		if (!$user_id)
		{
			$this->errors[] = 'Wrong transaction params...';
			return false;
		}		
		
		
		$this->user_id = $user_id;
		
		$profile = new Profile($this->user_id);
		$profile->paymentUpdateRecurringStatus('cancelled');

		if (!$this->paymod)
		{
			$paymentInfo = $profile->getPayment();
			
			if (!$paymentInfo) return false;
			
			$this->paymod = $paymentInfo['paymod'];
		}
		
		
		$this->invoice_no = $this->user_id.'-'.time();
		
		$sql = "INSERT INTO pm_transactions (
			`invoice_no`,
			`user_id`,
			`date`,
			`paymod`,
			`status`,
			`added`

		) VALUES (
			:invoice_no,
			:user_id,
			:date,
			:paymod,
			'cancelled',
			NOW()
		)";
		
		Yii::app()->db->createCommand($sql)
	    	->bindValue(":invoice_no", 	$this->invoice_no, 				PDO::PARAM_STR)
	    	->bindValue(":user_id", 	$this->user_id, 				PDO::PARAM_INT)
	    	->bindValue(":date", 		date('Y-m-d'), 					PDO::PARAM_STR)
	    	->bindValue(":paymod", 		$this->paymod, 					PDO::PARAM_STR)
			->execute();		
		
		$this->id = Yii::app()->db->lastInsertId;
		
		if (!$this->id)
			$this->errors[] = 'DB error...';
			
		return ($this->errors) ? false : true;
	}


	
	
	
}


