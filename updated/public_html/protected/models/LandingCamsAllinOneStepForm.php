<?php

class LandingCamsAllinOneStepForm extends CFormModel
{
	public $subStep;
	
	public $genders;
    public $email;
	public $username;
	public $birthday;
	public $password;
	public $form='cams';
	public $affid=1;
	public $role='free';
	public $ip;

	
	
	public $ccname;
	public $ccnum;
    public $ccmon;
	public $ccyear;	
	public $ccvv;
	public $firstname;
	public $lastname;
    public $address;
    public $country;
//public $countries;
    public $state="";//US only
//public $states;
    public $city;
    public $location_id;
    public $zip;	    
	//public $country;
	//public $state="";
	//public $city;
	//public $zip;
	//public $email;
	public $location;
    //public $ccyears;
    //public $ccmonths;	
	
    public function init()
    {
        parent::init();
        
        $this->affid = HelperAff::registerAffId();
        
		//$this->genders = CHelperProfile::getGenders();
        
        $this->ip = CHelperLocation::getIPReal();

		//init subStep
		if (CAMS)
		{
	        if (isset($_POST['LandingCamsAllinOneStepForm']['subStep']))
				$this->subStep = $_POST['LandingCamsAllinOneStepForm']['subStep'];
			elseif (Yii::app()->user->id)
	        	$this->subStep=1;
	        else
	        	$this->subStep=0;		
		}
		else
			$this->subStep=3;

        	
		//$this->countries = CMap::mergeArray( array(''=>'Select ...'), Yii::app()->location->getCountriesList());
		//$this->states = CMap::mergeArray( array(''=>'Select ...'), Yii::app()->location->getStatesList('US'));
		
		
        //for ($y=date("Y"); $y<=(date("Y")+10); $y++ )
        	//$this->ccyears[$y] = $y;
        
		//for ($m=1; $m<=12; $m++ )
			//$this->ccmonths[sprintf("%02d", $m)] = sprintf("%02d", $m);

        //$this->location = Yii::app()->user->Profile->getDataValue('location');
        
        //$this->state = "";
        
        //$this->email = Yii::app()->user->Profile->getDataValue('email');
        
/**/if (DEBUG_IP)
{
	if (!Yii::app()->user->id && !$this->username)
	{
		$this->username = 'c'.date("md").'_'.rand(1111, 9999);
		$this->email = $this->username.'@lo.lo';
		$this->password = 'ok1605';
	}
	
	
	//$this->ccnum = (CHelperPayment::getCamsWay(Yii::app()->user->id)=='nb') ? '4134158247831891' : '4012888888881881';
	switch(CHelperPayment::getCamsWay(Yii::app()->user->id))
	{
		case 'rg' : $this->ccnum = '4012888888881881'; break;
		case 'payon' : $this->ccnum = '4111111111111111'; break;
	}
	
	$this->ccmon = '12';
	$this->ccyear = '2013';
	$this->ccvv = '123';
	
	$this->firstname = 'Test';
	$this->lastname = 'Test';
	//$this->city = 'New York';
	$this->zip = '12345';
	$this->address = 'Street';
}


/*	if ( !Yii::app()->user->id )
	{
		$this->country = (isset($_SERVER['GEOIP_COUNTRY_CODE'])) ? $_SERVER['GEOIP_COUNTRY_CODE'] : 'US';
//$this->country='UA';		
		if ($this->country=='US')
			$this->state = (isset($_SERVER['GEOIP_REGION'])) ? $_SERVER['GEOIP_REGION'] : '';
//$this->state='NY';
		$this->city = (isset($_SERVER['GEOIP_CITY'])) ? $_SERVER['GEOIP_CITY'] : '';
	}
*/

    /*if (Yii::app()->user->id)
	{
		$profile = Yii::app()->user->Profile;
		$this->username = $profile->getDataValue('username');
		$this->email = $profile->getDataValue('email');
	}*/

    }
    	
	
	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		/**/if (Yii::app()->user->id)
		{
			$rules = array();
		}
		else
		{
			$rules = array(
				// username and password are required
				array('username, email, password', 'required'),//array('username, email, password, city', 'required'),
	            array('username', 'length', 'min'=>4, 'max'=>PROFILE_USERNAME_LEN_MAX),
	            array('username', 'usernameCheck'),			
				array('email', 'email'),///array('email, email2', 'email'),
	            array('email', 'emailCheck'),
	            array('password', 'length', 'min'=>6, 'max'=>32),
	            array('genders', 'in', 'range'=>CHelperProfile::getGenders(true) ),
	            array('birthday', 'birthdayCheck'),
            	array('ip', 'ipCheck'),
			);
		}
		
