
<div id="sendReportBugBox" class="modal hide fade form">

    <div class="modal-header">
        <a href="#" class="close" onclick="javascript:$('.videoRecorder iframe').show();" >&times;</a>
        <h3>Report Bug</h3>
    </div>
    <div id="sendReportBugStep1" class="modal-body">
<?php

    $form=$this->beginWidget('CActiveFormSw', array(
            'action'=>Yii::app()->createUrl('/ajax/reportBugMessage'),
            'id'    => 'reportBugForm',
            'enableAjaxValidation' => true,
            'enableClientValidation'=> true,
            'clientOptions' => array(
                'validationUrl'=>Yii::app()->createUrl('/ajax/reportBugMessage'),
                'validateOnSubmit' => true,
                'validateOnChange' => true,
                'afterValidate' => "js: function(form, data, hasError) {
                                                    //if no error in validation, send form data with Ajax
                                                    if (! hasError) {
                                                      
                                                      $('#ReportBugSendForm_action').val('doSend');
                                                      
                                                      $.ajax({
                                                        type: 'POST',
                                                        url: form[0].action,
                                                        data: $(form).serialize(),
                                                        success: function(ret) {
                                                            form[0].reset();
                                                            $('#sendReportBugBox .btn').button('reset');
                                                            $('#sendReportBugStep1').fadeOut(200, function(){
                                                                $('#sendReportBugStep2').fadeIn(200);
                                                            });
                                                            //$('#sendReportBugBox').modal('hide');
                                                        }
                                                      });                                                        
                                                    }
                                                    else
                                                       $('#sendReportBugBox .btn').button('reset');
                                                    
                                                    $('#ReportBugSendForm_action').val('');
                                                    
                                                    return false;
                }"                
                
            ),
    )); 
    
    
    echo $form->hiddenField($model, 'action');
    echo $form->hiddenField($model, 'url');
    
    //$id_to = Yii::app()->secur->encryptID( $profile->getDataValue('id') );
    //echo $form->hiddenField($model, 'id_to', array('value'=>$id_to));
    
?>
    
    
    <div class="rowForm">
        <dd>
            <?php /* <input id="messageSubject" value="" placeholder="Subject" maxlength="25" style="width: 100%;"  /> */ ?>
            <?php echo $form->textField($model,'subject', array('placeholder'=>'Subject', 'maxlength'=>'25', 'style'=>'')); ?>
        </dd>
        <?php echo $form->error($model,'subject') ?>
        <div class="clear"></div>
    </div>
    
    <div class="rowForm">
        <dd>
            <?php /* <textarea style="width: 100%; height: 170px;" id="messageBody" onkeyup="javascript:textchange(this.id,1000)" title="Write something..."></textarea> */ ?>
            <?php echo $form->textArea($model,'body', array('onkeyup'=>'javascript:textchange(this.id,1000)', 'placeholder'=>'Write something...', 'style'=>'height: 170px;')); ?>
        </dd>
        <?php echo $form->error($model,'body') ?>
        <div class="clear"></div>
        <div id="ReportBugSendForm_body_count_sym" class="left-chars-counter">1000</div>
    </div>
    
    

    
<?php $this->endWidget(); ?>    


<div class="rowForm submit">
    <button 
        class="btn" 
        data-loading-text="Sending..." 
        onclick="javascript:$('#sendReportBugBox .btn').button('loading'); $('#sendReportBugBox form').submit();" 
    >Send</button>
    <script type="text/javascript">
            $('#sendReportBugBox .close').click(function () {
                reportBugFormClose();
            })                 
    </script>        
</div>

</div>



<div id="sendReportBugStep2">
    Report bug sent!
    <br /><br /><br />
    <a class="btn" href="javascript:void(0)" onclick="javascript:reportBugFormClose();" >Close</a>
</div>

<script type="text/javascript">

    function reportBugFormClose()
    {
        $('#sendReportBugBox').modal('hide'); 
        $('#sendReportBugStep2').hide();
        $('#sendReportBugStep1').show();
        $('#sendReportBugBox .btn').button('reset');
        
        $('.videoRecorder iframe').show();
    }

</script>


</div>








