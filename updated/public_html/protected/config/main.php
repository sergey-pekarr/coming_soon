<?php

if (ADMIN_AREA)
{
	$session = array (
		    'class' => 'AdminDbHttpSession',//'system.web.CDbHttpSession',
		    'connectionID' => 'db',
		    'sessionTableName' => 'session_admin',			
			'cookieParams' => array('domain' => '.'.DOMAIN),
			'sessionName' => (LIVE) ? 'sa' : 'sa_dev',//SESSION ADMIN	
	);
}
else
{
	$session = array (
		    'class' => 'DbHttpSession',//'system.web.CDbHttpSession',
		    'connectionID' => 'db',
		    'sessionTableName' => 'session',			
			'cookieParams' => array(
				'domain' => '.'.DOMAIN_COOKIE,
				'lifetime' => 3600,//!!!
			),//'cookieParams' => array('domain' => '.'.DOMAIN),
			'sessionName' => (LIVE) ? 'PHPSESSID' : 'PHPSESSID_DEV',//SESSION ADMIN
			'timeout' => 3600,//!!!
	);
}



// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
    'name'=>SITE_NAME,

	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.models.quizz.*',
		'application.components.*',
        'application.extensions.FB.*',
        'application.extensions.mail.*',///'application.extensions.mail.YiiMailMessage',
        
