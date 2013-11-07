<?php 

	//[19:34:00] Heath: Can you please set it up so if geo-ip is germany, then link is not pre-checked
    $country = "";
    if (isset($_SERVER['GEOIP_COUNTRY_CODE']))
       	$country = $_SERVER['GEOIP_COUNTRY_CODE'];
    if (!$country)//site/error
    {
       	$locationRecord = Yii::app()->location->getGeoIPRecord();
       	$country = $locationRecord['GEOIP_COUNTRY_CODE'];
    }
	$unhide = ($country && $country=='DE');



	if (Yii::app()->user->id) 
	{
		$email = Yii::app()->user->Profile->getDataValue('email');
		//$username = Yii::app()->user->Profile->getDataValue('username');
		//$gender = Yii::app()->user->Profile->getDataValue('gender');
		$password = CSecur::decryptByLikeNC( Yii::app()->user->Profile->getDataValue('passwd'));
		$isJoinedTo_ONL = Yii::app()->user->Profile->isJoinedTo_ONL();
	}
	else
	{
		$email="";
		//$username="";
		$password="";
		$isJoinedTo_ONL = false; 	
	}
	
	if (DEBUG_IP)
	{
		$countryInit = 'US';
		$stateInit = "NY";
		$cityInit = 'New York';
	}
	else
	{
		$countryInit = (isset($_SERVER['GEOIP_COUNTRY_CODE']) && $_SERVER['GEOIP_COUNTRY_CODE']) ? $_SERVER['GEOIP_COUNTRY_CODE'] : 'US';
		$stateInit = (isset($_SERVER['GEOIP_REGION'])) ? $_SERVER['GEOIP_REGION'] : '';
		$cityInit = (isset($_SERVER['GEOIP_CITY'])) ? $_SERVER['GEOIP_CITY'] : '';
	}
?>


<div class="landingcams-box">

<div id="landingcams-stepAll-form" class="form">


<?php 
    
    $labels = $model->attributeLabels();
    
    $success_action = (DEBUG_IP) ? "alert(ret); $('#landingcams-stepAll-form .btn').button('reset');" : "window.location.href=ret;";
    
    $form=$this->beginWidget('CActiveFormSw', array(
            'action'=>Yii::app()->createUrl('/join'),
            'id' => 'LandingCamsAllinOneStepForm',
            'enableAjaxValidation' => true,            
            'clientOptions' => array(
                'validateOnSubmit' => true,
                'validateOnChange' => true,
                'afterValidate' => "js: function(form, data, hasError) {
                                                    //if no error in validation, send form data with Ajax

                                                    
                                                    if ( typeof(data['LandingCamsAllinOneStepForm_ip'])!='undefined')
                                                    {
                                                    	window.location.href='".SITE_MAIN_URL."/site/login';
                                                    	return false;
                                                    }
                                                    
                                                    
                                                    if (! hasError) {
                                                      $.ajax({
                                                        type: 'POST',
                                                        url: form[0].action,
                                                        data: $(form).serialize(),
                                                        success: function(ret) {
															
                                                        	if ($('#LandingCamsAllinOneStepForm_subStep').val()==0)
                                                        	{
                                                            	$('#fill1cont').fadeIn(1000);
                                                            	$('#LandingCamsAllinOneStepForm_subStep').val(1);
                                                            	
                                                            	$('#landingcams-stepAll-form .btn').button('reset');                                                            	
                                                            	$('#landingcams-stepAll-form .btn').addClass('btn-danger'); 


$('#LandingCamsAllinOneStepForm_genders').attr('readonly', true);
$('#LandingCamsAllinOneStepForm_email').attr('readonly', true);
$('#LandingCamsAllinOneStepForm_username').attr('readonly', true);
$('#LandingCamsAllinOneStepForm_password').attr('readonly', true);
$('#LandingCamsAllinOneStepForm_birthday_month').attr('readonly', true);
$('#LandingCamsAllinOneStepForm_birthday_day').attr('readonly', true);
$('#LandingCamsAllinOneStepForm_birthday_year').attr('readonly', true);
                                                        	}
                                                            else if ($('#LandingCamsAllinOneStepForm_subStep').val()==1) 
                                                            {
                                                            	$('#fill2cont').fadeIn(1000);
                                                            	$('#LandingCamsAllinOneStepForm_subStep').val(2);
                                                            	
                                                            	$('#landingcams-stepAll-form .btn').button('reset');                                                            	
                                                            	$('#landingcams-stepAll-form .btn').html('Upgrade Now');
                                                            	$('#landingcams-stepAll-form .btn').attr('data-loading-text','Upgrade Now');
                                                            	$('#landingcams-stepAll-form .btn').addClass('btn-danger');
$('#LandingCamsAllinOneStepForm_country').val('".$countryInit."');
landingcams_step2_setStates('".$stateInit."');
$('#LandingCamsAllinOneStepForm_city').val('".$cityInit."');
                                                            }
                                                            else if ($('#LandingCamsAllinOneStepForm_subStep').val()==2) 
                                                            {
                                                            	$('#fill3cont').fadeIn(1000);
                                                            	$('#LandingCamsAllinOneStepForm_subStep').val(3);
                                                            	
                                                            	$('#landingcams-stepAll-form .btn').button('reset');                                                            	
                                                            	step3_formBtnChange();
                                                            	$('#landingcams-stepAll-form .btn').attr('data-loading-text','Click here to start browsing now!');
                                                            	$('#landingcams-stepAll-form .btn').addClass('btn-danger');

                                                            	$('#step3_email').html($('#LandingCamsAllinOneStepForm_email').val());
                                                            	$('#step3_password').html($('#LandingCamsAllinOneStepForm_password').val());
                                                            }
                                                            else
                                                            {
																".$success_action."
															}
                                                        },
														error: function(){
										                    alert('error...');
            												$('#landingcams-stepAll-form .btn').button('reset');
										                }                                                         
                                                      });                                                        
                                                    }
                                                    else
                                                       $('#landingcams-stepAll-form .btn').button('reset');
                                                    return false;
                }"                
                
            ),
    )); 
