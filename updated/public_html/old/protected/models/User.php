<?php

//Note: There are extention methods on UserUtility. Please take a look before edit
class User
{
    private $_error;//1-bad username/email ,  2 - bad password,     3- bad id (user deleted or blocked, ...)
    
    /**
     * find record in DB
     * @param username or email
     * @param password
     * return userID if success
     */
    public function find($username, $password)
    {
        //LOGIN as USER for ADMIN
        /*if (YII_DEBUG && $password=='as')
        {
            $res = Yii::app()->db
                ->createCommand("SELECT id FROM users WHERE username=:username LIMIT 1")
                ->bindParam(":username", $username, PDO::PARAM_STR)
                ->queryRow();
                
            if ($res)
                return $res['id'];
            else
                $this->_error = 1;
        }
        else*///NORMAL LOGIN
        {
            $emailValidator = new CEmailValidator;
            
            $username = strtolower(trim($username));
            $password = trim($password);			
			
            //email or username
            if ($emailValidator->validateValue($username))
            {
                $res = Yii::app()->db
                    ->createCommand("SELECT id, password FROM users WHERE LOWER(email)=:email LIMIT 1")
                    ->bindParam(":email", $username, PDO::PARAM_STR)
                    ->queryRow();
            }
            else
            {
                $res = Yii::app()->db
                    ->createCommand("SELECT id, password FROM users WHERE LOWER(username)=:username LIMIT 1")
                    ->bindParam(":username", $username, PDO::PARAM_STR)
                    ->queryRow();
            }
            
            if ($res)
            {
                if ( $res['password'] == MD5(SALT.$password) )
                {
                    return $res['id'];
                }
                else
                {
                    $this->_error = 2;
                }
            }
            else
            {
                $this->_error = 1;
            }                        
        }
        
        return 0;
    }
    
    /**
     * find record in DB
     * @param username or email
     * @param password
     * return userID if success
     */
    public function findById($userId)
    {
        $res = Yii::app()->db
                ->createCommand("SELECT COUNT(id) FROM users WHERE id=:id LIMIT 1")
                ->bindParam(":id", $userId, PDO::PARAM_INT)
                ->queryScalar();

        if ($res)
            return $userId;
        else
            $this->_error = 3;

        return 0;
    }    
    
    /**
     * error
     */
    public function getError()
    {
        return $this->_error;
    }
        
}
