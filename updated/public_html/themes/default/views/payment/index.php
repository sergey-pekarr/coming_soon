<?php 
//$a = (isset($_GET['a'])) ? $_GET['a'] : '';
$save = 70;
$lifetimesave = 70;
?>
	
<div id="secure-discount-header" class="center">
	<img alt="" src="/images/img/secure/header<?php echo $save; ?>.png">
</div>


<?php if(isset($action)) { ?>
<div id="reason-box">
	<?php if(isset($profileid)) { 
        $profile = new Profile($profileid);
        $isonline = $profile->getDataValue('isOnline')
    ?>
	<div id="reason-profile">
		<img alt="Profile Picture" src="<?php echo $profile->imgUrl(); ?>" width="82" height="82">
		<p><strong><?php echo $profile->getDataValue('username').', '.$profile->getDataValue('age'); ?></strong><br>
            <?php echo $profile->getDataValue('city'); ?></p>
		<p><span class="<?php echo $isonline?'online':'online' ?>-text"></span><span style="float:left;"><?php echo $isonline?'Online':'Offline' ?></span></p>
		<div class="clear" style="margin:0px; border:none; padding:0px;"></div>
	</div>
	<?php } ?>
	<div id="reason-caption">
		<h3><?php echo $title; ?></h3>
		<p><?php echo $desc ?></p>
	</div>
	<div class="clear"></div>
</div>
<?php } ?>


<div class="payment-content">

	<noscript>
	    <div class="alert-message error bold center">
	        Javascript is disabled on your browser. Please enable JavaScript or upgrade to a Javascript-capable browser to use the site.
	    </div>
	</noscript>
	
	<?php if(!isset($action)) { ?>
	<div id="getlaid-banner"></div>
	<?php } ?>


	<div class="left">
		<?php $this->widget('application.components.payment.LeftSidebarWidget', array('paymod'=>$paymod)); ?>
	</div>
	
	
	<div class="right">
		<?php $this->widget('application.components.payment.ContentWidget', array('paymod'=>$paymod)); ?>
		
		<p class="bold">Anytime Money Back Guarantee</p>		
		
		<p class="nothankstext">
			<a class="button-back-members-area third-button" href="<?php echo SITE_URL ?>"></a>
		</p>		
		
		<div class="clear"></div>
		
		<p class="paymentToConatct">
			To contact support please fill out a ticket <a target="_blank" href="<?php echo TICKET_URL ?>">here</a> 
			or email 
			<script>
			  <!--
				contactUsShow();
			  //-->
			</script>
			<?php /* support@pinkmeets.com */ ?>
		</p>
		
		<p class="paymentToConatct" style="margin-top:10px">
			<?php if ($paymod == 'zombaio') {  ?>	 	
			
			<?php } elseif ($paymod == 'rg2') {  ?>
				The charge on your card will appear from pkmbilling.com
			<?php } else { ?>			
				
			<?php } ?>
		</p>
		
		
		
				
		</p>
	</div>

    <div class="clear"></div>
    	
</div>