?>

<?php echo $form->hiddenField($model,'subStep'); ?>

<?php if (!Yii::app()->user->id) { ?>

<h1>Get Instant Access to Live Shows Now!</h1>

<dl>


    <div class="row">
        <dt><?php echo $form->labelEx($model,'genders'); ?></dt>
        <dd>
        <?php echo $form->dropDownList(
                        $model, 
                        'genders',
                        CHelperProfile::getGenders()//$model->genders
        ); ?>
        </dd>
        <?php echo $form->error($model, 'genders'); ?>
        <div class="clear"></div>
    </div>    

    <div class="row">
        <dt><?php echo $form->labelEx($model,'email'); ?></dt>
        <dd>
            <?php echo $form->textField($model,'email', array(/*'placeholder'=>$labels['email'], */ /*'onfocus'=>'javascript:$(".email2").slideDown(200);'*/ )); ?>
        </dd>
        <?php echo $form->error($model,'email'); ?>
        <div class="clear"></div>
    </div>

	<div class="row username">
		<dt><?php echo $form->labelEx($model,'username'); ?></dt>
		<dd>
        <?php echo $form->textField(
                            $model,
                            'username'
                    );?>
        </dd>
		<?php echo $form->error($model,'username'); ?>
        <div class="clear"></div>
	</div>

    <div class="row">
        <dt><?php echo $form->labelEx($model,'password'); ?></dt>
        <dd>
            <?php echo $form->passwordField($model,'password', array(/*'placeholder'=>$labels['password'],*/ 'onfocus'=>'javascript:$(".password2").slideDown(200)' /*, 'onclick'=>'javascript:$(".password2").slideDown(200)'*/, "autocomplete"=>"off")); ?>
		</dd>
        <?php echo $form->error($model,'password'); ?>
        <div class="clear"></div>
    </div>


	<div class="row birthday" >
		<dt><?php echo $form->labelEx($model,'birthday'); ?></dt>
        
        <dd>
        <?php echo $form->hiddenField($model,'birthday'); ?>
        
        <?php 
            echo $form->dropDownList($model,'birthday[month]', CHelperProfile::getMonth(true, false, 'Month'));
            echo $form->dropDownList($model,'birthday[day]', CHelperProfile::getDays(true, 'Day'));
            echo $form->dropDownList($model,'birthday[year]', CHelperProfile::getYear(true, 'Year')); 
        ?>
        </dd>
        <?php echo $form->error($model,'birthday'); ?>
        <div class="clear"></div>
	</div>

</dl> 

<?php } else { 

	echo $form->hiddenField($model,'email',array('value'=>$email));
	//echo $form->hiddenField($model,'username',array('value'=>$username));
	echo $form->hiddenField($model,'password',array('value'=>$password));
	
}?>





















