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
    <?php CHelperAssets::cssUrl('payment', $compressCss, $combineCss); ?>
	
	<?php $this->widget('application.components.metric.GoogleAnalyticsWidget'); ?>
</head>
<body>

<div id="wrapper-payment">

	<p>
    	<a class="back" onclick="ShowExitPopup = false;window.onbeforeunload = null;"
        href="<?php echo SITE_URL ?>/">Back To Members Area</a>
	</p>
    
    <h1 class="sitelogo">
    	<a href="<?php echo SITE_URL ?>/">
    		<img src="/images/img/pinkmeets/logo1.png">
    	</a>
    </h1>
	


    	<?php echo $content; ?>
    	


	<div class="payment-footer">
		<?php $this->widget('application.components.payment.FooterWidget', array('paymode'=>MAIN_BILLER)); ?>
	</div>

</div>


<?php include_once dirname(__FILE__).'/_footer.php'; ?>



</body>
</html>


<?php
CHelperSite::showTimeDebug();
?>
    
