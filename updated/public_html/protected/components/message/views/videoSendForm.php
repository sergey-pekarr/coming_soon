<div id="sendVideoMessageBox" class="form">

    <div id="sendStep1">
<?php
    $form=$this->beginWidget('CActiveFormSw', array(
            'action'=>Yii::app()->createUrl('/profile/UserPrivateMessageVideo'),
            'id'    => 'messageForm',
            'enableAjaxValidation' => true,
            'enableClientValidation'=> true,
            'clientOptions' => array(
                'validationUrl'=>Yii::app()->createUrl('/profile/UserPrivateMessageVideo'),
                'validateOnSubmit' => true,
                'validateOnChange' => true,
                'afterValidate' => "js: function(form, data, hasError) {
                                                    //if no error in validation, send form data with Ajax
                                                    if (! hasError) {
                                                      
                                                      //$('#MessageSendForm_action').val('doSend');
                                                      
                                                      $.ajax({
                                                        type: 'POST',
                                                        url: form[0].action,
                                                        data: $(form).serialize(),
                                                        success: function(ret) {
                                                            form[0].reset();
                                                            $('#sendVideoMessageBox .btn').button('reset');
                                                            $('#sendStep1').fadeOut(200, function(){
                                                                $('#sendStep2').fadeIn(200);
                                                                
                                                                $('.videoMessageBox').remove();
                                                                $('.bodyMessageBox').css('float','none');
                                                                $('.bodyMessageBox').css('width','100%');
                                                                //$('.bodyMessageBox').css('height','480px');
                                                                
                                                            });
                                                            //$('#sendVideoMessageBox').modal('hide');
                                                        }
                                                      });                                                        
                                                    }
                                                    else
                                                       $('#sendVideoMessageBox .btn').button('reset');
                                                    
                                                    //$('#MessageSendForm_action').val('');
                                                    
                                                    return false;
                }"                
                
            ),
    )); 
    
    
    //echo $form->hiddenField($model, 'action');
    
    $id_to = Yii::app()->secur->encryptID( $profileTo->getDataValue('id') );
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
            <?php echo $form->textArea($model,'body', array('onkeyup'=>'javascript:textchange(this.id,1000)', 'placeholder'=>'Write something...'/*, 'style'=>'height: 170px;'*/)); ?>
        </dd>
        <?php echo $form->error($model,'body') ?>
        <div class="clear"></div>
        <div id="MessageVideoSendForm_body_count_sym" class="left-chars-counter">1000</div>
    </div>
    
    <div class="row streamName">
        <?php
        echo $form->hiddenField($model, 'streamName'/*, array('value'=>'videoStream_2962_1323873827546_894')*/);
        echo $form->error($model,'streamName');
        ?>
    </div>    

    
<?php $this->endWidget(); ?>    


<div class="row submit">
    <button 
        class="btn" 
        data-loading-text="Sending..." 
        onclick="javascript:$('#sendVideoMessageBox .btn').button('loading'); $('#sendVideoMessageBox form').submit();" 
    >Send</button>
       
</div>

</div>



<div id="sendStep2">
    Message sent!
    <br /><br /><br />
    <a href="<?php echo SITE_URL.'/profile/'.Yii::app()->secur->encryptID($profileTo->getDataValue('id')) ?>" >Return to <?php echo $profileTo->getDataValue('username') ?>'s profile</a>
</div>



</div>

<?php /* 
<a href='javascript:void(0);' onclick='javascript:$("#MessageVideoSendForm_streamName").val("streamName"); $("#MessageVideoSendForm_streamName").change();'>SetStreamName</a>
*/ ?>





