<div class="bg_gradient_blue2" style="position: static; margin: 0; padding: 120px 0; width: auto; height: auto; ">

<?php
$this->pageTitle=Yii::app()->name . ' - Login';
$this->breadcrumbs=array(
	'Login',
);
?>


<div class="form" id="admin-login-form_box">

<?php $form=$this->beginWidget('CActiveFormSw', array(
	'id'=>'admin-login-form',
	//'enableClientValidation'=>true,
	'clientOptions'=>array(
		'validateOnSubmit'=>true,
        'afterValidate' => "js: function(form, data, hasError) {
                                                    if (!hasError) {
                                                      /*$.ajax({
                                                        type: 'POST',
                                                        url: form[0].action,
                                                        data: $(form).serialize(),
                                                        success: function(ret) {
                                                            $('#admin-login-form .btn').addClass('success');
                                                            //window.location.reload();
                                                        }
                                                      });*/ 
                                                      $('#admin-login-form .btn').addClass('success');
                                                    }
                                                    else
                                                       $('#admin-login-form .btn').button('reset');
                                                    return false;
        }"
	),
)); ?>
    <h1>Admin Login</h1>

	<div class="row">
		<?php //echo $form->labelEx($model,'username'); ?>
		<?php 
        echo $form->textField(
            $model,'username',array(
                'placeholder'=>'login'
            )
        ); ?>
		<?php echo $form->error($model,'username'); ?>
	</div>

	<div class="row">
		<?php //echo $form->labelEx($model,'password'); ?>
		<?php 
        echo $form->passwordField(
            $model,
            'password',
            array(
                'placeholder'=>'password'
            )
        ); ?>
		<?php echo $form->error($model,'password'); ?>
	</div>


    

<?php $this->endWidget(); ?>

	<div class="submit">
        <button class="btn" data-loading-text="wait..." onclick="javascript:formSubmit('admin-login-form_box');" >Login</button>
	</div>

    <script type="text/javascript">
        $('#admin-login-form_box').keyup(function(e) {
            if (e.keyCode == 13)
                 formSubmit('admin-login-form_box');
        });
    </script>

</div><!-- form -->

</div>


