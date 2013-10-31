
<div id="logsEmailsFormBox" class="form">
<div class="row">
<?php 
$form=$this->beginWidget('CActiveFormSw', array(
    'action'=>Yii::app()->createUrl('/admin/logs/emails'),
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
    
    <dt><?php echo $form->labelEx($model,'sort'); ?></dt>
    <dd>
        <?php echo $form->dropDownList($model,'sort', array('idASC'=>'id ASC','idDESC'=>'id DESC')); ?>
    </dd>

    <dt><?php echo $form->labelEx($model,'template'); ?></dt>
    <dd>
        <?php echo $form->dropDownList($model,'template', $model->templatesTitle); ?>
    </dd>

<?php $this->endWidget(); ?>


<dd style="margin-left: 40px;">
    <button 
        class="btn actionShow" 
        data-loading-text="loading..." 
        onclick="javascript:$('#logsEmailsFormBox .actionShow').button('loading'); $('#logsEmailsFormBox form').submit();" 
    >
        &nbsp;&nbsp;&nbsp;Show&nbsp;&nbsp;&nbsp;
    </button>
</dd>


</div>
</div>
<div class="clear"></div>


<div class="listsInfo">
    <div class="left">
        Found: <?php echo $emails['count'] ?>
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


<h3>Emails</h3>
<table class="table table-condensed albums">

    <tr>
        <th class="center">#</th>
        <th class="center">id</th>
        <th>Status</th>
        <th>Added</th>
        <th>Template</th>
        <th>User</th>
        <th>To</th>
        <th>Subject</th>
        <th>Body</th>
    </tr>
<?php 
if ($emails['list'])
    foreach ($emails['list'] as $k=>$row) {
		
    	/*$to = (is_array($row['to'])) ? $row['to'] : unserialize($row['to']);
    	$to = @implode('<br />', $to);*/
    	$to = $row['to'];
    ?>
        <tr>
            <td><?php echo $k+1+($pages->getCurrentPage() * $pages->getPageSize()) ?></td> 
            <td><?php echo $row['id'] ?></td>        
        	
        	<td>
        		<?php if ($row['sent']=='0') { ?> 
        			<span class="label">waiting...</span>
        		<?php } else { ?>	
        			<span class="label label-success">sent</span> 
        		<?php } ?>
        		
        	</td>
        	
        	<td class="smaller">
        		<?php echo $row['added'] ?>
        		<br />
        		<span class="smaller"><?php echo CHelperDate::date_distanceOfTimeInWords( strtotime($row['added']), time()).' ago'; ?></span>
        	</td>
        	
        	<td><?php echo $row['template'] ?></td>
        	
            <td>
                <?php $this->widget('application.modules.admin.components.user.UserInfoShotWidget', array('userId'=>$row['user_id']));  ?>
            </td>
            
            <td><?php echo $to ?></td>
			
            <td><?php echo $row['subject'] ?></td>

            <td>
            	<a class="btn" href="javascript:bodyShow(<?php echo $row['id'] ?>)">Show</a>
            </td>
            
        </tr> 
        
    <?php
    }
?>
</table>



<script>
function bodyShow(id)
{
	$("#emailBodyModal .modal-body .subject").html('');
	$("#emailBodyModal .modal-body .body").html('<div class="center"><img src="/images/design/loading.gif" /></div>');
	$("#emailBodyModal").modal('show');
	
    $.post("/admin/logs/EmailsBodyShow", 
            {id:id}, 
            function(data){
                if(typeof(data) != 'undefined')
    			{
                    $("#emailBodyModal .modal-body .subject").html(data.subject);
                    $("#emailBodyModal .modal-body .body").html(data.body);
    			}
        }, "json");	
}
</script>




<div id="emailBodyModal" class="modal hide fade" style="min-width:600px; min-height: 300px">
    <div class="modal-header">
        <a class="close" onclick="javascript: $('#emailBodyModal').modal('hide');">&times;</a>
    </div>
    <div class="modal-body">
    	<div class="subject" style="font-weight: bold; margin-bottom: 20px; padding-left:20px"></div>
    	<div class="body"></div>
    </div>   
</div>


