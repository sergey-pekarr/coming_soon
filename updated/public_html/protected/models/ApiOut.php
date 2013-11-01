<?php
class ApiOut
{
	
	public $apis = array('cupid', 'cupid_flirt');//available apis (add ENUM in `log_api_out`)
	
	public $apiShift = array(
		'cupid' => 1,//days
		'cupid_flirt' => 3,//days
	);
	
	public $skipEmailsLike = array('@lo.lo', '@tst.pp.ua');
	
	
	public function __construct()
	{
		if (DEBUG_IP)
		{ 
			//$this->apis = array('cupid_flirt');
			
			if (LOCAL_OK)
				$this->skipEmailsLike = array();
		}
	}
	
	
	public function userSent($userId, $api)
	{
		$sql = "SELECT COUNT(id) FROM log_api_out WHERE api='{$api}' AND user_id={$userId}";
		$userSent = Yii::app()->db->createCommand($sql)->queryScalar();
		return ($userSent==0) ? false : true; 		
	}
	
	/*
	 * $fixMode - send user skipped in prior month...
	 */
	public function ApiPost($fixMode=false)
	{
		$userSent = array();//prevent sending the same user for different apis at one ApiPost() 
		$countPost = 0;
		
		foreach ($this->apis as $api)
		{
			if ($api=='cupid') continue;//email 20130827
			if ($api=='cupid_flirt') continue;//email 20130827
			
			
			$countPost = 0;
			
			$sql = "SELECT MAX(user_id) FROM log_api_out WHERE api='{$api}' LIMIT 1";
			$userIdLast = Yii::app()->db->createCommand($sql)->queryScalar();
			if (!$userIdLast) $userIdLast=0;
			
			if (!$fixMode)
			{
			
$limitPost = (DEBUG_IP) ? 1 : rand(150,300);
			
				if ($api=='cupid' || $api=='cupid_flirt')
				{
					$countries = "'US','CA','AU'";//skype Evgeniy 2013-03-13	 ,'GB'
					
					$sql = "SELECT u.id, u.email, a.joined FROM users as u, users_activity as a, users_location as l 
						WHERE 
						u.id>{$userIdLast} 
						AND 
						u.promo='0'
						AND 
						u.role<>'justjoined' 
						AND
						l.country IN ({$countries})
						AND
						u.id=a.user_id
						AND 
						u.id=l.user_id
						ORDER BY u.id LIMIT 1000
					";
				}
				else
					$sql = "SELECT u.id, u.email, a.joined FROM users as u, users_activity as a 
						WHERE 
						u.id>{$userIdLast} 
						AND 
						u.promo='0'
						AND
						u.role<>'justjoined' 
						AND 
						u.id=a.user_id
						ORDER BY u.id LIMIT 1000
					";
			}
			/*else
			{
$limitPost = (DEBUG_IP) ? 1 : rand(15,20);
				
				if ($api=='cupid' || $api=='cupid_flirt')
				{
					$countries = "'US','CA','GB','AU'";
											
					$sql = "SELECT u.id, u.email, a.joined FROM users as u, users_activity as a, users_location as l
						WHERE
						u.promo='0'
						AND
						l.country IN ({$countries})
						AND
						u.id=a.user_id
						AND
						u.id=l.user_id
						ORDER BY RAND() LIMIT 1000
					";
				}
				else
					$sql = "SELECT u.id, u.email, a.joined FROM users as u, users_activity as a
						WHERE
						u.promo='0'
						AND
						u.id=a.user_id
						ORDER BY RAND() LIMIT 1000
					";
			}*/
			$users = Yii::app()->db->createCommand($sql)->queryAll();
			
//CHelperSite::vd($users, 0);			
			if ($users)
				foreach ($users as $u)
				{
					if ($countPost>=$limitPost)
						break;
					
					//skip by email like @tst.pp.ua, @lo.lo, ...
					$continue = false;
					foreach($this->skipEmailsLike as $se)
						if (stristr($u['email'], $se))
							$continue = true;
					if ($continue)
						continue;
					
					//skip by joined time
					$joinedTime = strtotime($u['joined']);
					if ( !DEBUG_IP && $joinedTime > ( time() - 24*60*60*$this->apiShift[$api] ) )
						continue;
					
					//already send in this cron job
					if ($userSent && in_array($u['id'], $userSent))
						continue;
						
					//check if already sent (presents in db log)
					if ($this->userSent($u['id'], $api))
						continue;
					
//CHelperSite::vd($u, 0);
                   	//$result = call_user_func($function, $user);
                   	switch ($api)
                   	{
                   		case 'cupid':
                   			$result = $this->_call_cupid($u['id']);
                   			break;
                   		case 'cupid_flirt':
                   			$result = $this->_call_cupid_flirt($u['id']);
                   			break;                   			
                   		default: continue;
                   	}
                   	
    //echo date.'<br/> - ';
                    
    				// log results
                    //$response = addslashes($result['response']);
                    $sql = "INSERT INTO `log_api_out` (
                    	`api`, 
                    	`status`,
                    	`date`, 
                    	`added`, 
                    	`response`, 
                    	`user_id`
                    ) VALUES (
                    	:api, 
                    	:status, 
                    	CURDATE(),
                    	NOW(), 
                    	:response, 
                    	:user_id
                    )";
                    Yii::app()->db->createCommand($sql)	
                    	->bindValue(":api", $api, PDO::PARAM_STR)
                    	->bindValue(":status", $result['result'], PDO::PARAM_STR)
                    	->bindValue(":response", $result['response'], PDO::PARAM_STR)
                    	->bindValue(":user_id", $u['id'], PDO::PARAM_INT)
                    	->execute();
                    
                    $code = "";
                    if ($result['result']==1) $code = "OK";
                    if ($result['result']==-1) $code = "ERROR: {$result['response']}";

                    //echo date("r"), " [{$result[name]}] --> {$code}, USER ID: {$user_id} (nc) \n";
                    $res = date("r") . " [{$result['name']}] --> {$code}, USER ID: {$u['id']}";
					CHelperLog::logFile('api_out.log', $res);	

if (DEBUG_IP)
{
	CHelperSite::vd('****************************************************',0);                    
	CHelperSite::vd($res,0);
}

					$countPost++;
					
				}
//echo 'sent:' . $countPost;			
			
		}
		
