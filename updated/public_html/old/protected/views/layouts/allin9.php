<?php
$compressJs  = (LOCAL) ? false : true;
$compressCss = (LOCAL) ? false : true;

$combineJs  = (LOCAL) ? false : true;
$combineCss = (LOCAL) ? false : true;

$app = Yii::app();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
	<meta name="language" content="en" />
    
    
    <?php 
    CHelperAssets::jsUrl('site', $compressJs, $combineJs); ?>
    
    <link rel="shortcut icon" href="favicon.ico" />

	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
	
    <?php CHelperAssets::cssUrl('site', $compressCss, $combineCss); ?>
	
    <?php
    
    $deviceType = null;
	
    if($deviceType == null){
    	if(isset($_GET['mobile'])) $deviceType = 'mobile';
    	else if(isset($_GET['ipad'])) $deviceType = 'tablet';
    	else if(isset($_GET['tablet'])) $deviceType = 'tablet';
    }
	
    if($deviceType == null && isset(Yii::app()->session['device-type'])){
    	$deviceType = Yii::app()->session['device-type'];
    }
	
    if($deviceType == null){
    	require_once dirname(__FILE__).'/../../vendors/Mobile_Detect.php';
    	$detector = new Mobile_Detect();
    	if($detector->isMobile()) $deviceType = 'mobile';
    	else if($detector->isTablet()) $deviceType = 'tablet';
    	else $deviceType = 'pc';
    }
    
    //require_once dirname(__FILE__).'/../../vendors/Mobile_Detect.php';
    //$detector = new Mobile_Detect();
    //$ok = $detector->isMobile();
	
	Yii::app()->session['device-type'] = $deviceType;
    
    switch($deviceType){
    	case 'mobile': 
    		CHelperAssets::cssUrl('allin9Mobile', $compressCss, $combineCss); 
    		break;
    	
    	case 'tablet': 
    		CHelperAssets::cssUrl('allin9Tablet', $compressCss, $combineCss); 
    		break;
    	
    	default: 
    		CHelperAssets::cssUrl('allin9', $compressCss, $combineCss); 
    		break;			
    }
    ?>

	
	<?php $this->widget('application.components.userprofile.ScreenResolutionUpdateWidget'); ?>
	
	<?php $this->widget('application.components.metric.GoogleAnalyticsWidget'); ?>
	
	<?php $this->widget('application.components.panamus.FingerprintWidget'); ?>
</head>
<body>

	
<div id="wrapper-allin9">


    <noscript>
        <div class="alert-message error bold center">
            Javascript is disabled on your browser. Please enable JavaScript or upgrade to a Javascript-capable browser to use the site.
        </div>
    </noscript>



    <?php echo $content; ?>
	<div style="text-align: center; margin-top: 10px;">
		<?php if($deviceType == 'mobile' || $deviceType == 'tablet') { ?>
			<span>If your device is not mobile or tablet, </span><a href="allin9/normal">click here for normal signup</a>
		<?php } ?>
	</div>

</div>
<div class="clear"></div>







<?php $this->widget('application.components.common.ClientInfoUpdateWidget'); ?>

</body>
</html>


<?php
CHelperSite::showTimeDebug();
?>
    
