<div id="reg_login_form" class="form hide">
<?php 
    
    $form=$this->beginWidget('CActiveFormSw', array(
    	'action'=>Yii::app()->createUrl('site/login'),
            'id' => 'login-form',
            'enableAjaxValidation' => true,
            'clientOptions' => array(
                'validateOnSubmit' => true,
                'validateOnChange' => false,
                'afterValidate' => "js: function(form, data, hasError) {
                                                    if (!hasError) {
                                                      $.ajax({
                                                        type: 'POST',
                                                        url: form[0].action,
                                                        data: $(form).serialize(),
                                                        success: function(ret) {
                                                            $('#reg_login_form .btn').addClass('success');
                                                                eval(ret);//window.location.href=ret;//window.location.reload();
                                                        }
                                                      }); 
                                                    }
                                                    else
                                                       $('#reg_login_form .btn').button('reset');
                                                    return false;
                }"
            ),
    )); 
?>
<dl>
        <dt><?php echo $form->label($model,'username') ?></dt>
        <dd><?php echo $form->textField(
            $model,
            'username'
            //,( ($username) ? array('value'=>$username) : array('placeholder'=>'username') )
        ); ?>
        </dd>
        <?php //echo $form->error($model,'username'); ?>
        
		<dt><?php echo $form->label($model,'password') ?></dt>
        <dd><?php echo $form->passwordField(
            $model,
            'password'
            //,array('placeholder'=>'password')
        ); ?>
        </dd>
        <?php //echo $form->error($model,'password'); ?>

<?php /*        
        <div class="clear"></div>

        <br />
    
        <dt> <label>Remember</label></dt>
        <dd><?php echo $form->checkBox($model,'rememberMe'); ?></dd>
        
        <div class="clear"></div>
		<br /><br />
*/?>		
</dl>

<?php $this->endWidget(); ?>

<button class="btn" data-loading-text="wait..." onclick="javascript:formSubmit('reg_login_form');" >Login</button>

<?php /*
<div class="center" style="width: 200px; margin: 10px auto 0 auto;">
    <?php Yii::app()->eauth->renderWidget(); ?>
</div> 
*/ ?>

<script>
        $('#reg_login_form').keyup(function(e) {
            if (e.keyCode == 13)
                 formSubmit('reg_login_form');
        });
</script>

<div class="clear"></div>
   



</div>


