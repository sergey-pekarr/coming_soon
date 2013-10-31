<?php

class WebAdmin extends CWebUser 
{
    //private $_model = null;
    
    private $data = array(
    	'id'=>0,
    	'username'=>'',
    	'password'=>'',
    	'role'=>'guest',
    );
    
    
	/**
	 * Initializes the application component.
	 * This method overrides the parent implementation by starting session,
	 * performing cookie-based authentication if enabled, and updating the flash variables.
	 */
	public function init()
	{
		parent::init();
		
		$this->data = CHelperUser::adminsGetAdminData($this->id);
        
        FB::info($this,'admin INIT');
        FB::info($this->data,'ADMIN DATA');
        /*$this->data('id');
        FB::info($this,'user');*/
	}
        
    public function getDataValue($key) 
    {
        return $this->data[$key];    	
    }	
	
    public function getRole() 
    {
        /*if ($this->id)
        {
            $role = 'administrator';
        }
        else
        {
            $role = 'guest';
        }
        return $role;*/

		if ($this->id)
        {
            $role = $this->data['role'];
        }
        else
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
    
    

	/*
	 * check if controller/action allowed for current user
	 * 
	 * return 0,1
	 */
	public function isAllowed($controller, $action)
	{
		$allowed = false;
		
		$mkey = "admin_roleAllowed_".$controller."_".$action;
		$role = Yii::app()->cache->get( $mkey );
		
		if ($role===false)
		{
			$sql = "SELECT role FROM admins_menu WHERE controller=:controller AND action=:action LIMIT 1";
		    $role = Yii::app()->db->createCommand($sql)
				        	->bindValue(":controller", $controller, PDO::PARAM_STR)
				        	->bindValue(":action", $action, PDO::PARAM_STR)
				        	->queryScalar();
//FB::error($role, "ROLE DB");
			$role = ($role) ? $role : 'superadmin';
//FB::error($role, "ROLE");
//FB::error($this->role, "ROLE USER");

			Yii::app()->cache->set($mkey, $role, 300);
		}

		//$allowed = Yii::app()->controller->module->admin->checkAccess($role);//
		$allowed = Yii::app()->controller->module->adminAuthManager->checkAccess($role, $this->data['id']/*Yii::app()->controller->module->admin->id, false*/);
		//$allowed = $this->checkAccess($role);
		
		
//FB::warn($allowed);		
		return $allowed;
	}



	/*public function checkAccess($operation,$params=array(),$allowCaching=true)
	{
FB::error(Yii::app()->controller->module->admin->id, "Yii::app()->controller->module->admin->id");		
FB::error(Yii::app()->controller->module->admin->role, "Yii::app()->controller->module->admin->role");

		$adminAuthManager = new PhpAdminAuthManager();
		//return Yii::app()->getAdminAuthManager()->checkAccess($operation,$this->getId(),$params);
		return $adminAuthManager->checkAccess($operation,Yii::app()->controller->module->admin->id,$params);
	}*/
    
}
