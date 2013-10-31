<div id="reg_login_form" class="signin2 bgRadBox form">

    <h2>Login</h2>
    
<?php

    //echo '<div class="alert alert-error">';
    //echo CHtml::errorSummary($model);
    //echo '</div>';

    $form=$this->beginWidget('CActiveFormSw', array(
    	'action'=>Yii::app()->createUrl('old/site/login'),
            'id' => 'login-form',
            'enableAjaxValidation' => false,
            'clientOptions' => array(
                'validateOnSubmit' => false,
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
        <?php echo $form->error($model,'username'); ?>
        
        <div class="clear"></div>
        <br />
        
		<dt><?php echo $form->label($model,'password') ?></dt>
        <dd><?php echo $form->passwordField(
            $model,
            'password'
            //,array('placeholder'=>'password')
        ); ?>
        </dd>
        <?php echo $form->error($model,'password'); ?>
        
        <div class="clear"></div>
        <br />
    
        <dt> <label>Remember</label></dt>
        <dd><?php echo $form->checkBox($model,'rememberMe'); ?></dd>
        
        <div class="clear"></div>
		<br /><br />
</dl>

    <div class="clear"></div>

    <div class="center">
        <input type="submit" class="btn" data-loading-text="wait..." value="Sign In">
    </div>
<?php $this->endWidget(); ?>



<?php /*

<h3 class="or-separator" style="margin-top: 20px;">or</h3>
    
<div class="center" style="width: 200px; margin: 10px auto 0 auto;">
    <?php 
    if(Yii::app()->user->hasFlash('facebookError')) { ?>
        <div class="flash-error">    
            <?php echo Yii::app()->user->getFlash('facebookError'); ?>
        </div>            
    <?php  
    } else { 
        $this->widget('application.components.facebook.FacebookLoginWidget');
    }
    ?>
</div>    
*/ ?>



</div>


<div class="center" style="margin-top: 40px">
	<a class="btn" href="user/remindPassword">Forgot password</a>
</div>


















<?php /* 

<div id="reg_login_form" class="modal hide fade form">
    <div class="modal-header">
        <a href="#" class="close">&times;</a>
        <h3>Sign In</h3>
    </div>
    <div class="modal-body">

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
                                                            window.location.reload();
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
	<div class="row">
		<dt><?php echo $form->labelEx($model,'username'); ?></dt>
        <dd><?php echo $form->textField($model,'username'); ?></dd>
        <?php echo $form->error($model,'username'); ?>
        <div class="clear"></div>
	</div>
    

	<div class="row">
		<dt><?php echo $form->labelEx($model,'password'); ?></dt>
		<dd><?php echo $form->passwordField($model,'password'); ?></dd>
        <?php echo $form->error($model,'password'); ?>
        <div class="clear"></div>		
	</div>
    
	<div class="row rememberMe">
		<dt><?php echo $form->label($model,'remember me'); ?></dt>
        <dd><?php echo $form->checkBox($model,'rememberMe'); ?></dd>		
        <div class="clear"></div>
		<?php //echo $form->error($model,'rememberMe'); ?>
	</div>
</dl>

<?php $this->endWidget(); ?>

<div class="row center">
    <button class="btn" data-loading-text="wait..." onclick="javascript:formSubmit('reg_login_form');" >Sign In</button>
    <script type="text/javascript">
        $('#reg_login_form').keyup(function(e) {
            if (e.keyCode == 13)
                 formSubmit('reg_login_form');
        });
    </script>
</div>

    </div>
</div>


*/ 

