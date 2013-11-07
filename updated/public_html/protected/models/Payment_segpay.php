<?php
class Payment_segpay extends Payment
{
	private $pmName = 'segpay'; 
	
	public $availablePriceIds = array(1,3);
	public $defaultPriceId = 1;
	
	private $zsignupMode = true;//Segpay using for /zsignup or /payment
	
	private $testMode = false;
	
	//postback values (all possible values what use in the class)
	private $pb = array(
		'action'=>'',
		'stage'=>'',
		'approved'=>'',
		'trantype'=>'',
		'purchaseid'=>'',
		'tranid'=>'',
		'price'=>'',
		'currencycode'=>'',
		'eticketid'=>'',
		'ip'=>'',
		'initialvalue'=>'',
		'initialperiod'=>'',
		'recurringvalue'=>'',
		'recurringperiod'=>'',
		'desc'=>'',
		'username'=>'',
		'password'=>'',
		'name'=>'',
		'firstname'=>'',
		'lastname'=>'',
		'email'=>'',
		'phone'=>'',
		'address'=>'',
		'city'=>'',
		'state'=>'',
		'zipcode'=>'',
		'country'=>'',
		'merchantpartnerid'=>'',
		'transGUID'=>'',
		'standin'=>'',
		'xsellnum'=>'',
		'billertranstime'=>'',
		'REF1'=>'',
		'REF2'=>'',
		'TESTTRANS'=>''
	);


        
	
	public function __construct($id=0, $zsignupMode=true)
	{
		parent::__construct($id, $this->pmName);
		
		//if (DEBUG_IP) $this->testMode=true;
		
		$this->zsignupMode = $zsignupMode;
	} 

	
	
	public function startTransaction($user_id, $price_id)
	{
		parent::startTransaction($user_id, $price_id, 0, true);
		
		if ($this->errors)
			return false;

		$options = CHelperPayment::getOptionRow($price_id);//$options = $this->getOptionRow($price_id);
		$amount = ($options['price_trial']!=0) ? $options['price_trial'] : $options['price'];
		$amount_rebill = $options['price'];
		$term_trial = ($options['term_trial']!=0) ? $options['term_trial'] : $options['term'];
		$term = $options['term'];		
			
		$sql = "INSERT INTO `pm_segpay_trn` (
			`trn_id`,
			`user_id`,
			`amount`,
			`added`,
			`status`,
			`type`,
			`price_id`				
		) VALUES (
			:trn_id,
			:user_id,
			:amount,
			NOW(),
			'started',
			'initial',
			:price_id			
		)";
		
		Yii::app()->db->createCommand($sql)
	    	->bindValue(":trn_id", 		$this->id, 			PDO::PARAM_INT)
	    	->bindValue(":user_id", 	$this->user_id, 	PDO::PARAM_INT)
	    	->bindValue(":price_id", 	$this->price_id, 	PDO::PARAM_INT)
	    	->bindValue(":amount", 		$amount, 			PDO::PARAM_STR)
			->execute();		

			

		
	
		//PROCCESS
		$profile = new Profile($this->user_id);
		
		if ($this->zsignupMode)
			$x_decl_link = urlencode( SITE_URL."/allin1/step4" );
		else
			$x_decl_link = urlencode( SITE_URL );
			
		$x_decl_text = urlencode("CLICK HERE TO TRY ADDITIONAL PAYMENT OPTIONS");
		
		$x_auth_link = urlencode( SITE_URL."/payment/approved" );
		$x_auth_text = urlencode("Proceed to Meetsi!");

		$FIRSTNAME = "";//urlencode($USER['firstname']);
		$LASTNAME = "";//urlencode($USER['lastname']);
		$ADDRESS = "";//urlencode($USER['address_line1']);
		$POSTAL = "";//urlencode($USER['zip']);
		$CITY = "";//urlencode($USER['city']);
		$USERNAME = urlencode( $profile->getDataValue('username') );//urlencode($USER['username']);
		$x_billemail = urlencode( $profile->getDataValue('email') );//urlencode($USER['email']);
		//$PASSWORD = urlencode( CSecur::decryptByLikeNC( $profile->getDataValue('passwd') ) );//urlencode(decrypt_password($USER['passwd']));
		$PASSWORD = urlencode( Yii::app()->secur->decryptByLikeNC( $profile->getDataValue('passwd') ) );

		$options = CHelperPayment::getOptionRow($this->price_id);//$options = $this->getOptionRow($this->price_id);
		$affid = $profile->getDataValue('affid');
		
