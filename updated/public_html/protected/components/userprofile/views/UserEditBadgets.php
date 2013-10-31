<div class="form">
<?php
    $form=$this->beginWidget('CActiveFormSw', array(
    	'action'=>Yii::app()->createUrl('/profile/EditBadgetsTab'),
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
                                                            $('#badgetsEditTab .btn').button('success').addClass('success');
                                                            setTimeout(function() { $('#badgetsEditTab .btn').button('reset'); $('#badgetsEditTab *').removeClass('success'); }, 2000);
                                                        }
                                                      });
                                                    }
                                                    else
                                                        $('#badgetsEditTab .btn').button('reset');
                                                    
                                                    
                                                    return false;
                }" 
            ),
    ));
    
    

 
?>

<dl>

    <div class="row badgets">
        <dt><?php echo $form->labelEx($model,'badgets'); ?></dt>
        <dd>
            <?php echo $form->checkBoxList(
                        $model, 
                        'badgets', 
                        array('FB'=>"Facebook", 'TW'=>"Twitter", 'G+'=>"Google+"),
                        array('separator'=>'<br />')
            ); ?>
        </dd>
        <?php echo $form->error($model,'badgets'); ?>
        <div class="clear"></div>
    </div>





</dl>

<?php $this->endWidget();?>


<div class="submit">
    <button 
        class="btn" 
        data-loading-text='Saving...'
        data-success-text='Saved'
        onclick="javascript:formSubmit('badgetsEditTab');" 
    >Save</button>
</div>



</div>









