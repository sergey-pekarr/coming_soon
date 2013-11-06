<?php

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class UserRegistrationForm extends CFormModel
{
	public $genders;
    //public $birthday;
    
    //public $firstName;
    //public $lastName;
    
    public $email;
    ///public $email2;
	public $password;
    //public $password2;
    
	///public $agree;
    ///public $birthday;// = array('day'=>1, 'month', 'year');
    
    //public $reg;

	//private $_identity;
	
	public $age1;
	public $age2;

	public $affid=1;
	public $sbc=0;
    
    public $ip;
	
	
	public function init()
    {
        parent::init();
		
        $this->affid = HelperAff::registerAffId();
        $this->sbc = Yii::app()->session['sbc'];
        
        $this->age1 = 18;
        $this->age2 = 30; 
        
		$this->genders = CHelperProfile::getGenders();

        $this->ip = CHelperLocation::getIPReal();
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
			array('email, password, ip', 'required'),///array('email, password, agree', 'required'),
			array('email', 'email'),///array('email, email2', 'email'),
            array('email', 'emailCheck'),
            
            array('ip', 'ipCheck'),
            
            ///array('email2', 'compare', 'compareAttribute'=>'email'/*, 'on'=>'register'*/),

            array('password', 'length', 'min'=>6, 'max'=>32),
            //array('password2', 'compare', 'compareAttribute'=>'password'/*, 'on'=>'register'*/),
            
            //array('firstName', 'length', 'min'=>1, 'max'=>25),
            //array('lastName', 'length', 'min'=>1, 'max'=>25),
            
            
            //array('gender', 'CRangeValidator', 'on'=>array('x','y')),            
            array('genders', 'in', 'range'=>CHelperProfile::getGenders(true) ),
            //array('birthday', 'birthdayCheck'),

            array('age1, age2', 'in', 'range'=>CHelperProfile::getAges() ),
            
            ///array('agree', 'boolean'),            
            ///array('agree', 'agreeCheck'),
            //array('birthday', 'in', 'range'=>array(1,2,3,4) ),
            //array('birthday','birthdayCheck'),
            
			// password needs to be authenticated
			//array('password', 'authenticate'),
		);
	}

   

    /*public function birthdayCheck()
    {
        if (!$this->birthday['year'] || !$this->birthday['month'] || !$this->birthday['day'] || !Yii::app()->helperProfile->checkBirthday($this->birthday))//if ( !checkdate ( $this->birthday['month'] , $this->birthday['day'] , $this->birthday['year'] ) )
        {
            $this->addError('birthday', 'Date is invalid');
        }
        elseif (!Yii::app()->helperProfile->checkBirthdayAge18($this->birthday))
        {
            $this->addError('birthday', 'You must be at least 18 years old to register at this site.');
        }
    }*/
     
    public function emailCheck()
	{
		if (!$this->hasErrors() && Profile::emailExist($this->email))
        {
            $this->addError('email',"Email already exists.");
        }
	}

    public function ipCheck()
	{
        if ( CHelperProfile::checkExistedRegIP() )
        {
        	$this->addError('ip',"User allready registered");
        	return false;
        }
	}	
	
    /*public function agreeCheck()
    {
        if ( !$this->agree )
        {
            $this->addError('agree', 'Please confirm the terms and conditions');
        }
    }*/
    
        
	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
            //'firstName'=>'First name',
            //'lastName'=>'Last name',
            'genders'=>"You're a",
            //'birthday'=>'Birthday',
            'email'=>'Email',
            //'email2'=>'Confirm E-mail Address',
            'password'=>'Password',
            //'password2'=>'Confirm Password',
            
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


	public function doRegistration()
	{
		if(!$this->hasErrors())
        {
        	
        	$profile = new Profile;
            if ($profile->Registration($this->attributes))//if ($profile->RegistrationHome($this->attributes))
            {
                $profile->settingsUpdate('ageMin', $this->age1);
                $profile->settingsUpdate('ageMax', $this->age2);
            	
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
        return false;
	}
    
    
        
}
