<?php 		
	/*$m = GetGetValue('m'); 
    if($m == null) $m = 12;*/
	$lifetimesave = 70;
	
?>

<script>
var prices = new Array(0, 39, 9);
</script>

<div id="sidebar">
	<fieldset id="feature-list" class="widthlegend">
    	<legend style="margin-left:0px !important;">
    		<span class="txt">Choose Your Plan</span>
    		<span id="leleft" class="s"></span>
    		<span id="leright" class="s"></span>
    	</legend>
	</fieldset>
    
    <div id="subscription-container">
    	<input id="product-id" name="product_id" value="1" type="hidden">
		


    <?php /* foreach($pricingdata as $prmonth=> $pritem){
		$prclass='period-'.$prmonth; 
    $save = $pritem['off'] ?>
	<div style="opacity: 1;" id="<?php echo $prclass; ?>" class="s price-box">
	    <label for="<?php echo $prclass; ?>">
	        <strong class="s discount-banner save<?php echo $save ?>">Save<?php echo $save ?>%</strong>
	        <input id="<?php echo $prclass; ?>" class="subscription-input" name="period" value="<?php echo $prmonth; ?>"  <?php if($prmonth == $m) echo 'checked="checked"'; ?> type="radio"  onclick="ChangeOption(3);" >
            <div class="period">
                <p class="showterm">
                <?php echo $prmonth; ?> Month Access:</p>
                <p class="amount">
                <span class="oldprice">was $<?php echo $pritem['was']; ?></span> Now Only <span class="newprice">$<?php echo $pritem['price'];  ?></span></p>
				<br />
				<p class="amount">
                <span class="newprice">~$<?php echo  number_format(round( $pritem['price']/$prmonth));  ?>/Mon</span></p>
				<div class="clear"> </div>
            </div>
			<div>				
			</div>
        </label>
    </div>
	<?php }*/ ?>
		
		<?php 
		$save = 0;
		$prclass = "period-1";
		$prmonth = 1;
		$pritem = array(
			'was' => 39.00,
			'price' => 39.00,
		);
		?>
		<form id="pricesForm">
		<div style="opacity: 1;" class="s price-box">
		    <label for="<?php echo $prclass; ?>">
		        <strong class="s discount-banner save<?php echo $save ?>">Save<?php echo $save ?>%</strong>
		        <input class="subscription-input" name="period" value="1"  checked="true" type="radio" onclick="javascript:completeButtonPrices()" >
	            <div class="period">
	                <p class="showterm">
	                <?php echo $prmonth; ?> Month Access:</p>
	                <p class="amount" style="margin-top:10px">
	                <?php /* <span class="oldprice">was $<?php echo $pritem['was']; ?></span> Now Only <span class="newprice">$<?php echo $pritem['price'];  ?></span>*/ ?>
					$39/Mon
					</p>
					<?php /*<br />
					<p class="amount">
	                <span class="newprice">~$<?php echo  number_format(round( $pritem['price']/$prmonth));  ?>/Mon</span></p>*/ ?>
					<div class="clear"> </div>
	            </div>
				<div>				
				</div>
	        </label>
	    </div>
		
		<?php if ( Yii::app()->user->Profile->getDataValue('affid')<=1 && $this->paymod!='segpay' ) { ?>
		<div style="opacity: 1;" class="s price-box">
		    <label for="<?php echo $prclass; ?>">
		        <strong class="s discount-banner save<?php echo $save ?>">Save<?php echo $save ?>%</strong>
		        <input class="subscription-input" name="period" value="2"  type="radio" onclick="javascript:completeButtonPrices()">
	            <div class="period">
	                <p class="showterm">
	                1 Day Trial Access:</p>
	                <p class="amount" style="margin-top:10px">
					$9 for 24 hours, renews at $39/Mon
					</p>
					<div class="clear"> </div>
	            </div>
				<div>				
				</div>
	        </label>
	    </div>
	    <?php } ?>
	    
		<?php if (/*Yii::app()->user->Profile->getDataValue('affid')<=1 &&*/ $this->paymod=='segpay') { ?>
		<div style="opacity: 1;" class="s price-box">
		    <label for="<?php echo $prclass; ?>">
		        <strong class="s discount-banner save<?php echo $save ?>">Save<?php echo $save ?>%</strong>
		        <input class="subscription-input" name="period" value="3"  type="radio" onclick="javascript:completeButtonPrices()">
	            <div class="period">
	                <p class="showterm">
	                2 Days Trial Access:</p>
	                <p class="amount" style="margin-top:10px">
	                <?php /* <span class="oldprice">was $<?php echo $pritem['was']; ?></span> Now Only <span class="newprice">$<?php echo $pritem['price'];  ?></span>*/ ?>
					$9 for 2 days, renews at $39/Mon
					</p>
					<?php /*<br />
					<p class="amount">
	                <span class="newprice">~$<?php echo  number_format(round( $pritem['price']/$prmonth));  ?>/Mon</span></p>*/?>
					<div class="clear"> </div>
	            </div>
				<div>				
				</div>
	        </label>
	    </div>
	    <?php } ?>	    
	    
</form>

<script type="text/javascript">
function completeButtonPrices()
{
	var p=$('#pricesForm input[name=period]:checked').val();
	$('#complete-button').attr('href', '/payment/redirection?p='+p);

	$('#finalprice span').html( prices[p] );
	$('#PaymentForm_priceId').val( p );
}
</script>


		<div class="clear"></div>

	</div>
                <fieldset id="feature-list" class="widthlegend">
                    <legend><span class="txt">Premium Member Features</span><span id="leleft" class="s"></span><span
                        id="leright" class="s"></span></legend>
                    <table cellspacing="0" width="100%">
                        <tbody>
                            <tr class="row-1 ">
                                <td class="col3 center" width="30">
                                    <img class="s yesicon" alt="v/" src="/images/img/blank.gif">
                                </td>
                                <td class="featured">
                                    Anytime Money Back Guarantee
                                </td>
                            </tr>
                            <tr class="row-2 alternate">
                                <td class="col3 center" width="30">
                                    <img class="s yesicon" alt="v/" src="/images/img/blank.gif">
                                </td>
                                <td class="featured">
                                    Send Unlimited Messages
                                </td>
                            </tr>
                            <tr class="row-3 ">
                                <td class="col3 center" width="30">
                                    <img class="s yesicon" alt="v/" src="/images/img/blank.gif">
                                </td>
                                <td>
                                    View All Profiles &amp; Photos
                                </td>
                            </tr>
                            <tr class="row-4 alternate">
                                <td class="col3 center" width="30">
                                    <img class="s yesicon" alt="v/" src="/images/img/blank.gif">
                                </td>
                                <td>
                                    View Enlarged Photos
                                </td>
                            </tr>
                            <tr class="row-5 ">
                                <td class="col3 center" width="30">
                                    <img class="s yesicon" alt="v/" src="/images/img/blank.gif">
                                </td>
                                <td>
                                    Send Unlimited Winks
                                </td>
                            </tr>
                            <tr class="row-6 alternate">
                                <td class="col3 center" width="30">
                                    <img class="s yesicon" alt="v/" src="/images/img/blank.gif">
                                </td>
                                <td>
                                    Message Free Members
                                </td>
                            </tr>
                            <?php /*
							<tr class="row-7 ">
                                <td class="col3 center" width="30">
                                    <img class="s yesicon" alt="v/" src="/images/img/blank.gif">
                                </td>
                                <td>
                                    FREE XXX video content
                                </td>
                            </tr>
							*/ ?>
                        </tbody>
                    </table>
                </fieldset>				

							
				
 </div>