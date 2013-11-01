
<div id="usersFormBox" class="form">
<div class="row">
<?php 

$form=$this->beginWidget('CActiveFormSw', array(
    'action'=>Yii::app()->createUrl('/admin/users/index'),
    'method'=>'get',
)); 


	$this->widget(
		'application.modules.admin.components.forms.DateControlWidget', 
		array(
			'model'=>$model
		)
	);

?>
    <dt><?php echo $form->labelEx($model,'userRole'); ?></dt>
    <dd>
        <?php echo $form->dropDownList($model,'userRole', array(''=>'All','justjoined'=>'Just joined','free'=>'Free','gold'=>'Gold')); ?>
    </dd>  

    <dt><?php echo $form->labelEx($model,'form'); ?></dt>
    <dd>
        <?php echo $form->dropDownList($model,'form', CHelperAdmin::getForms(true)); ?>
    </dd> 


    <dt><?php echo $form->labelEx($model,'affid'); ?></dt>
    <dd>
        <?php echo $form->dropDownList($model,'affid', $affs); ?>
    </dd> 
 


    <dt><?php echo $form->labelEx($model,'perPage'); ?></dt>
    <dd>
        <?php echo $form->dropDownList($model,'perPage', array('10'=>'10','50'=>'50','100'=>'100','200'=>'200','500'=>'500','1000'=>'1000')); ?>
    </dd> 

    <dt><?php echo $form->labelEx($model,'sort'); ?></dt>
    <dd>
        <?php echo $form->dropDownList($model,'sort', array('idASC'=>'id ASC','idDESC'=>'id DESC')); ?>
    </dd> 


<?php $this->endWidget(); ?>


<dd style="margin-left: 40px;">
    <button 
        class="btn" 
        data-loading-text="loading..." 
        onclick="javascript:$('#usersFormBox .btn').button('loading'); $('#usersFormBox form').submit();" 
    >
        &nbsp;&nbsp;&nbsp;Show&nbsp;&nbsp;&nbsp;
    </button>
</dd>



</div>
</div>
<div class="clear"></div>


<div class="listsInfo">
    <div class="left">
        Found: <?php echo $users['count'] ?>
    </div>
      
    <?php 
        $this->widget(
                    'CLinkPager', 
                    array(
                        'pages' => $pages,
                        'currentPage'=>$pages->getCurrentPage(),//(false)
                        'header'=>'',
                        'htmlOptions'=>array('class'=>'pagination'),                        
                    )
    	);?>
    <div class="clear"></div>        
</div>



<h3>Users</h3>
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
    <th>Api sent</th>
    <th>&nbsp;</th>
</thead>

<tbody>
<?php 
if ($users['list'])
{
        foreach ($users['list'] as $k=>$userId)
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
            	
            	<td><?php echo $k+1+($pages->getCurrentPage() * $pages->getPageSize()) ?></td>
            	
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
                	<?php $this->widget('application.modules.admin.components.user.UserEmailInfoWidget', array('userId'=>$userId)) ?>
                </td>

                <td>
                    <?php $activity = $profile->getDataValue('activity') ?>
                    Joined: <?php echo $activity['joined'] ?>
                    
                    <br />
                    <?php if ( $activity['loginCount'] ) { ?>
                        Last activity: 
                        <?php echo CHelperDate::date_distanceOfTimeInWords( strtotime($activity['activityLast']), time()).' ago'; ?>
                        <br />
                        Logins: <?php echo $activity['loginCount'] ?>,
                    <?php } ?>
                    
                    <?php 
                    $ext = $profile->getDataValue('ext');
                    if ($ext['facebook']) { ?>
                        <br />
                        Joined from <span class="label notice">facebook</span>
                    <?php } ?>

                    
                    <?php $this->widget('application.modules.admin.components.user.UserInfoRefShowWidget', array('userId'=>$userId)) ?>
                    <?php /*if ($profile->getDataValue('info', 'ref_url')) { ?>
	                    Ref:<a href="javascript:void(0)" onclick="javascript:slideUpDownBox('user_ref_box_<?php echo $profile->getId() ?>')" title="<?php echo $profile->getDataValue('info', 'ref_url') ?>" >
	                    	<?php echo $profile->getDataValue('info', 'ref_domain') ?>
	                    </a>
	                    <div class="user_ref_box" id="user_ref_box_<?php echo $profile->getId() ?>">
	                    	<textarea rows="6" cols=""><?php echo $profile->getDataValue('info', 'ref_url') ?></textarea>
	                    </div>
                    <?php }*/ ?>                    
                    
                </td>
                
                <td><?php $this->widget('application.modules.admin.components.user.UserApiOutInfoWidget', array('userId'=>$userId)); ?></td>
                
                <?php /*
                <td><?php echo $loc; ?></td>
                */ ?>
                
                <td>
<?php /*                    
                    <a class="btn btn-danger" href="javascript:if (confirm('Delete? Are you sure?')) userDelete(<?php echo $profile->getDataValue('id'); ?>);">Delete</a>
                     &nbsp;&nbsp;&nbsp;
                    <a href="/admin/users/edit?id=<?php echo $profile->getDataValue('id'); ?>">Edit</a>
*/ ?>
					<a href="/admin/users/edit?id=<?php echo $profile->getDataValue('id'); ?>">Edit</a>
                </td>
            
            </tr>
             
        <?php
        }
} ?>
</tbody>
</table>