		$rules1 = array(
			array('ccnum, ccvv', 'required'),//ccname, 
			//array('ccname', 'length', 'min'=>3, 'max'=>255),
			
			array('ccnum', 'length', 'min'=>13, 'max'=>16),
			array('ccnum', 'match', 'pattern'=>CCARD_PATTERN),
			
			array('ccvv', 'length', 'min'=>3, 'max'=>4),
			array('ccvv', 'match', 'pattern'=>CVV_PATTERN),
			
			array('ccmon', 'in', 'range'=>CHelperPayment::getCCMonths() /*$this->ccmonths*/ ),
			array('ccyear', 'in', 'range'=>CHelperPayment::getCCYears()/*$this->ccyears*/ ),
           
		);
		
		$rules2 = array(
			array('firstname, lastname, address, country, zip, city', 'required'),//, country, city, zip, email
			
			array('firstname', 'length', 'min'=>2, 'max'=>40),
			array('lastname', 'length', 'min'=>2, 'max'=>40),
			array('address', 'length', 'min'=>5, 'max'=>80),
			
            array('location_id', 'locationCheck'),
            array('state', 'stateCheck'),
            array('city', 'length', 'min'=>3, 'max'=>150), 
			array('zip', 'match', 'pattern'=>ZIP_PATTERN),
			array('zip', 'length', 'min'=>3, 'max'=>13), 
		);		

		if ($this->subStep>=1)
		{
			$rules = CMap::mergeArray( $rules, $rules1 );		
		}		
		if ($this->subStep>=2)
		{
			$rules = CMap::mergeArray( $rules, $rules2 );		
		}

