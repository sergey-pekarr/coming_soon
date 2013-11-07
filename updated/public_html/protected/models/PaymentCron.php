<?php
class PaymentCron
{
    /*
     * prepare for admin Today Gold users
     */
    public static function todayGoldPrepare()
    {
		$startTime = time();
		$resCron = date("r") . " - ";    	
    	
    	$done=0;
    	
    	$daysDublOS = 1;
    	
    	$dublicate_sales_by_OS_2___EMAIL = array();
    	$dublicate_sales_by_IP_B___EMAIL = array();
    	
    	$sql = "SELECT id, user_id, date FROM pm_today_gold WHERE `date`='0000-00-00' AND `progress`='' ORDER BY id LIMIT 1";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
    	
if ($rows)
{
    foreach ($rows as $row)
    {

		$sql = "UPDATE pm_today_gold SET `progress`='started' WHERE id={$row['id']} LIMIT 1";
		Yii::app()->db->createCommand($sql)->execute();

//CHelperSite::vd($row);

        $user_id = $row['user_id'];

        $profile = new Profile($user_id);
		$user = $profile->getData();
        
		$user['phone'] = ''; 
		
		$user['payment'] = $profile->getPayment();//Yii::app()->db->createCommand("SELECT * FROM user_payment WHERE id={$user_id} LIMIT 1")->queryRow();
		
		$dt = (isset($user['payment']['firstpay'])) ? $user['payment']['firstpay'] : "";
		if (!$dt || $dt=='0000-00-00')
		{
			$sql = "UPDATE pm_today_gold SET `progress`='' WHERE id={$row['id']} LIMIT 1";
			Yii::app()->db->createCommand($sql)->execute();
			continue;
		} 		
		
        //$sql = "SELECT ip_signup as ip FROM `users_info` WHERE user_id={$user_id} LIMIT 1";
        $ip = $user['info']['ip_signup'];//Yii::app()->db->createCommand($sql)->queryScalar();        
		$ip = ip2long($ip);

        //$sql = "SELECT osdate_user.*, users.* FROM osdate_user LEFT JOIN users ON users.id=osdate_user.id WHERE osdate_user.id={$user_id} LIMIT 1";    
        //$user = $db->GetRow($sql);        
		
		
        ///$sql = "SELECT COUNT(*) FROM osdate_mailbox WHERE senderid='{$user_id}'";
        $user['msgs'] = 0;//$db->getOne($sql);
        
        ///$sql = "SELECT COUNT(*) FROM winks WHERE from_id='{$user_id}'";
        $user['winks'] = 0;//$db->getOne($sql);                
        
        $affid = $user['affid'];
        if (!$affid) $affid=1;    
        $affiliate = Yii::app()->dbSTATS->createCommand("SELECT * FROM cs_users WHERE id='{$affid}' LIMIT 1")->queryRow();//$db->getRow("SELECT * FROM ".STATS.".cs_users WHERE id='{$affid}' LIMIT 1");
        if ($affiliate)
        {
	        $affiliate['manager_name'] = Yii::app()->dbSTATS->createCommand("SELECT login FROM cs_users WHERE id='{$affiliate['manager_id']}' LIMIT 1")->queryScalar();//$db->getOne("SELECT login FROM ".STATS.".cs_users WHERE id='{$affiliate[manager_id]}' LIMIT 1");
	        $user['master_id']       = ($affiliate['master_id']) ? $affiliate['master_id'] : 0;
	        $user['manager_id']      = ($affiliate['manager_id']) ? $affiliate['manager_id'] : 0;
	        $user['manager_name']    = $affiliate['manager_name'];
	        $user['agent_name']      = $affiliate['login'];         
        }
        else
        {
	        $affiliate['manager_name'] = "";
	        $user['master_id'] = $user['manager_id'] = 0;
	        $user['manager_name'] = $user['agent_name'] = "";         
        }
        

        $user['signupip'] = long2ip($ip);
        
        
        //$record = GeoIP_record_by_addr($gi, $user['signupip']); 
        //$recordStr = $user['signupip'].' / '.$record->country_code.' / '.$record->region.' / '.$record->city;//;
        $location_id = Yii::app()->location->findLocationIdByIP($user['info']['ip_signup']);
        if ($location_id)
        {
	        $location = Yii::app()->location->getLocation($location_id);
	        $recordStr = $user['info']['ip_signup'].' / '.$location['country'].' / '.$location['state'].' / '.$location['city'];//;
	        
	        $user['geo_ip_country'] = $recordStr;
	        $user['geo_ip_country'] = addslashes($user['geo_ip_country']);         
        }
        else
        	$user['geo_ip_country'] = "-";
       
        
        $ipL = $ip;
        //http://screencast.com/t/6fh39xSQ1go
        $ipFresh = Yii::app()->db->createCommand("SELECT id FROM `pm_today_gold` WHERE ip_long={$ipL} AND `date`>('{$user['payment']['firstpay']}' - INTERVAL 182 DAY ) ORDER BY id LIMIT 1000")->queryColumn();
        $user['ipFresh'] = $ipFresh;        
        
        //ip range 123.123.123.0-255
        //> -In todays gold users, If an ip is within the same ip range 123.123.123.0-255 of any other sale that signed up within 6 months show link to that sale.
        $ip1 = ip2long('123.123.123.0');
        $ip2 = ip2long('123.123.123.255');
        $dublicate_sales_by_IP = "";
        if ($ipL>=$ip1 && $ipL<=$ip2)
        {
            //$sql = "SELECT id FROM osdate_user WHERE id IN (SELECT user_id FROM users_ip_signup WHERE ip>={$ip1} AND ip<={$ip2})";
            $sql = "SELECT user_id as id FROM `users_info` ip_signup_long>={$ip1} AND ip_signup_long<={$ip2} LIMIT 1000";
            $dublicatesIP = Yii::app()->db->createCommand($sql)->queryAll();;
            
            if ($dublicatesIP)
            {
                foreach ($dublicatesIP as $uid)
                    $dips[] = $uid['id'];
                    
                if ($dips)
                {
                    $sql = "SELECT id FROM `pm_today_gold` WHERE user_id<>{$user_id} AND user_id IN (".implode(',',$dips).") AND date > ('".$user['payment']['firstpay']."' - INTERVAL 182 DAY ) ";
                    $paidIds = Yii::app()->db->createCommand($sql)->queryAll();
                    if ($paidIds)
                        foreach ($paidIds as $pid)
                            $dublicate_sales_by_IP[] = $pid['id'];
                }
            }
        }
        $dublicate_sales_by_IP = ($dublicate_sales_by_IP) ? serialize($dublicate_sales_by_IP) : "";

		//n 2012-07-30:
		try{
				//Temporary try catch -> do not break current process. Will remove later when this function is stable
						$goldUserQry = "select p.user_id, u.gender, u.looking_for_gender, l.country, l.latitude, l.longitude, a.joined, u.role
									from users_plan as p
									inner join users as u on u.id = p.user_id
									inner join users_location as l on l.user_id = u.id
									inner join users_activity as a on a.user_id = u.id
									where u.id = $user_id and u.form = '1'";
						$goldUser = Yii::app()->db->createCommand($goldUserQry)->queryRow();
						if($goldUser)
						{
							CHelperAutoFlirt::buildFakePlan($goldUser['user_id'],$goldUser['gender'],$goldUser['looking_for_gender'],
								$goldUser['country'], $goldUser['latitude'],$goldUser['longitude'],
								$goldUser['joined'],'gold');
							$resCron .= " DONE: CHANGE AUTOFLIRT PLAN {$goldUser['user_id']}. ";
						}
		}
					catch(exception $ex){
						$resCron .= " ERROR: CHANGE AUTOFLIRT PLAN PROBLEM?. ";
						return $resCron;//oleg 2012-12-13
		}
        
        
        //ip range class B for last mounth
        //oleg please add to "todays gold users" some flag that lets me know when 2 sales within the same month are within the same B class. for example 172.202. if there are 2 sales within this IP range like 172.202.1.1-172.202.255.255 I need to know. I caught some guy doing fraud jsut resetting his DSL connection and getting a new ip in the same range. I need a flag like the "duplicate ip" but for "duplicate IP range"
        $ipTxt = long2ip($ip);
        
        $regexp = '/^[0-9]{1,3}\.[0-9]{1,3}\./'; 
        preg_match_all ($regexp, $ipTxt, $resIP);
        $resIP = $resIP[0][0];
        
        $ip1 = ip2long($resIP.'0.0');
        $ip2 = ip2long($resIP.'255.255');
        
        
        $dublicate_sales_by_IP_B = "";
        $manager_id = $user['manager_id'];
        if ($manager_id)
        {
        	//$sql = "SELECT id FROM users_paid WHERE manager_id={$manager_id} AND ip_long>={$ip1} AND ip_long<={$ip2} AND date >= ('".$row['date']."' - INTERVAL 31 DAY)";
            $sql = "SELECT id FROM `pm_today_gold` WHERE manager_id={$manager_id} AND ip_long>={$ip1} AND ip_long<={$ip2} AND date >= ('".$user['payment']['firstpay']."' - INTERVAL 31 DAY)";
            $dublicatesIP_B = Yii::app()->db->createCommand($sql)->queryAll();            
            if ($dublicatesIP_B)
            {
                $dublicate_sales_by_IP_B = array();
            	foreach ($dublicatesIP_B as $did)
                    $dublicate_sales_by_IP_B[] = $did['id'];
                    
				//$dublicate_sales_by_IP_B___EMAIL[] = "http://pinkmeets.com/admin/payments/todayGold/?sale=".$row['id'].",".implode(",", $dublicate_sales_by_IP_B);
				$dublicate_sales_by_IP_B___EMAIL[] = "http://meetsi.com/admin/payments/todayGold/?sale=".$row['id'].",".implode(",", $dublicate_sales_by_IP_B);
            }
            $dublicate_sales_by_IP_B = ($dublicate_sales_by_IP_B) ? serialize($dublicate_sales_by_IP_B) : "";
        }
        
        
        //city
        $user['city'] = addslashes($user['location']['city']);
        
        
        //proxy
        $proxy = @file_get_contents("http://www.winmxunlimited.net/utilities/api/proxy/check.php?ip=" . long2ip($ip));
        $proxy = trim($proxy);//!!!
        $user['proxy'] = '';
        if (in_array($proxy, array('Tor','Public','0','Invalid IP')))
            $user['proxy'] = $proxy;
        else
        {
            $resCron .= "\n HIGH PRIORITY: Sale ID ".$row['id'].": ".$proxy." \n";
        }        
		
        
        //proxy MAXMIND
        if ($user['form']=='cams')//for cams only
        {
	        //maxmind.com
			////heath@heathaxton.com / p1nkmeets		
			////$license_key = 'keyecryoORyNIji';
			
        	//new from email 2013-07-23: 
	        //l: heath@heathaxton.com
			//p: Jj894JFj8i0##
			//User ID 	71768
			//License key 	ecryoORyNIji
			$license_key = 'ecryoORyNIji';		
			$query = "https://minfraud.maxmind.com/app/ipauth_http?l=" . $license_key . "&i=" . long2ip($ip);
			$proxy_maxmind = file_get_contents($query);//score
			$proxy_maxmind_res = trim(str_replace("proxyScore=", "", $proxy_maxmind));
	    	//EMAILS ABOUT PRIVATE
			if ( $proxy_maxmind_res!='' && floatval($proxy_maxmind_res)>0.00 && floatval($proxy_maxmind_res)<=4.01 )
			{
				$prBody = "MaxMind proxy detected: \n";
				//$prBody.= "http://pinkmeets.com/admin/payments/todayGold/?sale=".$row['id'];
				$prBody.= "http://meetsi.com/admin/payments/todayGold/?sale=".$row['id'];
				$prBody.= "\n\n";
				//mail("admin@pinkmeets.com", "PM, Today Gold maxmind proxy detected", $prBody);
				mail("admin@meetsi.com", "PM, Today Gold maxmind proxy detected", $prBody);
				mail("its-your-dream@yandex.ru", "PM, Today Gold maxmind proxy detected", $prBody);
			}
        }
        else
        	$proxy_maxmind="";

        //PC_INFO
        require_once DIR_ROOT.'/protected/vendors/os.php';
        
        $userAgent = $user['info']['user_agent'];        
        $pc_info_os = OS::getOS($userAgent);        
        Yii::app()->browser->setUserAgent($userAgent);        
        if (!$pc_info_os) $pc_info_os = Yii::app()->browser->getPlatform();
        $pc_info_browser = Yii::app()->browser->getBrowser();
        $pc_info_browser_version = Yii::app()->browser->getVersion();
        $pc_info_screen_resolution = $user['info']['screen_resolution'];
        $pc_info_screen_resolution_array = ($pc_info_screen_resolution) ? @explode("x", $pc_info_screen_resolution) : array(0,0);
        
		$pc_info_os = addslashes($pc_info_os);//.', '.$user_pc_info['browser'].' '.$user_pc_info['browser_version'].', '.$user_pc_info['screen_resolution'];
        $pc_info_browser = addslashes($pc_info_browser);
        $pc_info_browser_version = addslashes($pc_info_browser_version);
        $pc_info_screen_resolution = addslashes($pc_info_screen_resolution);        

        //fixing dublicates for OS/BROWSER for manager for last 7 days
        //> -show me in a column if the OS, browser, and browser version are duplicates of other sales from that same manager within the past 7 days. I dont care about screen resolution duplicates right now. Show me link to duplicate sale like you did with IP's before we blocked sales from the same IP's.
        $manager_id = $user['manager_id'];
        $dublicate_sales_by_OS = "";
        
        if ($manager_id && $pc_info_os!='unknown' && $pc_info_browser!='unknown' && $pc_info_os!='' && $pc_info_browser!='')
        {
            
        	if ($daysDublOS==1)
                $sql = "SELECT id FROM `pm_today_gold` WHERE manager_id={$manager_id} AND `date`='{$user['payment']['firstpay']}' AND pc_info_os='{$pc_info_os}' AND pc_info_browser='{$pc_info_browser}' AND pc_info_browser_version='{$pc_info_browser_version}' ORDER BY ID";
            else
                $sql = "SELECT id FROM `pm_today_gold` WHERE manager_id={$manager_id} AND `date`>=('{$user['payment']['firstpay']}' - INTERVAL 7 DAY ) AND `date`<='{$user['payment']['firstpay']}' AND pc_info_os='{$pc_info_os}' AND pc_info_browser='{$pc_info_browser}' AND pc_info_browser_version='{$pc_info_browser_version}' ORDER BY id";
            
            $dublOS = Yii::app()->db->createCommand($sql)->queryAll();
            
            if ($dublOS)
                foreach ($dublOS as $d)
                    if ($d['id']!=$row['id'])
                        $dublicate_sales_by_OS[] = $d['id'];
        }
        $dublicate_sales_by_OS = ($dublicate_sales_by_OS) ? serialize($dublicate_sales_by_OS) : "";
        
        $dublicate_sales_by_OS_2 = "";
        if ($manager_id && $pc_info_screen_resolution!='') 
        {       
            //other dublicates    
            $sql = "SELECT id FROM `pm_today_gold` WHERE manager_id={$manager_id} AND `date`>=('{$user['payment']['firstpay']}' - INTERVAL 30 DAY ) AND pc_info_screen_resolution='{$pc_info_screen_resolution}' ORDER BY id";
            $res = Yii::app()->db->createCommand($sql)->queryAll();
            if ($res)
                foreach ($res as $d)
                    if ($d['id']!=$row['id'])
                        $dublicate_sales_by_OS_2[] = $d['id'];
            
            
            
            
            //for EMAIL with the same user_agent
            //2012-12-10 skype
            if (
            		count($dublicate_sales_by_OS_2)>=2
            		&&
            		!stristr($pc_info_browser,'iPhone')
            		&&
            		!stristr($pc_info_browser,'iPad')
            		&&
            		!stristr($pc_info_browser,'Android')
            		&&
            		intval($pc_info_screen_resolution_array[0])<1366
            )
            {
            	$sql = "SELECT id FROM `pm_today_gold` WHERE manager_id={$manager_id} AND `date`>=('{$user['payment']['firstpay']}' - INTERVAL 30 DAY ) AND pc_info_os='{$pc_info_os}' AND pc_info_browser='{$pc_info_browser}' AND pc_info_browser_version='{$pc_info_browser_version}' AND pc_info_screen_resolution='{$pc_info_screen_resolution}' ORDER BY id";
            	$res = Yii::app()->db->createCommand($sql)->queryAll();
            	$res_email = array(); 
            	if ($res)
            		foreach ($res as $d)
            			if ($d['id']!=$row['id'])
            				$res_email[] = $d['id'];            	
            	
            	
            	if (count($res_email)>=2)
//            		$dublicate_sales_by_OS_2___EMAIL[] = "http://pinkmeets.com/admin/payments/todayGold/?sale=".$row['id'].",".implode(",", $res_email);
            		$dublicate_sales_by_OS_2___EMAIL[] = "http://meetsi.com/admin/payments/todayGold/?sale=".$row['id'].",".implode(",", $res_email);
            }
        }
        
        //also can you have it email me a report if there are more then 3 duplicate screen resolutions to chris.newnham@yahoo.com
		//i would love an email from all sites if screen resolution is duplicated more then 2 times. except for iphone and ipad sales
		/*if (
			count($dublicate_sales_by_OS_2)>=2 
			&& 
			!stristr($pc_info_browser,'iPhone') 
			&& 
			!stristr($pc_info_browser,'iPad') 
			&& 
			!stristr($pc_info_browser,'Android')
			&&
			intval($pc_info_screen_resolution_array[0])<1366 
		)
        	$dublicate_sales_by_OS_2___EMAIL[] = "http://pinkmeets.com/admin/payments/todayGold/?sale=".$row['id'].",".implode(",", $dublicate_sales_by_OS_2);
        	$dublicate_sales_by_OS_2___EMAIL[] = "http://meetsi.com/admin/payments/todayGold/?sale=".$row['id'].",".implode(",", $dublicate_sales_by_OS_2);*/

        $dublicate_sales_by_OS_2 = ($dublicate_sales_by_OS_2) ? serialize($dublicate_sales_by_OS_2) : "";

        
        
        //payment time
        $sql = "SELECT `updated` FROM `pm_transactions` WHERE user_id=".$user_id." AND `status`='completed' ORDER BY id DESC LIMIT 1";
        $payment_dt = Yii::app()->db->createCommand($sql)->queryScalar();
        
        
        $u = $user;
        $ipFresh = serialize($u['ipFresh']);
        
        $sql = "UPDATE pm_today_gold SET
                    `progress`='done',
                    `date`='{$dt}',
                    `username`='{$u['username']}',
                    `email`=:email,
                    `affid`={$u['affid']},
                    `agent_name`='{$u['agent_name']}',
                    `manager_id`={$u['manager_id']},
                    `manager_name`='{$u['manager_name']}',
                    `master_id`={$u['master_id']},
                    `regdate`='" . $u['payment']['joined'] . "',
                    `payment_dt`='".$payment_dt."',
                    `pics`={$u['pics']},
                    `amount`=:amount,
                    `paymod`='{$u['payment']['paymod']}',
                    `logins`='{$u['activity']['loginCount']}',
                    `msgs`='{$u['msgs']}',
                    `winks`='{$u['winks']}',
                    `country`='{$u['location']['country']}',
                    `region`='{$u['location']['state']}',
                    `city`=:city,
                    `geo_ip_country`=:geo_ip_country,
                    `form`='{$u['form']}',
                    `phone`='{$u['phone']}',
                    `signupip`='{$u['signupip']}',
                    `ip_long` = {$ip},
                    `proxy`='{$u['proxy']}',
                    `proxy_maxmind`=:proxy_maxmind,
                    `ipFresh`='$ipFresh',
                    `dublicate_sales_by_IP`='{$dublicate_sales_by_IP}',
                    `dublicate_sales_by_IP_B`='{$dublicate_sales_by_IP_B}',
                    `pc_info_os`='{$pc_info_os}',
                    `pc_info_browser`='{$pc_info_browser}',
                    `pc_info_browser_version`='{$pc_info_browser_version}',
                    `pc_info_screen_resolution`='{$pc_info_screen_resolution}',
                    `dublicate_sales_by_OS` = '{$dublicate_sales_by_OS}',
                    `dublicate_sales_by_OS_2` = '{$dublicate_sales_by_OS_2}'
                WHERE id={$row['id']} LIMIT 1";
//CHelperSite::vd($sql, 0);
        $dbRes = Yii::app()->db->createCommand($sql)
        			->bindValue(":city", $u['location']['city'], PDO::PARAM_STR)
        			->bindValue(":geo_ip_country", $u['geo_ip_country'], PDO::PARAM_STR)
        			->bindValue(":amount", $u['payment']['amount'], PDO::PARAM_STR)
        			->bindValue(":email", $u['email'], PDO::PARAM_STR)
        			->bindValue(":proxy_maxmind", $proxy_maxmind, PDO::PARAM_STR)
        			->execute();


		if (!$dbRes)
		{
			$resCron .= $sql;
		}
        
		
		
		
		
		//PANAMUS
		if (PANAMUS_USE)
		{
	        $sql = "SELECT * FROM `pm_transactions` WHERE user_id=".$user_id." AND `status`='completed' ORDER BY id DESC LIMIT 1";
	        $txnInfo = Yii::app()->db->createCommand($sql)->queryRow();
	        
			$postPanamus = array(
				'consumer_email'=>$user['email'],
				'consumer_username'=>$user['username'],
				'consumer_password'=>CSecur::decryptByLikeNC( $user['passwd'] ),
				'transaction_amount'=>$txnInfo['amount'],
				'transaction_currency'=>'USD',
				'transaction_payment_method'=>$txnInfo['paymod'],
			);
			$panamus = new Panamus($user_id, true);
			$panamus->saveTransactionInfo($txnInfo['id'], $postPanamus);
		}
		
		
		
		
        $done++;
    }
}


$resCron .= "  DONE: " .$done.  " (" . (time()-$startTime) . " s)";		
    	





//update email_bounced
//TO DO

/*
$doneBounced = 0;
$sql = "SELECT id, email FROM users_paid WHERE `email_bounced`='' ORDER BY id DESC LIMIT 100";
$rows = $db->getAll($sql);
if ($rows)
{
    foreach ($rows as $row)
    {
        $email = $row['email'];
        
        $email_bounced = $db->getOne("SELECT COUNT(id) FROM bounce_log WHERE email='{$email}' LIMIT 1");
        $email_bounced = ($email_bounced) ? '1' : '0';

        $sql = "UPDATE users_paid SET `email_bounced`='{$email_bounced}' WHERE id={$row[id]} LIMIT 1";
//if (isset($_SERVER['HTTP_HOST']))
//    vd($email . ' - ' . $sql, 0);
        $db->query($sql);           
        
        if ($db->msg)
    	{
            vd($sql,0);
        }
        
        $doneBounced++;
    }
}
echo ", DONE BOUNCED: " .$doneBounced.  " (" . (time()-$startTime) . " s)";
*/

//update agent last login
$doneAgentLastLogin = 0;
$sql = "SELECT * FROM cs_users WHERE last_login > (NOW() - INTERVAL 5 MINUTE)";
$rows = Yii::app()->dbSTATS->createCommand($sql)->queryAll();
if ($rows)
{
    foreach ($rows as $row)
    {
        $affid = $row['id'];
        
        $sql = "UPDATE `pm_today_gold` SET `agent_last_login`='{$row['last_login']}' WHERE affid={$affid}";
        Yii::app()->db->createCommand($sql)->execute();           
        
        $doneAgentLastLogin++;
    }
}
$resCron .= ", DONE AGENT LAST LOGIN: " .$doneAgentLastLogin.  " (" . (time()-$startTime) . " s)";






//update	Logins/Messages/Winks every 24 hours

$doneLMW = 0;
$doneLMW_userDeleted = 0;
//in "todays gold users" i need a way to see logins/messages/winks it doesnt not have to be in realtime.it can be 24 hours old stats.
$countRecords = Yii::app()->db->createCommand("SELECT COUNT(id) FROM `pm_today_gold`")->queryScalar();
$countRecords = intval(ceil(1.2*$countRecords/1440));
if (!$countRecords) $countRecords = 1;

$sql = "SELECT id, user_id FROM `pm_today_gold` ORDER BY last_update_lmw LIMIT {$countRecords}";
$needUpdateLMW = Yii::app()->db->createCommand($sql)->queryAll();
if ($needUpdateLMW)
    foreach($needUpdateLMW as $r)
    {
        $user_id = $r['user_id'];
        
        $sql = "SELECT loginCount FROM `users_activity` WHERE user_id='{$user_id}' LIMIT 1";
        $logins = Yii::app()->db->createCommand($sql)->queryScalar();
        
        if ($logins===false)//user deleted
        {
            $sql = "UPDATE `pm_today_gold` SET `last_update_lmw`='2022-01-01 00:00:00' WHERE id={$r['id']} LIMIT 1";
            Yii::app()->db->createCommand($sql)->execute();
            $doneLMW_userDeleted++;
        }
        else
        {
            $sql = "SELECT COUNT(id_from) FROM `profile_messages` WHERE id_from='{$user_id}'";
            $msgs = Yii::app()->db->createCommand($sql)->queryScalar();
            
            $sql = "SELECT COUNT(id_from) FROM `profile_winks` WHERE id_from='{$user_id}'";
            $winks = Yii::app()->db->createCommand($sql)->queryScalar();        
            
            $sql = "UPDATE `pm_today_gold` SET `logins`='{$logins}', `msgs`={$msgs}, `winks`={$winks}, `last_update_lmw`=NOW() WHERE id={$r['id']} LIMIT 1";
            Yii::app()->db->createCommand($sql)->execute();
        }
       
        
        $doneLMW++;
    }
$resCron .= ", DONE UPDATE LWM COUNTS: " .$doneLMW ."(".$doneLMW_userDeleted.")". " (" . (time()-$startTime) . " s)";




//update billing address for zombaio
$doneBillingInfo = 0;
$sql = "SELECT id, user_id FROM `pm_today_gold` WHERE paymod='zombaio' AND `billing_info`='' ORDER BY id DESC LIMIT 100";
$rows = Yii::app()->db->createCommand($sql)->queryAll();
if ($rows)
{
    foreach ($rows as $row)
    {
        $user_id = $row['user_id'];
        
        $billing_info = '-';
        
        $sql = "SELECT * FROM `pm_zombaio_postback` WHERE user_id='{$user_id}' AND action='user.add' ORDER BY id DESC LIMIT 1";
        $tr = Yii::app()->db->createCommand($sql)->queryRow();

        if ($tr)
        {
            $billing_info = $tr['nameoncard'].",<br />";
            $billing_info.= $tr['address'].",<br />";
            $billing_info.= $tr['country']."/".$tr['region']."/".$tr['city']."/".$tr['postal'];
        }
        $billing_info = serialize($billing_info);
            
        
        $sql = "UPDATE `pm_today_gold` SET `billing_info`='{$billing_info}' WHERE id={$row['id']} LIMIT 1";
        Yii::app()->db->createCommand($sql)->execute();           
        
        $doneBillingInfo++;
    }
}
$resCron .= ", DONE BILLING INFO: " .$doneBillingInfo.  " (" . (time()-$startTime) . " s)";


//fixing proxy if was unavailable
//once per hour at 25' minute
if (date("i")=='25')
{
    $sql = "SELECT * FROM pm_today_gold WHERE `progress`='done' AND proxy='' ORDER BY id LIMIT 1";
	$row = Yii::app()->db->createCommand($sql)->queryRow();
	
	if ($row)
	{
        $proxy = @file_get_contents("http://www.winmxunlimited.net/utilities/api/proxy/check.php?ip=" . $row['signupip']);
        $proxy = trim($proxy);//!!!
        if (in_array($proxy, array('Tor','Public','0','Invalid IP')))
        {
    	    $sql = "UPDATE pm_today_gold SET
                    `proxy`=:proxy
                	WHERE id={$row['id']} LIMIT 1";

	        Yii::app()->db->createCommand($sql)
        			->bindValue(":proxy", $proxy, PDO::PARAM_STR)
        			->execute(); 

        	$resCron .= ", FIXED PROXY (record id):" .$row['id'] . " (" . (time()-$startTime) . " s)";
        }
	}
}





//EMAILS ABOUT DUPLICATES
$duplBody = "";
if ($dublicate_sales_by_OS_2___EMAIL)
{
	$duplBody.= "Screen resolution dupes: \n";
	$duplBody.= implode("\n", $dublicate_sales_by_OS_2___EMAIL);
//$resCron .= ", dublicate_sales_by_OS_2___EMAIL count sent: " .count(dublicate_sales_by_OS_2___EMAIL) . " (" . (time()-$startTime) . " s)";
	
	$duplBody.= "\n\n";
}
/*if ($dublicate_sales_by_IP_B___EMAIL)
{
	$duplBody.= "IP class B dupes \n";
	$duplBody.= implode("\n", $dublicate_sales_by_IP_B___EMAIL);
//$resCron .= ", dublicate_sales_by_IP_B___EMAIL count sent: " .count(dublicate_sales_by_IP_B___EMAIL) . " (" . (time()-$startTime) . " s)";
}*/
if ($duplBody)
{
	mail("admin@meetsi.com", "PM, Today Gold duplicates report", $duplBody);
}




    	return $resCron;
    } 	
	
}


