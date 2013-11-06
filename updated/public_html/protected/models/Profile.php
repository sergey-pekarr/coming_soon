<?php

class Profile
{
    private $id;
    private $data;
    private $useCache = true;
   
	/**
	 * Constructor.
	 * @param integer $id
     * @param boolean $useCache - use cache (for import or other)
	 */
	public function __construct($id=0, $useCache=true)
	{
		$this->id = intval($id);
		
		$this->useCache = $useCache;
		
        $this->getData();//!!!
	}    
    
    /**
     * update data in cache
     */
    private function _cacheUpdate()
    {
        if ( $this->useCache && $this->id &&  isset($this->data['id']) && $this->data['id']==$this->id)
        {
            $mkey = "profile_".$this->id;
            Yii::app()->cache->set($mkey, $this->data, Yii::app()->params->cache['profile']);
        }
    }
    /**
     * get data from cache
     */
    private function _cacheGet()
    {
        if ( $this->useCache && $this->id )
        {
            $mkey = "profile_".$this->id;
            return Yii::app()->cache->get( $mkey );
        }
        else
            return false;
    }
    /**
     * DELETE data in cache
     */
    private function _cacheDelete()
    {
        if ( $this->useCache && $this->id )
        {
            $mkey = "profile_".$this->id;
            Yii::app()->cache->delete($mkey);
        }
    }
	/**
	 * get user data from DB
	 */
	public function getData()
	{
/*        if ( !$this->id )
        {
//FB::error($_SESSION['user']['data'],'1');
            //Guest
            if (isset($_SESSION['user']['data']))
            {
                $this->data = $_SESSION['user']['data'];
            }
            else
            {
                $this->data['gender'] = 'M';
                $this->data['looking_for_gender'] = 'F';
    
                $location_id = Yii::app()->location->findLocationIdByIP();        
                $this->data['location'] = Yii::app()->location->getLocation( $location_id );
                 
                $this->data['settings']['ageMin'] = 18;
                $this->data['settings']['ageMax'] = 99;                 
                $this->data['settings']['onlineNow'] = 0;
                $this->data['settings']['withPhoto'] = 1;
                
                $modelPromos = new Promos;
                $this->data['promos'] = $modelPromos->createPromosLocationsForGuest($this->data);
           
                $_SESSION['user']['data'] = $this->data;
            }
//FB::error($_SESSION['user']['data'],'2');
            return $this->data;
        }
*/       

		
		
		if ( $this->id )
		{
	        $this->data = $this->_cacheGet();
	        if ( $this->data === false )
	        {
	            if ($this->useCache) FB::warn('CACHE IS EMPTY FOR USER '.$this->id);
	            
	            $this->data = Yii::app()->db
	                    ->createCommand("SELECT * FROM users WHERE id=:id LIMIT 1")
	                    ->bindValue(":id", $this->id, PDO::PARAM_INT)
	                    ->queryRow();
	            //Age
	            //$age = time() - strtotime($this->data['birthday']);
	            //$this->data['age'] = floor($age / 31556926); // 31556926 seconds in a year
	            $this->data['age'] = CHelperProfile::getAge($this->data['birthday']);
	            
	            //IMAGE
	            $this->data['image'] = Yii::app()->db
	                    ->createCommand("SELECT * FROM user_image WHERE user_id=:user_id LIMIT ".IMG_PROFILE_MAX)
	                    ->bindValue(":user_id", $this->id, PDO::PARAM_INT)
	                    ->queryAll();	            
	                    
	            //LOCATION
				$this->data['location'] = Yii::app()->db
	            	->createCommand("SELECT * FROM users_location WHERE user_id={$this->id} LIMIT 1")
	                ->queryRow();
	            $this->data['location']['stateName'] = Yii::app()->location->getRegionName($this->data['location']['country'], $this->data['location']['state']);
	                                
	            
	            //INFO
	            $this->data['info'] = Yii::app()->db
	                    ->createCommand("SELECT * FROM users_info WHERE user_id=:user_id LIMIT 1")
	                    ->bindValue(":user_id", $this->id, PDO::PARAM_INT)
	                    ->queryRow();
	                
	           
	            //PERSONAL
	            $this->data['personal'] = Yii::app()->db
	                    ->createCommand("SELECT * FROM users_personal WHERE user_id=:user_id LIMIT 1")
	                    ->bindValue(":user_id", $this->id, PDO::PARAM_INT)
	                    ->queryRow();
	            
	            //ACTIVITY
	            $this->data['activity'] = Yii::app()->db
	                    ->createCommand("SELECT * FROM users_activity WHERE user_id=:user_id LIMIT 1")
	                    ->bindValue(":user_id", $this->id, PDO::PARAM_INT)
	                    ->queryRow();            
	            
	            //SETTINGS
	            $this->data['settings'] = Yii::app()->db
	                    ->createCommand("SELECT * FROM users_settings WHERE user_id=:user_id LIMIT 1")
	                    ->bindValue(":user_id", $this->id, PDO::PARAM_INT)
	                    ->queryRow();
	            
	            /*$this->data['location'] = Yii::app()->db
	                    ->createCommand("SELECT * FROM users_location WHERE user_id=:user_id LIMIT 1")
	                    ->bindValue(":user_id", $this->id, PDO::PARAM_INT)
	                    ->queryRow();*/
	            
	            //$this->data['id_secur'] = Yii::app()->secur->encryptID($this->id);
	          
	            
	            //EXTERNALS LOGINS
	            //FB
	            $this->data['ext']['facebook'] = Yii::app()->db
	                    ->createCommand("SELECT * FROM ext_FB WHERE user_id=:user_id LIMIT 1")
	                    ->bindValue(":user_id", $this->id, PDO::PARAM_INT)
	                    ->queryRow();  
	            
	            $this->_cacheUpdate();
	        }
	        
	        
	        //tmp while cache not updated
	        if (!isset($this->data['expire_at']))
	        {
	        	$this->data['expire_at'] = "0000-00-00";
	        }
//CHelperSite::vd($this->data);	        
	        //fix expired for gold paid before 2012-07-24
			if ($this->data['role']=='gold' && $this->data['expire_at'] == "0000-00-00" && $this->data['promo']=='0')
		    {
		    	$this->getPayment();
		        	
		    	if ( isset( $this->data['payment']['price_id'] ) )
			    {
			    	$option = CHelperPayment::getOptionRow($this->data['payment']['price_id']);
			    	
			    	$term = ($option['term_trial']!=0) ? $option['term_trial'] : $option['term'];
			    	$expire_at = date("Y-m-d", ( strtotime($this->data['payment']['lastpay']) + 24*60*60*($term+2)) );
						
			    	$this->Update('expire_at', $expire_at);
			    }
			}
			
			
			//MAKE FREE AFTER expire + 10 days
			if ($this->data['role']=='gold' && $this->data['expire_at']!="0000-00-00" && $this->data['promo']=='0')
			{
		        if ( time() > ( 11*24*60*60 + strtotime( $this->data['expire_at'] )) )
		        	$this->makeFree("expired");
			}	
			
		}

        
        
        //user online
        if (isset($this->data['activity']['activityLast']))
			$this->data['isOnline'] = ( (time() - strtotime($this->data['activity']['activityLast']))<Yii::app()->params['user']['isOnline'] ) ? true : false;
	    else
	    	$this->data['isOnline'] = false;
			
        return $this->data;
	}     
    
	/*
	 * //PAYMENT
	 * table user_payment
	 * using in admin area 
	 */
	public function getPayment()
	{
		if (!$this->id) return false;
		
	    if (!isset($this->data['payment']) || !$this->data['payment'])
	    {
			$this->data['payment'] = Yii::app()->db
		        		->createCommand("SELECT * FROM user_payment WHERE id=:id LIMIT 1")
		                ->bindValue(":id", $this->id, PDO::PARAM_INT)
		                ->queryRow();
			                
		    $this->_cacheUpdate();        
			            
	    }        

	    return $this->data['payment'];
	}
	
	/*
	 * return N of primary image
	 */
	public function getImgPrimary()
	{
		if (!$this->id || !$this->data['image'])
			return false;
		
		$n = $this->data['image'][0]['n'];
		foreach ($this->data['image'] as $i)
			if ($i['primary'])
			{
				$n = $i['n'];
				break;
			}
			
		return $n;
	}
	
    /**
     * set approve if need
     * call from WebUser.php ONLY!!! and after dataread
     * not use cache!!!
     */
    public function simulateApproved()
    {
        //Simulate APPROVED
        if ($this->id && $this->data['role']=='limited')
        {
            $this->data['role'] = 'approved';
        }
    }
    
	public function getDataValue($key, $subKey='', $subKey2='')
	{
        if ($subKey && $subKey2)
            return @$this->data[$key][$subKey][$subKey2];
        elseif ($subKey)
            return @$this->data[$key][$subKey];
        else
            return @$this->data[$key];
	}
	public function getInfoValue($key)
	{
        return @$this->data['info'][$key];
	} 
	public function getLocationValue($key)
	{
        return @$this->data['location'][$key];
	}    
	public function getPersonalValue($key)
	{
        return @$this->data['personal'][$key];
	}
	public function getSettingsValue($key)
	{
        return @$this->data['settings'][$key];
	}
	public function getActivityValue($key)
	{
        return @$this->data['activity'][$key];
	}

	
	//calls from admin area only
	public function getApiOut()
	{
		if (!$this->id) return false;
		
	    if (!isset($this->data['apiOut']))
	    {
			$this->data['apiOut'] = Yii::app()->db
		        ->createCommand("SELECT * FROM `log_api_out` WHERE user_id=:id ORDER BY id")
		        ->bindValue(":id", $this->id, PDO::PARAM_INT)
		        ->queryAll();
			                
		    $this->_cacheUpdate();
	    }

	    return $this->data['apiOut'];
	}	
	
	
    public function createAllTables($data=array(), $promoIs=false)
    {
        if (!$this->id)
        {
            return;
        }
		
        //Location        
        /*if (isset($data['promo']) && $data['promo']=='1')
        {
        	$sql="INSERT INTO users_location (user_id) VALUES ({$this->id})";
	        Yii::app()->db->createCommand($sql)->execute();
        }
        else*/
        {
            $location_id = (isset($data['location_id'])) ? $data['location_id'] : Yii::app()->location->findLocationIdByIP();
            $location = Yii::app()->location->getLocation($location_id);
			
            if (isset($data['zip'])) 
            	$zip = $data['zip'];
            elseif (isset($location['zip']))
            	$zip = $location['zip'];
            else
            	$zip = '';
            
            $sql="INSERT INTO users_location (
                user_id,
                country,
                state,
                city,
                zip,
                latitude,
                longitude
            ) VALUES (
                :id,
                :country,
                :state,
                :city,
                :zip,
                :latitude,
                :longitude 
            )";
            
	        Yii::app()->db->createCommand($sql)
	        	->bindValue(":id", $this->id, PDO::PARAM_INT)
	        	->bindValue(":country", $location['country'], PDO::PARAM_STR)
	        	->bindValue(":state", $location['state'], PDO::PARAM_STR)
	        	->bindValue(":city", $location['city'], PDO::PARAM_STR)
	        	->bindValue(":zip", $zip, PDO::PARAM_STR)
	        	->bindValue(":latitude", $location['latitude'], PDO::PARAM_STR)
	        	->bindValue(":longitude", $location['longitude'], PDO::PARAM_STR)
	        	->execute();
        }
            

        
        //secut_id
        $sql = "INSERT INTO users_secur_id (user_id, secur_id) VALUES ({$this->id}, '".Yii::app()->secur->encryptID($this->id)."')";
        Yii::app()->db->createCommand($sql)->execute();
        