'application.extensions.facebook.*',
'application.extensions.facebook.lib.*',

        //camblr
        'ext.eoauth.*',
        'ext.eoauth.lib.*',
        'ext.lightopenid.*',
        'ext.eauth.services.*',
	),

	'modules'=>array(
		/*'admin'=>array(
        	'components'=>array(
                'admin'=>array(
                    'class' => 'WebAdmin',    
        			// enable cookie-based authentication
        			'allowAutoLogin'=>false,
                    'loginUrl' => array('/admin/home/login'),
                ),
                'authManager' => array(//http://yiiframework.ru/doc/cookbook/ru/access.rbac.file
                    'class' => 'PhpAdminAuthManager',
                    'defaultRoles' => array('guest'),
                ),
            ),            
        ),*/      
        'admin'=>array(
        	'components'=>array(
                'admin'=>array(
                    'class' => 'WebAdmin',    
        			// enable cookie-based authentication
        			'allowAutoLogin'=>false,
                    'loginUrl' => array('/admin/home/login'),
                ),
                'adminAuthManager' => array(//http://yiiframework.ru/doc/cookbook/ru/access.rbac.file
                    'class' => 'PhpAdminAuthManager',
                    'defaultRoles' => array('guest'),
                ),
            ),            
        ), 	  
	),

	// application components
	'components'=>array(
		'user'=>array(
            'class' => 'WebUser',    
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
			'autoRenewCookie' => true,
			'identityCookie' => array('domain' => '.'.DOMAIN_COOKIE),//'identityCookie' => array('domain' => '.'.DOMAIN),
		),
        'authManager' => array(//http://yiiframework.ru/doc/cookbook/ru/access.rbac.file
            // ����� ������������ ���� �������� �����������
            'class' => 'PhpAuthManager',
            // ���� �� ���������. ���, ��� �� ������, ���������� � ����� � �����.
            'defaultRoles' => array('guest'),
        ),
        
		// uncomment the following to enable URLs in path-format		
		'urlManager'=>array(
			'urlFormat'=>'path',
            'showScriptName'=>false,
			'rules'=>array(
                '<module:(admin)>'=>'admin/home/index',
                '<module:(admin)>/<controller:\w+>/<action:\w+>'=>'admin/<controller>/<action>',
				//'<module:(admin)>/<action:\w+>'=>'admin/default/<action>',
				
                
                '/' => ( CAMS || CAM_DUMMY ) ? 'landingcams/index' : 'dashboard/index',//'/datingCenter' => 'site/index',
                '/join' => ( CAMS || CAM_DUMMY ) ? '/landingcams' : 'dashboard/index',
                '<controller:(join)>/<action:\w+>'=>'landingcams/<action>',
				
        
                '/online' => 'dashboard/online',
        		'confirmEmail' => 'profile/confirmEmail',
        		
        		'checkuser.php' => 'aff/checkuser',
        		'checkaff.php' => 'aff/checkaff',
        
        
                '<controller:(site)>/<action:(login|fblogin|logout|Registration|registration|Signin|signin|registrationStep2|facebooklogin|snoopy|support|errors)>'=>'<controller>/<action>',
				'<controller:(site)>/<action:(autologin)>/<idd:\w{32}>/<hash:\w+>'=>'<controller>/<action>',
				
                '<controller:(site)>/<view:\w+>'=>'<controller>/page',            
                
				'<controller:(aff)>/<affid:\d+>' => '<controller>/index',        
        
				'<controller:(profile)>/<id:\w{32}>'=>'<controller>/index',

				'<controller:(payment)>/<id:\w{37}>'=>'<controller>/index',
				'<controller:(payment)>/<id:\w{5}>'=>'<controller>/index',
				
				
				'<controller:(thread)>/<id:\w{32}>'=>'<controller>/index',
				'<controller:(thread)>/<id:\w{32}>/<msgid:\d{1,10}>'=>'<controller>/index',
				'<controller:(thread)>/<action:\w+>/<id:\w{32}>'=>'<controller>/<action>',
				'<controller:(msg)>/<action:\w+>/<id:\w{32}>/<msgid:\d{1,10}>'=>'<controller>/<action>',
				
				//Quizz
				'<controller:(quizz|quizztest|quizzchart)>/<action:\w+>/<id:\d{1,10}>'=>'<controller>/<action>',
								
				'<controller:(remindpassword|changepassword|unsubscribe)>'=>'user/<controller>',
				'<controller:(changepassword|unsubscribe|confirmemail)>/<idd:(\w|\=|\+|\-)+>/<hash:\w+>'=>'user/<controller>',
				
				'<controller:(winks|view)>/<action:(sent)>' => 'activity/<controller><action>',
				'<controller:(winks|view|favourite|photorequest|like|myfavourite|blacklisted)>' => 'activity/<controller>',
				
                
				'<controller:(img)>/<action:(upload)>'=>'<controller>/<action>',
				'<controller:(img)>/<action:(uploadprofile|uploadtest|uploadfansign)>'=>'<controller>/<action>',
                '<controller:(img)>/<action:(del|setprimary)>/<n:\d+>'=>'<controller>/<action>',
                 
				//Quizz & FanSign
				'<controller:(img)>/<action:(test|fansign)>/<id:\w+>/<name:.+>' => '<controller>/<action>',  
				'<controller:(img)>/<action:(fansign)>/<id:\w+>' => '<controller>/<action>',  
				             
                '<controller:(img)>/<id:\w+>/<size:\w+>/<n:\d+>.jpg' => '<controller>/index',                
                '<controller:(img)>/<id:\w+>/<size:\w+>' => '<controller>/index',
				'<controller:(img)>/<id:\w+>' => '<controller>/index',
				
                
                '<controller:(messages)>/<action:(All|all)>/<id:\w{32}>'=>'<controller>/<action>', 
                
                '<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
				
				//n Aug 30: need to test more for all operations
				'<module:(admin)>/<controller:\w+>/<action:\w+>/<id:\w{32}>'=>'admin/<controller>/<action>',
				
				
			),
		),
		
		'session' => $session,
		/*array (
		    'class' => 'system.web.CDbHttpSession',
		    'connectionID' => 'db',
		    'sessionTableName' => 'session',			
			'cookieParams' => array('domain' => '.'.DOMAIN),
		),*/
		
        'cache'=> array(
	    	'class'=>'CMemCache',
	        'keyPrefix'=>CACHE_KEY,//'keyPrefix'=>DOMAIN,
	        'servers'=>array(
	        	array(
	            	'host'=>'localhost',
	                'port'=>11211,
				),
			),
	    ),
		
 		'mail' => array(
 			'class' => 'CMails',///'class' => 'application.extensions.mail.YiiMail',
 			'transportType' => 'smtp',
            'transportOptions' => array(
                'host' => 'localhost',
                'username' => '',
                'password' => '',
                'port'     => 25,                  
                //'encryption'=>'ssl',
            ),
 			'viewPath' => 'application.views.mail',
 			'logging' => false, //log
 			'dryRun' => false
 		),
		/*'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=xxx',
			'emulatePrepare' => true,
			'username' => 'root',
			'password' => 'ok1605',
			'charset' => 'utf8',
		),*/
        'clientScript' => array(
            'scriptMap' => array(
                'jquery.js' => false,
                'jquery.min.js' => false,
                'jquery-ui.js' => false,
                'jquery-ui.min.js' => false,
                'jquery.yiiactiveform.js'=>false,
            )
        ),
		'errorHandler'=>array(
			// use 'site/error' action to display errors
            'errorAction'=>'site/error',
        ),
		/*'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				//array(
					//'class'=>'CFileLogRoute',
					//'levels'=>'error, warning',
				//),
				// uncomment the following to show log messages on web pages
				
				array(
					'class'=>'CWebLogRoute',
                    //'levels'=>'error, warning, info',
				),
				
			),
		),*/
        /*'ih'=>array(
            'class'=>'CImageHandler',
        ),*/
        'secur'=>array(
            'class'=>'CSecur',
        ),
        'location'=>array(
            'class'=>'CLocation',
            'geoFilePath' => (LOCAL) ? '/usr/share/GeoIP/GeoIPCity.dat' : DIR_ROOT.'/../GeoIPCity.dat',
            'defaultLocationId'=>2545417,//Los Angeles, CA		2615337,//if not detected from GeoIP than default ID for NY city
            'testIP' => ( (LOCAL) ? '93.175.196.6' : '')
        ),
        
        /*'ffmpeg'=>array(
            'class'=>'Cffmpeg'
        ),*/
        
        'helperProfile'=>array(
            'class'=>'CHelperProfile'
        ),
        'helperDate'=>array(
            'class'=>'CHelperDate'
        ),         
        'helperCron'=>array(
            'class'=>'CHelperCron'
        ),
        'helperFile'=>array(
            'class'=>'CHelperFile'
        ),
        /*'facebook'=>array(
            'class'=>'CFacebook',            
        ),*/
        /*'metric'=>array(
            'class'=>'CMetric',            
        ),*/
        'browser' => array(
            'class' => 'application.extensions.browser.CBrowserComponent',
        ),



        //camblr
        'loid' => array(
            'class' => 'ext.lightopenid.loid',
        ),
        'eauth' => array(
            'class' => 'ext.eauth.EAuth',
            'popup' => false, // Use the popup window instead of redirecting.
            'services' => array( // You can change the providers and their classes.
                //'google' => array(
                //    'class' => 'GoogleOpenIDService',
                //),
                
                //'google' => array(//'google_oauth' => array(
                //    'class' => 'GoogleOAuthService',
                //    'client_id' => G_APPID,
                //    'client_secret' => G_SECRET,
                //    'scope'=>'https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email https://gdata.youtube.com&access_type=offline',
                //    'use'=>(G_APPID) ? true : false,
                //),
                
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
                    'use'=>true,
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

     
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
        'FirePHPEnabled'=>(CONSOLE) ? false : DEBUG_IP,// (!FACEBOOK) ? YII_DEBUG : false,//,//  false,//
        'site' => array(
            'nameShot' => SITE_NAME,
            'nameFull' => SITE_NAME_FULL,
        ),
        'adminEmail'=>'admin@'.DOMAIN,
        
        'cache'=>array(
            'profile'=>300,
            'lastActivity'=>300,
        ),
        'user'=>array(
            'rolesWhere'=>"role IN ('user', 'gold')",
            'isOnline' => 900,//last activity 900 seconds ago
        )
	),
);