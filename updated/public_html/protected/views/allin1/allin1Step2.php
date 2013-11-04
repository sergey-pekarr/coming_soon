<?php  /* <div class="logoText2" style="top:0">
	<span><a href="/site/page/contact"> 24/7 Live toll free customer support </a> </span> 
</div> */ ?>

<div style="text-align:center;">
	<img id="logo-allin1" src="/images/design/logo-allin1.png" />
	<?php  /*<br />
	<div style="font-size:11px">Welcome to the Worlds Largest, Naughtiest.. Adult Dating &amp; Sex Personals</div> 
	<h1>New user signup</h1> */ ?>
</div>


<div class="allin1-box allin1-step2">
	
	<form id="allin1-step2-form" action="/allin1/step2/" method="post">
	
		<table class="table" >
		    <?php 
		    if ($payment_options)
			    foreach ($payment_options as $o) { ?>
					<tr class="tr_<?php echo $o['id'] ?>">
				    	<td valign="middle" style="min-height:50px">
							<input 
								name="term" 
								type="radio" 
								value="<?php echo $o['id'] ?>" 
								onclick="setprice(<?php echo $o['id'] ?>)"
								<?php if ($o['id'] == $defaultPriceId) { ?>checked="checked"<?php } ?> 
							/>
						</td>
				      	<td valign="middle" style="cursor: pointer;" onclick="javascript:setprice(<?php echo $o['id'] ?>)">
				      	<div class="sbody"><?php echo $o['title'] ?></div>
				      	<?php echo $o['description'] ?>				      		
				      	</td>
				    </tr>
			<?php } ?>
		</table>


		<div id="finalprice" class="hide">
			Your credit card will be charged for: 
			<span></span>
		</div>
	
		<div class="center">
			<input type="submit"
			    class="btn success"
			    value="Continue »" 
			/>		
		</div>
		
	</form>



</div>


<script>
<!--
	var prices = new Array();
	<?php if ($payment_options)
		    foreach ($payment_options as $o) { ?>
				prices[<?php echo $o['id'] ?>] = <?php echo $o['price'] ?>;
	<?php } ?>

<?php /*
	var prices_local = new Array();
	{foreach from=$payment_options item=p}
	prices_local[{$p.id}] = "{$smarty.session.local_prices[$p.price]}";{/foreach}
<?php */ ?>

	$(document).ready(function() 
	{
		setprice(<?php echo $defaultPriceId ?>);
	});


	

	function setprice(id) 
	{
		$(".allin1-step2 input").removeAttr('checked');
		$(".allin1-step2 .tr_"+id+" input").attr('checked', 'checked');

		$("#finalprice").show();
		$("#finalprice span").html(prices[id]).show();
	}
//-->
</script>