		if ($this->testMode)
			$options['segpay_pricing_id']='133044:12217';
//		https://secure2.segpay.com/billing/poset.cgi?x-eticketid=133044:12217&x-billemail=techsupport%40segpay.com&x-billzip=12345&username=anyuser&password=anypassword&memberid=anymemberID&x-auth-link=http%3A%2F%2Fwww.yoursite.com%2Fmembers&x-auth-text=CLICK+HERE+TO+SIGNIN+TO+THE+MEMBERS+AREA!&x-decl-link= http%3A%2F%2Fwww.yoursite.com%2Fjoinbysegpaycheck.htm&x-decl-text=CLICK+HERE+TO+TRY+ADDITIONAL+PAYMENT+OPTIONS			
		$url = "https://secure2.segpay.com/billing/poset.cgi?x-eticketid={$options['segpay_pricing_id']}&x-billemail={$x_billemail}&merchantpartnerid={$affid}&x-auth-link={$x_auth_link}&x-auth-text={$x_auth_text}&x-decl-link={$x_decl_link}&x-decl-text={$x_decl_text}&username={$USERNAME}&Password={$PASSWORD}&REF1={$USERNAME}&REF2={$this->id}";
//https://secure2.segpay.com/billing/poset.cgi?x-eticketid=133044:12217       
		CHelperLog::logFile('pm_segpay_submit.log', $url);		
		
