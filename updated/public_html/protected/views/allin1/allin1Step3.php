<div class="allin1-box allin1-step3">

<div id="allin1-step3-form" class="form">


<?php 
    
    $labels = $model->attributeLabels();
    
    $form=$this->beginWidget('CActiveFormSw', array(
            'action'=>Yii::app()->createUrl('/allin1/step3'),
            'id' => 'Allin1Step3Form',
            'enableAjaxValidation' => true,            
            //'enableClientValidation'=> true,           
            'clientOptions' => array(
                //'validationUrl'=>Yii::app()->createUrl('UserRegistration/step1Validation'),
                'validateOnSubmit' => true,
                'validateOnChange' => true,
                'afterValidate' => "js: function(form, data, hasError) {
                                                    //if no error in validation, send form data with Ajax
                                                    if (! hasError) {
                                                      $.ajax({
                                                        type: 'POST',
                                                        url: form[0].action,
                                                        data: $(form).serialize(),
                                                        success: function(ret) {
                                                            if ($('#Allin1Step3Form_subStep').val()==1) 
                                                            {
                                                            	$('#fill2cont').fadeIn(1000);
                                                            	$('#Allin1Step3Form_subStep').val(2);
                                                            	
                                                            	$('#allin1-step3-form .btn').button('reset');                                                            	
                                                            	$('#allin1-step3-form .btn').html('Upgrade Now');
                                                            	$('#allin1-step3-form .btn').addClass('btn-danger');
                                                            }
                                                            else
                                                            {
//alert(ret);//
window.location.href=ret;
//$('#allin1-step3-form .btn').button('reset');
															}
                                                        }
                                                      });                                                        
                                                    }
                                                    else
                                                    {
//$('#allin1-step3-form .btn').button('reset');                                                       
                                                    	return false;
                                                    }
                }"
            ),
    )); 
?>

<?php echo $form->hiddenField($model,'subStep'); ?>


