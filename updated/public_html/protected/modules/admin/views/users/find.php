<div id="usersFindFormBox" class="form">



<div class="row">

<table class="table">

<?php 

$form=$this->beginWidget('CActiveFormSw', array(
    'action'=>Yii::app()->createUrl('/admin/users/find'),
    'method'=>'get',
)); 
?>
	<tr>
		<td>
		    <dt><?php echo $form->labelEx($model,'userId'); ?></dt>
		    <dd>
		        <?php echo $form->textField($model,'userId'); ?>
		    </dd>
		    <?php echo $form->error($model,'userId'); ?> 
		    <div class="clear"></div>		
		</td>

		<td>
		    <dt><?php echo $form->labelEx($model,'zombaio_sub_id'); ?></dt>
		    <dd>
		        <?php echo $form->textField($model,'zombaio_sub_id'); ?>
		    </dd>
		    <?php echo $form->error($model,'zombaio_sub_id'); ?> 
		    <div class="clear"></div>  		       		
		</td>
	</tr>
	

	<tr>
		<td>
		    <dt><?php echo $form->labelEx($model,'username'); ?></dt>
		    <dd>
		        <?php echo $form->textField($model,'username'); ?>
		    </dd>
		    <?php echo $form->error($model,'username'); ?> 
		    <div class="clear"></div>  		
		</td>

		<td>
		    <dt><?php echo $form->labelEx($model,'ccname1'); ?></dt>
		    <dd>
		        <?php echo $form->textField($model,'ccname1'); ?>
		    </dd>
		    <?php echo $form->error($model,'ccname1'); ?> 
		    <div class="clear"></div>	
    		
		</td>
	</tr>


	<tr>
		<td>
		    <dt><?php echo $form->labelEx($model,'email'); ?></dt>
		    <dd>
		        <?php echo $form->textField($model,'email'); ?>
		    </dd>
		    <?php echo $form->error($model,'email'); ?> 
		    <div class="clear"></div> 		
		</td>

		<td>
			<dt><?php echo $form->labelEx($model,'ccname2'); ?></dt>
		    <dd>
		        <?php echo $form->textField($model,'ccname2'); ?>
		    </dd>
		    <?php echo $form->error($model,'ccname2'); ?> 
		    <div class="clear"></div>	    		
		</td>
	</tr>


	<tr>
		<td>
		    <dt><?php echo $form->labelEx($model,'profileIdEncr'); ?></dt>
		    <dd>
		        <?php echo $form->textField($model,'profileIdEncr'); ?>
		    </dd>
		    <?php echo $form->error($model,'profileIdEncr'); ?> 
		    <div class="clear"></div>   		
		</td>

		<td>
		     		
		</td>
	</tr>

	<tr>
		<td>
		    <dt><?php echo $form->labelEx($model,'ref_domain'); ?></dt>
		    <dd>
		        <?php echo $form->textField($model,'ref_domain'); ?>
		    </dd>
		    <?php echo $form->error($model,'ref_domain'); ?> 
		    <div class="clear"></div> 			      		
		</td>

		<td>
    		
		</td>
	</tr>

</table>

<?php $this->endWidget(); ?>

<div class="center">
    <button 
        class="btn" 
        data-loading-text="loading..." 
        onclick="javascript:$('#usersFindFormBox .btn').button('loading'); $('#usersFindFormBox form').submit();" 
    >
        &nbsp;&nbsp;Search&nbsp;&nbsp;
    </button>
</div>

</div>
</div>
<div class="clear"></div>


<?php if (isset($res) && $res ) { ?>



<h3>Found <?php echo count($res) ?></h3>
<table class="table table-condensed albums">
<thead>
    <th>#</th>
    <th>ID</th>
    <th>Preview</th>
    <th>Info</th>
    <th>Payment Info</th>
    <th>Aff</th>
    <th>Form</th>    
    <th>Email</th>
    <th>Activity</th>
    <th>&nbsp;</th>
</thead>

<tbody>
<?php 
if ($res)
{
        foreach ($res as $k=>$userId)
        {
            $profile = new Profile($userId);
            
            /*$userData = $profile->getData();
	        $location = $userData['location'];        
	
			$loc = '<img class="flag" src="'.Yii::app()->location->flagUrl($location['country']).'" />'."&nbsp;";
		    $loc.= $location['city'].", "; 
		    $loc.= ($location['stateName']) ? $location['stateName'].", " : "";
		    $loc.= $location['country'];*/
            
            ?>
            
            <tr id="tr_<?php echo $profile->getDataValue('id'); ?>" >
            	
            	<td><?php echo $k+1 ?></td>
            	
                <td>
                    <?php echo $profile->getDataValue('id'); ?>
                </td>

                <td>
                    <?php $this->widget('application.modules.admin.components.user.UserPreviewWidget', array('userId'=>$userId)) ?>
                </td>                
                
                <td>
                    <?php $this->widget('application.modules.admin.components.user.UserInfoShotWidget', array('userId'=>$userId)) ?>
                </td>

                <td>
                    <?php $this->widget('application.modules.admin.components.user.UserPaymentInfoWidget', array('userId'=>$userId)) ?>
                </td>

				<td><?php echo ($profile->getDataValue('promo')=='0') ? $profile->getDataValue('affid') : "" ?></td>
				<td><?php echo ($profile->getDataValue('promo')=='0') ? $profile->getDataValue('form') : "" ?></td>
                <td>
                	<?php //echo ($profile->getDataValue('promo')=='0') ? $profile->getDataValue('email') : "" ?>
                	<?php $this->widget('application.modules.admin.components.user.UserEmailInfoWidget', array('userId'=>$profile->getDataValue('id'))) ?>
                </td>


                <td>
                    <?php 
                    	$activity = $profile->getDataValue('activity');
                    	$info = $profile->getDataValue('info');
                    ?>
                    Joined: <?php echo $activity['joined'] ?>
                    <br />
                    <?php if ( $activity['loginCount'] ) { ?>
                        
                        Last activity: 
                        <?php echo CHelperDate::date_distanceOfTimeInWords( strtotime($activity['activityLast']), time()).' ago'; ?>
                        <br />
                        Logins: <?php echo $activity['loginCount'] ?>
                    <?php } ?>
                    
                    <?php 
                    $ext = $profile->getDataValue('ext');
                    if ($ext['facebook']) { ?>
                        <br />
                        Joined from <span class="label notice">facebook</span>
                    <?php } ?>
                    
                    <?php $this->widget('application.modules.admin.components.user.UserInfoRefShowWidget', array('userId'=>$userId)) ?>
                    
                </td>
                
                <?php /*
                <td><?php echo $loc; ?></td>
                */ ?>
                
                <td>
                    <?php /*
                    <a class="btn btn-danger" href="javascript:if (confirm('Delete? Are you sure?')) userDelete(<?php echo $profile->getDataValue('id'); ?>);">Delete</a>
                     &nbsp;&nbsp;&nbsp;
                     */ ?>
                    <a class="btn" href="/admin/users/edit?id=<?php echo $profile->getDataValue('id'); ?>">Edit</a>
                </td>
            
            </tr>
             
        <?php
        }
} ?>
</tbody>
</table>




<?php } else { ?>
	<?php if ( isset($res) ) { ?>
		<h3 class="center">Not found...</h3>
	<?php } ?>
<?php } ?>