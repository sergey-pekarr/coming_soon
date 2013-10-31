<?php echo CHtml::form(
                        Yii::app()->createUrl('img/upload'),
                        'post',
                        array(
                            'enctype'=>'multipart/form-data'
                        )
); ?>
    
<?php echo CHtml::activeFileField($model, 'image', array('accept'=>"image/*")); ?>
<br />
<?php echo CHtml::submitButton('Upload', array('class' => 'btn'/*, 'disabled'=>'disabled'*/)); ?>
<?php echo CHtml::error($model, 'Upload'); ?>

<?php echo CHtml::endForm(); ?>

