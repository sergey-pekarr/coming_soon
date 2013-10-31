<div class="landingcams-box landingcams-step4">

	<p class="bold">
		There was a problem with your transaction <br /> and your card was declined. 
	</p>		
	
	<?php 
	$mes = (Yii::app()->user->hasFlash('errorCustom')) ? Yii::app()->user->getFlash('errorCustom') : "";
	
	if ($mes)
	{
		Yii::app()->user->setFlash('errorCustom',$mes);//if user refreshed page
		?>
		
		<div class="alert-message error bold center" style="margin-top: 30px; padding:20px; color: red; background-color: #F8E0E0">
			<?php echo $mes; ?>
		</div>
		
	<?php } else { ?>
		
		<p>	
			Please try another card
			<br />		
		</p>
		
		<p style="padding-top:30px;">
	    	redirecting  &nbsp;&nbsp;
			<img width="16" height="16" src="/images/ajax-loader.gif" />
		</p>		
		
		<script>
		<!--		
			$(document).ready(function() 
			{
				window.setTimeout(function() { window.location.href='<?php echo SITE_URL ?>'; }, 10000);
			});		
		//-->
		</script>		
		
	<?php } ?>	
	
</div>













