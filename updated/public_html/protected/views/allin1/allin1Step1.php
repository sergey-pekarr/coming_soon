<h1>Sign up for Meetsi.com!</h1>

<div class="allin1-box">

<div id="allin1-step1-form" class="form">


<?php 
    
    $labels = $model->attributeLabels();
    
    $form=$this->beginWidget('CActiveFormSw', array(
            'action'=>Yii::app()->createUrl('/allin1'),
            'id' => 'Allin1Step1Form',
            'enableAjaxValidation' => true,            
            //'enableClientValidation'=> true,           
            'clientOptions' => array(
                //'validationUrl'=>Yii::app()->createUrl('UserRegistration/step1Validation'),
                'validateOnSubmit' => true,
                'validateOnChange' => true,
                'afterValidate' => "js: function(form, data, hasError) {
                                                    //if no error in validation, send form data with Ajax

                                                    
                                                    if ( typeof(data['Allin1Step1Form_ip'])!='undefined')
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
															
                                                        	window.location.href='/allin1/step2';
															
                                                        }
                                                      });                                                        
                                                    }
                                                    else
                                                       $('#allin1-step1-form .btn').button('reset');
                                                    return false;
                }"                
                
            ),
    )); 
?>
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
                            'username',
                            array('value'=>Yii::app()->user->data('username'))
                    );?>
        </dd>
		<?php echo $form->error($model,'username'); ?>
        <div class="clear"></div>
	</div>

    <div class="row">
        <dt><?php echo $form->labelEx($model,'password'); ?></dt>
        <dd>
            <?php echo $form->passwordField($model,'password', array(/*'placeholder'=>$labels['password'],*/ 'onfocus'=>'javascript:$(".password2").slideDown(200)' /*, 'onclick'=>'javascript:$(".password2").slideDown(200)'*/)); ?>
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






	<div class="row location country">
		<?php echo $form->hiddenField($model,'location_id', array( 'value'=>0 ));//echo $form->hiddenField($model,'location_id', array( 'value'=>$location_id )); ?>

		<dt><?php echo $form->labelEx($model,'country'); ?></dt>
		<dd>
            <?php echo $form->dropDownList(
                        $model,
                        'country', 
                        $model->countries,//Yii::app()->location->getCountriesList(), 
                        array(
                            //'options' => array($location['country']=>array('selected'=>true)),
                            'onChange'=> 'javascript: allin1_onChangeCountry(this);',//'onChange'=> 'javascript: countryAllin1 = this.value; $("input#Allin1Step1Form_city").val("").focus()',
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
                        $model->states,//Yii::app()->location->getStatesList('US'), 
                        array(
                            //'options' => array($location['state']=>array('selected'=>true)),
                            //'onChange'=> 'javascript: countryAllin1 = this.value; $("input#Allin1Step1Form_city").val("").focus()',
                            //'onChange'=> 'javascript:updateStates(this.value)',
                        )
                   ); ?>
        </dd>
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





</dl> 

<?php $this->endWidget(); ?>

<div class="center">
	<button 
	    class="btn success" 
	    data-loading-text="Continue..."
	    onclick="javascript:formSubmit('allin1-step1-form');"    
	>Continue »</button>
</div>

    
</div><!-- form -->


</div>
