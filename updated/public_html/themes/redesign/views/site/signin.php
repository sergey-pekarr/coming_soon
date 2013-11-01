
<?php if ( Yii::app()->user->hasFlash('regAlreadyRegistered') ) { ?>
	<div class="alert alert-error bold center" style="margin-top:40px">
		<?php echo Yii::app()->user->getFlash('regAlreadyRegistered') ?>
	</div>
<?php } ?>

<?php //$this->widget('application.components.UserLoginFormWidget', array('type'=>2)); ?>
<?php $this->renderPartial('userlogin2', array('model'=>$model)); ?>

<h2 class="already_member" style="margin: 10px 0 20px 0; padding: 0; text-align: center;">
    Not a member? Click 
    <a class="color3" href="<?php echo Yii::app()->createAbsoluteUrl('/site/registration') ?>" >here</a>
    to register
</h2>