        //users_info        
        if ($promoIs)
        {
        	$ip = '0.0.0.0';
        	$http_user_agent = "";
        }            
        else
        {
        	if ( isset($data['ip']) && $data['ip'])
        		$ip = $data['ip'];
        	else	
        		$ip = Yii::app()->location->getIPReal();
        		
        	$http_user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : "";
        }
		
        if (isset($data['ref_url']))
        	$ref_url = $data['ref_url'];
        elseif (Yii::app()->session['ref_url'])
        	$ref_url = Yii::app()->session['ref_url'];
		elseif (isset( $_SERVER['HTTP_REFERER'] ))
			$ref_url = $_SERVER['HTTP_REFERER'];
		else
			$ref_url = "";
        
		$ref_domain = CHelperSite::parse_url_domain($ref_url);
		
        $sql="INSERT INTO users_info (user_id, ip_signup, ip_signup_long, ip_last, user_agent, ref_url, ref_domain) VALUES ({$this->id}, :ip_signup, :ip_signup_long, :ip_last, :user_agent, :ref_url, :ref_domain)";
        Yii::app()->db->createCommand($sql)
            ->bindValue(":ip_signup", $ip, PDO::PARAM_STR)
            ->bindValue(":ip_last", $ip, PDO::PARAM_STR)
            ->bindValue(":ip_signup_long", ip2long($ip), PDO::PARAM_INT)
            ->bindValue(":user_agent", $http_user_agent, PDO::PARAM_STR)
            ->bindValue(":ref_url", $ref_url, PDO::PARAM_STR)
            ->bindValue(":ref_domain", $ref_domain, PDO::PARAM_STR)
            ->execute();        


        //personal
        $sql = "INSERT INTO users_personal (user_id) VALUES ({$this->id})";
        Yii::app()->db->createCommand($sql)->execute();

        //activity
        $sql = "INSERT INTO users_activity (user_id, joined) VALUES ({$this->id}, NOW())";
        Yii::app()->db->createCommand($sql)->execute();

        //settings
        $sql = "INSERT INTO users_settings (user_id) VALUES ({$this->id})";
        Yii::app()->db->createCommand($sql)->execute();
        
		//dating
		$sql="INSERT INTO users_dating (user_id) VALUES ({$this->id})";
		Yii::app()->db->createCommand($sql)->execute();				
        /*if (!$promoIs)
        {
            $modelPromos = new Promos;
            $modelPromos->createPromosLocationsForUserFromSession($this->id, $_SESSION['user']['data']['promos']);
        }*/

		
		
        //init panamus and store panamus_id
        if ( PANAMUS_USE && !$promoIs )
        {
        	$panamus = new Panamus($this->id);
        	
        }
        
        
		$this->getData();//!!!
		
		//For signup form: moved the code to UserRegistrationStep2Form
		//CHelperAutoFlirt::buildFakePlan($this->id, $this->getDataValue('gender'), $this->getDataValue('looking_for_gender'), 
		//	$this->getLocationValue('country'), $this->getLocationValue('latitude'), $this->getLocationValue('longitude'),  
		//	date('Y-m-d H:i:s'));
		
		//How about zsignup?
    }
    
    
    /**
     * Create new user
     * return created user ID
     */
    public function Registration($data)
    {
//FB::warn($data);
        $sql="INSERT INTO users 
        		(email, username, password, passwd, role, birthday, gender, looking_for_gender, promo, form, affid, sbc) 
        		VALUES 
        		(:email, :username, :password, :passwd, :role, :birthday, :gender, :looking_for_gender, :promo, :form, :affid, :sbc)
        ";
        $connection=Yii::app()->db;
        $command=$connection->createCommand($sql);
        
        $data['email'] = trim($data['email']);//2012-11-01
        if (!$data['email']) return false;
        $command->bindValue(":email", $data['email'], PDO::PARAM_STR);
        
        $username = (isset($data['username']) && $data['username']) ? $data['username'] : Profile::usernameCreateFromEmail($data['email']);
        $command->bindValue(":username",$username,PDO::PARAM_STR);
        
        $password = MD5(SALT.$data['password']);
        $command->bindValue(":password",$password,PDO::PARAM_STR);
        
        //I hope we will not use this field in the future... - sending password as text in email is BAD
        $passwd = Yii::app()->secur->encryptByLikeNC( $data['password'] );
        $command->bindValue(":passwd",$passwd,PDO::PARAM_STR);

        $data['role'] = (isset($data['role'])) ? $data['role'] : 'justjoined';
        $command->bindValue(":role", $data['role'], PDO::PARAM_STR);        
        
        /*if (isset($data['birthday']))
			$this->data['birthday'] = date('Y-m-d', strtotime($data['birthday']['year'].'-'.$data['birthday']['month'].'-'.$data['birthday']['day']));
        else */
        	$this->data['birthday'] = '0000-00-00';
        $command->bindValue(":birthday",$this->data['birthday'],PDO::PARAM_STR);
		
        if (isset($data['genders']))
        	$gender = ($data['genders']==0 || $data['genders']==2) ? 'M' : 'F';
        else
        	$gender = (isset($data['gender'])) ? $data['gender'] : 'M';
        	
        $command->bindValue(":gender",$gender,PDO::PARAM_STR);
        
        if (isset($data['genders']))
        	$looking_for_gender = ($data['genders']==0 || $data['genders']==3) ? 'F' : 'M';
        else
        	$looking_for_gender = (isset($data['looking_for_gender'])) ? $data['looking_for_gender'] : 'F';
        $command->bindValue(":looking_for_gender",$looking_for_gender,PDO::PARAM_STR);        

        $data['promo'] = (isset($data['promo'])) ? $data['promo'] : '0';
        $command->bindValue(":promo", $data['promo'], PDO::PARAM_STR);
        
        $data['form'] = (isset($data['form'])) ? $data['form'] : '1';
        $command->bindValue(":form", $data['form'], PDO::PARAM_STR);
        
        $data['affid'] = (isset($data['affid']) && $data['affid']) ? $data['affid'] : 1;
        $command->bindValue(":affid", $data['affid'], PDO::PARAM_INT);        

        $data['sbc'] = (isset($data['sbc']) && $data['sbc']) ? $data['sbc'] : 0;
        $command->bindValue(":sbc", $data['sbc'], PDO::PARAM_INT);
        
        
        $command->execute();
        
        $this->id = Yii::app()->db->lastInsertId;
        
        
        //CREATE ALL OTHER TABLES
        $this->createAllTables($data);
        
        return $this->id;
    }       

    /**
     * Create new PROMO user 
     * return created user ID
     */
    public function RegistrationFromOldONL($data)
    {
//FB::warn($data);
        $sql="INSERT INTO users 
        		(username, role, birthday, gender, looking_for_gender, promo, form) 
        		VALUES 
        		(:username, 'gold', :birthday, :gender, :looking_for_gender, '1', '0')
        ";
        $connection=Yii::app()->db;
        $command=$connection->createCommand($sql);
        
        $command->bindValue(":username",$data['username'],PDO::PARAM_STR);
        
        $command->bindValue(":birthday",$data['birthday'],PDO::PARAM_STR);
		
        $command->bindValue(":gender",$data['gender'],PDO::PARAM_STR);
        
        $command->bindValue(":looking_for_gender",$data['looking_for_gender'],PDO::PARAM_STR);        

        $command->execute();
        
        $this->id = Yii::app()->db->lastInsertId;
        
        
        //CREATE ALL OTHER TABLES
        $this->createAllTables($data, true);
        
        return $this->id;
    }
    /**
     * Create new PROMO user from XPROFILES and GOLAM (rar) 
     * return created user ID
     */
    public function RegistrationFromXP($data)
    {
//FB::warn($data);
        $sql="INSERT INTO users 
        		(username, role, birthday, gender, looking_for_gender, promo, form) 
        		VALUES 
        		(:username, 'gold', :birthday, :gender, :looking_for_gender, '1', '0')
        ";
        $connection=Yii::app()->db;
        $command=$connection->createCommand($sql);
        
        $command->bindValue(":username",$data['username'],PDO::PARAM_STR);
        
        $command->bindValue(":birthday",$data['birthday'],PDO::PARAM_STR);
		
        $command->bindValue(":gender",$data['gender'],PDO::PARAM_STR);
        
        $command->bindValue(":looking_for_gender",$data['looking_for_gender'],PDO::PARAM_STR);        

        $command->execute();
        
        $this->id = Yii::app()->db->lastInsertId;
        
        
        //CREATE ALL OTHER TABLES
        $this->createAllTables($data, true);
        
        return $this->id;
    }     
    
    
    
    
    
    
    /**
     * Create new user
     * return created user ID
     */
