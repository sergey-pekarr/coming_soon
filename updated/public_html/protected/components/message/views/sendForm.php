<div id="sendMessageBox" class="modal hide fade form">

    <div class="modal-header">
        <a href="#" class="close">&times;</a>
        <h3>Send message</h3>
    </div>
    <div id="sendStep1" class="modal-body">
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
                                                            $('#sendStep1').fadeOut(200, function(){
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
            <?php echo $form->textField($model,'subject', array('placeholder'=>'Subject', 'maxlength'=>'25', 'style'=>'')); ?>
        </dd>
        <?php echo $form->error($model,'subject') ?>
        <div class="clear"></div>
    </div>
    
    <div class="row">
        <dd>
            <?php /* <textarea style="width: 100%; height: 170px;" id="messageBody" onkeyup="javascript:textchange(this.id,1000)" title="Write something..."></textarea> */ ?>
            <?php echo $form->textArea($model,'body', array('onkeyup'=>'javascript:textchange(this.id,1000)', 'placeholder'=>'Write something...', 'style'=>'height: 170px;')); ?>
        </dd>
        <?php echo $form->error($model,'body') ?>
        <div class="clear"></div>
        <div id="MessageSendForm_body_count_sym" class="left-chars-counter">1000</div>
    </div>
    
    

    
<?php $this->endWidget(); ?>    


<div class="row submit">
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

<script type="text/javascript">

    function messageFormClose()
    {
        $('#sendMessageBox').modal('hide');//$('#sendMessageBox').dialog('close'); 
        $('#sendStep2').hide();
        $('#sendStep1').show();
        $('#sendMessageBox .btn').button('reset');
    }

</script>

</div>








