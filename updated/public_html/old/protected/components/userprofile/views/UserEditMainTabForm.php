<div class="form">
<?php
    $form=$this->beginWidget('CActiveFormSw', array(
    	'action'=>Yii::app()->createUrl('/profile/EditMainTab'),
            //'id' => 'editSettingsForm',
            'enableAjaxValidation' => true,
            'clientOptions' => array(
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
                                                            $('#mainEditTab .btn').button('success').addClass('success');
                                                            setTimeout(function() { $('#mainEditTab .btn').button('reset'); $('#mainEditTab *').removeClass('success'); }, 2000);
                                                        }
                                                      });
                                                    }
                                                    else
                                                        $('#mainEditTab .btn').button('reset');
                                                    
                                                    
                                                    return false;
                }" 
            ),
    ));
    
    

 
?>

<?php //echo $form->hiddenField($model,'id', array('value'=>$profile->getDataValue('id'))); ?>

<dl>


    <div class="row">
        <dt><?php echo $form->labelEx($model,'username'); ?></dt>    
        <dd>
            <?php echo $form->textField(
                $model,
                'username'
            ); ?>
        </dd>
        <?php echo $form->error($model,'username'); ?>
        <div class="clear"></div>        
    </div>

	<div class="row birthday" >
		<dt><?php echo $form->labelEx($model,'birthday'); ?></dt>
        
        <dd>
        <?php echo $form->dropDownList($model,'birthday[year]', Yii::app()->helperProfile->getYear()); ?>
        <?php echo $form->dropDownList($model,'birthday[month]', Yii::app()->helperProfile->getMonth()); ?>
        <?php echo $form->dropDownList($model,'birthday[day]', Yii::app()->helperProfile->getDays()); ?>
        </dd>        
        <?php echo $form->error($model,'birthday'); ?>
        <div class="clear"></div>
	</div>

    <div class="row">
        <dt><?php echo $form->labelEx($model,'status'); ?></dt>
        <dd>
            <?php echo $form->dropDownList($model, 'status', array('single'=>"Single", 'complicated'=>"It's Complicated", 'dating'=>"Dating", 'married'=>"Married")); ?>
        </dd>
        <?php echo $form->error($model,'status'); ?>
        <div class="clear"></div>
    </div>

    <div class="row interesting">
        <dt><?php echo $form->labelEx($model,'interesting'); ?></dt>
        <dd>
            <?php echo $form->checkBoxList(
                        $model, 
                        'interesting', 
                        array('F'=>"Friends", 'D'=>"Flirting, Dating", 'N'=>"Networking"),
                        array('separator'=>'<br />')
            ); ?>
        </dd>
        <?php echo $form->error($model,'interesting'); ?>
        <div class="clear"></div>
    </div>


    <div class="row description">
        <dt>
            <?php echo $form->labelEx($model,'description'); ?>
        </dt>        
        <div class="clear"></div>        
        <?php 
            echo CHtml::activeTextArea(
                $model, 
                'description',
                array(
                    'onkeyup'=>'javascript:textchange(this.id,255)',
                )
            ); ?>
        <?php echo $form->error($model,'description'); ?>
        <div class="clear"></div>
        
        <div id="UserEditMainTabForm_description_count_sym" class="left-chars-counter">255</div>
        <div class="clear"></div>
    </div> 




</dl>

<?php $this->endWidget();?>

    
<div class="submit">
        <button 
            class="btn" 
            data-loading-text='Saving...'
            data-success-text='Saved'
            onclick="javascript:formSubmit('mainEditTab');" 
        >Save</button>
</div>

</div>