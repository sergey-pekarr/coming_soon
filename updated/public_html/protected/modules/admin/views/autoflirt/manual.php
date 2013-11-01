
<div id="FlirtManualFormBox" class="form" style="padding: 20px 0">
<div class="row">
<?php 

$form=$this->beginWidget('CActiveFormSw', array(
    'action'=>Yii::app()->createUrl('/admin/autoflirt/manual'),
    'method'=>'get',
));

	$this->widget(
		'application.modules.admin.components.forms.DateControlWidget', 
		array(
			'model'=>$model
		)
	);

?>

    <dt><?php echo $form->labelEx($model,'perPage'); ?></dt>
    <dd>
        <?php echo $form->dropDownList($model,'perPage', array('10'=>'10','50'=>'50','100'=>'100','200'=>'200','500'=>'500','1000'=>'1000')); ?>
    </dd> 
        
<?php $this->endWidget(); ?>


<dd style="margin-left: 40px;">
    <button 
        class="btn" 
        data-loading-text="loading..." 
        onclick="javascript:$('#FlirtManualFormBox .btn').button('loading'); $('#FlirtManualFormBox form').submit();" 
    >
        &nbsp;&nbsp;&nbsp;Show&nbsp;&nbsp;&nbsp;
    </button>
</dd>


</div>



</div>
<div class="clear"></div>


<h3>Member message center (<?php echo $rows['count']?>)</h3>
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

<?php 
//FB::warn($rows, 'rows');  	
if ($rows['list']) 
	foreach ($rows['list'] as $row) { 
  		
  			$profileFrom = new Profile($row['id_from']);
  			$profileTo = new Profile($row['id_to']);
  			
  			$usernames[$row['id_to']] = $profileTo->getDataValue('username');
  			$usernames[$row['id_from']] = $profileFrom->getDataValue('username');
?>
	<table id="msg_<?php echo $row['id'] ?>" class="table table-condensed FlirtManual">
		<tr>
			<th>Sender</th>
			<th>Recipient</th>
			<th>Replies</th>
			<th>Message</th>
		</tr>
		
		<tr>
			
			<td style="width: 200px">
				<div class="center">
					<?php $this->widget('application.modules.admin.components.user.UserPreviewWidget', array('userId'=>$row['id_from'], 'size'=>'medium')) ?>
				</div>
				<br />
				<?php $this->widget('application.modules.admin.components.user.UserInfoShotWidget', array('userId'=>$row['id_from'])) ?>				
			</td>
			
			<td class="promoUser" style="width: 200px; background-color:#FFDFDF">
				<div class="center">
					<?php $this->widget('application.modules.admin.components.user.UserPreviewWidget', array('userId'=>$row['id_to'], 'size'=>'medium')) ?>
				</div>
				<br />
				<?php $this->widget('application.modules.admin.components.user.UserInfoShotWidget', array('userId'=>$row['id_to'])) ?>				
			</td>
			
			<td style="width: 20px; text-align: center">
				<?php echo $row['replies'] ?>
			</td>
			
			<td style="padding: 20px">
				
				<?php  if ($row['allMessages']) { ?>
				<div class="allMessages">
					<table>
						<?php foreach ($row['allMessages'] as $m) { ?>
							<tr>
								<td><?php echo $usernames[$m['id_from']] ?>:</td>
								<td>
									<span class="smaller" style="color: #aaa"><?php echo $m['added']; ?> (<?php echo CHelperDate::date_distanceOfTimeInWords( strtotime($m['added']), time()).' ago'; ?>)</span>
									<br />
									<?php if ($m['subject']) { ?>
										<span class="bold"><?php echo $m['subject'] ?></span>
										<br />
									<?php } ?>
									<?php echo $m['text'] ?>
								</td>
							</tr>
						<?php } ?>
					</table>
				</div>
				<br />
				<?php } ?>
				
				
				<span class="smaller" style="color: #aaa">
					<?php echo $row['added']; ?> (<?php echo CHelperDate::date_distanceOfTimeInWords( strtotime($row['added']), time()).' ago'; ?>):
				</span>				
				<?php if ($row['subject']) { ?>
					<br />
					<span class="bold"><?php echo $row['subject'] ?></span>
				<?php } ?>
				<div style="background-color:lightyellow; padding: 4px"><?php echo $row['text'] ?></div>
				<br />
				
				<br />
				<table style="width: 90%">
					<?php /* <tr>
						<td width="1%">Subject: </td>
						<td><input type="text" value="" /></td>
					</tr> */ ?>
				
					<tr>
						<td width="1%">Message:</td>
						<td><textarea rows="3"></textarea></td>
					</tr>
					
					<tr>
						<td></td>
						<td>
							<button class="btn" onClick="javascript:sendFlirtMessage(<?php echo $row['id'] ?>)">Send</button>
						</td>
					</tr>
				</table>
			</td>
			
		</tr>
	</table>
<?php }?>





<script>
function sendFlirtMessage(id)
{
	//var subject = $("#msg_"+id+" input").val();
	var message = $("#msg_"+id+" textarea").val();

	if (/*subject=='' ||*/ message=='')
	{
		alert("Message empty...");
		return;
	}
	
    $.post(
            '/admin/ajax/sendFlirtMessage', 
            {id:id, /*subject:subject,*/ message:message},
            function(data) {
                if (data===undefined || data.success===undefined || data.success != 'Yes')
                   	alert('Error');
                else
	                if (data.success == 'Yes')
	                {
	                    //alert('success');
	                    $("#msg_"+id+" .promoUser").css('background-color', 'lightgreen');
	                    $("#msg_"+id+" button").hide();
	                }                
            },
            "json"
	);
	
	//alert(id+' - '+subject+' - '+message);
}
</script>



