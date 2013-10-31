<?php
$compressJs  = (LOCAL) ? false : true;
$compressCss = (LOCAL) ? false : true;

$combineJs  = (LOCAL) ? false : true;
$combineCss = (LOCAL) ? false : true;

$app = Yii::app();
?>
<!DOCTYPE html>
<html lang="en" xml:lang="en" xmlns:fb="http://ogp.me/ns/fb#" >
<head>
    <meta charset="utf-8" />
	<meta name="language" content="en" />
    
    
    <?php 
    CHelperAssets::jsUrl('site', $compressJs, $combineJs); ?>
    
    <link rel="shortcut icon" href="/favicon.ico" />

	<title><?php echo CHtml::encode($this->pageTitle); ?></title>

    <?php CHelperAssets::cssUrl('site', $compressCss, $combineCss); ?>
    <link href="/fonts/fonts.css" rel="stylesheet" type="text/css" />
    
    <?php $this->widget('application.components.userprofile.ScreenResolutionUpdateWidget'); ?>
   
    <?php $this->widget('application.components.metric.GoogleAnalyticsWidget'); ?>
    
    <?php 
	    if ( Yii::app()->browser->isIE10() ) {
			CHelperAssets::jsUrl('ie10', $compressJs, $combineJs);
		}       
    ?>
    
</head>
<body>

<?php if ( Yii::app()->user->role == 'free' ) { //if ( Yii::app()->user->checkAccess('free') && !Yii::app()->user->checkAccess('gold')) { ?>
	<div class="verify-banner regular ">
		<p> Don't forget... Upgrade NOW and SAVE an instant 50% on your Premium membership...</p>
		<p><a href="/payment" title="Unlock"> CLICK HERE</a> to meet thousands of like-minded singles near <?php echo Yii::app()->user->location('city') ?>!</p>
	</div>    	
<?php } ?>


<?php 
    //justjoined or free user
    if ( !Yii::app()->user->checkAccess('free') ) { ?>
    
		<div class="topbar">
	    	<div class="wrap">
	        	<div class="box1">
	            	<div class="callbox"><a href="/site/page/contact">24/7 Live toll free customer service</a></div>
	            </div>
	            <div class="box2">
	            	<?php /*
	            	<div class="login1"><a href="#"><img src="images/login1.jpg" width="111" height="27" /></a></div>
	                <div class="login2"><a href="#"><img src="images/login2.jpg" width="102" height="27" /></a></div>
	                */ ?>
	                
		                    <div class="login1">
		                        <a href="javascript:void(0)" onclick="javascript:loginFormShow();">
		                        	<img src="/images/design/login1.jpg" width="111" height="27" />
		                        </a>
							</div>
								
							<div class="login2">
								<a href="/?service=facebook" class="fblogin">
									<img src="/images/design/login2.jpg" width="102" height="27" />
								</a>
							</div>
		                	
		    				<?php $this->widget('application.components.UserLoginFormWidget'); ?>	                
	                
	                
	                <div class="clearfix"></div>
	            </div>
	            <div class="clearfix"></div>
	        </div>
	    </div>    
    
	    <div class="header">
	    	<div class="wrap">
	        	<div class="logo"><a href="/"><img src="/images/design/linkmeets_logo.png" width="253" height="69" /></a></div>
	            <div class="menu">
	            	<ul>
	                	<li><a href="/">Home</a></li>
	                    <li><a href="/site/registration">Member's Online</a></li>
	                    <li><a href="/site/registration">Search</a></li>
	                    <li><a href="/site/registration">Live Chat</a></li>
	                    <li><a href="/user/remindPassword">Forgot Password</a></li>
	                </ul>
	            </div>
	        </div>
	    </div>    

		
    <?php } else { 
		
		?>
    	<div id="header">
			<div id="header_center">
				
				<a id="header_logo" href="/"></a>

				<ul>
				<li>
					<a href="/">
						HOME
						<span> </span>
					</a>
				</li>
				<li>
					<a id="header-inbox" href="/msg/inbox">
						INBOX
						<span></span>
						<div class="tabnew"> </div>
					</a>
				</li>
				<li>
					<a id="header-online" href="/profiles/online">
						MEMBERS ONLINE
						<span></span>
					</a>
				</li>
				<li>
					<a id="header-search" href="/search">
						SEARCH
						<span></span>
					</a>
				</li>
				<li>
					<a href="/profile">
						MY PROFILE
						<span></span>
					</a>
				</li>
				<li>
					<a href="/account">
						MY ACCOUNT
						<span></span>
					</a>
				</li>
				<li>
					<a onclick="" href="/site/logout">
						LOGOUT
						<span></span>
					</a>
				</li>
				</ul>

				
			</div>

    	</div>
	<?php } ?>
            
	</div>
	<div class="clear"></div>

	<noscript>
		<div class="alert-message error bold center">
	    	Javascript is disabled on your browser. Please enable JavaScript or upgrade to a Javascript-capable browser to use the site.
		</div>
	</noscript>
               
	
	
	<?php echo $content; ?>
	

<?php include_once dirname(__FILE__).'/_footer.php'; ?>

<?php $this->widget('application.components.common.ClientInfoUpdateWidget'); ?>

<?php $this->widget('application.components.panamus.FingerprintWidget'); ?>

</body>
</html>


<?php
CHelperSite::showTimeDebug();
?>