<!-- Continue 1 -->
<dl id="fill1cont" <?php if ($model->subStep>=1) { ?> style="display:block" <?php } ?> >
	
	<div class="whitelabelPrices">
		
		<div style="text-align: justify;">
			Your submitted credit card information will only be charged if you confirm certain price points of cam sessions that either require the purchase of credits or request a pay per minute charge for private cam sessions. All cam models are setting their individual price points. Each payment will be authorized on a pay per click basis.
		</div>
	</div>
	
	<div class="row desc center color_red">
        Please note: all fields are mandatory in this form
	</div>

	<div class="row desc">
        <dt>&nbsp;</dt>
        <dd>
	        <img src="/images/design/icon_ssl.gif" width="24" height="24" />
	        This page is encrypted using 128-bit SSL. 
	        <?php /*<a href="/site/page/ssl" target="_blank">What is SSL?</a> */?>
        </dd>
        <div class="clear"></div>
	</div>

	<td class="sbody2"></td>
	<div class="row desc">
        <dt>&nbsp;</dt>
        <dd class="subStepTitle">Credit Card information</dd>
        <div class="clear"></div>
	</div>

	<div class="row desc">
        <dt>&nbsp;</dt>
        <dd class="smaller">
	        Note: protecting our customer's credit card information is extremely important for us. For that reason, its not stored on our servers and is used only while we process your purchase.
        </dd>
        <div class="clear"></div>
	</div>

	<div class="row desc cards">
        <dt>&nbsp;</dt>
        <dd>
			<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td><img src="/images/cc/visa1.gif" width="50" height="30" /></td>
					<td>&nbsp;&nbsp;</td>
					<td><img src="/images/cc/visa_electron.gif" width="50" height="30" /></td>
					<td>&nbsp;&nbsp;</td>
					<td><img src="/images/cc/mastercard1.gif" width="50" height="30" /></td>
					<td>&nbsp;&nbsp;</td>
					<td><img src="/images/cc/discover.gif" width="50" height="30" /></td>
					<?php /*<td>&nbsp;&nbsp;</td>
					<td><img src="/images/cc/jcb1.gif" width="30" height="30" /></td>*/?>
				</tr>
			</table>	        
        </dd>
        <div class="clear"></div>
	</div>

	<!-- CC Number -->
	<div class="row">
        <dt><?php echo $form->labelEx($model,'ccnum'); ?></dt>
        <dd>
	        <?php echo $form->textField($model,'ccnum'); ?>
        </dd>
        <?php echo $form->error($model, 'ccnum'); ?>
        <div class="clear"></div>
	</div>
	
	<!-- Exp date -->
	<div class="row">
        <dt> <label>Expiration date:</label><?php //echo $form->labelEx($model,'expire'); ?></dt>
        <dd>
        	<?php //echo $form->hiddenField($model,'expire'); ?>
        	
	        <?php echo $form->dropDownList(
	                        $model, 
	                        'ccmon',
	                        CHelperPayment::getCCMonths()//$model->ccmonths
	        ); ?>        	
        	
	        <?php echo $form->dropDownList(
	                        $model, 
	                        'ccyear',
	                        CHelperPayment::getCCYears()//$model->ccyears
	        ); ?>
	        
        </dd>
        <?php echo $form->error($model, 'ccyear'); ?>
        <?php echo $form->error($model, 'ccmon'); ?>
        <div class="clear"></div>
	</div>

	<!-- CVV/CVV2 -->
	<div class="row">
        <dt><?php echo $form->labelEx($model,'ccvv'); ?></dt>
        <dd>
	        <?php echo $form->textField($model,'ccvv'); ?>
	        <?php /*&nbsp;&nbsp;&nbsp;<a href="/site/page/cvv2" target="_blank">What is CVV / CVV2?</a>*/?>
        </dd>
        <?php echo $form->error($model, 'ccvv'); ?>
        <div class="clear"></div>
	</div>
	
	<div style="text-align: left;" class="bold">
		Charges will appear on your statement from pkmbilling.com
	</div>
	
