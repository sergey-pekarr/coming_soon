<div class="form">
<?php


    $lists = Yii::app()->helperProfile->getPersonalValueList(); //$model->getPersonalValueList();
    /*foreach ($lists as $k=>$v)
    {
        $keys[] = $k;        
    }*/
    $keys = array(
        'height',
        'race',
        'religion',
        'hairColor',
        'eyeColor',
        'bodyType',
    );

    $form=$this->beginWidget('CActiveFormSw', array(
    	'action'=>Yii::app()->createUrl('/profile/EditAppearanceTab'),
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
                                                            $('#appearanceEditTab .btn').button('success').addClass('success');
                                                            setTimeout(function() { $('#appearanceEditTab .btn').button('reset'); $('#appearanceEditTab *').removeClass('success'); }, 2000);
                                                        }
                                                      });
                                                    }
                                                    else
                                                        $('#appearanceEditTab .btn').button('reset');
                                                    
                                                    
                                                    return false;
                }" 
            ),
    ));
    
    

 
?>

<?php //echo $form->hiddenField($model,'id', array('value'=>$profile->getDataValue('id'))); ?>

<dl>

    <?php 
        $personalData = $profile->getDataValue('personal');
        foreach ($keys as $k) { ?>
        <div class="row">
            <dt><?php echo $form->labelEx($model,$k); ?></dt>
            <dd>		
        		<?php echo $form->dropDownList(
                                $model,
                                $k, 
                                $lists[$k], 
                                array(
                                    //'id'=>$k,
                                    //'style'=>'width:160px;',
                                    'options' => array($personalData[$k] =>array('selected'=>true)),
                                )
                           ); ?>
            </dd>
            <?php echo $form->error($model,$k); ?>
            <div class="clear"></div>
        </div>
    <?php } ?>





</dl>

<?php $this->endWidget();?>


<div class="submit">
    <button 
        class="btn" 
        data-loading-text='Saving...'
        data-success-text='Saved'
        onclick="javascript:formSubmit('appearanceEditTab');" 
    >Save</button>
</div>



</div>









