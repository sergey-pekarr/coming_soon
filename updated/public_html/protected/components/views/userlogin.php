<div class="reg_login_form">
    <?php 
        
        $form=$this->beginWidget('CActiveFormSw', array(
        	'action'=>Yii::app()->createUrl('site/login'),
                'id' => 'login-form',
                'enableAjaxValidation' => true,
                'clientOptions' => array(
                    'validateOnSubmit' => true,
                    'validateOnChange' => false,
                    'afterValidate' => "
                    js: function(form, data, hasError) {
                        if (!hasError) {
                          $.ajax({
                            type: 'POST',
                            url: form[0].action,
                            data: $(form).serialize(),
                            success: function(ret) {
                                $('#reg_login_form .login-button').addClass('success');
                                    eval(ret);//window.location.href=ret;//window.location.reload();
                            }
                          }); 
                        }
                        else
                           $('#reg_login_form .login-button').button('reset');
                        return false;
                    }"
                ),
        )); 
    ?>
    <dl>
        <dd>
            <?php 
                echo $form->textField(
                    $model,
                    'username',
                    array('placeholder'=>'Username')
                ); 
            ?>
        </dd>
        <dd>
            <?php 
                echo $form->passwordField(
                    $model,
                    'password',
                    array('placeholder'=>'Password')
                ); 
            ?>
        </dd>
        <dd>
            <?php echo $form->checkBox($model,'rememberMe', array('class' => 'remember-me-checkbox')); ?>
        </dd>
        <dd>
            <label class="checkbox-label remember-me-label" for="LoginForm[rememberMe]">Remember</label>
        </dd>
        <dd>
            <button class="login-button black-button" data-loading-text="wait..." onclick="javascript:formSubmit('reg_login_form');" >Sign In</button>
        </dd>
        <div class="clear"></div>        
    </dl>

    <?php $this->endWidget(); ?>


    <script>
            $('#reg_login_form').keyup(function(e) {
                if (e.keyCode == 13)
                     formSubmit('reg_login_form');
            });
    </script>

    <div class="clear"></div>
   
</div>