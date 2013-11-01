<div id="sendMessageBox" class="reply form">

    <div id="sendStep1">
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
                                                            setTimeout('window.location.href=window.location.href',3000);
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
                <?php echo $form->textArea($model,'body', array('onkeyup'=>'javascript:textchange(this.id,1000)', 'placeholder'=>'Write something...', 'style'=>'height: 60px;')); ?>
            </dd>
            <?php echo $form->error($model,'body') ?>
<?php $this->endWidget(); ?> 

            <div class="clear"></div>
            
            <div id="MessageSendForm_body_count_sym" class="left-chars-counter">1000</div>
            
            <div class="submit">
                <button 
                    class="btn" 
                    data-loading-text="Sending..." 
                    onclick="javascript:$('#sendMessageBox .btn').button('loading'); $('#sendMessageBox form').submit(); return false;" 
                >Send</button>
            </div>            
            
            <div class="clear"></div>
            
        </div>
    
    

    
  



    
    </div>
    
    
    
    <div id="sendStep2">
        Message sent!
    </div>
    
    <script type="text/javascript">
    
        function messageFormClose()
        {
            //$('#sendMessageBox').modal('hide');//$('#sendMessageBox').dialog('close'); 
            $('#sendStep2').hide();
            $('#sendStep1').show();
            $('#sendMessageBox .btn').button('reset');
        }
    
    </script>

</div>








