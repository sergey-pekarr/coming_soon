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
    CHelperAssets::jsUrl('site', $compressJs, $combineJs);
    CHelperAssets::jsUrl('cams', $compressJs, $combineJs); 
    ?>
    
    <link rel="shortcut icon" href="favicon_cams.ico" />

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
    		CHelperAssets::cssUrl('camsMobile', $compressCss, $combineCss); 
    		break;
    	
    	case 'tablet': 
    		CHelperAssets::cssUrl('camsTablet', $compressCss, $combineCss); 
    		break;
    	
    	default: 
    		CHelperAssets::cssUrl('cams', $compressCss, $combineCss); 
    		break;			
    }
    ?>

	
	<?php $this->widget('application.components.userprofile.ScreenResolutionUpdateWidget'); ?>
	
	<?php $this->widget('application.components.metric.GoogleAnalyticsWidget'); ?>
</head>
<body>


	
<div id="wrapper-landingcams">
	
	<div class="landingcams_header">
		<div class="landingcams_header_logo"></div>
	</div>

    <noscript>
        <div class="alert-message error bold center">
            Javascript is disabled on your browser. Please enable JavaScript or upgrade to a Javascript-capable browser to use the site.
        </div>
    </noscript>



    <?php echo $content; ?>
	<div style="text-align: center; margin-top: 10px;">
		<?php if($deviceType == 'mobile' || $deviceType == 'tablet') { ?>
			<span>If your device is not mobile or tablet, </span><a href="join/normal">click here for normal signup</a>
		<?php } ?>
	</div>

</div>
<div class="clear"></div>



<div class="landingCamsFooter">
	<div class="landingCamsFooter_box">
		<div class="left">
			<span>© 2003 – <?php echo date("Y") ?> meatycams.com. All rights reserved.</span>
			<br />
			<a target="_blank" href="http://www.meatycams.com/2257.html?">18 U.S.C. 2257 Record-Keeping Requirements Compliance Statement</a>
			<br />
			<a target="_blank" href="http://www.meatycams.com/terms.html?">Terms &amp; Conditions</a>
			<br />
			<a target="_blank" href="http://www.meatycams.com/privacy.html?">Privacy Policy</a>
			<br /><br />
			All models appearing on this website are 18 years or older.
			<br />
			<img width="88" src="/images/design/rta.gif" />
			<img width="88" src="/images/design/asacp.gif" />
			<br /><br />
		</div>
		
		<div class="right">
			<div class="footer-section">
	          <h3>Need assistance?</h3>
	          <p><span class="copyright">Our customer service representatives are available 24 hours a day, 7 days a week, 365 days a year to assist you with any issues you might have. We offer live chat support, email and phone support, whichever you prefer.</span></p>
	          <ul>
	            <li><a target="_blank" href="http://www.meatycams.com/customer-service/?">Customer service</a></li>
	            <li><a target="_blank" href="http://www.meatycams.com/customer-service/faq.html?">Frequently asked questions</a><a href="#"></a></li>
	            
	            <li style="line-height: 10px; margin: 4px 0">
					Livespace Investments Ltd. <br />
					6 Ioanni Stylianou #202, 2003 <br />
					Nicosia, Cyprus     
	            </li>

				<?php /*
	            <li>
	            	For billing issues please go to 
					pkmbilling.com or call (877) 759-7498
	            </li> */ ?>
	            
	          </ul>
	        </div>			
		</div>
		
		<div class="clear"></div>
	</div>
	

</div>



<?php $this->widget('application.components.common.ClientInfoUpdateWidget'); ?>

</body>
</html>


<?php
CHelperSite::showTimeDebug();
?>
    
