<?php

return CMap::mergeArray(
    // ����������� �� main.php
    require(dirname(__FILE__).'/main.php'),
    array(
		'components'=>array(
            'db'=>array(
    			'connectionString' => 'mysql:host=localhost;dbname='.DB_NAME,
    			'emulatePrepare' => true,
    			'username' => DB_USER,
    			'password' => DB_PASS,
    			'charset' => 'utf8',
    			//'initSQLs'=>array("set time_zone='-05:00';"),//'initSQLs'=>array("SET GLOBAL time_zone='+00:00'; set time_zone='+00:00';"),
    		),
            'dbSTATS' => array(
                'connectionString' => 'mysql:host=localhost;dbname='.DB_NAME_STATS,
                'username'         => DB_USER_STATS,
                'password'         => DB_PASS_STATS,
                'class'            => 'CDbConnection',          // DO NOT FORGET THIS!
    			'charset' => 'utf8',
                //'initSQLs'=>array("set time_zone='-05:00';"), //'initSQLs'=>array("SET GLOBAL time_zone='+00:00'; set time_zone='+00:00';"),   		
            ),
            'dbGEO' => array(
                'connectionString' => 'mysql:host=localhost;dbname='.DB_NAME_GEO,
                'username'         => DB_USER_GEO,
                'password'         => DB_PASS_GEO,
                'class'            => 'CDbConnection',          // DO NOT FORGET THIS!
    			'charset' => 'utf8',
                //'initSQLs'=>array("set time_zone='-05:00';"), //'initSQLs'=>array("SET GLOBAL time_zone='+00:00'; set time_zone='+00:00';"),   		
            ),             
            'dbquizz' => array(
                'connectionString' => 'mysql:host=localhost;dbname='.DB_NAME_QUIZZ,
                'username'         => DB_USER_QUIZZ,
                'password'         => DB_PASS_QUIZZ,
                'class'            => 'CDbConnection',
    			'charset' => 'utf8',		
            ),    		

            
            'log'=>array(
    			'class'=>'CLogRouter',
    			'routes'=>array(
    				array(
    					'class'=>'CFileLogRoute',
    					'levels'=>'error, warning',
    				),
    				// uncomment the following to show log messages on web pages
    				/*
    				array(
    					'class'=>'CWebLogRoute',
                        //'levels'=>'error, warning, info',
    				),*/
    				
    			),
    		),
        ),
    )
);
