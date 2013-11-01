<?php 
$cookieUsername = (isset(Yii::app()->request->cookies['username'])) ? Yii::app()->request->cookies['username']->value : '';

$username = ($cookieUsername) ? Yii::app()->secur->decryptByYii( $cookieUsername ) : '';


?>




<?php if (!$username) { ?>
    <h1>
        Video profiles connect you instantly with the people around you.

        <br />

        <span>
            Discover new friends, flirt, network and have fun. 
            <?php /*  <a href="javascript:void(0)" onclick="javascript:signin()">Try it now</a> */ ?>
        </span>
    </h1>
<?php } ?>

<div id="reg_login_form" class="form" <?php if (!$username) { ?>style="display: none;"<?php } ?> >

    <?php if (!$username) { ?>
        <h2 style="text-align: left;">Sign in</h2>
    <?php } else { ?>
        <h2>
            Welcome back, <?php echo $username ?>
            <a href="javascript:void(0);" onclick="javascript:coockieUsernameDelete();">Not <?php echo $username; ?>?</a>
        </h2>        
    <?php } ?>
    
    
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
        <dd><?php echo $form->textField(
            $model,
            'username',
            ( ($username) ? array('value'=>$username) : array('placeholder'=>'username') )
        ); ?>
        </dd>
        <?php echo $form->error($model,'username'); ?>

		<dd><?php echo $form->passwordField(
            $model,
            'password',
            array('placeholder'=>'password')
        ); ?>
        </dd>
        <?php echo $form->error($model,'password'); ?>
    
        <dd><?php echo $form->checkBox($model,'rememberMe'); ?></dd>
        <dt><?php echo $form->labelEx($model,'rememberMe'); ?></dt>		
</dl>

<?php $this->endWidget(); ?>

<button class="btn" data-loading-text="wait..." onclick="javascript:formSubmit('reg_login_form');" >Sign In</button>

<script type="text/javascript">
        $('#reg_login_form').keyup(function(e) {
            if (e.keyCode == 13)
                 formSubmit('reg_login_form');
        });
</script>


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

