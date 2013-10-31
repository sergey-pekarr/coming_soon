<div class="allin1-box allin1-step4">

	<p>
		There was a problem with your transaction and your card was declined.
	</p>		
	
	<p>	
		<span class="bold">Your Card Has Not Been Charged.</span> 
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
			Please try our secondary processor on the following page.
		</p>
		
		<p style="padding-top:30px;">
	    	redirecting  &nbsp;&nbsp;
	        <img width="16" height="16" src="/images/ajax-loader.gif" />
		</p>
		
		<script>
		<!--		
			$(document).ready(function() 
			{
				window.setTimeout(function() { window.location.href='<?php echo SITE_URL ?>/allin1/step2'; }, 6000);
			});		
		//-->
		</script>		
		
	<?php } ?>
</div>













