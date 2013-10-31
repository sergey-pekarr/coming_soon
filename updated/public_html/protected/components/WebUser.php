<?php

class WebUser extends CWebUser 
{
    private $_model = null;
    
    public $Profile;
    //public $Metric;
    
    //public $data = array();
    
	/**
	 * Initializes the application component.
	 * This method overrides the parent implementation by starting session,
	 * performing cookie-based authentication if enabled, and updating the flash variables.
	 */
	public function init()
	{
		parent::init();
        
        $this->Profile = new Profile($this->id);
        
        //$this->Metric = new Metric($this->id);
        
        if (!ADMIN)
        {
            $this->Profile->setActivity();
            
            
            //$this->Profile->simulateApproved();        
            
            //$this->Profile->getData();//!
            
            FB::info($this,'user');
            /*$this->data('id');
            FB::info($this,'user');*/            
        }
	}
    
	public function login($identity,$duration=0)
	{
		$id=$identity->getId();
		$states=$identity->getPersistentStates();
		if($this->beforeLogin($id,$states,false))
		{
			$this->changeIdentity($id,$identity->getName(),$states);
			if($duration>0)
			{
				if($this->allowAutoLogin)
					$this->saveToCookie($duration);
				else
					throw new CException(Yii::t('yii','{class}.allowAutoLogin must be set true in order to use cookie-based authentication.',
						array('{class}'=>get_class($this))));
			}
			$this->afterLogin(false);
		}
	}
	public function logout($destroySession=true)
	{
		if($this->beforeLogout())
		{
			if($this->allowAutoLogin)
			{
				Yii::app()->getRequest()->getCookies()->remove($this->getStateKeyPrefix());
				if($this->identityCookie!==null)
				{
					$cookie=$this->createIdentityCookie($this->getStateKeyPrefix());
					$cookie->value=null;
					$cookie->expire=0;
					Yii::app()->getRequest()->getCookies()->add($cookie->name,$cookie);
				}
			}
			if($destroySession)
				Yii::app()->getSession()->destroy();
			else
				$this->clearStates();
			$this->afterLogout();
		}
	}    
    

	public function loginFB($identity,$duration=0)
	{
		//$id=$identity->getId();
$id = $this->Profile->loginFB2();
if (!$id)
    return;        
        
        
        $states=$identity->getPersistentStates();
		if($this->beforeLogin($id,$states,false))
		{
			$this->changeIdentity($id,$identity->getName(),$states);
			if($duration>0)
			{
				if($this->allowAutoLogin)
					$this->saveToCookie($duration);
				else
					throw new CException(Yii::t('yii','{class}.allowAutoLogin must be set true in order to use cookie-based authentication.',
						array('{class}'=>get_class($this))));
			}
			$this->afterLogin(false);
		}
	}


	protected function beforeLogin($id,$states,$fromCookie)
	{
	   parent::beforeLogin($id,$states,$fromCookie);

       $this->id = $id;
       $this->Profile = new Profile($this->id);
       return true;
	}
	protected function afterLogin($fromCookie)
	{
	    parent::afterLogin($fromCookie);
        $this->Profile->afterLogin();
	}

	public function data($key)
	{
		return $this->Profile->getDataValue($key);
	}    
	public function location($key)
	{
		$location = $this->Profile->getDataValue('location');
        return $location[$key];
	} 
	public function info($key)
	{
		$info = $this->Profile->getDataValue('info');
        return $info[$key];
	} 
	public function personal($key)
	{
		$personal = $this->Profile->getDataValue('personal');
        return $personal[$key];
	}

	public function settings($key)
	{
		$settings = $this->Profile->getDataValue('settings');
        return $settings[$key];
	}
        
    function getRole() 
    {
        if (!$role = $this->data('role'))
        {
            $role = 'guest';
        }
        return $role;
    }
    
    /*private function getModel()
    {
        if (!$this->isGuest && $this->_model === null){
            $this->_model = User::model()->findByPk($this->id, array('select' => 'role'));
        }
        return $this->_model;
    }*/

    
}
