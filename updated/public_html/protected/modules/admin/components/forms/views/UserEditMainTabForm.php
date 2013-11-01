<div class="form">
<?php
    $form=$this->beginWidget('CActiveFormSw', array(
    	'action'=>Yii::app()->createUrl('/admin/users/EditMainTab'),
            //'id' => 'editSettingsForm',
            'enableAjaxValidation' => true,
            'clientOptions' => array(
                'validateOnSubmit' => true,
                'validateOnChange' => true,
                'afterValidate' => "js: function(form, data, hasError) {
                                                    //if no error in validation, send form data with Ajax
                                                    if (! hasError) {
                                                      $.ajax({
                                                        type: 'POST',
                                                        url: form[0].action,
                                                        data: $(form).serialize(),
                                                        success: function(ret) {
                                                            $('#mainEditTab .btn').button('success').addClass('success');
                                                            setTimeout(function() { $('#mainEditTab .btn').button('reset'); $('#mainEditTab *').removeClass('success'); }, 2000);
                                                        }
                                                      });
                                                    }
                                                    else
                                                        $('#mainEditTab .btn').button('reset');
                                                    
                                                    
                                                    return false;
                }" 
            ),
    ));
    
    

 
?>

<?php echo $form->hiddenField($model,'user_id', array('value'=>$model->user_id)); ?>

<table class="table" style="width:auto">
	<tr>
		
		<td>
		    <div class="row">
		        <dt><?php echo $form->labelEx($model,'username'); ?></dt>    
		        <dd><?php echo $form->textField( $model, 'username' ); ?></dd>
		        <?php echo $form->error($model,'username'); ?>
		        <div class="clear"></div>        
		    </div>

		    <div class="row">
		        <dt><?php echo $form->labelEx($model,'gender'); ?></dt>
		        <dd>
		        <?php echo $form->dropDownList(
		                        $model, 
		                        'gender',
		                        array('F'=>'F','M'=>'M','C'=>'C')
		        ); ?>
		        </dd>
		        <?php echo $form->error($model, 'gender'); ?>
		        <div class="clear"></div>
		    </div> 

		    <div class="row">
		        <dt><?php echo $form->labelEx($model,'looking_for_gender'); ?></dt>
		        <dd>
		        <?php echo $form->dropDownList(
		                        $model, 
		                        'looking_for_gender',
		                        array('M'=>'M', 'F'=>'F', 'C'=>'C', 'MF'=>'MF', 'MFC'=>'MFC')
		        ); ?>
		        </dd>
		        <?php echo $form->error($model, 'looking_for_gender'); ?>
		        <div class="clear"></div>
		    </div>
			
		    <div class="row <?php if ($model->promo=='1') { ?>hide<?php } ?>">
		        <dt>
		        	<?php //echo $form->labelEx($model,'email', array('style'=>'width:50px')); ?>
		        	Email
		        	&nbsp;
		        	<?php if ($model->emailApproved) { ?>
		        		<span class="label label-success">approved</span>
		        	<?php } else { ?>
		        		<span class="label">not approved</span>
		        	<?php } ?>
		        	
		        	<?php if ($model->emailBounced) { ?>
		        		<span class="label label-important">BOUNCED</span>
		        	<?php } ?>		        	
		        	
		        </dt>    
		        <dd><?php echo $form->textField( $model, 'email' ); ?></dd>
		        <?php echo $form->error($model,'email'); ?>
		        <div class="clear"></div>        
		    </div>
		    
		    
		    
		    
		    
		</td>
		
		<td>		
		    <div class="row <?php if ($model->promo=='1') { ?>hide<?php } ?>">
		        <dt><?php echo $form->labelEx($model,'password'); ?></dt>    
		        <dd><?php echo $form->textField( $model, 'password' ); ?></dd>
		        <?php echo $form->error($model,'password'); ?>
		        <div class="clear"></div>        
		    </div>

			<div class="row birthday" >
				<dt><?php echo $form->labelEx($model,'birthday'); ?></dt>
		        
		        <dd>
		        <?php //if ($model->birthday) { ?>
			        <?php echo $form->hiddenField($model,'birthday', array('value'=>implode('-',$model->birthday))); ?>
			        <?php echo $form->dropDownList($model,'birthday[year]', Yii::app()->helperProfile->getYear()); ?>
			        <?php echo $form->dropDownList($model,'birthday[month]', Yii::app()->helperProfile->getMonth()); ?>
			        <?php echo $form->dropDownList($model,'birthday[day]', Yii::app()->helperProfile->getDays()); ?>
		        <?php //} ?>
		        </dd>        
		        <?php echo $form->error($model,'birthday'); ?>
		        <div class="clear"></div>
			</div>
		    
		    <div class="row <?php if ($model->promo=='1') { ?>hide<?php } ?>">
		        <dt><?php echo $form->labelEx($model,'ip_signup'); ?></dt>    
		        <dd><?php echo $model->ip_signup ?></dd>
		        <div class="clear"></div>        
		    </div>
			
		    <div class="row <?php if ($model->promo=='1') { ?>hide<?php } ?>">
		        <dt><?php echo $form->labelEx($model,'joined'); ?></dt>    
		        <dd><?php echo date("Y-m-d", strtotime($model->joined)) ?></dd>
		        <div class="clear"></div>        
		    </div>			    

		    <div class="row <?php if ($model->promo=='1') { ?>hide<?php } ?>">
		        <dt><?php echo $form->labelEx($model,'affid'); ?></dt>    
		        <dd><?php echo $model->affid ?></dd>
		        <div class="clear"></div>        
		    </div>


		    <div class="row">
		        <dt>User ID</dt>    
		        <dd><?php echo $model->user_id ?></dd>
		        <div class="clear"></div>        
		    </div>		    
		    
		    
		</td>
		

		<td>
			<dt>Api sent</dt>
			<dd>
			<?php $this->widget('application.modules.admin.components.user.UserApiOutInfoWidget', array('userId'=>$model->user_id)); ?>
			</dd>
		</td>
		
	</tr>

</table>


<dl>





    <div class="row character">
        <dt>
            <?php echo $form->labelEx($model,'character'); ?>
        </dt>        
        <div class="clear"></div>        
        <?php echo CHtml::activeTextArea(
            $model, 
            'character',
            array(
                'onkeyup'=>'javascript:textchange(this.id,4000)',
            )
        ); ?>
        <?php echo $form->error($model,'character'); ?>
        <div class="clear"></div>
        <div id="UserEditMainTabForm_character_count_sym" class="left-chars-counter" style="width: 360px;"><?php echo 4000-strlen($model->character) ?></div>
        <div class="clear"></div>
    </div> 


    


</dl>

<?php $this->endWidget();?>

    <div class="submit">
        <button 
            class="btn" 
            data-loading-text='Saving...'
            data-success-text='Saved'
            onclick="javascript:formSubmit('mainEditTab');" 
        >Save</button>
    </div>

</div>