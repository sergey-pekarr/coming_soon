<div id="PaymentReversalsFormBox" class="form">
<div class="row">
<?php 

$form=$this->beginWidget('CActiveFormSw', array(
    'action'=>Yii::app()->createUrl('/admin/payments/ReversalReport'),
    'method'=>'get',
)); 

	$this->widget(
		'application.modules.admin.components.forms.DateControlWidget', 
		array(
			'model'=>$model
		)
	);

?>

    <dt><?php echo $form->labelEx($model,'mod'); ?></dt>
    <dd>
        <?php echo $form->dropDownList($model,'mod', HelperReversals::getModsForSelect()); ?>
    </dd> 

    <dt><?php echo $form->labelEx($model,'form'); ?></dt>
    <dd>
        <?php echo $form->dropDownList($model,'form', CHelperUser::adminsGetFormsForSelect()); ?>
    </dd>     
    
    <dt><?php echo $form->labelEx($model,'sort'); ?></dt>
    <dd>
        <?php echo $form->dropDownList($model,'sort', array('idASC'=>'id ASC','idDESC'=>'id DESC','affid'=>'affid')); ?>
    </dd>    
    
    <dt><?php echo $form->labelEx($model,'perPage'); ?></dt>
    <dd>
        <?php echo $form->dropDownList($model,'perPage', array('10'=>'10','50'=>'50','100'=>'100','200'=>'200','500'=>'500','1000'=>'1000')); ?>
    </dd> 

<?php $this->endWidget(); ?>


<dd style="margin-left: 40px;">
    <button 
        class="btn" 
        data-loading-text="loading..." 
        onclick="javascript:$('#PaymentReversalsFormBox .btn').button('loading'); $('#PaymentReversalsFormBox form').submit();" 
    >
        &nbsp;&nbsp;&nbsp;Show&nbsp;&nbsp;&nbsp;
    </button>
</dd>


</div>
</div>
<div class="clear"></div>


<div class="listsInfo">
    <div class="left">
        <h3>
        	Reversal report
        	<span class="smaller">(<?php echo $res['count'] ?>)</span>
        </h3>        
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







<table cellspacing="2" cellpadding="0" border="0" style="width: auto;">
<tr>
		<?php foreach (HelperReversals::getMods() as $k=>$m) { 
			if (!$m[3]) continue;
			?>
			<?php  /* <td style="background-color: <?php echo $m[1] ?>; border:1px solid #c0c0c0; width:32px">&nbsp;</td><td class="smaller"><?php echo $m[0] ?></td><td>&nbsp;</td> */ ?>
			<td class="smaller" style="background-color: <?php echo $m[1] ?>; border:1px solid #c0c0c0; width: 50px"><?php echo $m[0] ?></td><td>&nbsp;</td>
		<?php } ?>
</tr>
</table>




<table cellspacing="0" cellpadding="5" border="0" class="table" style="width: auto; margin-top: 20px">

<tr>
    <th width="80">Date Added</th>
    <th width="80">Date Shows</th>
    
    <th width="80">Aff ID</th>
    <th width="100">User ID</th>
    <th width="100">Username</th>
    <th width="200">Reason</th>
    <th width="70">Amount</th>
    <th width="70">Form</th>
    <?php /* <th width="70">Aff banned</th> */ ?>
</tr>

<?php if ( isset($res['list']) && $res['list'] ) { 

	$mods = HelperReversals::getMods();
	$forms = CHelperUser::adminsGetForms();
	?>
	<?php foreach ($res['list'] as $r) { 
		
		if (isset($r['type']))
		{
			$bg = $mods[$r['type']][1];
			$reasonText = $mods[$r['type']][0];
		}
		else
		{
			$bg = 'red';
			$reasonText = "";
		}
	?>

    <tr style="background-color:<?php echo $bg ?>;" id="tr_<?php echo $r['id'] ?>">
        <td><?php echo $r['date_real'] ?></td>
        <td><?php echo $r['date'] ?></td>
        
        <td><a href="/checkaff.php?user=<?php echo $r['aff_id'] ?>"><?php echo $r['aff_id'] ?></a></td>
        <td><a href="/admin/users/edit?id=<?php echo $r['user_id'] ?>"><?php echo $r['user_id'] ?></a></td>
        <td><?php echo $r['username'] ?></td>
        <td>
            <span id="reasonText_<?php echo $r['id'] ?>" ondblclick="javascript:jQuery('#newReasonBox_<?php echo $r['id'] ?>').show();" style="cursor: pointer;"><?php echo $reasonText ?></span>
            <div id="newReasonBox_<?php echo $r['id'] ?>" style="display:none">
                <select id="newReason_<?php echo $r['id'] ?>" onchange="javascript:changeReason(<?php echo $r['id'] ?>)">
                
                	<?php 
                	$mods = HelperReversals::getMods();
                	foreach ($mods as $k=>$m) { ?>
                        <option value="<?php echo $k ?>" <?php if ($k==$r['type'] ) { ?>selected="selected"<?php } ?>><?php echo $mods[$k]['0'] ?></option>
                    <?php } ?>
                    
                </select>
                <br />
                <br />
                or
                <span style="background-color:white; font-weight:bold">&nbsp;&nbsp;<a href="javascript:void(0)" onclick="javascript: if (confirm('Are you sure?')) deleteRow(<?php echo $r['id'] ?>)" >Delete</a>&nbsp;&nbsp;</span>
                
            </div>
            
        </td>
        
        <td><?php echo $r['amount'] ?></td>
        <td><?php echo $r['form'] ?></td>
        <?php /* <td {if $r.aff_banned=='1'}style="background-color:red;"{/if}>{if $r.aff_banned=='1'}banned{else}&nbsp;{/if}></td> */ ?>
        
    </tr>
  
<?php } ?>

<?php }  else { ?>
	<tr>
	<td colspan="30" style="padding:32px; text-align:center" align="center">
	No information found
	</td>
	</tr>
<?php } ?>
</table>






<script>
    function changeReason(id)
    {
        var newReason = jQuery("#newReason_"+id).val();
      
        jQuery.post(
            '/admin/payments/ReversalReportFix', 
            {action:'changeReason', id:id, newReason:newReason}, 
            function(data) {
                if (data.success=='Yes')
                {
                    jQuery('#reasonText_'+id).html(data.newReasonText);
                    jQuery('#newReasonBox_'+id).fadeOut(800, function(){
						jQuery('#tr_'+id).attr('style', 'background-color:'+data.bgcolor);
                    });
                }
                    
            },
            "json"
        );
    }    
    
    function deleteRow(id)
    {
        jQuery.post(
            '/admin/payments/ReversalReportFix', 
            {action:'delete', id:id}, 
            function(data) {
                if (data.success=='Yes')
                {
                    jQuery('#tr_'+id).fadeOut(800);
                }
                    
            },
            "json"
        );
    }     
</script>
