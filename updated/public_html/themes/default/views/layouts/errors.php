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
    ?>
    
    <link rel="shortcut icon" href="/favicon.ico" />

	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
	
    <?php CHelperAssets::cssUrl('site', $compressCss, $combineCss); ?>
	
	<?php $this->widget('application.components.metric.GoogleAnalyticsWidget'); ?>
</head>
<body style="background-color: #fff">


	
<div id="wrapper" style="width: 480px;">
	
    <noscript>
        <div class="alert-message error bold center">
            Javascript is disabled on your browser. Please enable JavaScript or upgrade to a Javascript-capable browser to use the site.
        </div>
    </noscript>

    <?php echo $content; ?>

</div>
<div class="clear"></div>

<?php if (!CAMS) include_once dirname(__FILE__).'/_footer.php'; ?>

</body>
</html>


<?php
CHelperSite::showTimeDebug();
?>
    
