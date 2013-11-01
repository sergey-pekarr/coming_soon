<?php
$this->pageTitle=Yii::app()->params['site']['nameFull'] . ' - Help';
$this->breadcrumbs=array(
	'Help',
);?>


<div class="helpContainer form" id="helpFormBox">

    <h2 class="help">Questions, Suggestions ?</h2>
    
    <?php if(Yii::app()->user->hasFlash('HelpSuccess')): ?>
        
        <?php /* 
        <div class="flash-success">
        	<?php echo Yii::app()->user->getFlash('HelpSuccess'); ?>
        </div> 
        */ ?>
        <div class="alert-message success">
            <p><strong><?php echo Yii::app()->user->getFlash('HelpSuccess'); ?></strong></p>
        </div>
        
    
    <?php else: ?>
        
        <?php $form = $this->beginWidget('CActiveFormSw', array(
            'id'=>'helpForm',
            //'focus'=>array($model,'email'),
        )); ?>
        
        
         
        <?php //echo CHtml::errorSummary($model); ?>
         
        <dl> 
            <div class="row">
            <dd>
                <?php echo $form->textField($model,'email', array(
                    'class'=>'email-address',
                    'placeholder'=>'Your Email',
                )); ?>
            </dd>
            <?php //echo $form->error($model,'email') ?>
            </div>
            
            <div class="row">
            <dd>
                <?php //echo CHtml::activeLabel($model,'message'); ?>
                <?php echo $form->textArea($model,'message', array(
                    'class'=>'message',
                    'onkeyup'=>'javascript:textchange(this.id,1000)',
                    'placeholder'=>'Message',
                )); ?>
            </dd>
             
            </div>
            <div id="HelpForm_message_count_sym" class="left-chars-counter">1000</div> 
            <div class="clear"></div>

        	<?php if(CCaptcha::checkRequirements()): ?>
        	<div class="row captcha">
        		<?php //echo $form->labelEx($model,'verifyCode'); ?>
        		<dt><?php $this->widget('CCaptcha'); ?></dt>
                <dd>
                    <?php echo $form->textField($model,'verifyCode',array('placeholder'=>'Verification Code')); ?>
            		<div class="hint">
                        Please enter the letters as they are shown in the image above.
                        Letters are not case-sensitive.
                    </div>            
        		</dd>
                <div class="clear"></div>
        
        		<?php echo $form->error($model,'verifyCode'); ?>
        	</div>
        	<?php endif; ?>
            
        </dl> 
         

     
        <?php $this->endWidget(); ?>
        
        <div class="submit">
            <button class="btn" data-loading-text="Sending..." onclick="javascript:formSubmit('helpFormBox');" >Send</button>
        </div>        
        
<?php endif; ?>
</div><!-- form -->
