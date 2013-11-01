<!DOCTYPE html>
<html lang="en" >
<head>
    <meta charset="utf-8" />
	<meta name="language" content="en" />
    
    <?php 
    $compressJs  = true;
    $compressCss = true;

    $combineJs  = true;
    $combineCss = true;
    
    CHelperAssets::jsUrl('site', $compressCss, $combineCss);//site JS
    if (!Yii::app()->controller->module->admin->isGuest)
    	CHelperAssets::jsUrl('admin', $compressCss, $combineCss);//admin JS
    
    ?>

    <link rel="shortcut icon" href="<?php echo Yii::app()->request->baseUrl; ?>/favicon.ico" type="image/x-icon" />
    
	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
    
	<?php 
	    if ( Yii::app()->browser->isIE10() ) {
			CHelperAssets::jsUrl('ie10', $compressJs, $combineJs);
		}
	?>    
    
<?php 


//Yii::app()->clientScript->registerCssFile( Yii::app()->assetManager->publish(YII_PATH.'/web/js/source/autocomplete/jquery.autocomplete.css') );
//Yii::app()->assetManager->publish(YII_PATH.'/web/js/source/autocomplete/indicator.gif');


/*Yii::app()->clientScript->registerCssFile(
    Yii::app()->assetManager->publish( dirname(__FILE__).'/../../../../../css/all.css', true )
);

Yii::app()->clientScript->registerCssFile(
    Yii::app()->assetManager->publish( dirname(__FILE__).'/../../css/admin.css', true )
);
Yii::app()->clientScript->registerCssFile(
    Yii::app()->assetManager->publish( dirname(__FILE__).'/../../css/adminform.css', true )
);*/


?>
<?php 
CHelperAssets::cssUrl('site', $compressCss, $combineCss);
if (!Yii::app()->controller->module->admin->isGuest)
	CHelperAssets::cssUrl('admin', $compressCss, $combineCss); 
?>

    
</head>

<body style="background-color: #EFEFEF">

<div id="page">
    
<?php 

