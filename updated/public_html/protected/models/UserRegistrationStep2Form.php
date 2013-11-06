<?php

/**
 * 
 */
class UserRegistrationStep2Form extends CFormModel
{
    public $username;
    public $birthday;
    public $country;
    public $city;
    public $location_id;
    public $zip;
	
    public $notifyMe=1;
    
    public function init()
    {
        $this->zip = '';
    }

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
            array('username, city, notifyMe', 'required'),
            array('username', 'length', 'min'=>PROFILE_USERNAME_LEN_MIN, 'max'=>PROFILE_USERNAME_LEN_MAX),
            array('username', 'usernameCheck'),
            array('location_id', 'locationCheck'),
            array('city', 'length', 'min'=>3, 'max'=>150), 
            array('zip', 'zipCheck'),
            array('zip', 'length', 'min'=>3, 'max'=>13),            
            
            array('birthday', 'birthdayCheck'),
		);
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
    
    public function locationCheck()
    {
        $mess = "Please enter correct city name.";
        if (!$this->location_id)
        {
            $this->addError('city',$mess);
        }
        else
        {
            $id = Yii::app()->dbGEO
                    ->createCommand("SELECT COUNT(id) FROM location_geoip_cities WHERE id=:id LIMIT 1")
                    ->bindParam(":id", $this->location_id, PDO::PARAM_INT)
                    ->queryScalar();
            if (!$id)
            {
                $this->addError('city',$mess);
            }
        }
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

    public function zipCheck()
    {
        if (!$this->zip)
        {
            $this->addError('zip', "Don't forget your Zipcode!");
        }
        elseif ( !preg_match(ZIP_PATTERN, $this->zip) )
        {
            $this->addError('zip', 'Wrong format of Zip code.');
        }
    }
	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'username'      =>'Name',
			'birthday'		=>'Your date of birth',
			'country'       =>"Country",
            'location_id'   =>'Location',
            'city'          =>'City',
            'zip'           =>'Zip Code',
		
			'notifyMe'		=>'Receive emails notifying me when another user contacts me',
		);
	}


	public function doRegistrationStep2()
	{
        $res = Yii::app()->user->Profile->RegistrationStep2($this->attributes);

        if (intval($this->notifyMe)==0)
        	Yii::app()->user->Profile->settingsUpdate('hided_notify', '1');
        
		Yii::app()->mail->prepareMailHtml(
	        	Yii::app()->user->Profile->getDataValue('email'), 
	            '', 
	            'welcome', 
	            array(
	                'user_id'	=> Yii::app()->user->Profile->getDataValue('id'),
				)
		); 

		//$profile = Yii::app()->user->Profile;
		
		//Replace by cron
		//CHelperAutoFlirt::buildFakePlan($profile->getId(), $profile->getDataValue('gender'), $profile->getDataValue('looking_for_gender'), 
		//	$profile->getLocationValue('country'), $profile->getLocationValue('latitude'), $profile->getLocationValue('longitude'),  
		//	date('Y-m-d H:i:s'));
        
	}
     
        
}