		return $countPost;
	}
	
	
	
	/*
	 * CUPID
	 */
	private function _call_cupid($userId)
	{
		$profile = new Profile($userId);
		$profileData = $profile->getData();
		
		$user['zip'] = $profileData['location']['zip'];
		if (!$user['zip'])
			$user['zip'] = 33408;
		
		$country = Yii::app()->location->getCountryCode3($profileData['location']['country']);
		if (!$country) $country = "USA";

	    /* Set the API interface url, testing or live location */
		switch($profileData['location']['country'])
		{
				
			default: 	$api_url = 'http://www.benaughty.com/coreg.php?a_aid=a60779a6&a_bid=64fb6f9a&data1='; //live interface
						$successDomain="benaughty.com";
						$a_bid = "64fb6f9a";
						break;
		}
	    $api_url .= 'pm_'.$userId;//&data1=
	    
		$d = explode("-", $profileData['birthday']);
		$BdayYear = $d[0];
		$BdayMonth = $d[1];
		$BdayDay = $d[2];
		
		if ($profileData['gender']=='M' || $profileData['gender']=='F')
			$sex = $profileData['gender'];
		else 
		{
			if ($profileData['gender']=='M' && $profileData['looking_for_gender']=='F')
				$sex='CMF';
			elseif ($profileData['gender']=='M' && $profileData['looking_for_gender']=='M')
				$sex='CMM';
			else
				$sex='CFF';
		}
		
		if ( ($sex=='M' || $sex=='F') && $sex!=$profileData['looking_for_gender'])
			$orientation = 'S';
		elseif ( ($sex=='M' || $sex=='F') && $sex==$profileData['looking_for_gender'])
			$orientation = 'G';
		else
			$orientation = 'B';
		
		
		$res = explode('@',$profileData['email']);
		$username = $res[0];		
		$username = (strlen($username) < 5) ? str_pad($username, 5 , "_", STR_PAD_RIGHT) : substr($username , 0 , 19);
		
		$post_fields = array();
		$post_fields['country'] = $country;
		$post_fields['dob'] = $BdayDay.'/'.$BdayMonth.'/'.$BdayYear;
		$post_fields['email'] = $profileData['email'];
		$post_fields['scr'] = $username;//strlen('PM'.$profileData['username']) < 5 ? str_pad('PM'.$profileData['username'], 5 , "_", STR_PAD_RIGHT) : substr('PM'.$profileData['username'] , 0 , 19);
		$post_fields['sex'] = $sex; // User’s sex M – man F – woman CMF  - couple man + woman CMM  - couple man + man CFF  - couple woman + woman 
		$post_fields['town'] = mb_substr($profileData['location']['city'], 0, 31);
		$post_fields['orientation'] = $orientation;//User’s orientation S – straight G – gay B – bisexual
		$post_fields['Looking_for'] = '12345';//Casual sites: 1 – saucy email or chat 2 – casual encounters 3 –discreet relationship 4 – couples 5 – other
		
		$post_fields['a_aid'] = "a0d7e0f9";

if (DEBUG_IP) CHelperSite::vd($post_fields, 0);		
		
		
		$httpPost = CHelperSite::curl_request($api_url, $post_fields);
CHelperSite::vd($httpPost, 0);
		$result = 0;
		if (trim($httpPost)=="1") $result = 1; else $result = -1;//if (stristr($httpPost, $successDomain)) $result = 1; else $result = -1;
		 
		$log = array(
				"response" => $httpPost,
				"result" => $result,
				"name" => "cupid",
		);	    
	    
  		/*$d = explode("-", $profileData['birthday']);
	    $BdayYear = $d[0];
	    $BdayMonth = $d[1];
	    $BdayDay = $d[2];
	


	    
	    
	
	    $post_fields = array();
	    $post_fields['email'] = $profileData['email'];
	    $post_fields['scr'] = strlen('PM'.$profileData['username']) < 5 ? str_pad('PM'.$profileData['username'], 5 , "_", STR_PAD_RIGHT) : substr('PM'.$profileData['username'] , 0 , 19);
	    $post_fields['dob'] = $BdayDay.'/'.$BdayMonth.'/'.$BdayYear;
	    $post_fields['sex'] = $profileData['gender'];
	    $post_fields['orientation'] = 'S'; // S - straight G - gay B - bisexual 
	    $post_fields['Looking_for'] = '12345'; // 1 - saucy email or chat 2 - casual encounters 3 - discreet relationship 4 - couples 5 - other
	    $post_fields['country'] = $country;
	    $post_fields['town'] = $profileData['location']['city'];
	    $post_fields['a_aid'] = "a60779a6";
	    $post_fields['a_bid'] = $a_bid;//"daf0445e";	    
//if (DEBUG_IP) { CHelperSite::vd($api_url, 0); CHelperSite::vd($post_fields); }	    
	    $httpPost = $this->curlRequest($api_url, $post_fields);//CHelperSite::curl_request($api_url, $post_fields);
//if (DEBUG_IP) CHelperSite::vd($httpPost);
	    $result = 0;
	    if (stristr($httpPost, $successDomain)) $result = 1; else $result = -1;
	    
	    $log = array(
		    "response" => $httpPost,
		    "result" => $result,
		    "name" => "cupid",
	    );*/
//CHelperSite::vd($log, 0);	    
	    return $log;	    
	    
	    
	}

	
	/*
	 * CUPID
	*/
	private function _call_cupid_flirt($userId)
	{
		$profile = new Profile($userId);
		$profileData = $profile->getData();
	
		$user['zip'] = $profileData['location']['zip'];
		if (!$user['zip'])
			$user['zip'] = 33408;
	
		$country = Yii::app()->location->getCountryCode3($profileData['location']['country']);
		if (!$country) $country = "USA";
		
		$api_url = 'http://www.flirt.com/coreg.php?a_aid=a60779a6&a_bid=f4c73dbd&data1=';
		$api_url .= 'pm_'.$userId;//&data1=
		
		$d = explode("-", $profileData['birthday']);
		$BdayYear = $d[0];
		$BdayMonth = $d[1];
		$BdayDay = $d[2];
		
		if ($profileData['gender']=='M' || $profileData['gender']=='F')
			$sex = $profileData['gender'];
		else 
		{
			if ($profileData['gender']=='M' && $profileData['looking_for_gender']=='F')
				$sex='CMF';
			elseif ($profileData['gender']=='M' && $profileData['looking_for_gender']=='M')
				$sex='CMM';
			else
				$sex='CFF';
		}
		
		if ( ($sex=='M' || $sex=='F') && $sex!=$profileData['looking_for_gender'])
			$orientation = 'S';
		elseif ( ($sex=='M' || $sex=='F') && $sex==$profileData['looking_for_gender'])
			$orientation = 'G';
		else
			$orientation = 'B';
		
		
		$res = explode('@',$profileData['email']);
		$username = $res[0];		
		$username = (strlen($username) < 5) ? str_pad($username, 5 , "_", STR_PAD_RIGHT) : substr($username , 0 , 19);
		
		$post_fields = array();
		$post_fields['country'] = $country;
		$post_fields['dob'] = $BdayDay.'/'.$BdayMonth.'/'.$BdayYear;
		$post_fields['email'] = $profileData['email'];
		$post_fields['scr'] = $username;//strlen('PM'.$profileData['username']) < 5 ? str_pad('PM'.$profileData['username'], 5 , "_", STR_PAD_RIGHT) : substr('PM'.$profileData['username'] , 0 , 19);
		$post_fields['sex'] = $sex; // User’s sex M – man F – woman CMF  - couple man + woman CMM  - couple man + man CFF  - couple woman + woman 
		$post_fields['town'] = mb_substr($profileData['location']['city'], 0, 31);
		$post_fields['orientation'] = $orientation;//User’s orientation S – straight G – gay B – bisexual
		$post_fields['Looking_for'] = '12345';//Casual sites: 1 – saucy email or chat 2 – casual encounters 3 –discreet relationship 4 – couples 5 – other
		
		$post_fields['a_aid'] = "a60779a6";

if (DEBUG_IP) CHelperSite::vd($post_fields, 0);		
		
		
		$httpPost = CHelperSite::curl_request($api_url, $post_fields);
		//CHelperSite::vd($httpPost);
		$result = 0;
		if (trim($httpPost)=="1") $result = 1; else $result = -1;//if (stristr($httpPost, "flirt.com")) $result = 1; else $result = -1;
		 
		$log = array(
				"response" => $httpPost,
				"result" => $result,
				"name" => "cupid",
		);
		//CHelperSite::vd($log, 0);
		return $log;
		 
		 
	}	


	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	

	
	
	public function curlRequest($url, $post="")
	{
	    $ch = curl_init();
	    $user_agent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; ru; rv:1.8.0.9) Gecko/20061206 Firefox/1.5.0.9";
	    $newurl = $url;
	    if (is_array($post))
	    {
	        $fields = http_build_query($post);
	        curl_setopt($ch, CURLOPT_POST, 1);
	        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
	    }
	    if (!is_array($post) && strlen($post) > 0)
	    {
	        $fields = $post;
	        curl_setopt($ch, CURLOPT_POST, 0);
	        $newurl = $url . "?" . $fields;
	    }
	    curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
	    
        //curl_setopt($ch, CURLOPT_REFERER, SITE_URL);
	    
	    curl_setopt($ch, CURLOPT_URL, $newurl);
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
	    curl_setopt($ch, CURLOPT_HEADER, 0);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

	    $result = curl_exec($ch);
	    $info = curl_getinfo($ch);
	    
	    curl_close($ch);
	    return trim($result);	
	}
	
	
	


	
	
	
	
	
}