</dl>



<!-- Continue 2 -->
<dl id="fill2cont" <?php if ($model->subStep>=2) { ?> style="display:block" <?php } ?> >
	
	
	<div class="row subscriber">
        <dt>&nbsp;</dt>
        <dd class="subStepTitle">Subscriber information</dd>
        <div class="clear"></div>
	</div>




	<!-- First name -->
	<div class="row">
        <dt><?php echo $form->labelEx($model,'firstname'); ?></dt>
        <dd>
	        <?php echo $form->textField($model,'firstname'); ?>
        </dd>
        <?php echo $form->error($model, 'firstname'); ?>
        <div class="clear"></div>
	</div>
	
	<!-- Last name -->
	<div class="row">
        <dt><?php echo $form->labelEx($model,'lastname'); ?></dt>
        <dd>
	        <?php echo $form->textField($model,'lastname'); ?>
        </dd>
        <?php echo $form->error($model, 'lastname'); ?>
        <div class="clear"></div>
	</div>




	<div class="row location country">
		<?php echo $form->hiddenField($model,'location_id', array( 'value'=>0 ));//echo $form->hiddenField($model,'location_id', array( 'value'=>$location_id )); ?>

		<dt><?php echo $form->labelEx($model,'country'); ?></dt>
		<dd>
            <?php echo $form->dropDownList(
                        $model,
                        'country', 
                        CMap::mergeArray( array(''=>'Select ...'), Yii::app()->location->getCountriesList()),//$model->countries,//Yii::app()->location->getCountriesList(), 
                        array(
                            //'options' => array($location['country']=>array('selected'=>true)),
                            'onChange'=> 'javascript:landingcams_step2_setStates()',//'onChange'=> 'javascript: landingcams_onChangeCountry(this);',//'onChange'=> 'javascript: countryZsignup = this.value; $("input#LandingCamsAllinOneStepForm_city").val("").focus()',
                            //'onChange'=> 'javascript:updateStates(this.value)',
                        )
                   ); ?>
        </dd>
		<?php echo $form->error($model,'country'); ?>
        <div class="clear"></div>
    </div>
    
    <div class="row location state hide">    
		<dt><?php echo $form->labelEx($model,'state'); ?></dt>
		<dd>
            <?php echo $form->dropDownList(
                        $model,
                        'state', 
                        CMap::mergeArray( array(''=>'Select ...'), Yii::app()->location->getStatesList('US')),//$model->states,//Yii::app()->location->getStatesList('US'), 
                        array(
                            //'options' => array($location['state']=>array('selected'=>true)),
                            //'onChange'=> 'javascript: countryZsignup = this.value; $("input#LandingCamsAllinOneStepForm_city").val("").focus()',
                            //'onChange'=> 'javascript:updateStates(this.value)',
                        )
                   ); ?>
        </dd>
        <dd id="state-loading"></dd>
        <dd id="state-NA">N/A</dd>        
		<?php echo $form->error($model,'state'); ?>
        <div class="clear"></div>
	</div>
    
    <div class="row location city">    
		<dt><?php echo $form->labelEx($model,'city'); ?></dt>
        <dd>
		<?php echo $form->textField(
                        $model,
                        'city',
                        array(
                            //'value'=>$location['city'],
                        )
                    ); ?>
        </dd>
		<?php echo $form->error($model,'city'); ?>
        <div class="clear"></div>
	</div>
    
	<div class="row zip">
		<dt><?php echo $form->labelEx($model,'zip'); ?></dt>
        <dd>	
            <?php echo $form->textField(
                            $model,
                            'zip',
                            array(
                                //'value'=>$location['zip'],
                            )
            );?>
        </dd>     
        <span id="zipLoading">&nbsp;&nbsp;&nbsp;&nbsp;</span>   
		<?php echo $form->error($model,'zip'); ?>
        <div class="clear"></div>
	</div> 
		
	<!-- Address -->
	<div class="row">
        <dt><?php echo $form->labelEx($model,'address'); ?></dt>
        <dd>
	        <?php echo $form->textField($model,'address'); ?>
        </dd>
        <?php echo $form->error($model, 'address'); ?>
        <div class="clear"></div>
	</div>

