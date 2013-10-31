<div id="feedbackButton">
    <a 
        href="#" 
        <?php if (!DEMO) { ?> 
        data-controls-modal="sendFeedbackBox" 
        data-backdrop="static" 
        onclick="javascript:$('.videoRecorder iframe').hide();"
        <?php } ?>
         
    >
        <img src="/images/design/feedback.png" />
    </a>
</div>






<div id="sendFeedbackBox" class="modal hide fade form">

    <div class="modal-header">
        <a href="#" class="close" onclick="javascript:$('.videoRecorder iframe').show();" >&times;</a>
        <h3>Leave feedback</h3>
    </div>
    <div id="sendFeedbackStep1" class="modal-body">
<?php

    $form=$this->beginWidget('CActiveFormSw', array(
            'action'=>Yii::app()->createUrl('/ajax/feedbackMessage'),
            'id'    => 'feedbackForm',
            'enableAjaxValidation' => true,
            'enableClientValidation'=> true,
            'clientOptions' => array(
                'validationUrl'=>Yii::app()->createUrl('/ajax/feedbackMessage'),
                'validateOnSubmit' => true,
                'validateOnChange' => true,
                'afterValidate' => "js: function(form, data, hasError) {
                                                    //if no error in validation, send form data with Ajax
                                                    if (! hasError) {
                                                      
                                                      $('#FeedbackSendForm_action').val('doSend');
                                                      
                                                      $.ajax({
                                                        type: 'POST',
                                                        url: form[0].action,
                                                        data: $(form).serialize(),
                                                        success: function(ret) {
                                                            form[0].reset();
                                                            $('#sendFeedbackBox .btn').button('reset');
                                                            $('#sendFeedbackStep1').fadeOut(200, function(){
                                                                $('#sendFeedbackStep2').fadeIn(200);
                                                            });
                                                            //$('#sendFeedbackBox').modal('hide');
                                                        }
                                                      });                                                        
                                                    }
                                                    else
                                                       $('#sendFeedbackBox .btn').button('reset');
                                                    
                                                    $('#FeedbackSendForm_action').val('');
                                                    
                                                    return false;
                }"                
                
            ),
    )); 
    
    
    echo $form->hiddenField($model, 'action');
    echo $form->hiddenField($model, 'url');
    
    //$id_to = Yii::app()->secur->encryptID( $profile->getDataValue('id') );
    //echo $form->hiddenField($model, 'id_to', array('value'=>$id_to));
    
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
        <div id="FeedbackSendForm_body_count_sym" class="left-chars-counter">1000</div>
    </div>
    
    

    
<?php $this->endWidget(); ?>    


<div class="row submit">
    <button 
        class="btn" 
        data-loading-text="Sending..." 
        onclick="javascript:$('#sendFeedbackBox .btn').button('loading'); $('#sendFeedbackBox form').submit();" 
    >Send</button>
    <script type="text/javascript">
            $('#sendFeedbackBox .close').click(function () {
                feedbackFormClose();
            })                 
    </script>        
</div>

</div>



<div id="sendFeedbackStep2">
    Feedback sent!
    <br /><br /><br />
    <a class="btn" href="javascript:void(0)" onclick="javascript:feedbackFormClose();" >Close</a>
</div>

<script type="text/javascript">

    function feedbackFormClose()
    {
        $('#sendFeedbackBox').modal('hide'); 
        $('#sendFeedbackStep2').hide();
        $('#sendFeedbackStep1').show();
        $('#sendFeedbackBox .btn').button('reset');
        
        $('.videoRecorder iframe').show();
    }

</script>


</div>