		return $rules;		
		
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
        if (!$this->birthday['year'] || !$this->birthday['month'] || !$this->birthday['day'] || !Yii::app()->helperProfile->checkBirthday($this->birthday))//if ( !checkdate ( $this->birthday['month'] , $this->birthday['day'] , $this->birthday['year'] ) )
        {
            $this->addError('birthday', 'Date is invalid');
        }
        elseif (!Yii::app()->helperProfile->checkBirthdayAge18($this->birthday))
        {
            $this->addError('birthday', 'You must be at least 18 years old to register at this site.');
        }
    }
     
    public function emailCheck()
	{
        if ( !preg_match('/^[0-9a-zA-Z@_\.-]{5,129}$/', $this->email) )
        {
        	$this->addError('email', 'Email is invalid');
        }
		else
		{
			$id = Profile::emailExist($this->email);
			if ($id && $id!=Yii::app()->user->id)
	        {
	            $this->addError('email',"Email already exists.");
	        }
		}
	}
    
    /*public function agreeCheck()
    {
        if ( !$this->agree )
        {
            $this->addError('agree', 'Please confirm the terms and conditions');
        }
    }*/

    public function ipCheck()
	{
       //skype 2013-03-12: remove this
	/*	
		if (Yii::app()->user->id==0 && CHelperProfile::checkExistedRegIP() )
        {
        	$this->addError('ip',"User allready registered");
        	return false;
        }
        */
	}	
        
	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
            //'firstName'=>'First name',
            //'lastName'=>'Last name',
            'genders'=>'I am a',
            'birthday'=>'Birthday',
            'email'=>'E-mail address',
            //'email2'=>'Confirm E-mail Address',
            'password'=>'Password',
            //'password2'=>'Confirm Password',

			'country'       =>"Country",
			'state'       	=>"State",
            'location_id'   =>'Your location',
            'city'          =>'City',
            'zip'           =>'Zip / Postal code',
		
		
            "ccname"           	=> "Cardholder's name",
			"ccnum"				=> "Card number",
			"ccvv"				=> "CVV / CVV2",
		
			"firstname"			=> "First name",
			"lastname"			=> "Last name",
			"address"			=> "Street address",
			"country"			=> "Country",
			"state"				=> "State / Province",
		);
	}

	/**
	 * Authenticates the password.
	 * This is the 'authenticate' validator as declared in rules().
	 */
	/*public function authenticate($attribute,$params)
	{
		if(!$this->hasErrors())
		{
			$this->_identity=new UserIdentity($this->username,$this->password);
			if(!$this->_identity->authenticate())
				$this->addError('password','Incorrect username or password.');
		}
	}*/


    public function stateCheck()
	{
        if ( $this->country=='US' )
        {
        	if (!Yii::app()->location->getRegionName($this->country, $this->state))
        	{
	        	$this->addError('state',"Please select state");
	        	return false;
        	}
        }
	}		
	
    public function locationCheck()
    {
        $mess = "Please enter correct city name.";
		if ($this->city)
        {
            		$city = trim($this->city);
            		
            		if ($this->country=='US')
            		{
            			$state = trim($this->state);
//FB::error($this->country.'-'.$city.'-'.$state);
            		
						/*$sql = "SELECT id FROM `location_geoip_cities` WHERE `country`=:country AND `state`=:state AND city LIKE :city LIMIT 1";
			            $loc_id = Yii::app()->db->createCommand($sql)
			                    ->bindValue(":country", $this->country, 	PDO::PARAM_STR)
			                    ->bindValue(":state", strtoupper($state), 	PDO::PARAM_STR)
			                    ->bindValue(":city", $city.'%', 			PDO::PARAM_STR) 
			                    ->queryScalar();*/
						$sql = "SELECT id FROM `location_geoip_cities` WHERE `country`=:country AND `state`=:state AND city=:city LIMIT 1";
			            $loc_id = Yii::app()->dbGEO->createCommand($sql)
			                    ->bindValue(":country", $this->country, 	PDO::PARAM_STR)
			                    ->bindValue(":state", strtoupper($state), 	PDO::PARAM_STR)
			                    ->bindValue(":city", $city, 				PDO::PARAM_STR) 
			                    ->queryScalar();
            		}
            		else
            		{
						$sql = "SELECT id FROM `location_geoip_cities` WHERE `country`=:country AND city=:city LIMIT 1";
			            $loc_id = Yii::app()->dbGEO->createCommand($sql)
			                    ->bindValue(":country", $this->country, 	PDO::PARAM_STR)
			                    ->bindValue(":city", $city, 				PDO::PARAM_STR) 
			                    ->queryScalar();            		
            		}

//FB::error($loc_id, 'loc_id'); 
	       	
	            if ($loc_id)
	            	$this->location_id = $loc_id; 
	        	else
	        		$this->location_id = Yii::app()->location->findLocationIdByIP();            	

	            if (!$this->location_id)
	        		$this->addError('city',$mess);	        		
		}
        else
        	$this->addError('city',$mess);

    }	

    /*public function zipCheck()
    {
        if ( $this->zip && !preg_match(ZIP_PATTERN, $this->zip) )
        {
            $this->addError('zip', 'Wrong format of Zip code.');
        }
    } */   
    
    
    
    
    
    
    
    
    
    /*public function doStep()
    {
    	if(!$this->hasErrors())
    	{
    		switch($this->subStep)
    		{
    			case 0: return $this->doRegistration();
    			case 3: return $this->doPayment();
    			default: return true;
    		}
    	}
    	
    	return false;
    }*/    
    
    
    
    
    
    
    
    

	public function doRegistration()
	{
		if(!$this->hasErrors())
        {
	        $profile = new Profile;
            if ($profile->Registration($this->attributes))
            {
                $modelLogin = new LoginForm;
                $modelLogin->attributes = array('username'=>$this->email, 'password'=>$this->password, 'rememberMe'=>true);
                $modelLogin->login();
                return true;
            }
            else
            {
                //$this->addError('reg','Registration error...');
            }
        }
        return false;/**/
	}
	
	
	
	public function updateProfile()
	{
//FB::error($this->attributes, 'attributes');		
		if(!$this->hasErrors())
		{
			if ($this->location_id)
			{
				Yii::app()->user->Profile->locationUpdate($this->location_id, $this->zip);
			}
			
			
			//if user something changed
/*			if (Yii::app()->user->id)
			{
//FB::error($this->attributes, 'attributes');
				$profile = Yii::app()->user->Profile;
				$password = CSecur::decryptByLikeNC( $profile->getDataValue('passwd'));
//FB::error($this->password.' - '.$password);		
				if ( $this->username && $this->username != $profile->getDataValue('username') ) $profile->Update('username', $this->username);
				if ( $this->email	 && $this->email != $profile->getDataValue('email') ) $profile->Update('email', $this->email);
				if ( $this->password && $this->password != $password ) $profile->Update('password', $this->password);			
			}*/
		}
	}
}
