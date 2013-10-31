<?php

class Allin9Step1Form extends CFormModel
{
	public $genders;
    public $email;
	public $username;
	
	public $birthday;
    
    //public $firstName;
    //public $lastName;
    
    
    ///public $email2;
	public $password;
    //public $password2;
    
	///public $agree;
    ///public $birthday;// = array('day'=>1, 'month', 'year');
    
    //public $reg;

	//private $_identity;


    public $country;
    public $countries;
    public $state;//US only
    public $states;
    public $city;
    public $location_id;
    public $zip;	
	
	public $form='932';
	public $affid=1;
	public $role='free';
    
	public $ip;
	
    public function init()
    {
        parent::init();
        
        $this->affid = HelperAff::registerAffId();
        
		//$this->genders = CHelperProfile::getGenders();
        
        $this->ip = CHelperLocation::getIPReal();


		$this->countries = CMap::mergeArray( array(''=>'Select ...'), Yii::app()->location->getCountriesList());
		$this->states = CMap::mergeArray( array(''=>'Select ...'), Yii::app()->location->getStatesList('US'));
    }
    	
	
	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
			// username and password are required
			array('username, email, password, country, zip, ip, city', 'required'),//array('username, email, password, city', 'required'),
            array('username', 'length', 'min'=>PROFILE_USERNAME_LEN_MIN, 'max'=>PROFILE_USERNAME_LEN_MAX),
            array('username', 'usernameCheck'),			
			array('email', 'email'),///array('email, email2', 'email'),
            array('email', 'emailCheck'),
            ///array('email2', 'compare', 'compareAttribute'=>'email'/*, 'on'=>'register'*/),

            array('password', 'length', 'min'=>6, 'max'=>32),
            //array('password2', 'compare', 'compareAttribute'=>'password'/*, 'on'=>'register'*/),
            
            //array('firstName', 'length', 'min'=>1, 'max'=>25),
            //array('lastName', 'length', 'min'=>1, 'max'=>25),
            
            
            //array('gender', 'CRangeValidator', 'on'=>array('x','y')),            
            array('genders', 'in', 'range'=>CHelperProfile::getGenders(true) ),
            array('birthday', 'birthdayCheck'),
            
            ///array('agree', 'boolean'),            
            ///array('agree', 'agreeCheck'),
            //array('birthday', 'in', 'range'=>array(1,2,3,4) ),
            //array('birthday','birthdayCheck'),
            
			// password needs to be authenticated
			//array('password', 'authenticate'),
			
            array('location_id', 'locationCheck'),
            array('state', 'stateCheck'),
            array('city', 'length', 'min'=>3, 'max'=>150), 
            array('zip', 'zipCheck'),
            array('zip', 'length', 'min'=>3, 'max'=>13),
            
            
            
            array('ip', 'ipCheck'),
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
        if (Profile::emailExist($this->email))
        {
            $this->addError('email',"Email already exists.");
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
        if ( CHelperProfile::checkExistedRegIP() )
        {
        	$this->addError('ip',"User allready registered");
        	return false;
        }
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
FB::error($this->country.'-'.$city.'-'.$state);
            		
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

    public function zipCheck()
    {
        /*if (!$this->zip)
        {
            $this->addError('zip', "Don't forget your Zipcode!");
        }
        else*/if ( $this->zip && !preg_match(ZIP_PATTERN, $this->zip) )
        {
            $this->addError('zip', 'Wrong format of Zip code.');
        }
    }    
    

	public function doAllin9Registration()
	{
		if(!$this->hasErrors())
        {
//FB::error($this->location_id);
			if (!$this->location_id)
	        {
	            if ($this->city)
	            {
	            	if (stristr($this->city, ','))
	            	{
	            		$loc = explode(',', $this->city);
	            		$city = trim($loc[0]);
	            		$state = trim($loc[1]);
//FB::error($this->country.'-'.$city.'-'.$state);            		
	            		$sql = "SELECT id FROM `location_geoip_cities` WHERE `country`=:country AND `state`=:state AND city LIKE :city LIMIT 1";
			            $loc_id = Yii::app()->dbGEO->createCommand($sql)
			                    ->bindValue(":country", $this->country, 	PDO::PARAM_STR)
			                    ->bindValue(":state", strtoupper($state), 	PDO::PARAM_STR)
			                    ->bindValue(":city", $city.'%', 			PDO::PARAM_STR) 
			                    ->queryScalar();
	/*					$sql = "SELECT id FROM `location_geoip_cities` WHERE `country`='{$this->country}' AND `state`=UPPER('{$state}') AND city LIKE '{$city}%' LIMIT 1";
	FB::error($sql);
			            $loc_id = Yii::app()->db->createCommand($sql)
			                    //->bindValue(":country", $this->country, PDO::PARAM_STR)
			                    //->bindValue(":state", $state, 		PDO::PARAM_STR)
			                    //->bindValue(":city", $city.'%', 	PDO::PARAM_STR) 
			                    ->queryScalar();
	*/            		
	            	}
	            	else
	            	{
						$city = trim($this->city);
						
	            		$sql = "SELECT id FROM `location_geoip_cities` WHERE `country`=:country AND city LIKE :city LIMIT 1";
			            $loc_id = Yii::app()->dbGEO->createCommand($sql)
			                    ->bindValue(":country", $this->country, PDO::PARAM_STR)
			                    ->bindValue(":city", $city.'%', 	PDO::PARAM_STR) 
			                    ->queryScalar(); 					
	            	}
	
//FB::error($loc_id); 
		       	
		            if ($loc_id)
		            	$this->location_id = $loc_id; 
		        	/*else
		        		$this->location_id = Yii::app()->location->findLocationIdByIP();*/            	
	
		            /*if (!$this->location_id)
		        		$this->addError('city',$mess);*/	        		
	            }
	            /*else
	            	$this->addError('city',$mess);*/
	
	        }
			
	        if (!$this->location_id)
				$this->location_id = Yii::app()->location->findLocationIdByIP(); 


//FB::warn($this->attributes);










            $profile = new Profile;
            if ($profile->Registration($this->attributes))
            {
                $modelLogin = new LoginForm;
                $modelLogin->attributes = array('username'=>$this->email, 'password'=>$this->password, 'rememberMe'=>true);
                $modelLogin->login();
                
                
                
                
				/*Yii::app()->mail->prepareMailHtml(
			        	$profile->getDataValue('email'), 
			            '', 
			            'welcome', 
			            array(
			                'user_id'	=> $profile->getDataValue('id'),
						)
				); */                
                
                
                
                return true;
            }
            else
            {
                //$this->addError('reg','Registration error...');
            }
        }
        return false;/**/
	}
    
    
        
}
