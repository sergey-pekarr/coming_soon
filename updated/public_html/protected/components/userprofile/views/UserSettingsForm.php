<div class="box_rs1 form">
<div class="accountSettings" id="editSettingsFormBox">

    <h2>Account Settings</h2>

<?php
    $lists = Yii::app()->helperProfile->getPersonalValueList(); //$model->getPersonalValueList();
    foreach ($lists as $k=>$v)
    {
        $keys[] = $k;        
    }
    
    
    
    $form=$this->beginWidget('CActiveFormSw', array(
    	'action'=>Yii::app()->createUrl('profile/settings'),
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
                                                        data: $(form).serialize()
                                                        ,
                                                        success: function(ret) {
                                                            //window.location.href='{$urlAfter}';//dialogRegStep3();
                                                            $('#UserSettingsForm_passwordOld').val('');
                                                            $('#UserSettingsForm_passwordNew').val('');
                                                            //$('#UserSettingsForm_emailNew').val('');
                                                            
                                                            alertDialog('Settings updated.');
                                                        }
                                                      });
                                                    };
                                                    
                                                    $('.accountSettings .btn').button('reset');
                                                    
                                                    
                                                    return false;
                }" 
            ),
    ));
    
    

 
?>
<dl>
    <div class="row">
        <dt><?php echo $form->labelEx($model,'passwordOld'); ?></dt>    
        <dd>
            <?php echo $form->passwordField($model,'passwordOld', array("autocomplete"=>"off")); ?>
        </dd>
        <?php echo $form->error($model,'passwordOld'); ?>
    </div>

    <div class="row">
        <dt><?php echo $form->labelEx($model,'passwordNew'); ?></dt>    
        <dd>
            <?php echo $form->passwordField($model,'passwordNew'); ?>
        </dd>
        <?php echo $form->error($model,'passwordNew'); ?>    
    </div>    

    <div class="row">
        <dt><?php echo $form->labelEx($model,'emailNew'); ?></dt>    
        <dd>
            <?php echo $form->textField(
                $model,
                'emailNew',
                array(
                    //'disabled'=>'disabled',
                    //'class'=>'danger',
                    //'rel'=>"twipsy",
                    //'data-original-title'=>"Requires password",
                )
            ); ?>
            <?php /* <span class="hint">Requires password</span> */ ?>
        </dd>
        <?php echo $form->error($model,'emailNew'); ?>     
    </div>    

    <div class="row">
        <dt class="email-digest-title"><?php echo $form->labelEx($model,'email_notifications'); ?></dt>    
        <dd>
            <?php echo $form->radioButtonList(
                            $model, 
                            'email_notifications', 
                            array('0'=>'No email notification', 'Instant'=>'Instant', 'Digest'=>'Digest'),
                            array('separator'=>'<br />')
                ); ?>
        </dd>
        <?php echo $form->error($model,'email_notifications'); ?> 
    </div>
    
</dl>

<?php /*
<div class="submit">
    <?php echo CHtml::submitButton(
            'Save', 
            array(
                'class' => 'btn',
                'data-loading-text'=>'Saving...',
            )
        );
    ?>
        
    <script type="text/javascript">
        $(function() {
            var btn = $('.accountSettings .btn').click(function () {
                btn.button('loading');
            })
        })     
    </script>
</div> */ ?>

<?php $this->endWidget();?>

<div class="center" style="margin-top: 20px;">
    <button 
        class="btn" 
        data-loading-text="Saving..." 
        onclick="javascript:formSubmit('editSettingsFormBox');" 
    >Save</button>
    <script type="text/javascript">
        $('#editSettingsFormBox').keyup(function(e) {
            if (e.keyCode == 13)
                 formSubmit('editSettingsFormBox');
        });
    </script>
</div>

</div>

</div>