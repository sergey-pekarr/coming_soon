
<div id="sendMessageBox" class="form example-twitter" style="display: none; position: absolute; z-index: 100; top: 300px; left: 50%; margin-left: -306px; ">
    <div style="height: 10px;">
        <a href="javascript:void(0)" onclick="javascript:$('#sendMessageBox').hide();" class="close">&times;</a>
    </div>
    <div id="sendMessStep1">
<?php
    $form=$this->beginWidget('CActiveFormSw', array(
            'action'=>Yii::app()->createUrl('/profile/UserPrivateMessage'),
            'id'    => 'messageForm',
            'enableAjaxValidation' => true,
            'enableClientValidation'=> true,
            'clientOptions' => array(
                'validationUrl'=>Yii::app()->createUrl('/profile/UserPrivateMessage'),
                'validateOnSubmit' => true,
                'validateOnChange' => true,
                'afterValidate' => "js: function(form, data, hasError) {
                                                    //if no error in validation, send form data with Ajax
                                                    if (! hasError) {
                                                      
                                                      $('#MessageSendForm_action').val('doSend');
                                                      
                                                      $.ajax({
                                                        type: 'POST',
                                                        url: form[0].action,
                                                        data: $(form).serialize(),
                                                        success: function(ret) {
                                                            form[0].reset();
                                                            $('#sendMessageBox .btn').button('reset');
                                                            $('#sendMessStep1').fadeOut(200, function(){
                                                                $('#sendStep2').fadeIn(200);
                                                                
                                                            });
                                                            //$('#sendMessageBox').modal('hide');
                                                        }
                                                      });                                                        
                                                    }
                                                    else
                                                       $('#sendMessageBox .btn').button('reset');
                                                    
                                                    $('#MessageSendForm_action').val('');
                                                    
                                                    return false;
                }"                
                
            ),
    )); 
    
    
    echo $form->hiddenField($model, 'action');
    
    $id_to = Yii::app()->secur->encryptID( $profile->getDataValue('id') );
    echo $form->hiddenField($model, 'id_to', array('value'=>$id_to));
    
?>
    
    
    <div class="row">
        <dd>
            <?php /* <input id="messageSubject" value="" placeholder="Subject" maxlength="25" style="width: 100%;"  /> */ ?>
            <?php echo $form->textField($model,'subject', array('placeholder'=>'Subject', 'maxlength'=>'25', 'style'=>'background-color:#fff')); ?>
        </dd>
        <?php echo $form->error($model,'subject') ?>
        <div class="clear"></div>
    </div>
    
    <div class="row">
        <dd>
            <?php /* <textarea style="width: 100%; height: 170px;" id="messageBody" onkeyup="javascript:textchange(this.id,1000)" title="Write something..."></textarea> */ ?>
            <?php echo $form->textArea($model,'body', array('onkeyup'=>'javascript:textchange(this.id,1000)', 'placeholder'=>'Write something...', 'style'=>'height: 80px; background-color:#fff')); ?>
        </dd>
        <?php echo $form->error($model,'body') ?>
        <div class="clear"></div>
        <div id="MessageSendForm_body_count_sym" class="left-chars-counter">1000</div>
    </div>
    
    

    
<?php $this->endWidget(); ?>    


<div class="row submit" style="margin: 0 auto; padding: 0;">
    <button 
        class="btn" 
        data-loading-text="Sending..." 
        onclick="javascript:$('#sendMessageBox .btn').button('loading'); $('#sendMessageBox form').submit();" 
    >Send</button>
    <?php /* echo CHtml::submitButton(
            'Send', 
            array(
                'class' => 'btn',
                'data-loading-text'=>'Sending...',
            )
    ); */ ?>
        
    <script type="text/javascript">
            $('#sendMessageBox .close').click(function () {
                messageFormClose();
            })                 
    </script>        
</div>

</div>



<div id="sendStep2">
    Message sent!
    <br /><br /><br />
    <a class="btn" href="javascript:void(0)" onclick="javascript:messageFormClose();" >Close</a>
</div>
</div>


<script type="text/javascript">

    function messageFormClose()
    {
        $('#sendMessageBox').hide(); 
        $('#sendStep2').hide();
        $('#sendMessStep1').show();
        $('#sendMessageBox .btn').button('reset');
    }

</script>
<?php /**/ ?>










