<?php

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class UserNameForm extends CFormModel
{
    public $username;

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
            array('username', 'required'),
            array('username', 'length', 'min'=>PROFILE_USERNAME_LEN_MIN, 'max'=>PROFILE_USERNAME_LEN_MAX),
            array('username', 'usernameCheck'), 
		);
	}

    public function usernameCheck()
	{
        $this->username = trim($this->username);
        
        if(!$this->hasErrors())
        {
            if ( !preg_match(PROFILE_USERNAME_PATTERN, $this->username) )
            {
                $this->addError('username',"The username must contain alphanumeric characters (A-Z, 0-9) or a period ('.').");
            }
            else
            {
                $id = Profile::usernameExist($this->username);
                
                if ( $id && $id!=Yii::app()->user->id )
                {
                    $this->addError('username',"Username already exists.");
                }
                
                if(!$this->hasErrors())
                {
                    Yii::app()->user->Profile->Update('username', $this->username);
                }
            }            
        }
	}

     
        
}