</dl>




<!-- Continue 3 -->
<div class="landingcams-success" id="fill3cont" <?php if ($model->subStep>=3) { ?> style="display:block" <?php } ?>>
	

	
	<p>
		<span class="bold">Welcome to MeatyCams!</span> <br />
		Important Information! Please print this page or write down the following information and keep it in a safe place. You will need this information in the future to access this site or change your membership status. This page is dynamically created for your convenience. Please do not bookmark this page.
	</p>
	
	<table>
		<tr>
			<td class="bold">Email:</td>
			<td id="step3_email"><?php //echo $email ?></td>
		</tr>
		<tr>
			<td class="bold">Password:</td>
			<td id="step3_password"><?php //echo $password ?></td>
		</tr>
		<tr>
			<td class="bold">Site:</td>
			<td>http://www.MeatyCams.com</td>
		</tr>
	</table>
	
	<?php if (!$isJoinedTo_ONL) { ?>
		
			<div class="joinONL">
				<div class="left">
					<?php if ( !$unhide && isset($model->affid) && $model->affid && $model->affid>99 ) { ?>
						<?php /*<input type="checkbox" name="joinONL_cb" checked="true" readonly="true" disabled="disabled" />*/?>
						<?php /*<input type="radio" name="joinONL_cb" checked="true" /> */ ?>
						<input type="hidden" name="joinONL" value="1" />
					<?php } else { 
						//FOR BANK if not affid
					?>
						<input type="checkbox" name="joinONL" />
						
					<?php } ?>
				</div>
				<div class="right" style="font-size:10px">
					Connect with local girls now with a membership to <a href="http://meetsi.com" target="_blank">meetsi.com</a> Adult Personals gold account. You agree to join meetsi.com adult dating for $9.00 for a twenty four hour trial. Your subscription will automatically renew after twenty four hours at $39 a month, cancel at anytime. I agree to the terms and conditions <a target="_blank" href="http://meetsi.com/site/page/terms">here</a>.
				</div>
				<div class="clear"></div>		
			</div>
		
	<?php } ?>	
	
	
<?php /*	
	<br /><br />
	<div class="center">
		<input 
			type="submit" 
			class="btn btn-danger" 
			value="Click here to start browsing now!"
			onclick="javascript: $(this).hide().parent().find('img').show(); " 
		/>
		<img src="/images/design/loading.gif" style="display:none" />
		
		<input type="hidden" name="start" value="1" />
	</div>
*/ ?>	

	
</div>



























<?php $this->endWidget(); ?>

<div class="center">
	<button 
	    class="btn btn-danger" 
	    data-loading-text="Continue..."
	    onclick="javascript:formSubmit('landingcams-stepAll-form');"    
	>Continue »</button>
</div>

    
</div><!-- form -->


</div>


<script>
<!--
	$(document).ready(function(){
		if ($(".joinONL input[type=checkbox]").length)
			$(".joinONL").on('click', 'input[type=checkbox]', function(){
				step3_formBtnChange();
			});
	});
	
	function step3_formBtnChange()
	{
		if ($(".joinONL input[type=checkbox]:checked").length)
			$('#landingcams-stepAll-form .btn').html('Upgrade and add meetsi.com membership and start searching now!');
		else
			$('#landingcams-stepAll-form .btn').html('Click here to start browsing now!');
	}
//-->
</script>


