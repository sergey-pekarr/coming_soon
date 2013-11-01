<?php


class UserEditMainTabForm extends CFormModel
{
	public $user_id;
    public $username;
    public $gender;
    public $looking_for_gender;
    public $email;
    public $birthday;
    public $password;
    public $affid;
    
    public $ip_signup;
    //public $ip_last;
    
    public $emailApproved;
    public $emailBounced;
    
	public $joined;    

	public $character;
	
	
    public $promo;
    

	public function __construct($user_id)
	{
		$this->user_id = $user_id;
		
		parent::__construct();//after user_id
	}    
    
    
    public function init()
    {
FB::error($this->user_id);
    	
        if ($this->user_id)
        {
            $profile = new Profile($this->user_id);
        	$userData = $profile->getData();
        	
            $this->username = $userData['username'];
            $this->gender = $userData['gender'];
            $this->looking_for_gender = $userData['looking_for_gender'];
            $this->email = $userData['email'];
            $this->affid = $userData['affid'];
            $this->password = CSecur::decryptByLikeNC( $userData['passwd'] );
            
            $this->joined = $userData['activity']['joined'];
            
            
            $this->ip_signup = $userData['info']['ip_signup'];
            //$this->ip_last = $userData['info']['ip_last'];
            
            //$this->birthday = $userData['birthday'];
$birthday = strtotime($userData['birthday']);
$this->birthday['year'] = date('Y', $birthday);
$this->birthday['month'] = date('m', $birthday);
$this->birthday['day'] = date('d', $birthday);            


            $this->character = $userData['personal']['character'];
			
            $this->emailApproved = ($userData['settings']['email_activated_at']!='0000-00-00 00:00:00');
            $this->emailBounced = ($userData['settings']['email_bounced'] == '1');
            
            $this->promo = $userData['promo'];
        }
    }
    
	public function rules()
	{
		$rules = array(
			array('username', 'required'),
            array('username', 'length', 'min'=>PROFILE_USERNAME_LEN_MIN, 'max'=>PROFILE_USERNAME_LEN_MAX),
            array('username', 'usernameCheck'),
            array('birthday','birthdayCheck'),
            array('gender', 'in', 'range'=>array('F','M','C') ),
            array('looking_for_gender', 'in', 'range'=>array('M', 'F', 'C', 'MF', 'MFC') ),
            array('character', 'length', 'min'=>0, 'max'=>4000),
		);
		
		$rulesReal = array(
            array('password', 'length', 'min'=>6, 'max'=>32),
            
			array('email', 'email'),///array('email, email2', 'email'),
            array('email', 'emailCheck'),
		);		
		
		if ($this->promo=='0')
		{
			$rules = CMap::mergeArray( $rules, $rulesReal );		
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
            if ( $id && $id!=$this->user_id )
            {
                $this->addError('username',"Username already exists.");
            }
        }
	}
	
    public function emailCheck()
	{
 		if ( !$this->hasErrors() && $this->promo=='0' )
        {
            $id = Profile::emailExist($this->email);
        	
            if ($id && $id!=$this->user_id)
        		$this->addError('email',"Email already exists.");
        }
	}	

    public function birthdayCheck()
    {
        if (!Yii::app()->helperProfile->checkBirthday($this->birthday))//if ( !checkdate ( $this->birthday['month'] , $this->birthday['day'] , $this->birthday['year'] ) )
        {
            $this->addError('birthday', 'Date is invalid');
        }
        elseif (!Yii::app()->helperProfile->checkBirthdayAge18($this->birthday))
        {
            $this->addError('birthday', 'You must be at least 18 years old.');
        }
    }



	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
            //'character'   =>  "Describe what you're about in less than 255 characters. Make it count!",
		);
	}

	

    public function saveMain()
    {
FB::error($this->username);

    	if (!$this->hasErrors() && $this->user_id)
        {

        	
        	$profile = new Profile($this->user_id);
            
            $profile->Update('username', trim($this->username) );
            $profile->Update('gender', $this->gender);
            $profile->Update('looking_for_gender', $this->looking_for_gender);
            
            $birthday = date('Y-m-d', strtotime($this->birthday['year'].'-'.$this->birthday['month'].'-'.$this->birthday['day']));
            $profile->Update('birthday', $birthday);
               

			$profile->personalUpdate('character', trim($this->character) );
            
            if ($this->promo=='0')
            {
	            $profile->Update('email', trim($this->email) );            
	            $profile->Update('password', trim($this->password) );
            }

            
        }

    }    
    
    
    
}