/*    public function RegistrationHome($data)
    {
        $sql="INSERT INTO users (email, username, password, gender, looking_for_gender) VALUES (:email, :username, :password, :gender, :looking_for_gender)";
        $connection=Yii::app()->db;
        $command=$connection->createCommand($sql);
        
        $command->bindValue(":email",$data['email'],PDO::PARAM_STR);
        
        $username = Profile::usernameCreateFromEmail($data['email']);
        $command->bindValue(":username",$username,PDO::PARAM_STR);
        
        $password = MD5(SALT.$data['password']);
        $command->bindValue(":password",$password,PDO::PARAM_STR);
        
        //$passwd = Yii::app()->secur->encryptByYii( $data['password'] );
        //$command->bindValue(":passwd",$passwd,PDO::PARAM_STR);
        
        //$birthday = date('Y-m-d', strtotime($data['birthday']['year'].'-'.$data['birthday']['month'].'-'.$data['birthday']['day']));
        //$command->bindValue(":birthday",$birthday,PDO::PARAM_STR);
        
        
        $gender = 'M';//$data['gender'];
        $command->bindValue(":gender",$gender,PDO::PARAM_STR);
        
        $looking_for_gender = 'F';// ($data['genders']==0 || $data['genders']==2) ? 'F' : 'M';
        $command->bindValue(":looking_for_gender",$looking_for_gender,PDO::PARAM_STR);        
        
        $command->execute();
        
        $this->id = Yii::app()->db->lastInsertId;
        
        
        //CREATE ALL OTHER TABLES
        $this->createAllTables($data);
        
        return $this->id;
    }
*/

    
    /**
     * Create new user
     * return created user ID
     * *********************************************************************************************************
     */
    public function RegistrationPromoFromAdmin($data)
    {
/*      
        $sql="INSERT INTO users (email, username, password, birthday, gender, role, promo) VALUES (:email, :username, :password, :birthday, :gender, 'limited', '1')";
        $connection=Yii::app()->db;
        $command=$connection->createCommand($sql);
        
        $data['email'] = $data['username'].'@swoonr.com';
        $command->bindValue(":email",$data['email'],PDO::PARAM_STR);
        $command->bindValue(":username",$data['username'],PDO::PARAM_STR);
        
        $password = MD5(SALT.'cybert1pp3r');
        $command->bindValue(":password",$password,PDO::PARAM_STR);
        
        $birthday = date('Y-m-d', strtotime($data['birthday']['year'].'-'.$data['birthday']['month'].'-'.$data['birthday']['day']));
        $command->bindValue(":birthday",$birthday,PDO::PARAM_STR);
        
        $gender = $data['gender'];
        $command->bindValue(":gender",$gender,PDO::PARAM_STR);
        
        //$looking_for_gender = ($data['genders']==0 || $data['genders']==2) ? 'F' : 'M';
        //$command->bindValue(":looking_for_gender",$looking_for_gender,PDO::PARAM_STR);        
        
        $command->execute();
        
        $this->id = Yii::app()->db->lastInsertId;
        
        //CREATE ALL OTHER TABLES
        $this->createAllTables(array(), true);
        
        //Personal
        $sql="UPDATE users_personal SET status=:status, interesting=:interesting, description=:description WHERE user_id=:user_id";
        $connection=Yii::app()->db;
        $command=$connection->createCommand($sql);
        $command->bindValue(":user_id", $this->id, PDO::PARAM_INT);
        $command->bindValue(":status", $data['status'], PDO::PARAM_STR);
        $data['interesting'] = implode('',$data['interesting']);
        $command->bindValue(":interesting", $data['interesting'], PDO::PARAM_STR);
        $command->bindValue(":description", $data['description'], PDO::PARAM_STR);
        $command->execute();        

        //location
        //$this->locationUpdate($data['location_id'], $data['zip']);
        
        
        //PROMO ROLE
        $front = (in_array('front', $data['promoRole'])) ? '1' : '0';
        $members = (in_array('members', $data['promoRole'])) ? '1' : '0';
        
        $force_country = '';
        $force_lat = $force_lon = 0.000000;
        switch ($data['force_location'])
        {
            case 'country_only':
                $force_country = $data['country_only'];
                break;
            case 'full':
                $forceLocation = Yii::app()->location->getLocation( $data['location_id'] );
//FB::warn($forceLocation,'$forceLocation');
                $force_lat = $forceLocation['latitude'];
                $force_lon = $forceLocation['longitude'];
                break;
        }
        
        
        $sql="INSERT INTO promo_roles (user_id, front, members, force_country, force_lat, force_lon) VALUES (:user_id, :front, :members, :force_country, :force_lat, :force_lon)";
        Yii::app()->db->createCommand($sql)
            ->bindValue(":user_id", $this->id, PDO::PARAM_INT)
            ->bindValue(":front", $front, PDO::PARAM_STR)
            ->bindValue(":members", $members, PDO::PARAM_STR)
            ->bindValue(":force_country", $force_country, PDO::PARAM_STR)
            ->bindValue(":force_lat", $force_lat, PDO::PARAM_STR)
            ->bindValue(":force_lon", $force_lon, PDO::PARAM_STR)
            ->execute();
        
     
        
        return $this->id;*/ 
    }

    public function RegistrationStep2($data)
    {
        if (!$this->id)
        {
            return false;
        }
        
        //data
        $this->data['username'] = $data['username'];
        //$this->data['gender'] = $data['gender'];
        
        /*switch ($this->data['looking_for_gender'] = @implode('',$data['looking_for_gender']))
        {
            case 'FM':
            case 'MF':
                $this->data['looking_for_gender'] = 'M,F';
                break;
        }*/
        
        $this->data['role'] = 'free';        
         
        
        //$sql="UPDATE users SET username=:username, gender=:gender, looking_for_gender=:looking_for_gender, role=:role, birthday=:birthday WHERE id=:id";
        //$sql="UPDATE users SET username=:username, role=:role, birthday=:birthday WHERE id=:id";
        $sql="UPDATE users SET username=:username, role=:role, birthday=:birthday WHERE id=:id";
        $connection=Yii::app()->db;
        $command=$connection->createCommand($sql);
        
        $command->bindValue(":id", $this->id, PDO::PARAM_INT);
        $command->bindValue(":username", $this->data['username'], PDO::PARAM_STR);
        
        $this->data['birthday'] = date('Y-m-d', strtotime($data['birthday']['year'].'-'.$data['birthday']['month'].'-'.$data['birthday']['day']));
        $command->bindValue(":birthday",$this->data['birthday'],PDO::PARAM_STR);

        //$command->bindValue(":looking_for_gender", $this->data['looking_for_gender'], PDO::PARAM_STR);
        $command->bindValue(":role", $this->data['role'], PDO::PARAM_INT);

        $command->execute();
        
        
        
/*        
        $this->data['status'] = $data['status'];
        $this->data['interesting'] = implode('',$data['interesting']);
        $this->data['height'] = $data['height'];
        $this->data['description'] = $data['description'];
        
        $sql="UPDATE users_personal SET status=:status, interesting=:interesting, height=:height, description=:description WHERE user_id=:id";
        $connection=Yii::app()->db;
        $command=$connection->createCommand($sql);        
        $command->bindValue(":id", $this->id, PDO::PARAM_INT);
        $command->bindValue(":status", $this->data['status'], PDO::PARAM_STR);
        $command->bindValue(":interesting", $this->data['interesting'], PDO::PARAM_STR);
        $command->bindValue(":height", $this->data['height'], PDO::PARAM_STR);
        $command->bindValue(":description", $this->data['description'], PDO::PARAM_STR);
        $command->execute();    
*/        
        Updates::addUpdate(2, $this->id);        



        //��������� location (����� ���������� ����)
        $this->locationUpdate($data['location_id'], $data['zip']);
        
        /*if ($data['extPhotoUse'][0]!='yes')
            $this->imgDel(0);*/        
        
        $this->_cacheDelete();//$this->_cacheUpdate();


        //welcome
//        Messages::addWelcome($this->id);
        //send welcome email
//        Yii::app()->mail->SendHtmlMail($this->id, '', 'welcome');
        
        //$promos = new Promos;
        //$promos->createPromosLocationsForUser($this->id);
        
        
        return true;
    }
    



    /**
     * Create new user from FB
     * return created user ID
     * *********************************************************************************************************
     */
    public function RegistrationFromFB($fb_me)
    {
        if (!$fb_me)
            return false;
        
        //check birthday
        $birthdayArray = Yii::app()->helperDate->getBirthday($fb_me['birthday']);
        if (!Yii::app()->helperProfile->checkBirthdayAge18($birthdayArray))
        {
            Yii::app()->user->setFlash('facebookError', 'You must be at least 18 years old to register at this site.');        
            return false;
        }
        //check email
        $emailValidator = new CEmailValidator;
        if (!$emailValidator->validateValue($fb_me['email']))
        {
            Yii::app()->user->setFlash('facebookError', 'Email is bad...');        
            return false;            
        }       
        //check email exists in DB
        /*if (Profile::emailExist($fb_me['email']))
        {
            Yii::app()->user->setFlash('facebookError', 'Email already exists.');        
            return false;            
        }*/
        if ( $idExisted = Profile::emailExist($fb_me['email']) )//combining accounts
        {
            $this->id = $idExisted;
        }
        else
        {
            $sql="INSERT INTO users (email, username, birthday, gender, looking_for_gender, role) VALUES (:email, :username, :birthday, :gender, :looking_for_gender, 'free')";
            $connection=Yii::app()->db;
            $command=$connection->createCommand($sql);
            
            $command->bindValue(":email",$fb_me['email'],PDO::PARAM_STR);
            
            $username = Profile::usernameCreateFromEmail($fb_me['email']);
            $command->bindValue(":username",$username,PDO::PARAM_STR);
            
            /*$password = MD5(SALT.'cybert1pp3r');
            $command->bindValue(":password",$password,PDO::PARAM_STR);
            */
            
            $birthday = date('Y-m-d', strtotime($birthdayArray['year'].'-'.$birthdayArray['month'].'-'.$birthdayArray['day']));
            $command->bindValue(":birthday",$birthday,PDO::PARAM_STR);
            
            $gender = ($fb_me['gender']=='female') ? 'F' : 'M';
            $command->bindValue(":gender",$gender,PDO::PARAM_STR);
            
            $looking_for_gender = ($gender=='M') ? 'F' : 'M';
            $command->bindValue(":looking_for_gender",$looking_for_gender,PDO::PARAM_STR);        
            
            $command->execute();
            
            $this->id = Yii::app()->db->lastInsertId;
            
            //CREATE ALL OTHER TABLES
            if ($this->id)
            {
                $this->createAllTables();
                
                //$promos = new Promos;
                //$promos->createPromosLocationsForUser($this->id);                
            }
        }


        if ($this->id)
        {
            $sql = "INSERT INTO ext_FB (user_id, fb_id, fb_me) VALUES (:user_id, :fb_id, :fb_me)";
            Yii::app()->db->createCommand($sql)        
                ->bindValue(":user_id", $this->id, PDO::PARAM_INT)
                ->bindValue(":fb_id", $fb_me['id'], PDO::PARAM_INT)
                ->bindValue(":fb_me", serialize($fb_me), PDO::PARAM_INT)
                ->execute();
                
            $this->_cacheDelete($this->id);
        }
        
        return true;
    }    
    /**
     * login FB with create account
     */
    function loginFB($fb_me)
    {
        
        $sql = "SELECT user_id FROM ext_FB WHERE fb_id=:fb_id LIMIT 1";
        $this->id = Yii::app()->db->createCommand($sql)        
            ->bindValue(":fb_id", $fb_me['id'], PDO::PARAM_INT)
            ->queryScalar(); 
        
        if ( !$this->id )
        {
            
//$fb_me_simulate = json_decode('{"id":"100002370683537","name":"Susan Miller","first_name":"Susan","last_name":"Miller","link":"http:\/\/www.facebook.com\/profile.php?id=100002370683537","location":{"id":"108260802527498","name":"Miami Beach, Florida"},"bio":"Hello ;)","gender":"female","email":"jes003@ymail.com","timezone":3,"locale":"en_US","verified":true,"updated_time":"2011-06-07T15:35:36+0000"}', true);            
            
            
            //Yii::app()->helperProfile->checkBirthdayAge18($this->birthday)
            
            $this->RegistrationFromFB($fb_me);
        }
        
        
        if ($this->id)
        {
            $identity = new UserIdentity('','');
            $identity->authenticateByUserId($this->id);
                        
            $error = 0;
            if($error===UserIdentity::ERROR_NONE)
            {
                $duration = 0;//3600*24*365; // 365 days
                Yii::app()->user->login($identity,$duration);
//CHelperSite::vd(Yii::app()->user->Profile);                
                if (FACEBOOK)
                    ;//Yii::app()->controller->redirect(Yii::app()->createAbsoluteUrl('fb/index'));//Yii::app()->controller->redirect(FB_APP_URL);
                else
                    Yii::app()->controller->redirect(Yii::app()->homeUrl);
                    
                return true;
            }            
        }

        
        return false;
    }

    /**
     * login App FB with create account
     */
    function loginFB2()
    {
        $fb_me = Yii::app()->session['facebook_user'];
        
        
        $sql = "SELECT user_id FROM ext_FB WHERE fb_id=:fb_id LIMIT 1";
        $this->id = Yii::app()->db->createCommand($sql)        
            ->bindValue(":fb_id", $fb_me['id'], PDO::PARAM_INT)
            ->queryScalar(); 
        
        if ( !$this->id )
        {
            $this->RegistrationFromFB($fb_me);
        }
        
        
        if ($this->id)
        {
            return $this->id;
        }

        
        return 0;
    }

    
    public function FbFriendsInvited()
    {
        $sql="UPDATE ext_FB SET friends_invited='1' WHERE user_id=:user_id";
        Yii::app()->db->createCommand($sql)        
            ->bindValue(":user_id", $this->id, PDO::PARAM_INT)
            ->execute();         
        $this->_cacheUpdate();
    } 



    /**
     * Create new user from FB
     * return created user ID
     * *********************************************************************************************************
     */
    public function RegistrationFromService($service, $me)
    {
        if (!$me)
            return false;
        
        //check birthday
        if ($me['birthday'])
        {
            $birthdayArray = Yii::app()->helperDate->getBirthday($me['birthday']);
            if (!Yii::app()->helperProfile->checkBirthdayAge18($birthdayArray))
            {
                Yii::app()->user->setFlash('serviceError', 'You must be at least 18 years old to register at this site.');        
                return false;
            }
            $birthday = date('Y-m-d', strtotime($birthdayArray['year'].'-'.$birthdayArray['month'].'-'.$birthdayArray['day']));
        }
        else
        {
            $birthday = "0000-00-00";            
        }


        //check email
        $emailValidator = new CEmailValidator;
        if (!$emailValidator->validateValue($me['email']))
        {
            Yii::app()->user->setFlash('serviceError', 'Email is bad...');        
            return false;            
        }       
        //check email exists in DB
        /*if (Profile::emailExist($fb_me['email']))
        {
            Yii::app()->user->setFlash('facebookError', 'Email already exists.');        
            return false;            
        }*/
        if ( $idExisted = Profile::emailExist($me['email']) )//combining accounts
        {
            $this->id = $idExisted;
        }
        else
        {
            $sql="INSERT INTO users (email, username, birthday, gender, looking_for_gender) VALUES (:email, :username, :birthday, :gender, :looking_for_gender)";
            $connection=Yii::app()->db;
            $command=$connection->createCommand($sql);
            
            $command->bindValue(":email",$me['email'],PDO::PARAM_STR);
            
            $username = Profile::usernameCreateFromEmail($me['email']);
            $command->bindValue(":username",$username,PDO::PARAM_STR);
            
            
            $command->bindValue(":birthday",$birthday,PDO::PARAM_STR);
            
            $gender = ($me['gender']=='female') ? 'F' : 'M';
            $command->bindValue(":gender",$gender,PDO::PARAM_STR);
            
            $looking_for_gender = ($gender=='M') ? 'F' : 'M';
            $command->bindValue(":looking_for_gender",$looking_for_gender,PDO::PARAM_STR);        
            
            $command->execute();
            
            $this->id = Yii::app()->db->lastInsertId;
            
            //CREATE ALL OTHER TABLES
            if ($this->id)
            {
                $this->createAllTables();
                
                $promos = new Promos;
                $promos->createPromosLocationsForUser($this->id);                
            }
        }


        if ($this->id)
        {
/*CHelperSite::vd($service,0);
CHelperSite::vd($me);*/
            switch($service->serviceName)
            {
                case 'facebook' : 
                    
                    $sql = "INSERT INTO ext_FB (user_id, fb_id, fb_me) VALUES (:user_id, :fb_id, :fb_me)";
                    Yii::app()->db->createCommand($sql)        
                        ->bindValue(":user_id", $this->id, PDO::PARAM_INT)
                        ->bindValue(":fb_id", $me['id'], PDO::PARAM_INT)
                        ->bindValue(":fb_me", serialize($me), PDO::PARAM_STR)
                        ->execute();

                    // update email_activated_at for FB registration
                    if (!$idExisted)
	                    Yii::app()->db->createCommand("UPDATE users_settings SET email_activated_at = NOW() WHERE user_id = :user_id LIMIT 1")
	                        ->bindValue(":user_id", $this->id, PDO::PARAM_INT)
	                        ->execute();
                                        
                    break;
                
                case 'google_oauth':
                //case 'google' : 
                    
                    $sql = "INSERT INTO ext_G (user_id, g_id, g_me) VALUES (:user_id, :g_id, :g_me)";
                    Yii::app()->db->createCommand($sql)        
                        ->bindValue(":user_id", $this->id, PDO::PARAM_INT)
                        ->bindValue(":g_id", $me['id'], PDO::PARAM_STR)//str!!!
                        ->bindValue(":g_me", serialize($me), PDO::PARAM_STR)
                        ->execute();
                                        
                    break;                    
                    
            }
            

                
            $this->_cacheDelete($this->id);
        }
        
        return true;
    }    
    /**
     * getUserIdService (with create account if need)
     */
    function getUserIdService($service)
    {
        switch($service->serviceName)
        {
            case 'facebook':

                $sql = "SELECT user_id FROM ext_FB WHERE fb_id=:fb_id LIMIT 1";
                $this->id = Yii::app()->db->createCommand($sql)        
                    ->bindValue(":fb_id", $service->id, PDO::PARAM_INT)
                    ->queryScalar(); 
                break;
            
            case 'google_oauth':
            //case 'google':

                $sql = "SELECT user_id FROM ext_G WHERE g_id=:g_id LIMIT 1";
                $this->id = Yii::app()->db->createCommand($sql)        
                    ->bindValue(":g_id", $service->id, PDO::PARAM_STR)//str!!!
                    ->queryScalar(); 
                break;                
                
        }

        if ( !$this->id )
        {
            $this->RegistrationFromService($service, $service->attributes['me']);
        }  
        
        return $this->id;
    }




    
    public function afterLogin()
    {
        if ($this->id)
        {
            $this->getData();
            
            $this->activityUpdate('loginLast');
            $this->activityUpdate('activityLast');
            $this->activityUpdate('loginCount');
            
            /*if (!FACEBOOK && $this->data['username'])
            {
                $coockieValue = Yii::app()->secur->encryptByYii( $this->data['username'] );
                $cookie = new CHttpCookie('username', $coockieValue);
                $cookie->expire = time()+60*60*24*360; 
                Yii::app()->request->cookies['username'] = $cookie;
            }*/
        }
    }
    /*static function coockieUsernameDelete()
    {
        $cookie = new CHttpCookie('username', '');
        $cookie->expire = time()-60*60*24; 
        Yii::app()->request->cookies['username'] = $cookie;
        
        Yii::app()->getSession()->destroy();
    }*/
    
    
    /**
     * UPGRADE TO GOLD after first payment or rebill
     * simple return (without true/false) - needs for rebills if user will delete or other reason...
     */
    public function Upgrade($trn_id)
    {
    	if (!$this->id)
    		return;
	
    	
    	//table user_payment to !!!
    	$trn = Payment::getTransactionInfo($trn_id);
  	
    	if ($trn && $this->id==$trn['user_id'])
    	{
    		$option = CHelperPayment::getOptionRow( $trn['price_id'] );
    		
    		//first
    		if ($trn['status']=='completed')
	    	{
	    		/*$sql = "INSERT IGNORE INTO `user_payment` (
		    		`id`,
		    		`paymod`,
		    		`firstpay`,
		    		`lastpay`,
		    		`amount`,
		    		`affid`,
		    		`joined`,
		    		`active`,
		    		`form`,
		    		`price_id`
		    	) VALUES (
		    		:id,
		    		:paymod,
		    		:firstpay,
		    		:lastpay,
		    		:amount,
		    		:affid,
		    		:joined,
		    		:active,
		    		:form,
		    		:price_id
		    	)";*/
				$sql = "INSERT INTO `user_payment` (
		    		`id`,
		    		`paymod`,
		    		`firstpay`,
		    		`lastpay`,
		    		`amount`,
		    		`affid`,
		    		`joined`,
		    		`active`,
		    		`form`,
		    		`price_id`,
		    		`status`
		    	) VALUES (
		    		:id,
		    		:paymod,
		    		:firstpay,
		    		:lastpay,
		    		:amount,
		    		:affid,
		    		:joined,
		    		:active,
		    		:form,
		    		:price_id,
		    		'active'
		    	)
		    	ON DUPLICATE KEY UPDATE 
		    		`paymod`=:paymod,
		    		`firstpay`=:firstpay,
		    		`lastpay`=:lastpay,
		    		`amount`=:amount,
		    		`active`=:active,
		    		`price_id`=:price_id,
		    		`status`='active'
		    	";		    		
		    	
		    	$joined = $this->data['activity']['joined'];
	    		$joined = date('Y-m-d', strtotime($joined));
	    		
			    Yii::app()->db->createCommand($sql)
			    	->bindValue(":id", 			$this->id, PDO::PARAM_INT)
			    	->bindValue(":paymod", 		$trn['paymod'], PDO::PARAM_STR)
					->bindValue(":firstpay", 	date('Y-m-d'), PDO::PARAM_STR)
					->bindValue(":lastpay", 	date('Y-m-d'), PDO::PARAM_STR)
					->bindValue(":amount", 		$trn['amount'], PDO::PARAM_STR)
					->bindValue(":affid", 		$this->data['affid'], PDO::PARAM_INT)
					->bindValue(":joined", 		$joined, PDO::PARAM_STR)
					->bindValue(":active", 		1, PDO::PARAM_INT)
					->bindValue(":form", 		$this->data['form'], PDO::PARAM_STR)
					->bindValue(":price_id", 	$trn['price_id'], PDO::PARAM_INT)
					->execute();		    	

					
		    	//upgrade status and expire
		    	if ($option['term_trial']!=0)
					$this->makeGold( $option['term_trial'] );
		    	else
		    		$this->makeGold( $option['term'] );
		    	
	    	}
	    	elseif ($trn['status']=='renewal')//REBILL
	    	{
	    		$sql = "UPDATE `user_payment` SET `lastpay`=CURDATE() WHERE id=:id LIMIT 1";
	    		
			    Yii::app()->db->createCommand($sql)
					->bindValue(":id", $this->id, PDO::PARAM_INT)
					->execute();
				
				//upgrade status and expire
				if 
				(
					$this->data['role']!='deleted'
					&&
					$this->data['role']!='banned'
				)
					$this->makeGold( $option['term'] );
	    	}
	    	else
	    	{
	    	
	    	}
	    	
    	}    	
    	
    	$this->_cacheDelete();
    }
    
    
    /**
     * UPGRADE for WHITELABEL stats
     */
    public function UpgradeCams($trn_id, $amount=0)
    {
    	if (!$this->id)
    		return;
    	 
    	//table user_payment to !!!
    	$trn = Payment::getTransactionInfo($trn_id);

    	if ($trn && $this->id==$trn['user_id'])
    	{
    		$option = CHelperPayment::getOptionRow( $trn['price_id'] );
    
    		//first
    		if ($trn['status']=='authed' || $trn['status']=='completed_cams')//if ($trn['status']=='completed')
    		{
    			/*if ($trn['status']=='authed' && !$this->isJoinedTo_ONL() && !$this->isJoinedTo_NC())
    				$hide='1';
    			else*/ 
    				$hide='0';
    			
    			$sql = "INSERT IGNORE INTO `user_payment_cams` (
				    		`id`,
				    		`paymod`,
				    		`firstpay`,
				    		`lastpay`,
				    		`amount`,
				    		`affid`,
				    		`joined`,
				    		`active`,
				    		`form`,
				    		`price_id`,
				    		`hide`
				    	) VALUES (
				    		:id,
				    		:paymod,
				    		:firstpay,
				    		:lastpay,
				    		:amount,
				    		:affid,
				    		:joined,
				    		:active,
				    		:form,
				    		:price_id,
				    		'{$hide}'
				    	)
		    			ON DUPLICATE KEY UPDATE amount=amount+:amount, lastpay=:lastpay;	
    			";//
    		  
    			$joined = $this->data['activity']['joined'];
    			$joined = date('Y-m-d', strtotime($joined));
    	   		
    			Yii::app()->db->createCommand($sql)
	    			->bindValue(":id", 			$this->id, PDO::PARAM_INT)
	    			->bindValue(":paymod", 		$trn['paymod'], PDO::PARAM_STR)
	    			->bindValue(":firstpay", 	date('Y-m-d'), PDO::PARAM_STR)
	    			->bindValue(":lastpay", 	date('Y-m-d'), PDO::PARAM_STR)
	    			->bindValue(":amount", 		$amount/*$trn['amount']*/, PDO::PARAM_STR)
	    			->bindValue(":affid", 		$this->data['affid'], PDO::PARAM_INT)
	    			->bindValue(":joined", 		$joined, PDO::PARAM_STR)
	    			->bindValue(":active", 		1, PDO::PARAM_INT)
	    			->bindValue(":form", 		$this->data['form'], PDO::PARAM_STR)
	    			->bindValue(":price_id", 	$trn['price_id'], PDO::PARAM_INT)
	    			->execute();
    		}
    		else
    		{
    
    		}
    
    	}
    	 
    	$this->_cacheDelete();
    }        
    
    
    /*
     * after refunded
     * 
     * 2012-09-05 added `reason`
     */
    public function makeFree($reason="")
    {
        if (!$this->id) return;

		$this->Update('role', 'free');
        	
        if ($reason)
        	$this->paymentUpdateRecurringStatus($reason);
    }
    
    /*
     * make GOLD
     * term in days
     */
    public function makeGold($term, $fromDateTime="")
    {
        if (!$this->id) return;

		$this->Update('role', 'gold');
		
		if ($fromDateTime)
			$expire_at = date("Y-m-d", ( strtotime($fromDateTime) + 24*60*60*($term+1)) );
		else
			$expire_at = date("Y-m-d", ( time() + 24*60*60*($term+1)) );
		
		$this->Update('expire_at', $expire_at);
    }    
    
    /*
     * call AFTER subscription was cancelled
     */
    public function paymentUpdateRecurringStatus($v)
    {
    	if (!$this->id) return false;
    	
    	$this->getPayment();
    	if (!$this->data['payment'])
    		return false;    	
    	if ($this->data['payment']['status']=='refunded' || $this->data['payment']['status']=='chargeback')//most info priority
    		return true;
    	
        $sql="UPDATE user_payment  SET status=:status WHERE id=:user_id LIMIT 1;
        	  UPDATE pm_today_gold SET status=:status WHERE user_id=:user_id LIMIT 1;";

        $connection=Yii::app()->db;
        $command=$connection->createCommand($sql);        
        $command->bindValue(":user_id", $this->id, PDO::PARAM_INT);
        
        switch ($v)
        {
        	//case 'active':
            case 'expired':
            case 'cancelled':
            case 'refunded':
            case 'chargeback':
                $command->bindValue(":status", $v, PDO::PARAM_STR);                
                break;              
            
            default: 
                $command->cancel();
                return false;
        }
     
        $res = $command->execute();
        
		$this->_cacheDelete();
		
		return $res;
    }    

    
    /*
     * Subscription was cancelled
     */
    public function paymentCancelledInfo()
    {
    	if (!$this->id) return;
    	
    	$this->getPayment();
    	if (!$this->data['payment'])
    		return false;    	
    	if ($this->data['payment']['status']=='active')
    		return false; 	
    	
    	$sql = "SELECT * FROM `pm_transactions` WHERE user_id={$this->id} AND `status`='cancelled' AND paymod='{$this->data['payment']['paymod']}' LIMIT 1";
    	return Yii::app()->db->createCommand($sql)->queryRow();
    }
    
    
    
    /**
     * update activity every 5 minutes
    */
    function setActivity()
    {
        if (!$this->id)
        	return;
        
    	if (isset($this->data['activity']['activityLast']))
        {
	    	$activityLast = $this->data['activity']['activityLast'];
	        //if ((time()-strtotime($activityLast))>5*60)
			if ((time()-strtotime($activityLast))>60) //n 2012-07-25: Changed as discussed to Oleg
	            $this->activityUpdate('activityLast');
        }
        else
        	$this->activityUpdate('activityLast');
    }    
    
    /**
     * update user location
     */
    public function locationUpdate($location_id, $zip)
    {
    	
        $this->data['location'] = Yii::app()->location->getLocation($location_id);
        $this->data['location']['zip'] = $zip; 

        $sql="UPDATE users_location SET 
            country=:country, 
            state=:state, 
            city=:city,
            zip=:zip,
            latitude=:latitude, 
            longitude=:longitude 
            WHERE user_id=:user_id";
        
        $res = Yii::app()->db->createCommand($sql)
            ->bindValue(":country", $this->data['location']['country'], PDO::PARAM_STR)
            ->bindValue(":state", $this->data['location']['state'], PDO::PARAM_STR)
            ->bindValue(":city", $this->data['location']['city'], PDO::PARAM_STR)
            ->bindValue(":zip", $zip, PDO::PARAM_STR)
            ->bindValue(":latitude", $this->data['location']['latitude'], PDO::PARAM_STR)
            ->bindValue(":longitude", $this->data['location']['longitude'], PDO::PARAM_STR)
            ->bindValue(":user_id", $this->id, PDO::PARAM_INT)
            ->execute();        
        
        
        $this->_cacheDelete();//$this->_cacheUpdate();
        
        return $res;
    }
    

	public function locationUpdate2($country,$state,$city,$latitude,$longitude,$zip){
		$this->data['location']['zip'] = $zip; 

		$sql="UPDATE users_location SET 
            country=:country, 
            state=:state, 
            city=:city,
            zip=:zip,
            latitude=:latitude, 
            longitude=:longitude 
            WHERE user_id=:user_id";
		
		Yii::app()->db->createCommand($sql)
			->bindValue(":country", $country, PDO::PARAM_STR)
			->bindValue(":state", $state, PDO::PARAM_STR)
			->bindValue(":city", $city, PDO::PARAM_STR)
			->bindValue(":zip", $zip, PDO::PARAM_STR)
			->bindValue(":latitude", $latitude, PDO::PARAM_STR)
			->bindValue(":longitude", $longitude, PDO::PARAM_STR)
			->bindValue(":user_id", $this->id, PDO::PARAM_INT)
			->execute();  
		
		$this->_cacheDelete();
	}
	
    /**
     * update table users
     */ 
    public function Update($k, $v)
    {
        if (!$this->id)
        {
            return false;
        }
        
        if ($this->data[$k] == $v)
        {
            return true;
        }        
        
        $sql="UPDATE users SET {$k}=:{$k} WHERE id=:user_id LIMIT 1";

        $connection=Yii::app()->db;
        $command=$connection->createCommand($sql);        
        $command->bindValue(":user_id", $this->id, PDO::PARAM_INT);
        
        switch ($k)
        {
            case 'username':
            case 'email':
            case 'role':
            case 'expire_at':
            case 'gender':
            case 'looking_for_gender':
            case 'panamus_id':	
                $this->data[$k] = $v;
                $command->bindValue(":".$k, $this->data[$k], PDO::PARAM_STR);                
                break;              

            /*case 'ratio': 
                $v = ($v=='16:9') ? '16:9' : '4:3';//16:9, not 1.77!!!!   not for console !!!!!
                $this->data[$k] = $v;
                $command->bindValue(":".$k, $this->data[$k], PDO::PARAM_STR);                
                break;*/             
            
            case 'birthday':
                $this->data['age'] = CHelperProfile::getAge($v);
                
                $this->data[$k] = $v;
                $command->bindValue(":".$k, $this->data[$k], PDO::PARAM_STR);
                break;            
            
            case 'location_id': 
                $this->data['location_id'] = $v;
                $this->data['location'] = Yii::app()->location->getLocation($this->data['location_id']);
                $command->bindValue(":location_id", $this->data['location_id'], PDO::PARAM_INT);                
                break;

            case 'pics':
                $this->data['pics'] = $v;
                $command->bindValue(":pics", $this->data['pics'], PDO::PARAM_INT);                
                break;

            case 'password': 
                $passwordNew = MD5(SALT.$v);
                if ($this->data['password'] == $passwordNew)
                {
                    $command->cancel();
                    return true;
                }
                $this->data['password'] = $passwordNew;
                $command->bindValue(":password", $this->data['password'], PDO::PARAM_STR);
                
                $passwd = Yii::app()->secur->encryptByLikeNC( $v );
                $this->data['passwd'] = $passwd; 
                $sql_2 = "UPDATE users SET passwd=:passwd WHERE id=:id LIMIT 1";
				Yii::app()->db->createCommand($sql_2)
					->bindValue(":id", $this->id, PDO::PARAM_INT)                
					->bindValue(":passwd", $passwd, PDO::PARAM_STR)
					->execute();
                
                break;

            
            default: 
                $command->cancel();
                return false;
        }
     
        $command->execute();
        
        $this->_cacheUpdate();
        
        return true;                
    }    
    
    /**
     * update profile data for guest (in session)
     */
    public function updateGuestData($k,$v)
    {
        if ($this->id)
        {
            return;
        }
      
        switch ($k)
        {
            case 'gender':
                if (in_array($v, array('M','F')))
                {
                    $this->data[$k] = $_SESSION['user']['data']['gender'] = $v;                    
                }
                return;
                
            case 'looking_for_gender':
                if (is_array($v) && !empty($v))//from forms
                {
                    $v = implode(',',$v);
                }
                
                if ($v)
                {
                    $this->data[$k] = $_SESSION['user']['data']['looking_for_gender'] = $v;                    
                }
                return;
                                
            case 'location':
                if ($v)
                {
                    $this->data['location'] = $_SESSION['user']['data']['location'] = $v;
                }
                return;
                
            case 'ageMin':
            case 'ageMax':
                if ($v>=18 && $v<=99)
                {
                    $this->data['settings'][$k] = $_SESSION['user']['data']['settings'][$k] = $v;
                }
                return;
                
            case 'onlineNow':
            case 'withPhoto':
                $this->data['settings'][$k] = $_SESSION['user']['data']['settings'][$k] = $v;
                return;
                
                                
        }
    }
    
    /**
     * update table users_info
     */    
    public function infoUpdate($k, $v)
    {
        if (!$this->id)
        {
            return false;
        }

        if ($this->data['info'][$k] == $v)
        {
            return true;
        }
        
        $sql="UPDATE users_info SET {$k}=:{$k} WHERE user_id=:user_id LIMIT 1";

        $connection=Yii::app()->db;
        $command=$connection->createCommand($sql);        
        $command->bindValue(":user_id", $this->id, PDO::PARAM_INT);
        
        switch ($k)
        {
            case 'ip_signup':
            case 'ip_last':
            case 'screen_resolution':
                $this->data['info'][$k] = $v;
                $command->bindValue(":{$k}", $v, PDO::PARAM_STR);                
                break;

            case 'ip_signup_long':
                $this->data['info'][$k] = $v;
                $command->bindValue(":{$k}", $v, PDO::PARAM_INT);                
                break;           
                
            /*case 'img_primary':
                if ($v>count($this->data['info']['imgs']))
                {
                    return false;
                }
                Updates::addUpdate(4, $this->id);*/
            case 'img_last':
                $this->data['info'][$k] = $v;
                $command->bindValue(":{$k}", $v, PDO::PARAM_INT);
                break;
                                 
            default: 
                $command->cancel();
                return false;
        }
        $command->execute();
        
        $this->_cacheUpdate();
        
        return true;                
    }
    /**
     * update table users_personal
     */    
    public function personalUpdate($k, $v)
    {
        if (!$this->id)
        {
            return false;
        }
        
        if ($this->data['personal'][$k] == $v)
        {
            return true;
        }

        $sql="UPDATE users_personal SET `{$k}`=:{$k} WHERE user_id=:user_id LIMIT 1";

        $connection=Yii::app()->db;
        $command=$connection->createCommand($sql);        
        $command->bindValue(":user_id", $this->id, PDO::PARAM_INT);
        
        switch ($k)
        {
            case 'status':
            case 'description':
                $this->data['personal'][$k] = $v;
                $command->bindValue(":{$k}", $v, PDO::PARAM_STR);                
                break;                

            case 'badgets':
                $v = implode(',',$v);
                $this->data['personal'][$k] = $v;
                $command->bindValue(":{$k}", $v, PDO::PARAM_STR);                
                break;             

			//n 2012-07-04: Table changed. Will update later
            //case 'interesting':
            //    $v = implode('',$v);
            //    $this->data['personal'][$k] = $v;
            //    $command->bindValue(":{$k}", $v, PDO::PARAM_STR);                
            //    break;             
            //
            //
            //case 'height':
            //case 'race':
            //case 'religion':
            //case 'hairColor':
            //case 'eyeColor':
            //case 'bodyType':
            //case 'profession':
            //case 'smoker':
            //case 'drink':
            //    $this->data['personal'][$k] = $v;
            //    $command->bindValue(":{$k}", $v, PDO::PARAM_INT);                
            //    break;
			
			case 'for_fun':
			case 'destination':
			case 'favourite_things':
			case 'favourite_book':
			case 'job':
			case 'headline':
			case 'character':
			case 'interests':
			case 'looking_for':
			case 'turn_on':
			case 'turn_off':
			    $this->data['personal'][$k] = $v;
				$command->bindValue(":{$k}", $v, PDO::PARAM_STR);                
			    break;

			case 'eye_color':
			case 'hair_color':
			case 'body_type':
			case 'personality':
			case 'relationship_status':
			case 'smoking':
			case 'drinking':
			case 'nationality':
			case 'ethnicity':
			case '1st_language':
			case '2nd_language':
			case 'appearance':
			case 'hair_length':
			case 'height':
			case 'best_feature':
			case 'piercings_tattoos':
			case 'style':
			case 'live':
			case 'income':
			case 'occupation':
			case 'religion':
			case 'kinky':
			case 'oral':
			case 'anal':
			case 'experience':
			case 'education':
			case 'glasses':
			case 'startsign':
				$this->data['personal'][$k] = $v;
				$command->bindValue(":{$k}", $v, PDO::PARAM_INT);                
				break;

            
            default: 
                $command->cancel();
                return false;
        }
        $command->execute();
        
        $this->_cacheUpdate();
        
        return true;                
    }
    /**
     * update table users_settings
     */    
    public function settingsUpdate($k, $v)
    {
        if (!$this->id)
        {
            return false;
        }

        if ($this->data['settings'][$k] == $v)
        {
            return true;
        }
        
        $sql="UPDATE users_settings SET {$k}=:{$k} WHERE user_id=:user_id LIMIT 1";

        $connection=Yii::app()->db;
        $command=$connection->createCommand($sql);        
        $command->bindValue(":user_id", $this->id, PDO::PARAM_INT);
        
        switch ($k)
        {
            case 'ageMin':
            case 'ageMax':
                $this->data['settings'][$k] = $v;
                $command->bindValue(":{$k}", $v, PDO::PARAM_INT);                
                break;
            case 'email_bounced': 
            case 'email_activated_at':
            case 'email_notifications':                
            case 'hided_notify':
            case 'hided_new_message':
			case 'mood':
			case 'moodstatus':
			case 'status':
			case 'cams_joined':
			case 'cams_autologin':				
                $this->data['settings'][$k] = $v;
                $command->bindValue(":{$k}", $v, PDO::PARAM_STR);
                break;
            default: 
                $command->cancel();
                return false;
        }
        $command->execute();
        
        $this->_cacheUpdate();
        
        return true;                
    }    
    /**
     * update table users_activity
     */    
    public function activityUpdate($k, $v='')
    {
        if (!$this->id)
        {
            return false;
        }

        if ($v && $this->data['activity'][$k] == $v)
        {
            return true;
        }
        
        $sql="UPDATE users_activity SET {$k}=:{$k} WHERE user_id=:user_id LIMIT 1";

        $connection=Yii::app()->db;
        $command=$connection->createCommand($sql);        
        $command->bindValue(":user_id", $this->id, PDO::PARAM_INT);
        
        switch ($k)
        {
            case 'loginLast':
            case 'activityLast':
                $this->data['activity'][$k] = date("Y-m-d H:i:s");
                $command->bindValue(":{$k}", $this->data['activity'][$k], PDO::PARAM_STR);                
                break;
            case 'loginCount':
            case 'viewProfileCount':
                $this->data['activity'][$k] = 1 + $this->data['activity'][$k];
                $command->bindValue(":{$k}", $this->data['activity'][$k], PDO::PARAM_INT);                
                break;                
            default: 
                $command->cancel();
                return false;
        }
        $command->execute();
        
        $this->_cacheUpdate();
        
        return true;                
    } 

    /**
     * update table user_image
     */    
    public function imageUpdate($n, $k, $v='')
    {
    	if (!$this->id || !$this->data['image'])
        {
            return false;
        }
		
        $i=$this->imgGetIndx($n);
        if ( $i===false )
        	return false;

        if ($v && $this->data['image'][$i][$k] == $v)
            return true;
        
        $sql="UPDATE user_image SET `{$k}`=:{$k} WHERE user_id=:user_id AND n=:n LIMIT 1";

        $connection=Yii::app()->db;
        $command=$connection->createCommand($sql);        
        $command->bindValue(":user_id", $this->id, PDO::PARAM_INT);
        $command->bindValue(":n", $n, PDO::PARAM_INT);
        
        switch ($k)
        {
            case 'xrated':
            case 'approved':
            	$this->data['image'][$i][$k] = $v;
                $command->bindValue(":{$k}", $v, PDO::PARAM_STR);
                break;            	

			case 'primary':
               	foreach ($this->data['image'] as $kkk=>$vvv)
               		$this->data['image'][$kkk]['primary'] = '0';
               	
               	Yii::app()->db->createCommand("UPDATE user_image SET `primary`='0' WHERE user_id={$this->id}")->execute();
            	
            	$this->data['image'][$i][$k] = $v;
                $command->bindValue(":{$k}", $v, PDO::PARAM_STR);
                
                break;
            default: 
                $command->cancel();
                return false;
        }
        $command->execute();
        
        $this->_cacheUpdate();
        
        return true;                
    } 

    /**
     * viewed to user
     */
    public function viewedTo($id_from)
    {
        if ($this->id && $this->id!=$id_from )
        {
            if ($id_from && !ADMIN)
            {
                $this->activityUpdate('viewProfileCount');
                
                $sql = "INSERT INTO profile_view (id_from, id_to, `added`, `count`) VALUES ({$id_from}, {$this->id}, NOW(), 1)
                        ON DUPLICATE KEY UPDATE `read`='0', `added`=NOW(), count=count+1;
                        
                        INSERT INTO profile_viewed (user_id, count) VALUES ({$this->id}, 1)
  						ON DUPLICATE KEY UPDATE count=count+1;
                ";
                Yii::app()->db->createCommand($sql)->execute();
                
                
                $mkey = "Profiles_findProfileViewedTo_".$this->id;            
                Yii::app()->cache->delete($mkey);
                
                $mkey = "Profiles_findProfileViewedFrom_".$id_from;
                Yii::app()->cache->delete($mkey);                
            }
            /*else
            {
                $viewedIds = $_SESSION['user']['data']['viewedTo'];
                $viewedIds[] = $this->id;
                $viewedIds = array_unique($viewedIds);
                $_SESSION['user']['data']['viewedTo'] = $viewedIds;                
            }*/
        }
    }

    /**
     * viewed to user get info
     */
    public function viewedToGet()
    {
        if ($this->id)
        {
            $sql = "SELECT id_to FROM profile_view WHERE id_from=".$this->id;            
            $res = Yii::app()->db->createCommand($sql)->queryColumn();
        }
        else
        {
            $res = $_SESSION['user']['data']['viewedTo'];
        }
        return $res;
    }
    
    /**
     * Is user liked yet?
     */
    public function LikeToIs($user_id_to)
    {
        if ($this->id && $user_id_to)
        {
            $sql = "SELECT * FROM profile_like WHERE id_from={$this->id} AND id_to={$user_id_to}";
            return Yii::app()->db->createCommand($sql)->queryRow();
        }
    }  
    /**
     * Is user liked from user?
     */
    public function LikeFromIs($user_id_to)
    {
        if ($this->id && $user_id_to)
        {
            $sql = "SELECT * FROM profile_like WHERE id_from={$user_id_to} AND id_to={$this->id}";
            return Yii::app()->db->createCommand($sql)->queryRow();
        }
    }       
    /**
     * Like user
     */
    public function Like($user_id_to, $like, $gender)
    {
        $res = "";        
        $like = intval($like);
        if ($this->id && $user_id_to && $like)
        {
            $sql = "INSERT INTO profile_like (id_from, id_to, `like`, `when`, `new`) VALUES ({$this->id}, {$user_id_to}, {$like}, NOW(), 1)";

            Yii::app()->db->createCommand($sql)->execute();
            
            if ($gender)
            {
                $HisHer = ($gender=='F') ? "her" : "him";
                $res = "You like ".$HisHer."!";
            }
            else
            {
                if ($like==1)
                    $res = "You like each other!";
                else
                    $res = "Ignored";
            }
            
            $mkey = "Profiles_findProfileLikeTo_".Yii::app()->user->id;
            Yii::app()->cache->delete($mkey);
            $mkey = "Profiles_findProfileLikeFrom_".Yii::app()->user->id;
            Yii::app()->cache->delete($mkey);
            $mkey = "Profiles_findProfileLikeTo_".$user_id_to;
            Yii::app()->cache->delete($mkey);
            $mkey = "Profiles_findProfileLikeFrom_".$user_id_to;
            Yii::app()->cache->delete($mkey);            
            $mkey = "Profiles_findProfileMatches_".Yii::app()->user->id;
            Yii::app()->cache->delete($mkey);
            $mkey = "Profiles_findProfileMatches_".$user_id_to;
            Yii::app()->cache->delete($mkey);            
        }
        return $res;
    }     
    
    
    
    
    
    /**
     * add img
     */    
	public function imgAdd($tmpName, $fbimage = null)
    {
        if (!$this->id || !$tmpName)
            return false;
        
        /*if ($this->getDataValue('pics')>=9)
        {
            return false;
        }*/
        
        $n = $this->getInfoValue('img_last');
        $n++;
       
        $modelImg = new Img;
        if ($modelImg->saveProfileImage($this->id, $tmpName, $n))
        {
        	
            /*$this->data['info']['img_last'] = $n;
            
            $imgs = $this->data['info']['imgs'];                
            $imgs[] = $n;
            
            $this->data['info']['imgs'] = $imgs;
            
            $sql="UPDATE users_info SET imgs=:imgs, img_last=:img_last WHERE user_id=:user_id";
            Yii::app()->db->createCommand($sql)
            	->bindValue(":user_id", $this->id, PDO::PARAM_INT)
            	->bindValue(":img_last", $n, PDO::PARAM_INT)
            	->bindValue(":imgs", serialize($imgs), PDO::PARAM_STR)        
            	->execute();*/
			$sql="UPDATE users_info SET img_last=:img_last WHERE user_id=:user_id";
            Yii::app()->db->createCommand($sql)
            	->bindValue(":user_id", $this->id, PDO::PARAM_INT)
            	->bindValue(":img_last", $n, PDO::PARAM_INT)
            	->execute();            
            
            $sql = "INSERT INTO `user_image` (user_id, n) VALUES (:user_id, :n)";
            Yii::app()->db->createCommand($sql)
            	->bindValue(":user_id", $this->id, 	PDO::PARAM_INT)
            	->bindValue(":n", 		$n, 		PDO::PARAM_INT)	
            	->execute();
			$imgId = Yii::app()->db->lastInsertId; 
			
			//n 2012-08-01: Import image from fb
			if($fbimage != null){
				$sql = "INSERT INTO `user_image_fb` (user_id, n, image) VALUES (:user_id, :n, :image) 
						on duplicate key update image = :image";
				Yii::app()->db->createCommand($sql)
					->bindValue(":user_id", $this->id, 	PDO::PARAM_INT)
					->bindValue(":n", 		$n, 		PDO::PARAM_INT)	
					->bindValue(":image", 	$fbimage, 	PDO::PARAM_STR)	
					->execute();
			}           	
            
			if ($this->data['image'] && is_array($this->data['image']))
				$this->Update('pics', 1 + count( $this->data['image'] ));
			else
				$this->Update('pics', 1);
				
			$this->_cacheDelete();//!!!
			$this->getData();//!!!
        }
        else
        	$n = 0;

        return $n;
    }    
    /**
     * del img from info
     */    
    public function imgDel($n)
    {
    	if (!$this->id || !$n)
    		return false;
    	
    	$sql = "DELETE FROM user_image WHERE user_id=:user_id AND n=:n LIMIT 1;";
    	$sql.= "DELETE FROM user_image_fb WHERE user_id=:user_id AND n=:n LIMIT 1;";//n 2012-08-01: Import image from fb
		Yii::app()->db->createCommand($sql)
            	->bindValue(":user_id", $this->id, 	PDO::PARAM_INT)
            	->bindValue(":n", 		$n, 		PDO::PARAM_INT)	
            	->execute();    	
            	            	
		$img = new Img();
		$img->delProfileImg($this->id, $n);
		
		
		$imgsCount = count($this->data['image']);
		$imgsCount--;
		$this->Update('pics', $imgsCount);
		
		$this->_cacheDelete();
		
		

    }      


    
    /**
     * return user have (true/false) image $i
     */
    public function imgHave($i)
    {
        return true;////($this->data['info']['imgs'][$i]) ? true : false;
    }    

    /**
     * return indx of image from $this->date['image']
     */
    public function imgGetIndx($n)
    {
        if (!$this->id || !$this->data['image'])
			return false;        
    	
		foreach ($this->data['image'] as $k=>$v)
			if ($v['n']==$n)
				return $k;
			
    	return false;
    }     
    
    /**
     * return profile image url
     */
    public function imgUrl($size='medium', $i=0, $primary=true)
    {
        if (!$this->id)
			return false;
		
		$allowedSizesForFreeUser = array('small','medium','big');
			
		$url = "";
		
    	if ( isset($this->data['image'][$i]) )//if ( $n = $this->data['info']['imgs'][$i] )
        {
            if ($primary)
            {
            	$n = $this->getImgPrimary(); //$i = $this->data['info']['img_primary'];
            	$i = $this->imgGetIndx($n);
            }                   
            else 
            	$n = $this->data['image'][$i]['n'];

         	
            //allow to view
            $allow = false;
            
            /*if ( in_array($size, $allowedSizesForFreeUser) )
            	$allow = true;
            
            if 
            ( 
            	!Yii::app()->user->checkAccess('gold')//members only 
            	&& 
            	($this->data['image'][$i]['xrated']=='0' || $this->data['image'][$i]['xrated']=='naked') 
            )
            	$allow = false;
            
            //allow for owner
            if ( Yii::app()->user->id==$this->id )
            	$allow = true;
            
            $url = "";*/
			$allow = true;
			            
            if ($allow || ADMIN)
            {
	            //get url if image exists in cache
            	if (in_array($size, array('small','medium')))
            		$url = CHelperProfile::imageCachePrepare($this->id, $n, $size);
            	
            	if (!$url)
            		$url = /*SITE_URL .*/ '/img/'.Yii::app()->secur->encryptID($this->data['id']) . '/'.$size . '/'.$n.'.jpg';
            }
            else
            {
            	$url = /*SITE_URL .*/ '/images/design/membersonly_'.$size . '.png';
            }
        }
        
        
        if (!$url)
        {
            if ( !isset($this->data['gender']) || $this->data['gender']=='F')
            {
                $url = /*SITE_URL .*/ '/images/design/nophoto_female_'.$size.'.jpg';
            }
            else
            {
                $url = /*SITE_URL .*/ '/images/design/nophoto_male_'.$size.'.jpg';
            }
        }
//FB::error($url, 'PROFILE URL');        
        return $url;                
    }

    

    /**
     * BAN user
     */
    public function banUser()
    {
        $this->Update('role', 'banned');
    }
    /**
     * UnBan user
     */
    public function UnBanUser()
    {
        if ($this->data['pics'])
            $role = 'approved';
        else 
            $role = 'justjoined';
            
        $this->Update('role', $role);
        return $role;
    }
    
    
    public function textInteresting()
    {
        if (!$this->id)
            return '';
        
        $interesting = $this->data['personal']['interesting'];
        return $interesting;
    }
    
    /*
     * return profile URL
     * byID or by Username - may be need in the future
     */
	public function getUrlProfile($byId=true)
	{
		if ($byId)
			return '/profile/'.Yii::app()->secur->encryptID($this->id);
		else
			return '...';
	}
    
    //n 2012-07-02
	public function getId()// Yii style: getId, not getid		 function getid(){//oleg: dublicate of $this->getDataValue('id')
	{
		return $this->id;
	}
	
