<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class AdminIdentity extends CUserIdentity
{
    protected $_id; //userID
    

    /*private $_admins = array(
        'oleg'=>'8b7bb6574a5f8020a7a981efd406fb7b',//Ks...
        'admin'=>'c4aaee3de37175e1ce872d441ac362be',//fO3h5Hc8RJ
    );*/

    /**
	 * Authenticates a user.
	 * The example implementation makes sure if the username and password
	 * are both 'demo'.
	 * In practical applications, this should be changed to authenticate
	 * against some persistent user identity storage (e.g. database).
	 * @return boolean whether authentication succeeds.
	 */
	public function authenticate()
	{
        if (!LOCAL) sleep(1);
        
        $id = CHelperUser::adminsFindAdmin($this->username, MD5(SALT.$this->password));
        
		if ($id)//if ( $this->_admins[$this->username] == MD5(SALT.$this->password) )
        {
            $this->errorCode = self::ERROR_NONE;
            $this->_id = $id;
        }
        else
        {
            $this->errorCode = 1;
        }
        
        /*$emailValidator = new CEmailValidator;
        
        $model = new User;
        if ( $this->_id = $model->find($this->username, $this->password) )
        {
            $this->errorCode = self::ERROR_NONE;
        }
        else
        {
            $this->errorCode = $model->getError();
        }*/
            
		return !$this->errorCode;
	}
    
    /**
     * return user ID in DB
     */
    public function getId()
    {
        return $this->_id;
    }

}