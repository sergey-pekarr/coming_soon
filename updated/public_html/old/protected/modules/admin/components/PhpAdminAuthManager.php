<?php

class PhpAdminAuthManager extends CPhpAuthManager{
    public function init(){
        if($this->authFile===null){
            $this->authFile=Yii::getPathOfAlias('application.modules.admin.config.auth').'.php';
        }
 //FB::warn(Yii::app()->controller->module->admin->role . ' - ' . Yii::app()->controller->module->admin->id, '---------------------*************--------------------');
        parent::init();
 		
        
        //not working...?
        if( !Yii::app()->controller->module->admin->isGuest ){
            $this->assign(Yii::app()->controller->module->admin->role, Yii::app()->controller->module->admin->id);
        }/**/
    }
    
    
    
    
    
    
    
    
    
    

	/**
	 * Performs access check for the specified user.
	 * @param string $itemName the name of the operation that need access check
	 * @param mixed $userId the user ID. This should can be either an integer and a string representing
	 * the unique identifier of a user. See {@link IWebUser::getId}.
	 * @param array $params name-value pairs that would be passed to biz rules associated
	 * with the tasks and roles assigned to the user.
	 * @return boolean whether the operations can be performed by the user.
	 */
	/*public function checkAccess($itemName,$userId,$params=array())
	{

		if(!isset($this->_items[$itemName]))
			return false;
		$item=$this->_items[$itemName];
		Yii::trace('Checking permission "'.$item->getName().'"','system.web.auth.CPhpAuthManager');
		if($this->executeBizRule($item->getBizRule(),$params,$item->getData()))
		{
			if(in_array($itemName,$this->defaultRoles))
				return true;
			if(isset($this->_assignments[$userId][$itemName]))
			{
				$assignment=$this->_assignments[$userId][$itemName];
				if($this->executeBizRule($assignment->getBizRule(),$params,$assignment->getData()))
					return true;
			}
			foreach($this->_children as $parentName=>$children)
			{
				if(isset($children[$itemName]) && $this->checkAccess($parentName,$userId,$params))
					return true;
			}
		}
		return false;
	}*/
}