<dl>
	
	<div class="row desc">
        <dt>&nbsp;</dt>
        <dd class="color_red">Please note: all fields are mandatory in this form</dd>
        <div class="clear"></div>
	</div>

	<div class="row desc">
        <dt>&nbsp;</dt>
        <dd>
	        <img src="/images/design/icon_ssl.gif" width="24" height="24" />
	        This page is encrypted using 128-bit SSL. <a href="/site/page/ssl" target="_blank">What is SSL?</a>
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
        <dd>
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
					<td>&nbsp;&nbsp;</td>
					<td><img src="/images/cc/jcb1.gif" width="30" height="30" /></td>
				</tr>
			</table>	        
        </dd>
        <div class="clear"></div>
	</div>

	<!-- Name on card -->
    <div class="row">
        <dt><?php echo $form->labelEx($model,'ccname'); ?></dt>
        <dd>
	        <?php echo $form->textField($model,'ccname'); ?>
        </dd>
        <?php echo $form->error($model, 'ccname'); ?>
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
	                        $model->ccmonths
	        ); ?>        	
        	
	        <?php echo $form->dropDownList(
	                        $model, 
	                        'ccyear',
	                        $model->ccyears
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
	        &nbsp;&nbsp;&nbsp;<a href="/site/page/cvv2" target="_blank">What is CVV / CVV2?</a>
        </dd>
        <?php echo $form->error($model, 'ccvv'); ?>
        <div class="clear"></div>
	</div>
	
</dl>



<!-- Continue -->
<dl id="fill2cont">
	
	<td class="sbody2"></td>
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
	
	<!-- Address -->
	<div class="row">
        <dt><?php echo $form->labelEx($model,'address'); ?></dt>
        <dd>
	        <?php echo $form->textField($model,'address'); ?>
        </dd>
        <?php echo $form->error($model, 'address'); ?>
        <div class="clear"></div>
	</div>

	<!-- Country -->
	<div class="row">
        <dt><?php echo $form->labelEx($model,'country'); ?></dt>
        <dd>
            <?php 
            	echo $form->dropDownList(
                        $model,
                        'country', 
                        Yii::app()->location->getCountriesList(), 
                        array(
                            'options' => array( $model->location['country']=>array('selected'=>true)),
                            'onChange'=> 'javascript:allin1_step3_setStates()',
                            //'onChange'=> 'javascript:updateStates(this.value)',
                        )
                ); ?>       	
	        
        </dd>
        <?php echo $form->error($model, 'country'); ?>
        <div class="clear"></div>
	</div>	

	
	<!-- State -->
	<div class="row">
        <dt><?php echo $form->labelEx($model,'state'); ?></dt>
        <dd>
            <?php 
            	echo $form->dropDownList(
                        $model,
                        'state', 
                        Yii::app()->location->getStatesList($model->location['country']), 
                        array(
                            'options' => array( $model->location['state']=>array('selected'=>true))
                        )
                ); ?>       	
	        
        </dd>
        <dd id="state-loading"></dd>
        <dd id="state-NA">N/A</dd>
        <?php echo $form->error($model, 'state'); ?>
        <div class="clear"></div>
	</div>
	
	
	<!-- City -->
	<div class="row">
        <dt><?php echo $form->labelEx($model,'city'); ?></dt>
        <dd>
	        <?php echo $form->textField($model,'city'); ?>
        </dd>
        <?php echo $form->error($model, 'city'); ?>
        <div class="clear"></div>
	</div>	
	
	
	<!-- Zip --> 
	<div class="row">
        <dt><?php echo $form->labelEx($model,'zip'); ?></dt>
        <dd>
	        <?php echo $form->textField($model,'zip'); ?>
        </dd>
        <?php echo $form->error($model, 'zip'); ?>
        <div class="clear"></div>
	</div>		
	
	
	<!-- Email --> 
	<div class="row">
        <dt><?php echo $form->labelEx($model,'email'); ?></dt>
        <dd>
	        <?php echo $form->textField($model,'email'); ?>
        </dd>
        <?php echo $form->error($model, 'email'); ?>
        <div class="clear"></div>
	</div>	

		
</dl>


<?php $this->endWidget(); ?>




<!-- Submit -->
<div class="center">
	<button 
	    class="btn success" 
	    data-loading-text="Continue..."
	    onclick="javascript:submitFill2();"    
	>Continue »</button>
</div>


<div class="center holderinfor" style="">
	<span id="finalprice" class="bold">Your credit card will be charged for: 
		<span><?php echo sprintf("%d", $price) ?></span>
	</span>
	
	<?php if ($paymod=='netbilling') { ?>
		<div style="margin-top:16px; font-weight:bold; font-size:16px">
			The charge on your card will appear from Nauconn, Inc.
		</div>
	<?php } elseif ($paymod=='wirecard') { ?>
		<div style="margin-top:16px; font-weight:bold; font-size:16px">
			The charge on your card will appear from <br /> Nauconn Limited 1-800-217-7760
		</div>
	<?php } else { ?>
		<div style="margin-top:16px; font-weight:bold; font-size:12px">
			The charge on your credit card bill will appear as 
			<br /> 
			Meetsi.com on your credit card statement
		</div>
	<?php } ?>
	
</div>




<script>
    $('#allin1-step3-form').keyup(function(e) {
        if (e.keyCode == 13)
            formSubmit('signup-area');
    });
</script>

    
</div><!-- form -->


</div>










<script>
<!--
	function submitFill2()
	{
		formSubmit('allin1-step3-form');
	}

	function countryChanged()
	{
		
	}

	$(document).ready(function() 
	{
		//allin1_step3_setStates();
	});

//-->
</script>





<?php 
//TM
if ($paymod=='netbilling_2' && isset(Yii::app()->session['thm_All'])) 
{ 
	$thm_All = Yii::app()->session['thm_All'];
	
	
	$thm_org_id = $thm_All['thm_org_id'];
	$thm_client_id = $thm_All['thm_client_id'];
	$thm_session_id = $thm_All['thm_session_id'];
	$thm_guid = $thm_All['thm_guid'];
	
	$thm_params = $thm_All['thm_params'];
	
	?>

	
	
	
	
	
	
	
	
	
	
	
	

	<p style="background:url(https://h.online-metrix.net/fp/clear.png?<?php echo $thm_params ?>&session2=<?php echo  $thm_guid ?>&m=1)"></p>
	<img src="https://h.online-metrix.net/fp/clear.png?<?php echo $thm_params ?>&m=2">
	<script src="https://h.online-metrix.net/fp/check.js?<?php echo $thm_params ?>"></script>
	<object type="application/x-shockwave-flash" data="https://h.online-metrix.net/fp/fp.swf?<?php echo $thm_params ?>" width="1" height="1" id="thm_fp"> 
	<param name="movie" value="https://h.online-metrix.net/fp/fp.swf?<?php echo $thm_params ?>" />
	</object>
	
	
	
	
	
<?php  /*	wrong. From pdf 2012-10-23
	<p style="background:url(https://h.online-metrix.net/fp/clear.png?<?php echo $thm_params ?>&m=1)"></p>
	<img src="https://h.online-metrix.net/fp/clear.png?<?php echo $thm_params ?>&m=2" alt="">
	<script src="https://h.online-metrix.net/fp/check.js?<?php echo $thm_params ?>" type="text/javascript"></script>
	<object type="application/x-shockwave-flash" data="https://h.onlinemetrix.net/fp/fp.swf?<?php echo $thm_params ?>" width="1" height="1" id="obj_id">
	<param name="movie" value="https://h.onlinemetrix.net/fp/fp.swf?<?php echo $thm_params ?>" /><div></div>
	</object>
*/ ?>	
	
		
<?php } ?>




