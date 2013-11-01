<div class="userInfoShot">
<?php
if (isset($profile)) { 
	$expire_at = $profile->getDataValue('expire_at');
	$paymentData = $profile->getPayment();
	?>
	
	<?php if ($paymentData) { ?>
	    $<?php echo $paymentData['amount'] ?>, <?php echo $paymentData['paymod'] ?>
		<br />		
	    <span class="smaller">FirstPay: <?php echo $paymentData['firstpay'] ?></span> 
	    <br />
	    <span class="smaller">LastPay: <?php echo $paymentData['lastpay'] ?></span> 
	    <br />
	    <span class="smaller" <?php if ($paymentData['status']!='active') { ?>style="color:red"<?php } ?>>Recurring: <?php echo $paymentData['status'] ?></span> 
	    <br />
	    
    <?php } ?>
	
	<?php if ($expire_at!='0000-00-00') { ?>
		<span class="smaller" <?php if ( time()>strtotime($expire_at.' 00:00:00') ) { ?> style="color:red" <?php } ?> >Expire: <?php echo $expire_at ?></span> 
	<?php } ?>


	
<?php } else { ?>

	<span class="smaller">Error</span>

<?php } ?>

</div>
