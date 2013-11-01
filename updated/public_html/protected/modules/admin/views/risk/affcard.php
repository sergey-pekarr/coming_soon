<h3>
    Affiliate and Card information</h3>

<div id="refundFormControl">
<?php 

$form=$this->beginWidget('CActiveFormSw', array(
	'action'=>Yii::app()->createUrl('/admin/risk/affcardinfo'),
	'method'=>'get',
	)); 
?>
<table>
	<tr>
		<td>
			<?php 
			$this->widget(
				'application.modules.admin.components.forms.DateControlWidget', 
				array(
						'model'=> $model
						)
					);
			?>
		</td>
		<td></td>
		<td style="vertical-align: top;">
			    <dt><?php echo $form->labelEx($model,'aff'); ?></dt>
				<dd>
					<?php echo $form->dropDownList($model,'aff', $model->affForSelect, array('style' => 'width: 150px')); ?>
				</dd> 
		</td>
		<td></td>
		<td style="vertical-align: top;">
			    <dt><?php echo $form->labelEx($model,'sort'); ?></dt>
				<dd>
					<?php echo $form->dropDownList($model,'sort', array('Id' => 'Id', 'Name' => 'Name'), array('style' => 'width: 100px;')); ?>
				</dd> 
		</td>
		<td></td>
		<td>	<dt><?php echo $form->labelEx($model,'ids'); ?></dt>
				<dd>
					<?php echo $form->textField($model,'ids', array('style' => 'width: 100px;')); ?>
				</dd> 
		</td>
		<td></td>
		<td style="vertical-align: top;">		
		    <button class="btn" data-loading-text="loading..." 
				onclick="javascript:$('#refundFormControl .btn').button('loading'); $('#refundFormControl form').submit();">
				&nbsp;&nbsp;&nbsp;Show&nbsp;&nbsp;&nbsp;
			</button>
		</td>
	</tr>
	<tr>
		<td colspan="2">
		    <?php 
		    $this->widget(
		    	'CLinkPager', 
		    	array(
		    			'pages' => $pages,
		    			'currentPage'=>$pages->getCurrentPage(),
		    			'header'=>'',
		    			'htmlOptions'=>array('class'=>'pagination'),                        
		    			)
		    		);
		    ?>
		</td>
	</tr>
</table>
<?php $this->endWidget(); ?>
</div>
<style>
	#refundFormControl table td
	{
		vertical-align:top;
	}
	.duplicate
	{
		font-weight: bold;
		color: blue;
	}
	.highlight
	{
		font-weight: bold;
		color: red;
	}
</style>
<?
//Id, g.user_id, g.username, g.email, t.ccname, t.ccnum, t.firstname, t.lastname, t.address, t.country, t.state, t.city, t.email
?>
<hr style="margin: 10px 5px;" />
<div>
    <table id="refundItems" class="table summary">
        <thead>
            <td>
                No
            </td>
			<td>
				TransDate
			</td>
			<td>
			</td>
            <td>
                Id
            </td>
            <td>
                UserId
            </td>
            <td>
                Username
            </td>
            <td>
                Email
            </td>
            <td>
                ccname
            </td>
            <td>
                ccnum
            </td>
            <td>
                First Name
            </td>
            <td>
                Last Name
            </td>
            <td>
                Address
            </td>
            <td>
                Country
            </td>
            <td>
                State
            </td>
            <td>
                City
            </td>
            <td>
                Email
            </td>
        </thead>
        <tbody>
		<?php
		$manager = null;
		$showManager = false;
		foreach($items as $item){
			if($manager != null && $manager != $item['manager_name']){
				$showManager = true;
				break;
			}
			$manager = $item['manager_name'];
		}
		$i = 1;
		for($j = 0; $j < count($items); $j++){
			$item = $items[$j];
			$dup = false;
			if($j>0 && $items[$j-1]['firstname'] == $item['firstname'] && $items[$j-1]['lastname'] == $item['lastname']) $dup = true;
			if($j< count($items) - 1 && $items[$j+1]['firstname'] == $item['firstname'] && $items[$j+1]['lastname'] == $item['lastname']) $dup = true;
			if(in_array($item['id'], $ids)) $dup = true;
		?>
<tr class="<?php if($dup) echo 'duplicate'; ?>">
				<td><?php echo $i; ?></td>
				<td><?php echo date('Y-m-d', strtotime($item['transdate'])); ?></td>
				<td><?php if($showManager) echo $item['manager_name']; ?></td>
				<td><?php echo $item['id']; ?></td>
				<td><?php echo $item['user_id']; ?></td>
				<td><?php echo $item['username']; ?></td>
				<td><?php echo $item['email']; ?></td>
				<td><?php echo $item['ccname']; ?></td>
				<td><?php echo $item['ccnum']; ?></td>
				<td><?php echo $item['firstname']; ?></td>
				<td><?php echo $item['lastname']; ?></td>
				<td><?php echo $item['address']; ?></td>
				<td><?php echo $item['country']; ?></td>
				<td><?php echo $item['state']; ?></td>
				<td><?php echo $item['city']; ?></td>
				<td><?php echo $item['email']; ?></td>
</tr>
		<?php 
		$i++;
		} ?>		
        </tbody>
    </table>
</div>
<style>
table#refundItems thead td
{
	text-align:center;
}
table#refundItems tbody td
{
	text-align:center;
}
table#refundItems tbody td:nth-child(2), table#refundItems tbody td:nth-child(3)
{
	text-align:left;
}
</style>