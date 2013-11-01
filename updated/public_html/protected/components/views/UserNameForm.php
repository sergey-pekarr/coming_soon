<span id="profileUserName" class="form">

<?php 
    
    $form=$this->beginWidget('CActiveForm', array(
    	'action'=>Yii::app()->createUrl('profile/username'),
            //'id' => 'reg_step2_form',
            'enableAjaxValidation' => true,
            'clientOptions' => array(
                'validateOnSubmit' => true,
                'validateOnChange' => false,
                'afterValidate' => "js: function(form, data, hasError) {
                                                    //if no error in validation, send form data with Ajax
                                                    if (! hasError) 
                                                    {
                                                        usernameOld = $('#UserNameForm_username').val();
                                                        
                                                        $('#profileUserName').hide();
                                                        $('.info-username').html(usernameOld);
                                                        $('#user-username').show(); 
                                                    }
                                                    else
                                                    {
                                                        var text='';
                                                        for (var i=0; i<data['UserNameForm_username'].length; i++)
                                                        {
                                                            text += data['UserNameForm_username'][i]+'<br />';
                                                        }   
                                                        alertDialog(text);                                                     
                                                    }
                                                    return false;
                }" 
            ),
    )); 
?>

		<?php echo $form->textField(
                        $model,
                        'username',
                        array(
                            'style'=>'width:160px;',
                            'value'=>Yii::app()->user->data('username'),
                            //'style'=>'width:180px;',
                            'class'=>'text'
                        )
                    ); ?>
		<div style="display: none;">
            <?php echo $form->error($model,'username'); ?>
        </div>
    
        <?php //echo $form->error($model,'reg2'); ?>

        <?php echo CHtml::submitButton('Ok', array('class' => 'button')); ?>
        <input type="button" class="button inactive" href="javascript:void(0)" 
            onclick="javascript:
                $('.info-username').html(usernameOld);
                $('#profileUserName').hide();
                $('#user-username').show();
                $('#UserNameForm_username').val(usernameOld);                           
           "
           value="Cancel"
        />
	
<?php $this->endWidget(); ?>

<script type="text/javascript">
            
    var usernameOld = "<?php echo Yii::app()->user->data('username') ?>"; 
    
    $(document).ready(function() 
    {
        $("#user-username").click(function(){
           $("#user-username").hide();
           $("#profileUserName").show();
        });
    })
</script>


</span>