		return $url;			
			
	}	

	/*
	 * set POSTBACK values
	 */	
	private function setPB($pb)
	{
		foreach ($pb as $k=>$v) $this->pb[$k] = trim($v);
	}

	
    public function getSegpayOptionId($eticketid)
    {
        $sql = "SELECT id FROM `pm_pricing` WHERE segpay_pricing_id=:segpay_pricing_id LIMIT 1";
        return Yii::app()->db->createCommand($sql)
        			->bindValue(":segpay_pricing_id", $eticketid, PDO::PARAM_STR)
        			->queryScalar();
    }  
    	
	public function postback($p)
	{
		//LOGS
		//check IP
		$remoteIP = CHelperLocation::getIPReal();
		//$availableIP = array();
		$s = "";
		$badIP = false;
		/*if ( !in_array($remoteIP, $availableIP) && !DEBUG_IP )
		{
			$badIP = true;
			$s = "BAD IP - ";
		}*/		
		$s = date("r") . " - " . $remoteIP .' --- '.var_export($p, true) . "\n";
		CHelperLog::logFile('pm_segpay_postback.log', $s);		
		
		if ($badIP)
		{
		    header("HTTP/1.0 401 Unauthorized");
		    Yii::app()->end();
		}

		//
		$this->setPB($p);		
		$this->pb['action']=strtolower($this->pb['action']);
		$this->pb['stage']=strtolower($this->pb['stage']);
		$this->pb['approved']=strtolower($this->pb['approved']);
		$this->pb['trantype']=strtolower($this->pb['trantype']);
		
		
		$this->id = intval($this->pb["REF2"]);

	
		// + update customer info
		$this->logPostbackTransaction();		

		
		//log $_POST
		$s = serialize($p);
		$sql = "INSERT INTO pm_segpay_log (`log`, `type`) VALUES (:p, 'postback')";
		Yii::app()->db->createCommand($sql)
			->bindValue(":p", $s, PDO::PARAM_STR)
			->execute();		
			
			
			
		//PROCCESS
		$action = strtolower( $this->pb['action'] );
///		$trans_transtype = $this->pb['trans_transtype'];
		$username = addslashes($this->pb['username']);
		$price = addslashes($this->pb['price']);
		$option_id = $this->getSegpayOptionId($this->pb['eticketid']);

		$trantype = addslashes($this->pb['trantype']);
		$approved = addslashes($this->pb['approved']);
		$segpay_id = intval($this->pb['tranid']);//segpay_id
		
		$stage = addslashes($this->pb['stage']);
			
		if ($action=='auth')
		{
			switch ($trantype)
			{
				case 'sale':
					
					if ($stage=='initial')
					{
						
						if (!$this->id)
						{
						    $this->user_id = ($username) ? $this->findUserIdByUsername($username) : $this->findUserIdByUsername($this->pb['REF1']);
							$sql = "SELECT id FROM `pm_segpay_trn` WHERE user_id={$this->user_id} AND status='started' AND type='initial' AND price_id={$option_id} ORDER BY id DESC LIMIT 1";
						    $this->id = Yii::app()->db->createCommand($sql)->queryScalar();     
						}					
	
			            if ($approved=='yes')
			            {
			                if ( $this->id && $this->ApproveInitial($segpay_id) )
			                {
			                    return "OK";
			                }
			                else
			                {
			                    return "ERROR|Add error";
			                }              
			            }
			            else
			            {
			                $this->DeclineInitial($segpay_id);
			                return "OK";
			            } 					
						
						
						return 'OK';
					}
					elseif ($stage=='rebill' || $stage=='conversion')
					{
			            if ($approved != 'yes')
			            	return 'OK';
						
			            //find user id
			            $purchaseid = intval($this->pb['purchaseid']);
			            $sql = "SELECT user_id FROM `pm_segpay_postback` WHERE purchaseid={$purchaseid} AND trantype='sale' AND approved='yes' AND stage='initial' AND action='auth' ORDER BY id DESC LIMIT 1";
			            $this->user_id = Yii::app()->db->createCommand($sql)->queryScalar();
			            if (!$this->user_id)
			            	return "OK";
			            
			            //find initial trn
			            $sql = "SELECT id FROM `pm_transactions` WHERE user_id={$this->user_id} AND status='completed' AND `paymod`='segpay' ORDER BY id DESC LIMIT 1";
			            $this->id = Yii::app()->db->createCommand($sql)->queryScalar();
			            if (!$this->id)
			            	return "OK";		            
	            
			            
			            parent::Rebill();	
			            	
			            	
						return 'OK';
					} 
					else
						return 'OK';
					
					
				
					
					
					
			    case 'charge'://Chargeback transaction.
			    case 'credit'://Refund transaction
			        
			    	$this->cancelRecurring($this->pb['purchaseid']);	    	
			    	return 'OK';
			        	
			        	
			    default: return 'OK';
			}			
		}
		elseif ($action=='void')
		{
			$this->cancelRecurring($this->pb['purchaseid']);	    	
			return 'OK';
		}
		else
			return 'OK';
	}	
	
	

	
	public function ApproveInitial($segpay_id)
	{
		if (!$this->id) return false;
		
        $trn = self::getTransactionInfo($this->id);
        $this->user_id = $trn['user_id'];
        
        //if more than 1 time ...
        $profile = new Profile($this->user_id);
        if ($profile->getDataValue('role')=='gold')
            return true;
		
        //COMPLETE transaction
        $this->CompleteTransaction();
        
		//UPGRADE USER TO GOLD
		$profile->Upgrade($trn['id']);
		
        // update pm_segpay_trn
        $sql = "UPDATE `pm_segpay_trn` SET segpay_id=:segpay_id, status='approved' WHERE trn_id=:trn_id";
	    Yii::app()->db->createCommand($sql)
	    	->bindValue(":segpay_id", $segpay_id, PDO::PARAM_INT)	
	    	->bindValue(":trn_id", $this->id, PDO::PARAM_INT)
			->execute();
			
		return true;
	}	
	
	
	
	public function DeclineInitial($segpay_id)
	{
	    if (!$this->id) return;
	    
		$sql = "UPDATE `pm_segpay_trn` SET segpay_id=:segpay_id, status='declined' WHERE trn_id=:trn_id";
	    Yii::app()->db->createCommand($sql)
	    	->bindValue(":segpay_id", $segpay_id, PDO::PARAM_INT)	
	    	->bindValue(":trn_id", $this->id, PDO::PARAM_INT)
			->execute();

			
		$sql = "UPDATE `pm_transactions` SET status='started' WHERE id=:id";
	    Yii::app()->db->createCommand($sql)
	    	->bindValue(":id", $this->id, PDO::PARAM_INT)
			->execute();

			
        
        //fixing if was approved before this:
        $trn = self::getTransactionInfo($this->id);
        $this->user_id = $trn['user_id'];	
        
        if (!$this->user_id) return;
        
        $user = Yii::app()->db->createCommand("SELECT * FROM user_payment WHERE id={$this->user_id} LIMIT 1")->queryRow();        
        
        if ($user)
        {
            if ($user['paymod']=='segpay')
            {
				Yii::app()->db->createCommand("DELETE FROM user_payment WHERE id={$this->user_id} LIMIT 1")->execute();
				
				$profile = new Profile($this->user_id);
				$profile->makeFree();
            	//$levelend = time();
                //$sql = "UPDATE osdate_user SET level='4', levelend='{$levelend}', amount=0, firstpay='0000-00-00 00:00:00', lastpay='0000-00-00 00:00:00', paymod='unknown', `active`=1 WHERE id='{$user_id}' LIMIT 1";//oleg 2011-07-19
                //$db->query($sql);
            }
            
            Yii::app()->db->createCommand("DELETE FROM `pm_today_gold` WHERE user_id={$this->user_id} AND paymod='segpay'")->execute();
        }	
	}
	

	/*
	 * cancel recurring
	 */
	public function cancelRecurring($purchaseid)
	{
		//find user id
		$purchaseid = intval($purchaseid);
		$sql = "SELECT user_id FROM `pm_segpay_postback` WHERE purchaseid={$purchaseid} AND trantype='sale' AND approved='yes' AND stage='initial' AND action='auth' ORDER BY id DESC LIMIT 1";
		$this->user_id = Yii::app()->db->createCommand($sql)->queryScalar();
		
		if (!$this->user_id) return;
		
		parent::cancelRecurring($this->user_id);
	}	

	
	public function logPostbackTransaction()
	{
        $action = addslashes($this->pb['action']);
        $stage = addslashes($this->pb['stage']);
        $approved = addslashes($this->pb['approved']);
        $trantype = addslashes($this->pb['trantype']);
        $purchaseid = intval($this->pb['purchaseid']);
        $tranid = intval($this->pb['tranid']);
        $price = addslashes($this->pb['price']);
        $currencycode = addslashes($this->pb['currencycode']);
        $eticketid = addslashes($this->pb['eticketid']);
        $ipaddress = addslashes($this->pb['ip']);
        $ival = addslashes($this->pb['initialvalue']);
        $iint = intval($this->pb['initialperiod']);
        $rval = addslashes($this->pb['recurringvalue']);
        $rint = intval($this->pb['recurringperiod']);
        $desc = addslashes($this->pb['desc']);
        $username = addslashes($this->pb['username']);
        $password = addslashes($this->pb['password']);
        $billname = addslashes($this->pb['name']);
        $billnamefirst = addslashes($this->pb['firstname']);
        $billnamelast = addslashes($this->pb['lastname']);
        $billemail = addslashes($this->pb['email']);
        $billphone = addslashes($this->pb['phone']);
        $billaddr = addslashes($this->pb['address']);
        $billcity = addslashes($this->pb['city']);
        $billstate = addslashes($this->pb['state']);
        $billzip = addslashes($this->pb['zipcode']);
        $billcntry = addslashes($this->pb['country']);
        $transguid = addslashes($this->pb['transGUID']);
        $standin = addslashes($this->pb['standin']);
        $xsellnum = addslashes($this->pb['xsellnum']);
        $transtime = addslashes($this->pb['billertranstime']);
        $ref1 = addslashes($this->pb['REF1']);
        $ref2 = intval($this->pb['REF2']);
		
        $user_id = 0;
        if ($username) $user_id = $this->findUserIdByUsername($username);
        if ($ref1) $user_id = $this->findUserIdByUsername($ref1);
        
        $affid = intval($this->pb['merchantpartnerid']);		
		
        $this->user_id = $user_id;
       
        $sql = "INSERT INTO `pm_segpay_postback` (
                `user_id`,
                `affid`,
                `action`,
                `stage`,
                `approved`,
                `trantype`,
                `purchaseid`,
                `tranid`,
                `price`,
                `currencycode`,
                `eticketid`,
                `ipaddress`,
                `ival`,
                `iint`,
                `rval`,
                `rint`,
                `desc`,
                `username`,
                `password`,
                `billname`,
                `billnamefirst`,
                `billnamelast`,
                `billemail`,
                `billphone`,
                `billaddr`,
                `billcity`,
                `billstate`,
                `billzip`,
                `billcntry`,
                `transguid`,
                `standin`,
                `xsellnum`,
                `transtime`,
                `ref1`,
                `ref2`,
                `ts`,
                `date`
            ) VALUES (
                '{$user_id}',
                '{$affid}',
                '{$action}',
                '{$stage}',
                '{$approved}',
                '{$trantype}',
                '{$purchaseid}',
                '{$tranid}',
                '{$price}',
                '{$currencycode}',
                '{$eticketid}',
                '{$ipaddress}',
                '{$ival}',
                '{$iint}',
                '{$rval}',
                '{$rint}',
                '{$desc}',
                '{$username}',
                '{$password}',
                '{$billname}',
                '{$billnamefirst}',
                '{$billnamelast}',
                '{$billemail}',
                '{$billphone}',
                '{$billaddr}',
                '{$billcity}',
                '{$billstate}',
                '{$billzip}',
                '{$billcntry}',
                '{$transguid}',
                '{$standin}',
                '{$xsellnum}',
                '{$transtime}',
                '{$ref1}',
                '{$ref2}',
                UNIX_TIMESTAMP(), 
                CURDATE()
        )";		
		Yii::app()->db->createCommand($sql)->execute();
		
		
			
		//update customer info in main trn
		if ($action=='auth' && $stage=='initial')
		{
			$ccname = "";
			$ccnum = "";
			$state = "";
			$ccnum_hash = "";
			$this->updateCustomerInfo($ccname, $ccnum, $billnamefirst, $billnamelast, $billaddr, $billcntry, $billstate, $billcity, $billzip, $billemail, $ipaddress, $ccnum_hash);
		}

			
	    return true;	
	}	
	
}