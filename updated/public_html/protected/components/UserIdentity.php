<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{
    protected $_id; //userID

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
        $model = new User;
        if ( $this->_id = $model->find($this->username, $this->password) )
        {
            $this->errorCode = self::ERROR_NONE;
        }
        else
        {
            $this->errorCode = $model->getError();
            if (!LOCAL) sleep(1);//÷òî áû íå ïîäáèğàëè ïàğîëü...
        }
            
		return !$this->errorCode;
	}

	public function authenticateByUserId($userId)
	{
        $model = new User;
        
        $this->_id =  $model->findById($userId);
//echo $this->_id;
        if ( $this->_id )
        {
            $this->errorCode = self::ERROR_NONE;
        }
        else
        {
            $this->errorCode = $model->getError();
            if (!LOCAL) sleep(1);//÷òî áû íå ïîäáèğàëè ïàğîëü...
        }
        
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