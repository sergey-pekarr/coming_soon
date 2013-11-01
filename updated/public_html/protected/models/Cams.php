<?php

/*
 * email 2013-06-21
This is a secure communications.
This message will self-destruct in 5 minutes. The message is only viewable from your IP address.
Please send any questions to hosting-support.


1. Log in at Affiliate.Streamate.com with the following credentials
2. After logging in, go to the Affiliate Tab

Username: pentae@me.com
Password: xxx


1click info
RID: xxx
AuthKey: xxx


 */


class Cams
{
    
	private function _getRefID($way)
	{
		switch ($way)
		{
			case 'payon': return '';
			case 'rg': return 'xxx';						
			default: return "";
		}
	}
	
	private function _getSecretKey($way)
	{
		switch ($way)
		{
			default: return "xxx";
		}
	}	
	
	
	
    /**
     * register on whitelabel site
     * CALL ONCE!!!
     */
	public function regWhiteLabel($trn_id)
	{
		//include_once DIR_ROOT."/protected/vendors/cams/OneclickEncoding.php";
//if (DEBUG_IP) FB::warn($trn_id);/////		
		$trn_id = intval($trn_id);
		if (!$trn_id) return false;
		
		
		
//$trn_id=(LOCAL)? 228 : 121740;
//$user_id=(LOCAL)? 44034 : 588209;
		
		$trnInfo = Payment::getTransactionInfo($trn_id);
FB::warn($trnInfo);/////		
		if ( !$trnInfo || !isset($trnInfo['user_id']) || !$trnInfo['user_id'] ) return false;
		
		$userId = $trnInfo['user_id'];
		$profile = new Profile($userId);
		$profileData = $profile->getData();
		$userPass = CSecur::decryptByLikeNC($profileData['passwd']);		
		
		$way = CHelperPayment::getCamsWay($userId);
		$cams_refId = $this->_getRefID($way);
//if (DEBUG_IP) FB::warn($way.'-'.$cams_refId);/////		
		if ($way=='payon')
		{
			//payon
			$url="http://processor.meatycams.com/p/payon/postback_oneclick.cgi";			
			
			$sql = "SELECT * FROM  `pm_wirecard_payon_trn` WHERE trn_id={$trn_id} LIMIT 1";
			$trnInfo_sub = Yii::app()->db->createCommand($sql)->queryRow();
			
			if (!$trnInfo_sub) return false;			
			
			//JOIN
			$post = array(
					'action'=>"join",
					'firstname'=>$trnInfo['firstname'],
					'lastname'=>$trnInfo['lastname'],
					'zip'=>$trnInfo['zip'],
					'country'=>$trnInfo['country'],
					'ipaddress'=>$trnInfo['ip'],
					'email'=>$profileData['email'],
					'password'=>$userPass,
					'lastfour'=>substr($trnInfo['ccnum'], -4, 4),
					'firstfour'=>substr($trnInfo['ccnum'], 0, 4),
					'ccname'=>$trnInfo['ccnum_type'],
					'channel_id'=>$trnInfo_sub['cams_channel_id'],
					'unique_id'=>$trnInfo_sub['UniqueID'],
					'currency'=>'USD',
					'referrerid'=>$cams_refId,
			
					//'tracking_id'=>$userId,//needs for XMLFeedOnCharge queries
					'tracking_id'=>$profileData['affid'],
					'tracking_key'=>$userId,
			);
			
			$res = CHelperSite::curl_request($url, $post);
			
		}
		else 
		{
			
			//rg
			$url="http://processor.meatycams.com/rocketgate/postback_oneclick.cgi";

			$sql = "SELECT * FROM `pm_rg_cams_trn` WHERE trn_id={$trn_id} LIMIT 1";
			$trnInfo_sub = Yii::app()->db->createCommand($sql)->queryRow();
//FB::warn($trnInfo_sub, '$trnInfo_sub');		
			if (!$trnInfo_sub) return false;			
			
			$modelPayment = new Payment_rg_cams();
			
			
			//CustomerID
			$sql = "SELECT trn_id FROM pm_rg_cams_trn WHERE user_id='{$userId}' AND cams_type='sale' AND `type`='initial' AND `status`='approved' ORDER BY id DESC LIMIT 1";			
			$custID = Yii::app()->db->createCommand($sql)->queryScalar();
			if (!$custID)//without upsell
				$custID = $trnInfo_sub['trn_id'];
			
			//JOIN
			$post = array(
					'action'=>"join",
					'custFirstName'=>$trnInfo['firstname'],
					'custLastName'=>$trnInfo['lastname'],
					'custZip'=>$trnInfo['zip'],
					'custCountry'=>$trnInfo['country'],
					'custIP'=>$trnInfo['ip'],
					'custEmail'=>$profileData['email'],
					'custPassword'=>$userPass,
					'referredCustomerID'=>$custID,//$userId,	//Rocketgate customer id used to bill this customer
					'referringMerchantID'=>$modelPayment->getMerchantID(),//Rocketgate merchant id that this user bills on
					'cardHash'=>$trnInfo_sub['cardhash'],//Rocketgate card hash value used to bill this customer 
					'cardLast4'=>substr($trnInfo['ccnum'], -4, 4),
					'referrerid'=>$cams_refId,
			
					//'tracking_id'=>$userId,//needs for XMLFeedOnCharge queries
					'tracking_id'=>$profileData['affid'],
					'tracking_key'=>$userId,
			);
FB::warn($post, 'streamate_post');

/*if (DEBUG_IP)*/ CHelperLog::logFile('tmp_rg_cams_1click.log', $post);			
			
			$res = CHelperSite::curl_request($url, $post);			
//FB::warn($res);			
		}
/*if (DEBUG_IP) 
{
	$log = date("r") . " - trnId:" . $trn_id . ' --- '.var_export($res, true) . "\n";
	CHelperLog::logFile('CAMS_ok_test_join.txt', $log);
FB::warn($res);/////
}*/		

//if (DEBUG_IP) CHelperSite::vd($post, 0);
//if (DEBUG_IP) CHelperSite::vd($res, 0);
		if ($res!="OK")
		{
			$log = date("r") . " - trnId:" . $trn_id . ' --- '.var_export($res, true) . "\n";
			CHelperLog::logFile('CAMS_error_join.txt', $log);
			return false;
		}
		else
			$this->getWhiteLabel_1_click_link($userId);//generate and store 1_click_url

		return true;
	}
	
	
	/*
	 * get OneClick Auto-Login
	 */
    public function getWhiteLabel_1_click_link($userId)
    {
    	include_once DIR_ROOT."/protected/vendors/cams/OneclickEncoding.php";
    	
    	
    	$userId = intval($userId);
    	if (!$userId) return false;
    	    	
    	$profile = new Profile($userId);
    	$profileData = $profile->getData();
    	
    	if (!$profile->isJoinedTo_NLC())
    		return "";
    	
	    $way = CHelperPayment::getCamsWay($userId);
	    $cams_refId = $this->_getRefID($way);    		
    	$cams_SecretKey = $this->_getSecretKey($way);
    	
    	if ($profileData['settings']['cams_autologin']!='')
    	{
    		$url = $profileData['settings']['cams_autologin'];
    	}
    	else
    	{
	    	$userPass = CSecur::decryptByLikeNC($profileData['passwd']);
	    	    	
	    	$encoded = Oneclick::EncodeURLParameters(
	    		$profileData['email'],//$trnInfo['email'], // user's plain text email
	    		$userPass, // user's plain text password
	    		$cams_SecretKey, // secret key given to you by Streamate
	    		true // returns the values in an array
	    	);
	    	

	    	
	    	$url = "?email=".$encoded['email'];
	    	$url.= "&pwd=".$encoded['pwd'];
	    	$url.= "&chksm=".$encoded['chksm'];
	    	$url.= "&chksmv=".$encoded['chksmv'];
	    	$url.= "&rid=".$cams_refId;
	    	$url.= "&repage=cam";
	    	$url.= "&reargs=";
	    	
	    	
	    	$profile->settingsUpdate('cams_autologin', $url);
    	}
    	
    	if ($way=='rg')
    		$url = "http://processor.meatycams.com/rocketgate/oneclick.php" . $url;
    	else
			$url = "http://processor.meatycams.com/p/payon/oneclick.php" . $url;
    	
    	return $url;    	
    }
    
    
    /*
     * Set an Existing User as Fraud
     */
    public function setFraud($user_id/*, $trn_id, $email=''*/)
    {
    	$profile = new Profile($user_id);
    	
    	//allready frauded or not joined... 
    	if ($profile->getDataValue('settings', 'cams_joined')!='1')
    		return "";    	
    	
//    	if (!$email)
//    		$email = $profile->getDataValue('email');
    	
    	/*if (CHelperPayment::getCamsWay($user_id)=='nb')
    	{
    		$url="http://processor.meatycams.com/netbilling/postback.cgi";
    		
			$post = array(
					'action'=>"fraud",
					'cust_email'=>$email,
					'trans_id'=>$trn_id,
			);
			
			$res = CHelperSite::curl_request($url, $post);     		
    	}
    	elseif (CHelperPayment::getCamsWay($user_id)=='payon')
    	{
    		$url="http://processor.meatycams.com/p/payon/postback_oneclick.cgi";
    		
			$post = array(
					'action'=>"fraud",
					'unique_id'=>$trn_id,
			);
			
			$res = CHelperSite::curl_request($url, $post);     	
    	}
    	else*/
    	{
//return true;    		
//throw new CHttpException(404, 'NOR READY - setFraud');//$url="http://processor.meatycams.com/rocketgate/postback_oneclick.cgi";//???
			return "";//not found this function in oneclickapi for RG...
    	}
		
    	$res = trim($res);
    	
    	if ($res=="OK")
    	{
			$profile->settingsUpdate('cams_joined', 'fraud');
			$profile->settingsUpdate('cams_autologin', '');
    	}
    	else
			CHelperLog::logFile('errors_cams_fraud.log', $user_id);
		
		return $res;
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    /*
     * query Streamate XML feed on charges
     */
    public function XMLFeedOnCharge($date="", $way='rg')
    {
    	$result = array(
    		'NumUsersReturned'=>0,
    		'TotalRowsQueried'=>0,
    		'trns'=>array(),
    	);
		
    	/**
    	 * email 2013-06-11
    	 * https://securepost.flyingcroc.net/secure.cgi?key=xxx
    	 * 
    	 * Username: xxx
    	 * Password: xxx
    	 * Direct Signup RID: xxx
    	 * 1click RID: xxx
    	 */    	
		$username = "xxx";
		$password = "xxx";

    	if (!$date) $date=date("Y-m-d");
	
//*****************************************************************************
//or use Direct Signup RID: 2169 ??????????????????????????????????????????????
//*****************************************************************************
    	$cams_refId = $this->_getRefID($way);
		
    	
    	
//CHelperSite::vd($userId.'-'.$way.'-'.$cams_refId);    	
    	$post = '<?xml version="1.0" encoding="UTF-8"?>
				<SMLQuery>
					<Options MaxResults="500" Username="'.$username.'" Password="'.$password.'" />
					<UserBilling './*QueryId="x"*/' View="detailed">
						<Constraints>
							<ReferrerID>'.$cams_refId.'</ReferrerID>
							<Date>'.$date.'</Date>
						</Constraints>
						<Include>
							'./*<NewUsers />
							<ZeroDollarSignups />*/'
						</Include>
					</UserBilling>
				</SMLQuery>';
    	
//CHelperSite::vd( HelperXML::xml2ary($post),0 );    	
    	$res = $this->streamate_curl("http://affiliate.streamate.com/SMLive/SMLResult.xml", $post);
//if (DEBUG_IP) CHelperSite::vd($res);   	


//CHelperSite::vd($res);
//CHelperSite::vd("*****************************************************", 0);
    		
		$NumUsersReturned = (isset($res["SMLResult"]["_c"]["UserBilling"]["_a"]["NumUsersReturned"])) ? $res["SMLResult"]["_c"]["UserBilling"]["_a"]["NumUsersReturned"] : false;

		if ($NumUsersReturned===false)
		{
			die ("XMLFeedOnCharge: connection ERROR...");
		}
		
		if ($NumUsersReturned)
		{
			$result['NumUsersReturned'] = $NumUsersReturned; 
			$result['TotalRowsQueried'] = $res["SMLResult"]["_c"]["UserBilling"]["_a"]["TotalRowsQueried"];
			
			$User = $res["SMLResult"]["_c"]["UserBilling"]["_c"]["User"];

			
			
			if ($User && is_array($User))
			{
//CHelperSite::vd($User);
				$users=array();
				///$sales=array();
				$trns=array();
				if (count($User)==2 && isset($User["_a"]))
					$users[]=$User;
				else
					$users = $User;
					
//CHelperSite::vd($users);					
					
				foreach ($users as $u)
				{
					//$user_id = ($u["_a"]["TrackingID"]) ? intval($u["_a"]["TrackingID"]) : Profile::emailExist($u["_a"]["Email"]);
					$sales=array();///
					
					$user_id=0;
					if ($u["_a"]["TrackingID"])
					{
						if (intval($u["_a"]["TrackingID"])>500000)
							$user_id = intval($u["_a"]["TrackingID"]);
						else
							$user_id = ($u["_a"]["PassThru"]) ? intval($u["_a"]["PassThru"]) : 0;
					}
					else
						$user_id = ($u["_a"]["PassThru"]) ? intval($u["_a"]["PassThru"]) : 0;
						
					if (!$user_id)
						$user_id = Profile::emailExist($u["_a"]["Email"]);
						
					if (!$user_id) continue;
					
					$Billing = $u["_c"]["Billing"]["_c"]["Data"]; 
					
					$salesTmp=array();
					if (count($Billing)==2 && isset($Billing["_a"]))
						$salesTmp[]=$Billing;
					else
						$salesTmp=$Billing;
					
					//select only success sales
					foreach($salesTmp as $s)
					{
						if ($s["_a"]["SuccessCount"]!="0" && $s["_a"]["SuccessAmt"]!="0")
							$sales[]=$s["_a"];
					}
					
					if ($sales)
						$trns[$user_id]=$sales;
					
//CHelperSite::vd("****************************", 0);
//CHelperSite::vd($user_id, 0);
//CHelperSite::vd($sales);
//CHelperSite::vd($u);
				
				}
				
				$result['trns'] = $trns;
			}
			
			
		}
		
		
    	
//CHelperSite::vd($result);
		
		return $result;
    	
    }
    
	/**
	 * return array
	 */
	private function streamate_curl($url, $post, $headerSTR="Content-Type: text/xml")
	{
	    $ch = curl_init ();
	    curl_setopt ($ch, CURLOPT_URL, $url);
	    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);

	    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
	    if ($headerSTR != "") {
	        curl_setopt ($ch, CURLOPT_HTTPHEADER, array($headerSTR) );
	    }
	    
	    curl_setopt ($ch, CURLOPT_POST, 1);
	    curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
	
	    curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
	    curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
	    $result = curl_exec ($ch);
		//FB::info($result);
	    curl_close ($ch);
	
		//FB::info($result, 'b_curl_string result');	    
	    $resArray = HelperXML::xml2ary($result);

	
	    return $resArray;
	}    
    
}
