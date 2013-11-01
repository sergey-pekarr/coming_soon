<?php

class ApiSignup extends CFormModel
{
	public $key;
	public $username;
	public $password;
	public $email;
	public $birthday;
    public $country;
    public $state;
    public $zip;
    public $city;	
	public $affid;
	public $gender;
	public $looking_for_gender;	

	public $location_id;	
	public $form='88';	
	public $role='free';    
	public $ip='1.0.0.0';
	
	
    public function init()
    {
        parent::init();
        
        $this->country = strtoupper( trim($this->country) );
        $this->state = strtoupper( trim($this->state) );
        
        $this->ip='0.0.0.0';//$this->ip = CHelperLocation::getIPReal();
    }
    	
	
	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
			array('key, username, email, password, birthday, zip, gender, affid, looking_for_gender', 'required'),
            array('username', 'length', 'min'=>PROFILE_USERNAME_LEN_MIN, 'max'=>PROFILE_USERNAME_LEN_MAX),
            array('username', 'usernameCheck'),			
			array('email', 'email'),
            array('email', 'emailCheck'),

            array('key', 'keyCheck'),
            
            array('affid', 'numerical', 'allowEmpty'=>false, 'integerOnly'=>true, 'min'=>100, 'tooSmall'=>'Bad affid'),
            
            array('password', 'length', 'min'=>6, 'max'=>32),
            
            array('birthday', 'birthdayCheck'),
            
            array('country', 'countryCheck'),
            array('state', 'stateCheck'),
            
            //array('city', 'length', 'min'=>3, 'max'=>150),
            array('city', 'locationCheck'), 
            array('zip', 'zipCheck'),
            array('zip', 'length', 'min'=>3, 'max'=>13),
            
            array('ip', 'length', 'min'=>0, 'max'=>15),
            array('ip', 'ipCheck'),
            