//MENU
$adminModule = Yii::app()->controller->module->admin;
if ( !$adminModule->isGuest )
{
	//ADMINs menu
	//if ( Yii::app()->controller->module->adminAuthManager->checkAccess('administrator', Yii::app()->controller->module->admin->id) )
	{
		$this->widget('application.extensions.mbmenu.MbMenu',array(
			'items'=>array(
						
						array('label'=>'Home', 'url'=>array('/admin/home/index'),
							'items'=>array(
								array('label'=>'Site Home', 'url'=>SITE_URL/*array('/files/index')*/, 'linkOptions'=>array('target'=>'_blank')),
								array('label'=>''),
								array('label'=>'LogOut', 'url'=>array('/admin/home/logout')),
								),
							),

						array('label'=>'Users', '',
							'items'=>array(
								array(
									'label'=>'Users', 
									'url'=>array('/admin/users/index'),
									'visible'=>Yii::app()->controller->module->admin->isAllowed('users', 'index'),
									),

								array(
									'label'=>'Find', 
									'url'=>array('/admin/users/find'),
									'visible'=>Yii::app()->controller->module->admin->isAllowed('users', 'find'),
									),                            
								
								array(
									'label'=>'Approve images', 
									'url'=>array('/admin/users/approveImage'),
									'visible'=>Yii::app()->controller->module->admin->isAllowed('users', 'approveImage'),
									),
								array(
									'label'=>'xRate images', 
									'url'=>array('/admin/users/xrateImage'),
									'visible'=>Yii::app()->controller->module->admin->isAllowed('users', 'xrateImage'),
								),
								//                            array('label'=>''),
								//                            array('label'=>'Last activity', 'url'=>array('/admin/metric/index')),
								),
							),
						
						array('label'=>'Payments', '',
							'items'=>array(
								array(
									'label'=>"Today's Gold users", 
									'url'=>array('/admin/payments/todayGold'),
									'visible'=>Yii::app()->controller->module->admin->isAllowed('payments', 'todayGold'),
									),
								
								array(
									'label'=>'Billing Controll', 
									'url'=>array('/admin/payments/billingControll'),
									'visible'=>Yii::app()->controller->module->admin->isAllowed('payments', 'billingControll'),
									),

								array(
									'label'=>'Transactions', 
									'url'=>array('/admin/payments/TransactionsReport'),
									/*'items'=>array(
										array(
											'label'=>'Netbillig_2', 
											'url'=>array('/admin/payments/transactionsReport?PaymentTransactionsForm[paymod]=netbilling_2'),
											'visible'=>Yii::app()->controller->module->admin->isAllowed('payments', 'transactionsreport'),
											),
										),*/
									'visible'=>Yii::app()->controller->module->admin->isAllowed('payments', 'transactionsreport'),
									),                           	
								
								array('label'=>''),
								
								array(
									'label'=>'Reversals', '',
									'items'=>array(
										array(
											'label'=>'Reversal Form', 
											'url'=>array('/admin/payments/reversalForm'),
											'visible'=>Yii::app()->controller->module->admin->isAllowed('payments', 'reversalForm'),
											),
										array(
											'label'=>'Reversal Report', 
											'url'=>array('/admin/payments/reversalReport'),
											'visible'=>Yii::app()->controller->module->admin->isAllowed('payments', 'reversalReport'),
											),   
										array(
											'label'=>'Weekly/monthly Reversal Report', 
											'url'=>array('/admin/payments/RfdNChbkReport'),
											'visible'=>Yii::app()->controller->module->admin->isAllowed('payments', 'RfdNChbkReport'),
											),                         		
										),
									),
								
								array('label'=>'Unresolve Risks', 'url'=>array('/admin/risk'), 
									'visible'=>Yii::app()->controller->module->admin->isAllowed('risk', 'index'),
								),
								
								//                            array('label'=>''),
								),
							'visible'=>Yii::app()->controller->module->admin->isAllowed('payments', 'reversalForm'),
							),                    

						/*array('label'=>'Aff / Stats', '',
							'items'=>array(
								array(
									'label'=>'New Managers', 
									'url'=>array('/admin/aff/newManagers'),
									'visible'=>Yii::app()->controller->module->admin->isAllowed('aff', 'newManagers'),
									),
								array('label'=>''),
								array(
									'label'=>'All stats', 
									'url'=>array('/admin/statsAll/index'),
									'visible'=>Yii::app()->controller->module->admin->isAllowed('statsAll', 'index'),
									),                        	
								),
							'visible'=>Yii::app()->controller->module->admin->isAllowed('aff', 'newManagers'),
							
							), */                   
						
						array('label'=>'Logs', '',
							'items'=>array(
								array(
									'label'=>'Emails', 
									'url'=>array('/admin/logs/emails'),
									'visible'=>Yii::app()->controller->module->admin->isAllowed('logs', 'emails'),
									),
								array(
									'label'=>'Api', 
									'url'=>array('/admin/logs/ApiOutLog'),
									'visible'=>Yii::app()->controller->module->admin->isAllowed('logs', 'apioutlog'),
									),                      	
								),
								'visible'=>Yii::app()->controller->module->admin->isAllowed('logs', 'emails'),							
							),

						
						array(
							'label'=>'Flirt', 
							'url'=>array(''), 
							'visible'=>Yii::app()->controller->module->admin->isAllowed('autoflirt', 'index'),
				            'items'=>array(
				                   array(
					                   	'label'=>'Manual Flirt', 
										'url'=>array('/admin/autoflirt/manual'),
										'visible'=>Yii::app()->controller->module->admin->isAllowed('autoflirt', 'index'),
									),
									
									array('label'=>''),
									
									array(
					                   	'label'=>'Auto Flirt Config', 
										'url'=>array('/admin/autoflirt'),
										'visible'=>Yii::app()->controller->module->admin->isAllowed('autoflirt', 'index'),
									),
							),
						),
						/*array('label'=>'Fan girl', 'url'=>array('/admin/fan'), 'visible'=>Yii::app()->controller->module->admin->isAllowed('fan', 'index'),
							'items'=>array(
								array(
									'label'=>'Recent Request', 
									'url'=>array('/admin/fan'),
									'visible'=>Yii::app()->controller->module->admin->isAllowed('fan', 'index'),
									),
								array(
									'label'=>'All Fangirls', 
									'url'=>array('/admin/fan/all'),
									'visible'=>Yii::app()->controller->module->admin->isAllowed('fan', 'index'),
									),
								array(
									'label'=>'Setting', 
									'url'=>array('/admin/fan/setting'),
									'visible'=>Yii::app()->controller->module->admin->isAllowed('fan', 'setting'),
									),
								array(
									'label'=>'Report', 
									'url'=>array('/admin/fan/report'),
									'visible'=>Yii::app()->controller->module->admin->isAllowed('fan', 'report'),
									),
								array(
									'label'=>'Payout', 
									'url'=>array('/admin/fan/payout'),
									'visible'=>Yii::app()->controller->module->admin->isAllowed('fan', 'payout'),
									),
								),
							),*/
					
					

					array('label'=>'System', '',
							'items'=>array(
									array(
											'label'=>'Info',
											'url'=>array('/admin/system/info'),
											'visible'=>Yii::app()->controller->module->admin->isAllowed('system', 'info'),
									),
									//array('label'=>'Clear cache', 'url'=>array('/admin/system/clearcache')),
									array(
											'label'=>'Clear cache',
											'url'=>'javascript:clearCache()',
											'visible'=>Yii::app()->controller->module->admin->isAllowed('ajax', 'clearCache'),
									),
							),
							'visible'=>Yii::app()->controller->module->admin->isAllowed('system', 'info'),
					),					
						)
					));    		
	}
	/*else //MANAGER MENU
	{

	   	$this->widget('application.extensions.mbmenu.MbMenu',array(
	           'items'=>array(
				
	               array('label'=>'Home', 'url'=>array('/admin/home/index'),
	                   'items'=>array(
	                       array('label'=>'Site Home', 'url'=>SITE_URL, 'linkOptions'=>array('target'=>'_blank')),
	                       array('label'=>'LogOut', 'url'=>array('/admin/home/logout')),
	                   ),
	               ),
	           )
	   	));        		
		
	}*/
	

}    
?> 
    
    <?php if ( Yii::app()->controller->module->admin->id ) { ?>
	<div class="topInfoBar">
    	<span class="welcomeUser" title="Role: <?php echo Yii::app()->controller->module->admin->getDataValue('role') ?>">
    	Welcome, <?php echo Yii::app()->controller->module->admin->getDataValue('name') ?>!
    	</span>
    	&nbsp;&nbsp;&nbsp;
    	<span class="dateTime" title="Server time"></span>
    	</div>
    	<?php } ?>
    
    <div id="content">
        <?php echo $content; ?>
    </div>
    
    <?php /* 
    <div id="footer">

    </div><!-- footer -->
    */ ?>
    
    
</div><!-- page -->


<div id="metricBox" style="text-align: center; width: 600px; margin-left: -300px; "  class="modal fade form hide">
    <div class="modal-header">
        <a href="javascript: void(0)" class="close" onclick="javascript: $('#metricBox').modal('hide');">&times;</a>
    </div>    
    <div class="modal-body">
        <div id="metricLog" style="overflow: scroll; text-align: left; height: 400px;"></div>
    </div>
</div>


<div id="modalBox" style="text-align: center; width: 800px; margin-left: -400px; "  class="modal fade form hide">
    <div class="modal-header">
        <a href="javascript: void(0)" class="close" onclick="javascript: $('#modalBox').modal('hide');">&times;</a>
        <h3></h3>
    </div>    
    <div class="modal-body"></div>
</div>


<script>
ts = <?php echo time() ?>;
</script>

<?php CHelperSite::showTimeDebug(); ?>
</body>
</html>