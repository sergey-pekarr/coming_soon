<div id="editMyProfile" class="personal-info form">

    <h2 id="editPersonalInfoTitle">Personal Information</h2>

<?php 
    
    
    $lists = Yii::app()->helperProfile->getPersonalValueList(); //$model->getPersonalValueList();
    foreach ($lists as $k=>$v)
    {
        $keys[] = $k;        
    }
    
    
    
    $form=$this->beginWidget('CActiveFormSw', array(
    	'action'=>Yii::app()->createUrl('profile/personal'),
            'id' => 'editProfileForm',
            'enableAjaxValidation' => true,
            'clientOptions' => array(
                'validateOnSubmit' => true,
                'validateOnChange' => false,
                'afterValidate' => "js: function(form, data, hasError) {
                                                    //if no error in validation, send form data with Ajax
                                                    if (! hasError) 
                                                    {
                                                        $('#editProfileForm .errorMessage').hide();
                                                    }
                                                    
                                                    $('#updateButton').show();
                                                    
                                                    return false;
                }" 
            ),
    ));
    
    

 
?>
<dl>
    
    <dt> </dt>    
    <dd>
        <?php echo $form->labelEx($model,'description', array('style'=>'font-weight:bold;')); ?>
        <?php echo CHtml::activeTextArea(
                            $model, 
                            'description'/*,
                            array('text'=>'asdasdasdasdasd')*/                            
        ); ?>         
    </dd>
    <?php echo $form->error($model,'description'); ?> 
    <div class="clear"></div>
    
<?php /*
    <dt><?php echo $form->labelEx($model,'looking_for_gender'); ?></dt>
    <dd>
        <?php echo $form->checkBoxList(
                        $model, 
                        'looking_for_gender', 
                        array('F'=>'Woman', 'M'=>'Man'),
                        array('separator'=>'&nbsp;&nbsp;&nbsp;')
            ); ?>
         <?php echo $form->error($model,'looking_for_gender'); ?>    
    </dd>
*/ ?>     


    <dt><?php echo $form->labelEx($model,'status'); ?></dt>
    <dd>
        <?php echo $form->dropDownList($model,'status', array('single'=>"Single", 'complicated'=>"It's Complicated", 'dating'=>"Dating", 'married'=>"Married")); ?>
    </dd>
    <?php echo $form->error($model,'status'); ?>
    <div class="clear"></div>
            
    <dt><?php echo $form->labelEx($model,'interesting'); ?></dt>
    <dd class="interesting">
        <?php echo $form->checkBoxList(
            $model, 
            'interesting', 
            array('F'=>"Friends", 'D'=>"Flirting, Dating", 'N'=>"Networking"),
            array('separator'=>'<br />')
        ); ?>
    </dd>
    <?php echo $form->error($model,'interesting'); ?>
    <div class="clear"></div>
    
    <?php foreach ($keys as $k) { ?>
    
        <dt><?php echo $form->labelEx($model,$k); ?>:</dt>
        <dd>		
    		<?php echo $form->dropDownList(
                            $model,
                            $k, 
                            $lists[$k], 
                            array(
                                //'id'=>$k,
                                //'style'=>'width:160px;',
                                'options' => array(Yii::app()->user->personal($k) =>array('selected'=>true)),
                            )
                       ); ?>
        </dd>
        <?php echo $form->error($model,$k); ?>
        <div class="clear"></div>
    <?php } ?>
    
    
    <dt><?php echo $form->labelEx($model,'birthday'); ?>:</dt>
		
    <dd>  
        <?php
            $bd = strtotime(Yii::app()->user->data('birthday'));
        ?>
        
        <?php echo $form->dropDownList(
                        $model,
                        'birthday[day]', 
                        Yii::app()->helperProfile->getDays(),
                        array(
                            'class'=>'birthday',
                            'options' => array(date("d",$bd) =>array('selected'=>true)),
                        )
        ); ?>
        <?php echo $form->dropDownList(
                        $model,
                        'birthday[month]', 
                        Yii::app()->helperProfile->getMonth(),
                        array(
                            'class'=>'birthday',
                            'options' => array(date("m",$bd) =>array('selected'=>true)),
                        )
        ); ?>
        <?php echo $form->dropDownList(
                        $model,
                        'birthday[year]', 
                        Yii::app()->helperProfile->getYear(),
                        array(
                            'class'=>'birthday year',
                            'options' => array(date("Y",$bd) =>array('selected'=>true)),
                        )
        ); ?>
        
        <br /><br />
        <?php echo $form->error($model,'birthday'); ?>
    </dd>
    
</dl>

    <div class="submit" style="height: 30px;">
        <?php echo CHtml::submitButton('Update Profile', array('class' => 'button', 'id'=>'updateButton', 'onclick'=>'$(this).hide();')); ?>
    </div>

<?php $this->endWidget();?>


</div>