/*	
	
	function getencrypid(){//oleg: WHAT the dublicate???
		return Yii::app()->secur->encryptID($this->id);
	}	
	
	function profilelink(){
		$username = $this->getDataValue('username');
		$encid = $this->getencrypid();
		return "<a href='/profile?id={$encid}'>{$username}</a>";//oleg: WHAT the HTML code in this class?
	}

	
oleg: 
1) this class is not a place for creating texts
2) this is dublicate of CHelperProfile::showProfileInfoSimple(...)
	function profileLocationText(){
		$text = '';
		$city = $this->getLocationValue('city');
		if($city != null and $city != '') $text.= ($text != ''?', ':'').$city;
		$state = $this->getLocationValue('stateName');
		if($state != null and $state != '') $text.= ($text != ''?', ':'').$state;
		$country = $this->getLocationValue('country');
		if($country != null and $country != '') $text.= ($text != ''?', ':'').$country;
		return $text;
	}
*/
	//End of n 2012-07-02
    
	
	//n 2012-07-05
	
	/**
	 * Return user's dating record
	 * Note: Dating data is rarely used. Dont need to cache when initialize this install.
	 * Cache for the first time uage
	 */ 
	function getDating(){
		if(!isset($this->data['dating'])){
			$this->data['dating'] = Yii::app()->db
				->createCommand("SELECT * FROM users_dating WHERE user_id=:user_id LIMIT 1")
				->bindValue(":user_id", $this->id, PDO::PARAM_INT)
				->queryRow();
			
			//Correct data with old version. Will be remove later
			if(!$this->data['dating']){
				$sql="INSERT INTO users_dating (user_id) VALUES ({$this->id})";
				Yii::app()->db->createCommand($sql)->execute();	
				$this->data['dating']['user_id'] = $this->id;
			}
			
		}
		
		$this->data['dating']['age'] = $this->getSettingsValue('ageMin') - 17;
		$this->data['dating']['maxage'] = $this->getSettingsValue('ageMax') - 17;
		return $this->data['dating'];
	}
	
	/**
	 * Update full record might be faster?
	 * Infact, we dont use any field alone, we get and update full row
	 */
	function datingUpdate($record){
		//"age", "maxage", 
		if(isset($record['age'])) $this->settingsUpdate('ageMin', $record['age'] + 17);
		if(isset($record['maxage'])) $this->settingsUpdate('ageMax', $record['maxage'] + 17);
		
		$textfields =array("body_type", "drinking", "eye_color", "hair_color", "interests", "looking_for", "personality", 
					"relationship_status", "smoking", "anal", "experience", "live", "kinky", "occupation", "oral", "religion", 
					"language", "appearance", "ethnicity", "hair_length", "best_feature", "piercings_tattoos", "nationality", "style");
		$intfields = array("income", "maxincome", "height", "maxheight", 'education','glasses','startsign');
		
		
        $sql="UPDATE users_dating SET ";
		$setql = '';
		foreach($textfields as $name){
			if(isset($record[$name])){
				$setql .= ($setql==''?' ':', ')." $name=:$name";
			}
		}
		foreach($intfields as $name){
			if(isset($record[$name])){
				$setql .= ($setql==''?' ':', ')." $name=:$name";
			}
		}
		$sql .= $setql." WHERE user_id=:user_id";
		
		
		$connection=Yii::app()->db;
        $command=$connection->createCommand($sql);        
		$command->bindValue(":user_id", $this->id, PDO::PARAM_INT);
		foreach($textfields as $name){
			if(isset($record[$name])){
				if($record[$name] != '') $record[$name]=','.$record[$name].',';
				$command->bindValue(":$name", $record[$name], PDO::PARAM_STR);
				$this->data['dating'][$name] = $record[$name];
			}
		}
		foreach($intfields as $name){
			if(isset($record[$name])){
				$command->bindValue(":$name", $record[$name], PDO::PARAM_INT);
				$this->data['dating'][$name] = $record[$name];
			}
		}
				
        $command->execute();
		$this->_cacheUpdate();
	}
	
	//End n
	
    /**
     * STATIC
     * DELETE user
     */
    static function deleteUser($id)
    {
        if (!$id = intval($id))
            return false;
        
        /*$sql = "
            DELETE FROM users WHERE id={$id};
            DELETE FROM users_secur_id WHERE secur_id='".Yii::app()->secur->encryptID($id)."';
            DELETE FROM users_activity WHERE user_id={$id};
            DELETE FROM users_info WHERE user_id={$id};
            DELETE FROM users_location WHERE user_id={$id};
            DELETE FROM users_personal WHERE user_id={$id};
            DELETE FROM users_settings WHERE user_id={$id};
			DELETE FROM users_dating WHERE user_id={$id};
			DELETE FROM user_image WHERE user_id={$id};

			DELETE FROM ext_FB WHERE user_id={$id};
            DELETE FROM ext_G WHERE user_id={$id};
            DELETE FROM profile_like WHERE id_from={$id} OR id_to={$id};
            DELETE FROM profile_messages WHERE id_from={$id} OR id_to={$id};
            DELETE FROM profile_updates WHERE user_id={$id};
            DELETE FROM profile_view WHERE id_from={$id} OR id_to={$id};
            DELETE FROM profile_winks WHERE id_from={$id} OR id_to={$id};
        ";
        
        Yii::app()->db->createCommand($sql)->execute();*/
		$sql = "
            UPDATE users SET `role`='deleted' WHERE id={$id};
            DELETE FROM user_image WHERE user_id={$id};
        ";
        
        Yii::app()->db->createCommand($sql)->execute();
            
        $dir = CHelperProfile::getUserImgDir($id);
        CHelperFile::clearDir( $dir );
        
		$mkey = "profile_".$id;
		Yii::app()->cache->delete($mkey);	// Yii::app()->cache->flush();
    }    
    
    /**
     * STATIC
     * check if username exists
     * return ID!!!
     */
    public static function usernameExist($username)
    {
    	$username = strtolower(trim($username));
    	
    	$res = Yii::app()->db
                ->createCommand("SELECT id FROM users WHERE LOWER(username)=:username LIMIT 1")
                ->bindValue(":username", $username, PDO::PARAM_STR)
                ->queryScalar();
        return ($res) ? $res : 0;
    }
    /**
     * STATIC
     * check if email exists
     * return ID!!!
     */
    public static function emailExist($email)
    {
        $email = strtolower(trim($email));
        
    	$res = Yii::app()->db
                ->createCommand("SELECT id FROM users WHERE LOWER(email)=:email LIMIT 1")
                ->bindValue(":email", $email, PDO::PARAM_STR)
                ->queryScalar();
        return ($res) ? $res : 0;
    }    	 

    /**
     * STATIC
     * create username from email
     */
    static function usernameCreateFromEmail($email)
    {
        $res = explode('@',$email);
        $username = $res[0];
        
        if ( preg_match(PROFILE_USERNAME_PATTERN, $username ) )
        	if (!Profile::usernameExist($username))
        		return $username;
        
        
        if ( strlen($username)>(PROFILE_USERNAME_LEN_MAX-5) )
            $username = substr($username, 0, (PROFILE_USERNAME_LEN_MAX-5));
		
		$usernameTMP = $username;
            
        if ( strlen($username)<PROFILE_USERNAME_LEN_MIN )
        	$username = $usernameTMP . rand(11111, 99999);
		
        while (Profile::usernameExist($username))
        {
            $username = $usernameTMP . rand(11111, 99999);
        }
        
        return $username;
    }
	

    
    
    
    
    
	//CAMS
	/*
	 * //PAYMENT CAMS
	* table user_payment_cams
	*/
	public function getPaymentCams()
	{
		if (!$this->id) return false;
	
		if (!isset($this->data['payment_cams']) || !$this->data['payment_cams'])
		{
			$this->data['payment_cams'] = Yii::app()->db
				->createCommand("SELECT * FROM user_payment_cams WHERE id=:id LIMIT 1")
				->bindValue(":id", $this->id, PDO::PARAM_INT)
				->queryRow();
			 
			$this->_cacheUpdate();
		}
	
		return $this->data['payment_cams'];
	}

	
	/*
	 * joined to whitelabel
	 */
	public function isJoinedTo_NLC()
	{
		return ($this->data['settings']['cams_joined']=='1');
	}	
	/*
	 * joined to PM as gold
	 */
	public function isJoinedTo_ONL()
	{
		return ($this->data['role']=='gold');
	}
    
    
}