            array('gender', 'in', 'range'=>array('M','F','C')),
            array('looking_for_gender', 'in', 'range'=>array('M','F','C')),
		);
	}

    public function usernameCheck()
	{
        $this->username = trim($this->username);
        
        if ( !preg_match(PROFILE_USERNAME_PATTERN, $this->username) )
        {
            $this->addError('username',"The username must contain alphanumeric characters (A-Z, 0-9) or a period ('.').");
        }
        else
        {
            $id = Profile::usernameExist($this->username);
            if ($id && $id!=Yii::app()->user->id )
            {
                $this->addError('username',"Username already exists.");
            }
        }
	}   

    public function birthdayCheck()
    {
    	$pattern = '/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/';//YYYY-MM-DD
        if (!preg_match($pattern, $this->birthday))
        {
        	$this->addError('birthday', 'Date is invalid.');
        	return;
        }
        	
    	list($year,$month,$day) = explode("-",$this->birthday);
        
		$this->birthday = array(
			'year' => $year,
			'month' => $month,
			'day' => $day
		);
       
    	if (!CHelperProfile::checkBirthday($this->birthday))
        {
            $this->addError('birthday', 'Date is invalid.');
        }
        elseif (!Yii::app()->helperProfile->checkBirthdayAge18($this->birthday))
        {
            $this->addError('birthday', 'You must be at least 18 years old to register at this site.');
        }
    }
     
    public function emailCheck()
	{
        if ($this->email) $this->email=trim($this->email);
		
		if (Profile::emailExist($this->email))
        {
            $this->addError('email',"Email already exists.");
        }
	}
    
    public function keyCheck()
	{
		$sql = "SELECT COUNT(apikey) FROM `api_keys` WHERE `apikey`=:apikey LIMIT 1";
		$exists = Yii::app()->db->createCommand($sql)
					->bindValue(":apikey", trim($this->key), PDO::PARAM_STR)
		            ->queryScalar();
		            
		if (!$exists)
			$this->addError('key',"Wrong api key.");
	}

    public function ipCheck()
	{
        if ($this->ip)
        {
            $this->ip = trim($this->ip);
        	
        	$valid = false;
    
		    $regexp = '/^((1?\d{1,2}|2[0-4]\d|25[0-5])\.){3}(1?\d{1,2}|2[0-4]\d|25[0-5])$/';
		    
		    if (!preg_match($regexp, $this->ip))
		    	$this->addError('ip',"Wrong ip.");
        }
        else
        {
        	$this->ip='0.0.0.0';//to prevent storing Aff ip as user ip
        }
	}	
	
    public function countryCheck()
	{
        if ( !$this->country )
        {
			if ($this->ip && $this->ip!='0.0.0.0')
				return;
        	
        	if(!$this->hasErrors())
        	{
	        	$this->addError('country',"Bad country.");
	        	return;        	
        	}        
        }
		
        $this->country = trim($this->country);
        
		$countries = Yii::app()->location->getCountriesList();
		
		if ( !isset($countries[$this->country]) )
        {
        	$this->addError('country',"Bad country.");
        	return;
        }
	}	

    public function stateCheck()
	{
		if ($this->state) $this->state = strtoupper(trim($this->state));

	    if ( !$this->state && $this->ip && $this->ip!='0.0.0.0')
			return;
		
		if ($this->country == 'US')
        	if (!Yii::app()->location->getRegionName($this->country, $this->state))
        		$this->addError('state',"Bad state.");
	}		

	
    public function locationCheck()
    {
		if($this->hasErrors()) return;
    	
    	//try to find from IP
    	if($this->ip && $this->ip!='0.0.0.0')
		{
			$this->location_id = Yii::app()->location->findLocationIdByIP($this->ip);
		}
		
		if(!$this->location_id && !$this->city)
		{
			$this->addError('city',"Empty city.");
			return;
		}
		
    	//try to find from country/[state]/city
    	if(!$this->location_id && $this->country && $this->city)
		{
			if ($this->country=='US')
			{
	    		$sql = "SELECT id FROM `location_geoip_cities` WHERE `country`=:country AND `state`=:state AND `city`=:city LIMIT 1";
				$this->location_id = Yii::app()->dbGEO->createCommand($sql)
					->bindValue(":country", $this->country, PDO::PARAM_STR)
				    ->bindValue(":state", 	$this->state, 	PDO::PARAM_STR)
				    ->bindValue(":city", 	$this->city, 	PDO::PARAM_STR) 
				    ->queryScalar();   			
			}
			else
			{
	    		$sql = "SELECT id FROM `location_geoip_cities` WHERE `country`=:country AND `city`=:city LIMIT 1";
				$this->location_id = Yii::app()->dbGEO->createCommand($sql)
					->bindValue(":country", $this->country, PDO::PARAM_STR)
				    ->bindValue(":city", 	$this->city, 	PDO::PARAM_STR) 
				    ->queryScalar();  			
			}
			
			if (!$this->hasErrors() && !$this->location_id)
			{
				$this->addError('city',"Bad city.");
				return;
			}
		}
		
		if (!$this->location_id)
			$this->addError('city',"Location not detected.");		
    }	

    public function zipCheck()
    {
        if ($this->zip) $this->zip = trim($this->zip);
    	
    	if ( $this->zip && !preg_match(ZIP_PATTERN, $this->zip) )
        {
            $this->addError('zip', 'Wrong format of Zip code.');
        }
    }    
    

	public function doApiRegistration()
	{
		$user_id = 0;
		
		if(!$this->hasErrors())
        {
        	$profile = new Profile;
            if ($profile->Registration($this->attributes))
            {
				Yii::app()->mail->prepareMailHtml(
			        	$profile->getDataValue('email'), 
			            '', 
			            'welcome', 
			            array(
			                'user_id'	=> $profile->getDataValue('id'),
						)
				);
                
				$user_id = $profile->getDataValue('id');
            }
            else
            {
                //$this->addError('reg','Registration error...');
            }
        }
        return $user_id;
	}
    
    
        
}
