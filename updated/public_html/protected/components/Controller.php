<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='//layouts/column1';   //     '//'... - ��� ���������          '/'... - ��� � ������
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu=array();
	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs=array();

    public $userProfile = null;

	

    
    public function init()
    {
//if (DEBUG_IP) FB::error(date('Y-m-d H:i:s'));
    	
    	//SKIPING FOR PAYMENT API CONTROLLER 
    	$uri = strtolower($_SERVER['REQUEST_URI']);//strtolower(Yii::app()->request->requestUri);
    	if (stristr($uri, '/paymentapi/')) {//	ERROR FOR ZOMBAIO !!!!!!!:	if (preg_match("/^(\/paymentapi)$/", $uri))
    		if (!defined('ADMIN')) define('ADMIN',false);
			return;
		}
		
    	if ( ADMIN_AREA || (isset($_SERVER['HTTP_REFERER']) && stristr($_SERVER['HTTP_REFERER'], '/admin')) )
		    Yii::app()->session['ADMIN']=true;    		
		
		if (!defined('ADMIN'))
			define('ADMIN', (isset(Yii::app()->session['ADMIN']) && Yii::app()->session['ADMIN']) );
		FB::warn(ADMIN, 'ADMIN');

		
    	if (stristr($uri, '/site/autologin'))
    		return;		
    		
    		
    	if (stristr($uri, 'checkuser.php') || stristr($uri, 'checkaff.php'))//	/aff/checkuser
    		return;    	

    	if (stristr($uri, '/api/signup') || stristr($uri, '/api/keytest'))//	ERROR FOR ZOMBAIO !!!!!!!:	if (preg_match("/^(\/paymentapi)$/", $uri))
    		return;    		

		//do not use if (ADMIN) return;    		

if (stristr($uri, '/api/index'))
return;            
        
        if (SITE_UNDER_UPDATE)
        {
            //FB::info(Yii::app()->request->requestUri);
            if (Yii::app()->request->requestUri!='/site/underupdate')
            {
                $this->redirect(Yii::app()->createAbsoluteUrl('site/underupdate'));
            }
        }
//FB::info("FirePHP enabled");
//        FB::info(Yii::app()->user);
        //FB::info(Yii::app()->location->getGeoIPRecord('93.175.196.6'));
        //FB::info(Yii::app()->location->getGeoIPRecord('46.137.182.111'));

//FB::info(Yii::app()->controller->getId(), '****************** controller->getId()');
//FB::info(Yii::app()->controller->action->id, '****************** controller->controller->action->id');
//FB::info(Yii::app()->getRequest()->requestUri, '****************** getRequest()->requestUri');
        
        
        $controller = strtolower(Yii::app()->controller->getId());
        
        
        //blocking for some countries
        $blockedCountries = array('PH','PK');
        $country = "";
        if (isset($_SERVER['GEOIP_COUNTRY_CODE']))
        	$country = $_SERVER['GEOIP_COUNTRY_CODE'];
        if (!$country)//site/error
        {
        	$locationRecord = Yii::app()->location->getGeoIPRecord();
        	$country = $locationRecord['GEOIP_COUNTRY_CODE'];
        }
        if ( in_array($country, $blockedCountries) && !stristr($uri, '/site/errors') )
        {
        	Yii::app()->user->setFlash('errorCustom','We are sorry, but your country is not supported.');
        	$this->redirect('/site/errors');
        }
        	
        
        
        
        if ($controller=='img')
            return;
        
		if ($controller=='test')
            return;
            

		//CAMS
		if ( (defined("CAMS") && CAMS) || (defined("CAM_DUMMY") && CAM_DUMMY) )
		{
			if ($controller!='landingcams' && $controller!='ajax' && ($controller=='site' && !stristr($uri, '/site/errors') && !stristr($uri, '/site/logout')) )
				throw new CHttpException(404, 'Page not found.');
			else
				return;
		}
            
            
            
        //INIT not included into /config/main.php
        $this->initComponents();
        
        
        
        //REFERER URL
        if (Yii::app()->user->isGuest && !isset(Yii::app()->session['ref_url']) && isset($_SERVER['HTTP_REFERER']) )
        {
        	Yii::app()->session['ref_url'] = $_SERVER['HTTP_REFERER'];
        }
        
        if (
        		/*Yii::app()->user->id 
        		&& */
        		(
        			Yii::app()->user->Profile->GetDataValue('role')=='deleted'
        			||
        			Yii::app()->user->Profile->GetDataValue('role')=='banned'
        		)
        )
        {
        	Yii::app()->user->Logout();
        	$this->redirect('/');
        }
		
        
        //BANNED IPs
        $ip=CHelperLocation::getIPReal();
        if (in_array($ip, array('108.41.178.36')))
        {
        	throw new CHttpException(403, 'Your IP has been banned');
        }        
        
/*
2013-02-09
        //allin1
        if (Yii::app()->user->Profile->GetDataValue('role')=='free' && Yii::app()->user->Profile->GetDataValue('form')=='93')//if (Yii::app()->user->Profile->GetDataValue('role')=='free93')
        	if 
        	(
            	$controller != 'allin1'  
                && 
                $controller != 'ajax'
                &&
				$controller != 'payment'
				&&
				!stristr($uri, '/site/page/contact')
				&&
				!stristr($uri, '/site/page/terms')
				&&
				!stristr($uri, '/site/page/privacy')
				&&
				!stristr($uri, '/site/page/help')
				&&
				!stristr($uri, '/site/page/recordkeeping')
        	)
        		$this->redirect('/allin1/step2');
*/
        
/* 
        if (Yii::app()->user->Profile->getSettingsValue('email_bounced')=='1')
        	if 
        	(
            	$controller != 'ajax'
            	&&
        		!stristr($uri, '/confirmemail')            	
            	&&
        		!stristr($uri, 'profile/confirmEmail')
            	&&
        		!stristr($uri, '/profile/verifyemail')
            	&&
            	!stristr($uri, '/profile/resendverify')
            	&&
        		!stristr($uri, 'site/logout')            	
//&&                
//!stristr($request, '/EMAIL_CHANGE_URL')//change after email changing  form will be ready
        	)
        		$this->redirect('/profile/verifyemail');        		
*/        		

        if ( !Yii::app()->user->checkAccess('free') )
        	if 
        	(
				$controller != 'site' 
					&&
					$controller != 'landing' 
					&&
					$controller != 'landing2' 
					&&
					$controller != 'viewsingle' 
					&&
				$controller != 'aff' 
				&&
				$controller != 'userregistration' 
				&&
				$controller != 'dashboard'//home page
				&& 
                $controller != 'ajax'
                &&
                $controller != 'allin1'
                &&
                $controller != 'allin9'                
                &&        			
                !stristr($uri, '/user/remindpassword')
        	)
        		$this->redirect('/');	
        
/*
 * oleg, ONL, 2012-06-26, uncomment and fix later... !!!!!!!!!!!
        if (Yii::app()->user->role=='justjoined' )
        {
            if (
                    $request != '/'  
            		&&
            		
            		$request != '/site/registrationStep2'  
                    && 
                    Yii::app()->controller->getId() != 'ajax'
                    &&
                    $request != '/UserRegistration/Step2_FormLoad'// '/UserRegistration/Step2'
                    &&
                    $request != '/site/login'
                    &&
                    $request != '/site/logout'  
                    &&
                    $request != '/site/terms'
                    &&
                    $request != '/site/privacy'
                    &&
                    $request != '/site/about'
                    &&
                    $request != '/help'
                    
                    &&
                    !stristr($request, '/img/')
               )
               $this->redirect(Yii::app()->createAbsoluteUrl('/'));//'site/registrationStep2'
        }
*/
        if (Yii::app()->user->id) {
            $this->userProfile = new Profile(Yii::app()->user->id);
        }

        if (YII_DEBUG)
        {
            $end_time = microtime();// ������ �� ��, ��� � � start.php, ������ ���������� ������ ����������
            $end_array = explode(" ",$end_time);
            $end_time = $end_array[1] + $end_array[0];
            $time = $end_time - TIME_START;// �������� �� ��������� ������� ���������
            FB::info(sprintf(" %.3f sec.",$time), 'TIME_INIT');            
        }
    }
    
    
    function initComponents()
    {
        /*
        
        returned to main.php
        
        //init EAUTH
        Yii::app()->setComponents(
            array(
                'eauth'=>array(
                    'class' => 'ext.eauth.EAuth',
                    'popup' => false, // Use the popup window instead of redirecting.
                    'services' => array( // You can change the providers and their classes.
                        //'google' => array(
                        //    'class' => 'GoogleOpenIDService',
                        //),
                        
                        'google' => array(//'google_oauth' => array(
                            'class' => 'GoogleOAuthService',
                            'client_id' => '894902651135.apps.googleusercontent.com',
                            'client_secret' => 'FZEMnhpsr4A1stlWE5oRmDLo',
                            'scope'=>'https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email https://gdata.youtube.com',
                            'use'=>true,
                        ),
                        
                        //'yandex' => array(
                        //    'class' => 'YandexOpenIDService',
                        //),
                        //'twitter' => array(
                        //    'class' => 'TwitterOAuthService',
                        //    'key' => '...',
                        //    'secret' => '...',
                        //),
                        'facebook' => array(
                            'class' => 'FacebookOAuthService',
                            'client_id' => FB_APPID,
                            'client_secret' => FB_SECRET,
                            'scope' => "user_birthday,email",
                            'use'=>(FB_APPID) ? true : false,
                        ),
                        //'vkontakte' => array(
                        //    'class' => 'VKontakteOAuthService',
                        //    'client_id' => '...',
                        //    'client_secret' => '...',
                        //),
                        //'mailru' => array(
                        //    'class' => 'MailruOAuthService',
                        //    'client_id' => '...',
                        //    'client_secret' => '...',
                        //),
                    ),
                ),
            )
        );      */  
    }
    
}