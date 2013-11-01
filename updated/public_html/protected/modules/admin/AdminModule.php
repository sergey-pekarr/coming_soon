<?php

class AdminModule extends CWebModule
{
	public function init()
	{
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
			'admin.models.*',
			'admin.components.*',
		));
		
		if (!defined('ADMIN'))
			define('ADMIN', true);
	}

	public function beforeControllerAction($controller, $action)
	{
		if(parent::beforeControllerAction($controller, $action))
		{
			// this method is called before any module controller action is performed
			// you may place customized code here
            
            $adminModule = Yii::app()->controller->module->admin;
			
            //SSL for admin
			//if ( defined("SITE_URL_SSL") && !CHelperSite::isSSL() )
			//	Yii::app()->controller->redirect( SITE_URL_SSL.$_SERVER['REQUEST_URI'] );            
            
			if ($adminModule->isGuest && !stristr(Yii::app()->getRequest()->requestUri, $adminModule->loginUrl[0])){
				$controller = Yii::app()->controller;
				
				if (!stristr(Yii::app()->getRequest()->requestUri, $adminModule->loginUrl[0]))
					$controller->redirect($controller->createUrl( $adminModule->loginUrl[0].'?redirect='.urlencode(Yii::app()->getRequest()->requestUri) ));
				else
					$controller->redirect($controller->createUrl( $adminModule->loginUrl[0] ));
			}
            
			return true;
		}
		else
			return false;
	}
    
    
    /*public function isA()
    {
        return Yii::app()->controller->module->admin->isGuest;
    }*/
    
}
