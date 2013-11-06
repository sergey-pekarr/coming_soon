<div id="signup-area" class="form">


<?php 
    
    $labels = $model->attributeLabels();
    
    $form=$this->beginWidget('CActiveFormSw', array(
            'action'=>Yii::app()->createUrl('/UserRegistration/step1'),
            //'id' => 'validation',
            'enableAjaxValidation' => true,            
            //'enableClientValidation'=> true,           
            'clientOptions' => array(
                'validationUrl'=>Yii::app()->createUrl('/UserRegistration/step1Validation'),
                'validateOnSubmit' => true,
                'validateOnChange' => true,
                'afterValidate' => "js:function(form, data, hasError) {
                        //if no error in validation, send form data with Ajax

                        
                        if ( typeof(data['UserRegistrationForm_ip'])!='undefined')
                        {
                        	window.location.href='/site/login';
                        	return false;
                        }
                        		                                                     
                        
                        
                        if (! hasError) {
                          $.ajax({
                            type: 'POST',
                            url: form[0].action,
                            data: $(form).serialize(),
                            success: function(ret) {
                            	regStep2FormBoxLoad();//window.location.reload();//window.location.href='/site/registrationStep2';
                            }
                          });                                                        
                        }
                        else
                        {
                        	$('#signup-area .btn').button('reset');
                        	$('input.submit').removeAttr('disabled');
                        }
                           
                        return false;
                    }"
                
            ),
    )); 
?>

<dl>



	<div class="row birthday" >
		<dt><?php echo $form->labelEx($model,'birthday'); ?></dt>
        
        <dd>
        <?php echo $form->hiddenField($model,'birthday'); ?>
        
        <?php
            /*echo $form->dropDownList($model,'birthday[year]', Yii::app()->helperProfile->getYear(true));
            echo $form->dropDownList($model,'birthday[month]', Yii::app()->helperProfile->getMonth(true));
            echo $form->dropDownList($model,'birthday[day]', Yii::app()->helperProfile->getDays(true));*/
            echo $form->textField($model, 'birthday');
        ?>
        </dd>
        <?php echo $form->error($model,'birthday'); ?>
        <div class="clear"></div>
	</div>
<?php /*
    <div class="textInput">
        <dt><?php echo $form->labelEx($model,'firstName'); ?></dt>
        <dd>
            <?php echo $form->textField($model,'firstName'/*, array('placeholder'=>$labels['firstName'])*//*); ?>
        </dd>
        <?php echo $form->error($model,'firstName'); ?>
        <div class="clear"></div>
    </div>

    <div class="textInput">
        <dt><?php echo $form->labelEx($model,'lastName'); ?></dt>
        <dd>
            <?php echo $form->textField($model,'lastName'/*, array('placeholder'=>$labels['lastName'])*//*); ?>
        </dd>
        <?php echo $form->error($model,'lastName'); ?>
        <div class="clear"></div>
    </div>
*/ ?>      

    <div class="row">
        <dt><?php echo $form->labelEx($model,'email'); ?></dt>
        <dd>
            <?php echo $form->textField($model,'email', array(/*'placeholder'=>$labels['email'], */ /*'onfocus'=>'javascript:$(".email2").slideDown(200);'*/ )); ?>
        </dd>
        <?php echo $form->error($model,'email'); ?>
        <div class="clear"></div>
    </div>
    
    
<?php /*    
    <div class="row email2">
        <dt><?php echo $form->labelEx($model,'email2'); ?></dt>
        <dd>
            <?php echo $form->textField($model,'email2'/*, array('placeholder'=>$labels['email2'])*//*); ?>
        </dd>
        <?php echo $form->error($model,'email2'); ?>
        <div class="clear"></div>
    </div>
*/ ?>

    <div class="row">
        <dt><?php echo $form->labelEx($model,'password'); ?></dt>
        <dd>
            <?php echo $form->passwordField($model,'password', array(/*'placeholder'=>$labels['password'],*/ 'onfocus'=>'javascript:$(".password2").slideDown(200)' /*, 'onclick'=>'javascript:$(".password2").slideDown(200)'*/)); ?>
		</dd>
        <?php echo $form->error($model,'password'); ?>
        <div class="clear"></div>
    </div>
<?php /*     
    <div class="row password2" >
		<dt><?php echo $form->labelEx($model,'password2'); ?></dt>        
        <dd>
            <?php echo $form->passwordField($model,'password2'); ?>
        </dd>
		<?php echo $form->error($model,'password2'); ?>
        <div class="clear"></div>
    </div>
*/ ?>
</dl> 
    <?php 
/*  
    //echo CHtml::submitButton('', array('id'=>'submit', 'class' => 'submit-home')); ?>
    <?php echo CHtml::submitButton(
        'Continue', 
        array(
            //'id'=>'submit', 
            'class' => 'btn success',
            'data-loading-text'=>'Continue...',
        )
    ); 
    //<button class="btn" data-loading-text="loading stuff..." >Continue</button>
    ?>
    <script type="text/javascript">
        $(function() {
            var btn = $('#signup-area .btn').click(function () {
                btn.button('loading');
            })
        })     
    </script>
*/ ?>

<?php $this->endWidget(); ?>

<?php /* ?>
<button 
    class="btn success" 
    data-loading-text="Continue..."
    onclick="javascript:formSubmit('signup-area');"    
>Continue</button>
<?php */ ?>
<div class="center">
	<input type="button" class="black-button submit-button" onclick="javascript: $(this).attr('disabled', 'disabled'); formSubmit('signup-area');" value="Continue" />
</div>

<script>
    $('#signup-area').keyup(function(e) {
        if (e.keyCode == 13)
            formSubmit('signup-area');
    });
</script>

    
</div><!-- form -->


